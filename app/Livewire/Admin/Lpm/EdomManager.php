<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class EdomManager extends Component
{
    public $taAktif;
    public $stats = [];
    public $prodiStats = [];
    public $topPerformers = [];

    public function mount()
    {
        $this->taAktif = SistemHelper::getTahunAktif();
        $this->loadRealStats();
    }

    /**
     * Mengambil data riil dari tabel lpm_edom_jawaban dengan dukungan Team Teaching
     */
    public function loadRealStats()
    {
        if (!$this->taAktif) return;

        // 1. Hitung Partisipasi Global (SSOT dari krs_detail)
        $totalKrsDetail = DB::table('krs_detail as kd')
            ->join('krs as k', 'kd.krs_id', '=', 'k.id')
            ->where('k.tahun_akademik_id', $this->taAktif->id)
            ->where('k.status_krs', 'DISETUJUI')
            ->count();

        $filledKrsDetail = DB::table('krs_detail as kd')
            ->join('krs as k', 'kd.krs_id', '=', 'k.id')
            ->where('k.tahun_akademik_id', $this->taAktif->id)
            ->where('kd.is_edom_filled', true)
            ->count();

        // 2. Hitung Rata-rata Skor Universitas
        $avgUniv = DB::table('lpm_edom_jawaban as ej')
            ->join('krs_detail as kd', 'ej.krs_detail_id', '=', 'kd.id')
            ->join('krs as k', 'kd.krs_id', '=', 'k.id')
            ->where('k.tahun_akademik_id', $this->taAktif->id)
            ->avg('ej.skor');

        $this->stats = [
            'total_responden'    => $filledKrsDetail,
            'total_kewajiban'    => $totalKrsDetail,
            'partisipasi_persen' => $totalKrsDetail > 0 ? ($filledKrsDetail / $totalKrsDetail * 100) : 0,
            'rata_rata_univ'     => number_format($avgUniv ?? 0, 2),
            'kategori_univ'      => $this->getKategoriLabel($avgUniv)
        ];

        // 3. Performa per Dosen (Ranking) - MENDUKUNG TEAM TEACHING
        // Skor ditarik dari relasi jadwal_kuliah_dosen
        $this->topPerformers = DB::table('lpm_edom_jawaban as ej')
            ->join('krs_detail as kd', 'ej.krs_detail_id', '=', 'kd.id')
            ->join('krs as k', 'kd.krs_id', '=', 'k.id')
            ->join('jadwal_kuliah as jk', 'kd.jadwal_kuliah_id', '=', 'jk.id')
            ->join('jadwal_kuliah_dosen as jkd', 'jk.id', '=', 'jkd.jadwal_kuliah_id')
            ->join('trx_dosen as d', 'jkd.dosen_id', '=', 'd.id')
            ->join('ref_person as p', 'd.person_id', '=', 'p.id')
            ->join('ref_prodi as pr', 'd.prodi_id', '=', 'pr.id')
            ->where('k.tahun_akademik_id', $this->taAktif->id)
            ->select(
                'p.nama_lengkap',
                'pr.nama_prodi as prodi',
                DB::raw('AVG(ej.skor) as skor_rata'),
                DB::raw('COUNT(DISTINCT k.mahasiswa_id) as jumlah_mhs')
            )
            ->groupBy('d.id', 'p.nama_lengkap', 'pr.nama_prodi')
            ->orderBy('skor_rata', 'desc')
            ->limit(10)
            ->get();

        // 4. Statistik per Prodi
        $this->prodiStats = DB::table('ref_prodi as pr')
            ->leftJoin('trx_dosen as d', 'pr.id', '=', 'd.prodi_id')
            ->leftJoin('jadwal_kuliah_dosen as jkd', 'd.id', '=', 'jkd.dosen_id')
            ->leftJoin('krs_detail as kd', 'jkd.jadwal_kuliah_id', '=', 'kd.id')
            ->leftJoin('lpm_edom_jawaban as ej', 'kd.id', '=', 'ej.krs_detail_id')
            ->select(
                'pr.nama_prodi',
                'pr.jenjang',
                DB::raw('COALESCE(AVG(ej.skor), 0) as skor'),
                DB::raw('COUNT(DISTINCT ej.id) as total_filled')
            )
            ->groupBy('pr.id', 'pr.nama_prodi', 'pr.jenjang')
            ->orderBy('skor', 'desc')
            ->get();
    }

    private function getKategoriLabel($skor)
    {
        if ($skor >= 3.5) return 'Sangat Baik';
        if ($skor >= 3.0) return 'Baik';
        if ($skor >= 2.0) return 'Cukup';
        return 'Kurang';
    }

    public function render()
    {
        return view('livewire.admin.lpm.edom-manager');
    }
}