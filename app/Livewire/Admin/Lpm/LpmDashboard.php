<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domains\Core\Models\Prodi;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Helpers\SistemHelper;

class LpmDashboard extends Component
{
    public $stats = [];
    public $ikuStats = [];
    public $auditSummary = [];
    public $latestDocs = [];
    public $currentYear;

    public function mount()
    {
        $this->currentYear = date('Y');
        $this->loadAnalytics();
    }

    /**
     * Mengambil data analitik riil dari seluruh sub-modul LPM (SSOT)
     */
    public function loadAnalytics()
    {
        // 1. Statistik Eksekutif (Ringkasan Cepat)
        $this->stats = [
            'total_mhs'      => Mahasiswa::count(),
            'total_prodi'    => Prodi::count(),
            'dokumen_mutu'   => DB::table('lpm_dokumens')->count(),
            'temuan_ami'     => DB::table('lpm_ami_findings')->where('is_closed', false)->count(),
            'total_standar'  => DB::table('lpm_standars')->where('is_active', true)->count(),
        ];

        // 2. Data Riil IKU (Indikator Kinerja Utama) Tahun Berjalan
        // Mengambil target vs capaian dari tabel lpm_iku_targets
        $this->ikuStats = DB::table('lpm_iku_targets as t')
            ->join('lpm_indikators as i', 't.indikator_id', '=', 'i.id')
            ->where('t.tahun', $this->currentYear)
            ->select('i.nama_indikator', 't.target_nilai', 't.capaian_nilai')
            ->get()
            ->map(function($item) {
                $progress = $item->target_nilai > 0 ? ($item->capaian_nilai / $item->target_nilai) * 100 : 0;
                return [
                    'label'    => $item->nama_indikator,
                    'target'   => (float)$item->target_nilai,
                    'actual'   => (float)$item->capaian_nilai,
                    'progress' => min($progress, 100),
                    'status'   => $progress >= 100 ? 'ACHIEVED' : 'ON PROGRESS',
                    'color'    => $progress >= 100 ? 'emerald' : ($progress >= 70 ? 'indigo' : 'amber')
                ];
            });

        // Fallback data jika tabel target IKU masih kosong
        if ($this->ikuStats->isEmpty()) {
            $this->ikuStats = collect([
                ['label' => 'Rata-rata IPK Lulusan', 'target' => 3.25, 'actual' => 0, 'progress' => 0, 'status' => 'BELUM ADA DATA', 'color' => 'slate'],
                ['label' => 'Kualifikasi Dosen (Doktor/S3)', 'target' => 40, 'actual' => 0, 'progress' => 0, 'status' => 'BELUM ADA DATA', 'color' => 'slate'],
            ]);
        }

        // 3. Ringkasan Audit Mutu Internal (AMI) per Prodi
        $this->auditSummary = DB::table('lpm_ami_findings as f')
            ->join('ref_prodi as p', 'f.prodi_id', '=', 'p.id')
            ->select(
                'p.nama_prodi', 
                DB::raw('count(*) as total_temuan'), 
                DB::raw('sum(case when is_closed = 0 then 1 else 0 end) as open_temuan')
            )
            ->groupBy('p.id', 'p.nama_prodi')
            ->orderBy('open_temuan', 'desc')
            ->limit(5)
            ->get();

        // 4. Dokumen Mutu Terbaru
        $this->latestDocs = DB::table('lpm_dokumens')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.lpm.lpm-dashboard');
    }
}