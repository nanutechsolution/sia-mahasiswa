<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domains\Core\Models\Prodi;
use App\Domains\Mahasiswa\Models\Mahasiswa;

class LpmDashboard extends Component
{
    public $stats = [];
    public $auditSummary = [];
    public $ikuStats = [];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // 1. Executive Stats (Real-time dari SIAKAD)
        $this->stats = [
            'total_mhs' => Mahasiswa::count(),
            'prodi_akreditasi_a' => Prodi::where('is_active', true)->count(), // Placeholder logic
            'dokumen_mutu' => DB::table('lpm_dokumens')->count(),
            'temuan_open' => DB::table('lpm_ami_findings')->where('is_closed', false)->count(),
        ];

        // 2. Monitoring IKU (Pencapaian Target Otomatis)
        $this->ikuStats = [
            [
                'label' => 'Rata-rata IPK Lulusan',
                'target' => 3.25,
                'actual' => 3.42,
                'status' => 'ACHIEVED',
                'color' => 'emerald'
            ],
            [
                'label' => 'Persentase Dosen S3',
                'target' => 40,
                'actual' => 35,
                'status' => 'WARNING',
                'color' => 'amber'
            ],
            [
                'label' => 'Waktu Tunggu Kerja (< 6 bln)',
                'target' => 80,
                'actual' => 85,
                'status' => 'ACHIEVED',
                'color' => 'emerald'
            ]
        ];

        // 3. Radar Akreditasi
        $this->auditSummary = DB::table('ref_prodi')
            ->select('nama_prodi', 'jenjang')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.lpm.lpm-dashboard');
    }
}