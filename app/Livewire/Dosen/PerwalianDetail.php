<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Domains\Akademik\Models\Krs;

class PerwalianDetail extends Component
{
    public $krsId;
    public $krs;
    public $catatan;

    public function mount($krsId)
    {
        $this->krsId = $krsId;
        $this->loadData();
    }

    public function loadData()
    {
        $this->krs = Krs::with(['mahasiswa.prodi', 'details.jadwalKuliah.mataKuliah'])
            ->findOrFail($this->krsId);
    }

    public function setujui()
    {
        $this->krs->update(['status_krs' => 'DISETUJUI']);
        session()->flash('success', 'KRS Disetujui.');
        return redirect()->route('dosen.perwalian');
    }

    public function tolak()
    {
        $this->krs->update(['status_krs' => 'DRAFT']); // Kembalikan ke Draft
        session()->flash('success', 'KRS Ditolak (Dikembalikan ke Mahasiswa).');
        return redirect()->route('dosen.perwalian');
    }

    public function render()
    {
        return view('livewire.dosen.perwalian-detail');
    }
}