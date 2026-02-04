<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use Illuminate\Support\Facades\DB;

class EdomManager extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadEdomStats();
    }

    public function loadEdomStats()
    {
        // Simulasi data EDOM (Evaluasi Dosen oleh Mahasiswa)
        // Data ini biasanya dihitung dari tabel survei mahasiswa
        $this->stats = [
            'total_responden' => 1240,
            'rata_rata_univ' => 3.75,
            'partisipasi_mhs' => 85, // Persen
            'top_performers' => [
                ['nama' => 'Dr. Budi Santoso', 'skor' => 3.92, 'prodi' => 'TI'],
                ['nama' => 'Siti Aminah, M.T', 'skor' => 3.88, 'prodi' => 'SI'],
                ['nama' => 'Rudi Hartono, M.Cs', 'skor' => 3.85, 'prodi' => 'TI'],
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.lpm.edom-manager');
    }
}