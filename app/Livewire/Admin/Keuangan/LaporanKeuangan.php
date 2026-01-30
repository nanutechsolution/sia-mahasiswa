<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\ProgramKelas;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKeuangan extends Component
{
    use WithPagination;

    // Filter State
    public $semesterId;
    public $filterProdiId;
    public $filterProgramKelasId;
    public $filterAngkatan;
    public $filterStatus; // LUNAS, CICIL, BELUM, LEBIH_BAYAR (Anomali)
    public $search = '';

    // UI State
    public $showDetailModal = false;
    public $selectedTagihan = null;
    public $viewMode = 'dashboard'; // 'dashboard' or 'table_only'

    // Statistik Global
    public $globalStats = [];

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
        $this->calculateGlobalStats();
    }

    public function updated($fields)
    {
        $this->resetPage();
        $this->calculateGlobalStats(); // Recalculate saat filter berubah
    }

    // --- ANALYTICS ENGINE (POWERFULL FEATURE) ---
    
    public function calculateGlobalStats()
    {
        // Query Dasar (Sesuai Filter)
        $query = $this->buildQuery(true); // true = skip order/paginate

        $aggregates = $query->selectRaw('
            SUM(total_tagihan) as total_bill, 
            SUM(total_bayar) as total_paid,
            COUNT(*) as total_mhs,
            SUM(CASE WHEN status_bayar = "LUNAS" THEN 1 ELSE 0 END) as count_lunas,
            SUM(CASE WHEN total_bayar > total_tagihan THEN 1 ELSE 0 END) as count_anomali
        ')->first();

        $this->globalStats = [
            'bill' => $aggregates->total_bill ?? 0,
            'paid' => $aggregates->total_paid ?? 0,
            'debt' => ($aggregates->total_bill ?? 0) - ($aggregates->total_paid ?? 0),
            'rate' => ($aggregates->total_bill > 0) ? (($aggregates->total_paid / $aggregates->total_bill) * 100) : 0,
            'students' => $aggregates->total_mhs ?? 0,
            'lunas_count' => $aggregates->count_lunas ?? 0,
            'anomali_count' => $aggregates->count_anomali ?? 0,
        ];
    }

    /**
     * Mengambil Performa Pembayaran Per Prodi (Untuk Grafik/Bar)
     */
    public function getProdiPerformanceProperty()
    {
        return TagihanMahasiswa::where('tahun_akademik_id', $this->semesterId)
            ->join('mahasiswas', 'tagihan_mahasiswas.mahasiswa_id', '=', 'mahasiswas.id')
            ->join('ref_prodi', 'mahasiswas.prodi_id', '=', 'ref_prodi.id')
            ->selectRaw('
                ref_prodi.nama_prodi, 
                ref_prodi.kode_prodi_internal,
                SUM(tagihan_mahasiswas.total_tagihan) as target,
                SUM(tagihan_mahasiswas.total_bayar) as realisasi
            ')
            ->groupBy('ref_prodi.nama_prodi', 'ref_prodi.kode_prodi_internal')
            ->orderByDesc('realisasi')
            ->get()
            ->map(function($item) {
                $item->persen = $item->target > 0 ? ($item->realisasi / $item->target) * 100 : 0;
                return $item;
            });
    }

    /**
     * Mengambil Tren Pembayaran 6 Bulan Terakhir (Cashflow)
     */
    public function getMonthlyTrendProperty()
    {
        // Menggunakan tabel pembayaran untuk real cashflow
        return PembayaranMahasiswa::selectRaw("DATE_FORMAT(tanggal_bayar, '%Y-%m') as bulan, SUM(nominal_bayar) as total")
            ->where('status_verifikasi', 'VERIFIED')
            ->where('tanggal_bayar', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get()
            ->map(function($item) {
                $item->bulan_label = Carbon::createFromFormat('Y-m', $item->bulan)->isoFormat('MMM Y');
                return $item;
            });
    }

    // --- CORE QUERY ---
    private function buildQuery($isStats = false)
    {
        $query = TagihanMahasiswa::query()
            ->with(['mahasiswa.prodi', 'mahasiswa.programKelas', 'mahasiswa.person', 'tahunAkademik'])
            ->where('tahun_akademik_id', $this->semesterId);

        // Filter Logic
        if ($this->filterProdiId) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $this->filterProdiId));
        }
        if ($this->filterProgramKelasId) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_kelas_id', $this->filterProgramKelasId));
        }
        if ($this->filterAngkatan) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('angkatan_id', $this->filterAngkatan));
        }

        // Advanced Status Filter
        if ($this->filterStatus) {
            if ($this->filterStatus == 'LUNAS') $query->where('status_bayar', 'LUNAS');
            elseif ($this->filterStatus == 'CICIL') $query->where('status_bayar', 'BELUM')->where('total_bayar', '>', 0);
            elseif ($this->filterStatus == 'BELUM') $query->where('status_bayar', 'BELUM')->where('total_bayar', '=', 0);
            elseif ($this->filterStatus == 'LEBIH_BAYAR') $query->whereColumn('total_bayar', '>', 'total_tagihan');
        }

        if ($this->search) {
            $query->whereHas('mahasiswa', function ($q) {
                $q->whereHas('person', fn($qp) => $qp->where('nama_lengkap', 'like', '%' . $this->search . '%'))
                  ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function render()
    {
        $tagihans = $this->buildQuery()
            ->orderByRaw('total_tagihan - total_bayar DESC') // Prioritaskan penunggak terbesar
            ->paginate(15);

        return view('livewire.admin.keuangan.laporan-keuangan', [
            'tagihans' => $tagihans,
            'semesters' => TahunAkademik::orderBy('kode_tahun', 'desc')->get(),
            'prodis' => Prodi::all(),
            'programKelas' => ProgramKelas::where('is_active', true)->get(),
            'angkatans' => DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get(),
        ]);
    }

    // --- EXPORT CSV (Enterprise Format) ---
    public function exportLaporan()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['NIM', 'Nama Lengkap', 'Prodi', 'Kelas', 'Angkatan', 'Semester', 'Tagihan (Rp)', 'Terbayar (Rp)', 'Sisa (Rp)', 'Status', 'Persentase', 'Last Update']);

            $this->buildQuery()->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $persen = $row->total_tagihan > 0 ? ($row->total_bayar / $row->total_tagihan * 100) : 0;
                    fputcsv($handle, [
                        $row->mahasiswa->nim,
                        $row->mahasiswa->person->nama_lengkap ?? $row->mahasiswa->nama_lengkap,
                        $row->mahasiswa->prodi->nama_prodi,
                        $row->mahasiswa->programKelas->nama_program,
                        $row->mahasiswa->angkatan_id,
                        $row->tahunAkademik->nama_tahun,
                        $row->total_tagihan,
                        $row->total_bayar,
                        $row->sisa_tagihan,
                        $row->status_bayar,
                        number_format($persen, 2) . '%',
                        $row->updated_at->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($handle);
        }, 'Laporan_Keuangan_BAUK_' . date('Y-m-d_H-i') . '.csv');
    }

    // Modal Details
    public function openDetail($id) {
        $this->selectedTagihan = TagihanMahasiswa::with(['pembayarans', 'mahasiswa.person', 'mahasiswa.prodi'])->find($id);
        $this->showDetailModal = true;
    }
    public function closeDetail() { $this->showDetailModal = false; $this->selectedTagihan = null; }
}