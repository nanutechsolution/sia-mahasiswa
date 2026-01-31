<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\ProgramKelas;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKeuangan extends Component
{
    use WithPagination;

    // Filters
    public $semesterId;
    public $filterProdiId;
    public $filterProgramKelasId;
    public $filterAngkatan;
    public $filterStatus; // LUNAS, CICIL, BELUM, LEBIH_BAYAR
    public $search = '';

    // UI State
    public $showDetailModal = false;
    public $selectedTagihan = null;
    
    // Audit Data
    public $auditTimeline = [];

    // Statistik Dashboard
    public $globalStats = [];

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
        $this->calculateGlobalStats();
    }

    public function updated($fields) 
    { 
        $this->resetPage(); 
        $this->calculateGlobalStats(); 
    }
    
    // --- ANALYTICS ENGINE (REAL-TIME) ---

    public function calculateGlobalStats()
    {
        $query = $this->buildQuery(true); // Query tanpa limit/order

        // Menggunakan subquery/join untuk menghitung net value secara akurat di level database
        // Namun untuk performa di Livewire, kita gunakan agregasi kasar dulu, detail di row table
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

    // --- CORE QUERY BUILDER ---

    private function buildQuery($isStats = false)
    {
        // Eager load relasi penting & relasi audit (creator, verifier)
        $query = TagihanMahasiswa::query()
            ->with([
                'mahasiswa.prodi', 
                'mahasiswa.programKelas', 
                'mahasiswa.person', 
                'tahunAkademik', 
                'adjustments', // Penting untuk hitungan koreksi
                'pembayarans',
                'creator' // Siapa pembuat tagihan
            ])
            ->where('tahun_akademik_id', $this->semesterId);

        // Filter Logic
        if ($this->filterProdiId) $query->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $this->filterProdiId));
        if ($this->filterProgramKelasId) $query->whereHas('mahasiswa', fn($q) => $q->where('program_kelas_id', $this->filterProgramKelasId));
        if ($this->filterAngkatan) $query->whereHas('mahasiswa', fn($q) => $q->where('angkatan_id', $this->filterAngkatan));

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
        // Clone query agar tidak konflik dengan perhitungan stats
        $tagihans = (clone $this->buildQuery())
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

    // --- MODAL DETAILS & AUDIT TRAIL ---
    
    public function openDetail($id)
    {
        // Load data lengkap termasuk Aktor (User)
        $this->selectedTagihan = TagihanMahasiswa::with([
            'pembayarans.verifier', // Load Verifikator Pembayaran
            'adjustments.creator',  // Load Pembuat Koreksi
            'creator',              // Load Pembuat Tagihan
            'mahasiswa.person', 
            'mahasiswa.prodi'
        ])->find($id);

        $this->buildAuditTimeline(); 
        $this->showDetailModal = true;
    }

    /**
     * Menyusun Kronologi Keuangan Lengkap
     */
    private function buildAuditTimeline()
    {
        $timeline = [];

        // 1. Tagihan Dibuat (Start)
        $creatorName = $this->selectedTagihan->creator->name ?? 'System / Generator';
        $timeline[] = [
            'type' => 'BILL_CREATED',
            'date' => $this->selectedTagihan->created_at,
            'title' => 'Tagihan Diterbitkan',
            'amount' => $this->selectedTagihan->total_tagihan, // Base amount
            'user' => $creatorName,
            'desc' => $this->selectedTagihan->deskripsi,
            'status' => 'OPEN'
        ];

        // 2. Koreksi/Adjustment (Beasiswa/Potongan)
        foreach ($this->selectedTagihan->adjustments as $adj) {
            $timeline[] = [
                'type' => 'ADJUSTMENT',
                'date' => $adj->created_at,
                'title' => 'Koreksi: ' . $adj->jenis_adjustment,
                'amount' => -$adj->nominal, // Mengurangi beban
                'user' => $adj->creator->name ?? 'Admin Keuangan',
                'desc' => $adj->keterangan,
                'status' => 'APPROVED'
            ];
        }

        // 3. Pembayaran Masuk
        foreach ($this->selectedTagihan->pembayarans as $pay) {
            $verifier = $pay->verifier->name ?? 'System Auto-Verif';
            if ($pay->status_verifikasi == 'PENDING') $verifier = '-';

            $timeline[] = [
                'type' => 'PAYMENT',
                'date' => Carbon::parse($pay->created_at),
                'title' => 'Pembayaran Masuk',
                'amount' => -$pay->nominal_bayar,
                'user' => 'Mahasiswa / Teller',
                'desc' => 'Via ' . $pay->metode_pembayaran . ' | Verifikator: ' . $verifier,
                'status' => $pay->status_verifikasi
            ];
        }

        // 4. Log Deposit/Refund (KeuanganSaldoTransaction)
        $saldoTrans = KeuanganSaldoTransaction::where('referensi_id', $this->selectedTagihan->kode_transaksi)->get();
        foreach ($saldoTrans as $st) {
            if ($st->tipe == 'IN') {
                $timeline[] = [
                    'type' => 'WALLET_IN',
                    'date' => $st->created_at,
                    'title' => 'Deposit ke Dompet',
                    'amount' => 0, 
                    'user' => 'System',
                    'desc' => $st->keterangan,
                    'status' => 'DEPOSIT'
                ];
            }
        }

        // Sort: Terlama di atas (Kronologis)
        usort($timeline, fn($a, $b) => $a['date'] <=> $b['date']);
        
        $this->auditTimeline = $timeline;
    }
    
    public function closeDetail() { $this->showDetailModal = false; $this->selectedTagihan = null; }
    
    public function exportLaporan() 
    { 
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['NIM', 'Nama Lengkap', 'Prodi', 'Kelas', 'Semester', 'Tagihan Awal', 'Total Koreksi', 'Tagihan Netto', 'Terbayar', 'Sisa', 'Status']);

            $this->buildQuery()->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $koreksi = $row->adjustments->sum('nominal');
                    $netto = max(0, $row->total_tagihan - $koreksi);
                    $sisaReal = max(0, $netto - $row->total_bayar);

                    fputcsv($handle, [
                        $row->mahasiswa->nim,
                        $row->mahasiswa->person->nama_lengkap ?? '',
                        $row->mahasiswa->prodi->nama_prodi,
                        $row->mahasiswa->programKelas->nama_program,
                        $row->tahunAkademik->nama_tahun,
                        $row->total_tagihan, // Awal
                        $koreksi,            // Koreksi
                        $netto,              // Netto
                        $row->total_bayar,
                        $sisaReal,
                        $row->status_bayar
                    ]);
                }
            });
            fclose($handle);
        }, 'Laporan_Keuangan_Lengkap.csv');
    }
}