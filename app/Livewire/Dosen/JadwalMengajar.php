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
         
        // Validasi Relasi SSOT (User -> Person -> Dosen)
        if (!$user->person || !$user->person->dosen) {
            abort(403, 'Akun Anda belum terhubung dengan Data Dosen. Silakan hubungi Admin BAAK.');
        }

        $this->dosen = $user->person->dosen;
        
        $taId = SistemHelper::idTahunAktif();
        $this->taAktif = SistemHelper::getTahunAktif();

        if ($taId) {
            // PERBAIKAN: Menggunakan whereHas untuk mendukung Team Teaching (Many-to-Many)
            // Serta memuat relasi 'ruang' (RefRuang) dan 'dosens' (Team)
            $this->jadwals = JadwalKuliah::with(['mataKuliah', 'programKelasAllow', 'ruang', 'dosens.person'])
                ->whereHas('dosens', function ($q) {
                    $q->where('dosen_id', $this->dosen->id);
                })
                ->where('tahun_akademik_id', $taId)
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                ->orderBy('jam_mulai', 'asc')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.dosen.jadwal-mengajar');
    }
}