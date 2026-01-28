<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\KrsDetail;

class TranskripPage extends Component
{
    public $mahasiswa;
    // HAPUS property $transkripGrouped dari sini karena menyebabkan error serialization
    
    public $totalSks = 0;
    public $totalMutu = 0;
    public $ipk = 0;

    public function mount()
    {
        $user = Auth::user();
        $this->mahasiswa = Mahasiswa::with(['prodi', 'programKelas'])
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    public function render()
    {
        // Pindahkan logika query ke sini agar data segar setiap render
        // dan tidak perlu diserialisasi oleh Livewire
        $riwayatBelajar = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
            ->join('ref_tahun_akademik', 'krs.tahun_akademik_id', '=', 'ref_tahun_akademik.id')
            ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
            ->where('krs.mahasiswa_id', $this->mahasiswa->id)
            ->where('krs_detail.is_published', true)
            ->select(
                'krs_detail.*',
                'ref_tahun_akademik.nama_tahun as nama_semester',
                'ref_tahun_akademik.kode_tahun',
                'master_mata_kuliahs.kode_mk',
                'master_mata_kuliahs.nama_mk',
                'master_mata_kuliahs.sks_default'
            )
            ->orderBy('ref_tahun_akademik.kode_tahun', 'asc')
            ->get();

        // Hitung Statistik IPK
        $this->totalSks = 0;
        $this->totalMutu = 0;
        
        foreach ($riwayatBelajar as $mk) {
            $this->totalSks += $mk->sks_default;
            $this->totalMutu += ($mk->sks_default * $mk->nilai_indeks);
        }

        if ($this->totalSks > 0) {
            $this->ipk = $this->totalMutu / $this->totalSks;
        }

        // Grouping data untuk View
        $transkripGrouped = $riwayatBelajar->groupBy('nama_semester');

        return view('livewire.mahasiswa.transkrip-page', [
            'transkripGrouped' => $transkripGrouped
        ]);
    }
}