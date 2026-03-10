<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\KrsDetail;
use Illuminate\Support\Facades\DB;

class TranskripPage extends Component
{
    public $mahasiswa;

    // Statistik Akademik
    public $totalSks = 0;
    public $totalMutu = 0;
    public $ipk = 0;

    public function mount()
    {
        $user = Auth::user();

        // Validasi koneksi User ke Person
        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT). Silakan hubungi Admin.');
        }

        $this->mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();
    }

    public function render()
    {
        $riwayatBelajar = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
            ->join('ref_tahun_akademik', 'krs.tahun_akademik_id', '=', 'ref_tahun_akademik.id')

            // LEFT JOIN supaya data lama tetap muncul
            ->leftJoin('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->leftJoin('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')

            ->where('krs.mahasiswa_id', $this->mahasiswa->id)
            ->where('krs_detail.is_published', true)

            ->select(
                'krs_detail.*',
                'ref_tahun_akademik.nama_tahun as nama_semester',
                'ref_tahun_akademik.kode_tahun',

                // Gunakan master jika ada, kalau tidak pakai snapshot
                DB::raw('COALESCE(master_mata_kuliahs.kode_mk, krs_detail.kode_mk_snapshot) as kode_mk'),
                DB::raw('COALESCE(master_mata_kuliahs.nama_mk, krs_detail.nama_mk_snapshot) as nama_mk'),
                DB::raw('COALESCE(master_mata_kuliahs.sks_default, krs_detail.sks_snapshot) as sks_default')
            )

            ->orderBy('ref_tahun_akademik.kode_tahun', 'asc')
            ->orderBy('kode_mk', 'asc')
            ->get();

        // Reset statistik
        $this->totalSks = 0;
        $this->totalMutu = 0;

        foreach ($riwayatBelajar as $mk) {
            $this->totalSks += $mk->sks_default;
            $this->totalMutu += ($mk->sks_default * $mk->nilai_indeks);
        }

        if ($this->totalSks > 0) {
            $this->ipk = $this->totalMutu / $this->totalSks;
        }

        // Group per semester
        $transkripGrouped = $riwayatBelajar->groupBy('nama_semester');

        return view('livewire.mahasiswa.transkrip-page', [
            'transkripGrouped' => $transkripGrouped
        ]);
    }
}