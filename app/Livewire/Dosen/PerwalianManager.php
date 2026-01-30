<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Helpers\SistemHelper;

class PerwalianManager extends Component
{
    public $dosen;
    public $mahasiswas;
    public $taAktifId;
    public $taAktifNama;

    // Stats
    public $totalMhs = 0;
    public $menungguAcc = 0;
    public $sudahAcc = 0;

    public function mount()
    {
        $user = Auth::user();
        
        // [SSOT] Ambil Dosen via Person
        if (!$user->person || !$user->person->dosen) {
            abort(403, 'Akun Anda belum terhubung dengan Data Dosen (SSOT).');
        }
        $this->dosen = $user->person->dosen;

        $this->taAktifId = SistemHelper::idTahunAktif();
        $this->taAktifNama = SistemHelper::getTahunAktif()->nama_tahun ?? '-';
    }

    public function render()
    {
        // Ambil mahasiswa bimbingan & Load KRS semester ini
        $this->mahasiswas = Mahasiswa::with(['programKelas', 'person', 'prodi', 'krs' => function($q) {
                $q->where('tahun_akademik_id', $this->taAktifId);
            }])
            ->where('dosen_wali_id', $this->dosen->id)
            ->orderBy('nim') // Urutkan by NIM (String di tabel tapi biasanya angka)
            ->get();

        // Hitung Statistik
        $this->totalMhs = $this->mahasiswas->count();
        $this->menungguAcc = 0;
        $this->sudahAcc = 0;

        foreach ($this->mahasiswas as $mhs) {
            $krs = $mhs->krs->first();
            if ($krs) {
                if ($krs->status_krs == 'AJUKAN') $this->menungguAcc++;
                if ($krs->status_krs == 'DISETUJUI') $this->sudahAcc++;
            }
        }

        return view('livewire.dosen.perwalian-manager');
    }
}