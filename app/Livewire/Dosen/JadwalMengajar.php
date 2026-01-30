<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
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
        
        // [SSOT FIX] Ambil Profil Dosen via Person
        // User -> belongsTo Person -> hasOne Dosen
        if (!$user->person || !$user->person->dosen) {
            abort(403, 'Akun Anda belum terhubung dengan Data Dosen (SSOT). Silakan hubungi Admin BAAK untuk perbaikan data.');
        }

        $this->dosen = $user->person->dosen;
        
        $taId = SistemHelper::idTahunAktif();
        $this->taAktif = SistemHelper::getTahunAktif();

        if ($taId) {
            $this->jadwals = JadwalKuliah::with(['mataKuliah', 'programKelasAllow'])
                ->where('dosen_id', $this->dosen->id)
                ->where('tahun_akademik_id', $taId)
                ->orderBy('hari', 'desc')
                ->orderBy('jam_mulai', 'asc')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.dosen.jadwal-mengajar');
    }
}