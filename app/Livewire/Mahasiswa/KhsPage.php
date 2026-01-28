<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Helpers\SistemHelper; // Import

class KhsPage extends Component
{
    public $mahasiswa;
    public $krs;
    public $riwayat;
    public $details = [];
    public $tahunAkademikId;

    public function mount()
    {
        // Ambil TA Aktif
        $this->tahunAkademikId = SistemHelper::idTahunAktif();
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        $this->mahasiswa = Mahasiswa::with(['prodi', 'programKelas'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!$this->tahunAkademikId) return;

        // Ambil KRS Dinamis
        $this->krs = Krs::with(['tahunAkademik'])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId) // Dinamis
            ->first();

        if ($this->krs) {
            $this->details = $this->krs->details()
                ->with(['jadwalKuliah.mataKuliah'])
                ->where('is_published', true) 
                ->get();
        }

        // Ambil IPS Dinamis
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId) // Dinamis
            ->first();
    }

    public function render()
    {
        return view('livewire.mahasiswa.khs-page');
    }
}