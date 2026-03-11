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

    // Filter UI
    public $semesterId;
    public $filterProdiId;
    public $filterStatus; 
    public $search = '';

    // UI State
    public $showDetailModal = false;
    public $selectedTagihan = null;
    public $auditTimeline = [];
    public $globalStats = [];

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
        $this->calculateGlobalStats();
    }

    public function updated($propertyName) 
    { 
        if (in_array($propertyName, ['semesterId', 'filterProdiId', 'filterStatus', 'search'])) {
            $this->resetPage(); 
            $this->calculateGlobalStats(); 
        }
    }
    
    /**
     * ENGINE KALKULASI AKUNTANSI (ENTERPRISE GRADE)
     * Memisahkan Arus Kas Riil, Subsidi Beasiswa, dan Perpindahan Saldo Internal.
     */
    public function calculateGlobalStats()
    {
        // 1. Ambil ID Tagihan sesuai filter yang sedang aktif
        $filteredIds = $this->buildQuery(true)->pluck('id');
        $mhsIds = DB::table('tagihan_mahasiswas')->whereIn('id', $filteredIds)->distinct()->pluck('mahasiswa_id');

        // 2. Kalkulasi Data Mentah (Agregat)
        $totalBruto = DB::table('tagihan_mahasiswas')->whereIn('id', $filteredIds)->sum('total_tagihan');
        
        // Kas Riil: Hanya pembayaran VALID yang BUKAN dari Saldo Dompet
        $totalCashIn = DB::table('pembayaran_mahasiswas')
            ->whereIn('tagihan_id', $filteredIds)
            ->where('status_verifikasi', 'VALID')
            ->where('metode_pembayaran', '!=', 'SALDO_DOMPET')
            ->sum('nominal_bayar');

        // Pelunasan Internal: Pembayaran menggunakan Saldo Dompet
        $totalViaSaldo = DB::table('pembayaran_mahasiswas')
            ->whereIn('tagihan_id', $filteredIds)
            ->where('status_verifikasi', 'VALID')
            ->where('metode_pembayaran', 'SALDO_DOMPET')
            ->sum('nominal_bayar');

        // 3. Iterasi per Baris untuk Akurasi Beasiswa & Sisa Piutang
        $actualScholarshipUsed = 0;
        $totalActualDebt = 0;
        $totalNetto = 0;

        $rawRows = DB::table('tagihan_mahasiswas')->whereIn('id', $filteredIds)->get();
        foreach ($rawRows as $row) {
            $adj = DB::table('keuangan_adjustments')->where('tagihan_id', $row->id)->sum('nominal');
            
            // Beasiswa yang "terpakai" maksimal sebesar nilai tagihan itu sendiri
            $scholarshipUsed = min($row->total_tagihan, $adj);
            $actualScholarshipUsed += $scholarshipUsed;

            $netto = max(0, $row->total_tagihan - $adj);
            $totalNetto += $netto;

            $sisa = max(0, $netto - $row->total_bayar);
            $totalActualDebt += $sisa;
        }

        // 4. Saldo yang saat ini dikuasai Mahasiswa (Liability Kampus)
        $totalDepositMhs = DB::table('keuangan_saldos')->whereIn('mahasiswa_id', $mhsIds)->sum('saldo');

        $this->globalStats = [
            'bruto'         => $totalBruto,
            'cash_in'       => $totalCashIn,      // Likuiditas riil (Bank/Tunai)
            'scholarship'   => $actualScholarshipUsed, // Potongan yang sah memotong tagihan
            'paid_internal' => $totalViaSaldo,    // Pelunasan lewat saldo
            'debt'          => $totalActualDebt,  // Hutang riil yang masih ditunggu
            'deposit'       => $totalDepositMhs,  // Sisa uang mhs yang mengendap
            'collection_rate' => ($totalNetto > 0) ? ((($totalCashIn + $totalViaSaldo) / $totalNetto) * 100) : 0,
            'count_mhs'     => count($mhsIds)
        ];
    }

    private function buildQuery($isStats = false)
    {
        $query = TagihanMahasiswa::query();
        if (!$isStats) { 
            $query->with(['mahasiswa.prodi', 'mahasiswa.person', 'tahunAkademik', 'adjustments', 'pembayarans', 'creator']); 
        }

        if ($this->semesterId === 'all') { } 
        elseif ($this->semesterId === 'legacy') { $query->whereNull('tahun_akademik_id'); } 
        elseif ($this->semesterId) { $query->where('tahun_akademik_id', $this->semesterId); }

        if ($this->filterProdiId) $query->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $this->filterProdiId));
        
        if ($this->filterStatus) {
            if ($this->filterStatus == 'LUNAS') $query->where('status_bayar', 'LUNAS');
            elseif ($this->filterStatus == 'CICIL') $query->where('status_bayar', 'BELUM')->where('total_bayar', '>', 0);
            elseif ($this->filterStatus == 'BELUM') $query->where('status_bayar', 'BELUM')->where('total_bayar', '=', 0);
            elseif ($this->filterStatus == 'LEBIH_BAYAR') $query->whereRaw('(total_tagihan - total_bayar) < 0');
        }

        if ($this->search) {
            $query->whereHas('mahasiswa', function ($q) {
                $q->whereHas('person', fn($qp) => $qp->where('nama_lengkap', 'like', '%' . $this->search . '%'))
                  ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }
        return $query;
    }

    /**
     * EXPORT DATA EXCEL DENGAN HEADER AKUNTANSI YANG BENAR
     */
    public function exportLaporan() 
    { 
        $timestamp = now()->format('Ymd_His');
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'NIM', 'Nama Mahasiswa', 'Prodi', 'Periode', 'Tagihan Bruto', 
                'Potongan/Beasiswa', 'Kewajiban Netto', 'Bayar (Cash)', 
                'Bayar (Saldo)', 'Sisa Piutang', 'Status', 'Admin Penerbit'
            ]);

            $this->buildQuery()->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $totalAdj = $row->adjustments->sum('nominal');
                    $netto = max(0, $row->total_tagihan - $totalAdj);
                    
                    $paidInternal = $row->pembayarans->where('metode_pembayaran', 'SALDO_DOMPET')->where('status_verifikasi', 'VALID')->sum('nominal_bayar');
                    $paidCash = $row->total_bayar - $paidInternal;
                    $sisa = max(0, $netto - $row->total_bayar);

                    fputcsv($handle, [
                        $row->mahasiswa->nim,
                        strtoupper($row->mahasiswa->person->nama_lengkap ?? ''),
                        $row->mahasiswa->prodi->nama_prodi,
                        $row->tahunAkademik->nama_tahun ?? 'LEGACY',
                        (int) $row->total_tagihan,
                        (int) $totalAdj,
                        (int) $netto,
                        (int) $paidCash,
                        (int) $paidInternal,
                        (int) $sisa,
                        $row->status_bayar,
                        $row->creator->name ?? 'System'
                    ]);
                }
            });
            fclose($handle);
        }, "Audit_Keuangan_UNMARIS_{$timestamp}.csv");
    }

    public function openDetail($id) {
        $this->selectedTagihan = TagihanMahasiswa::with(['pembayarans.verifier', 'adjustments.creator', 'creator', 'mahasiswa.person', 'mahasiswa.prodi'])->find($id);
        $this->buildAuditTimeline(); 
        $this->showDetailModal = true;
    }

    private function buildAuditTimeline() {
        $timeline = [];
        if(!$this->selectedTagihan) return;
        $t = $this->selectedTagihan;
        
        $timeline[] = [
            'type' => 'BILL_CREATED', 
            'date' => $t->created_at, 
            'title' => 'Invoice Diterbitkan', 
            'amount' => $t->total_tagihan, 
            'user' => $t->creator->name ?? 'System', 
            'desc' => $t->deskripsi
        ];

        foreach ($t->adjustments as $adj) {
            $timeline[] = [
                'type' => 'ADJUSTMENT', 
                'date' => $adj->created_at, 
                'title' => 'Koreksi Beasiswa', 
                'amount' => -$adj->nominal, 
                'user' => $adj->creator->name ?? 'Admin', 
                'desc' => $adj->keterangan
            ];
        }

        foreach ($t->pembayarans as $pay) {
            $timeline[] = [
                'type' => 'PAYMENT', 
                'date' => Carbon::parse($pay->tanggal_bayar ?? $pay->created_at), 
                'title' => 'Penerimaan Dana: ' . $pay->status_verifikasi, 
                'amount' => -$pay->nominal_bayar, 
                'user' => $pay->verifier->name ?? 'Mahasiswa', 
                'desc' => "Metode: {$pay->metode_pembayaran} | Status: {$pay->status_verifikasi}"
            ];
        }

        usort($timeline, fn($a, $b) => $a['date'] <=> $b['date']);
        $this->auditTimeline = $timeline;
    }
    
    public function closeDetail() { $this->showDetailModal = false; $this->selectedTagihan = null; }

    public function render() {
        $tagihans = $this->buildQuery()->orderByRaw('total_tagihan - total_bayar DESC')->paginate(15);
        return view('livewire.admin.keuangan.laporan-keuangan', [
            'tagihans' => $tagihans,
            'semesters' => TahunAkademik::orderBy('kode_tahun', 'desc')->get(),
            'prodis' => Prodi::all()
        ]);
    }
}