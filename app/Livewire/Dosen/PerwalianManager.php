<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Helpers\SistemHelper;

class PerwalianManager extends Component
{
    public $dosen;
    public $mahasiswas;
    public $taAktifId;

    public function mount()
    {
        $user = Auth::user();
        $this->dosen = Dosen::where('user_id', $user->id)->firstOrFail();
        $this->taAktifId = SistemHelper::idTahunAktif();
    }

    public function render()
    {
        // Ambil mahasiswa yang dibimbing dosen ini
        // Dan load status KRS mereka di semester aktif
        $this->mahasiswas = Mahasiswa::with(['programKelas', 'krs' => function($q) {
                $q->where('tahun_akademik_id', $this->taAktifId);
            }])
            ->where('dosen_wali_id', $this->dosen->id)
            ->get();

        return view('livewire.dosen.perwalian-manager');
    }
}