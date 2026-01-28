<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Helpers\SistemHelper;

class JadwalMengajar extends Component
{
    public $dosen;
    public $jadwals = [];
    public $taAktif;

    public function mount()
    {
        $user = Auth::user();
        $this->dosen = $user->profileable;
        if (!$this->dosen || !($this->dosen instanceof \App\Domains\Akademik\Models\Dosen)) {
            abort(403, 'Data dosen belum terhubung.');
        }
        // Cari Profile Dosen berdasarkan User Login
        // $this->dosen = Dosen::where('user_id', $user->id)->firstOrFail();
        if (!$this->dosen) {
            abort(403, 'Data dosen belum terhubung.');
        }
        $taId = SistemHelper::idTahunAktif();
        $this->taAktif = SistemHelper::getTahunAktif();

        if ($taId) {
            $this->jadwals = JadwalKuliah::with(['mataKuliah', 'programKelasAllow'])
                ->where('dosen_id', $this->dosen->id)
                ->where('tahun_akademik_id', $taId)
                ->orderBy('hari', 'desc')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.dosen.jadwal-mengajar');
    }
}
