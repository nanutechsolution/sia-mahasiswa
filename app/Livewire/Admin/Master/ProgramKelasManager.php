<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Domains\Core\Models\ProgramKelas;

class ProgramKelasManager extends Component
{
    public $programs;
    
    // Form State
    public $pkId;
    public $nama_program;
    public $kode_internal;
    public $min_pembayaran_persen;
    
    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $this->programs = ProgramKelas::all();
        return view('livewire.admin.master.program-kelas-manager');
    }

    public function edit($id)
    {
        $pk = ProgramKelas::find($id);
        $this->pkId = $id;
        $this->nama_program = $pk->nama_program;
        $this->kode_internal = $pk->kode_internal;
        $this->min_pembayaran_persen = $pk->min_pembayaran_persen;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama_program' => 'required',
            'min_pembayaran_persen' => 'required|integer|min:0|max:100',
        ]);

        $pk = ProgramKelas::find($this->pkId);
        $pk->update([
            'nama_program' => $this->nama_program,
            'min_pembayaran_persen' => $this->min_pembayaran_persen
        ]);

        $this->showForm = false;
        session()->flash('success', 'Setting Program Kelas berhasil diupdate.');
    }

    // Kita batasi Create/Delete hanya via Database/Seeder untuk keamanan struktur,
    // Admin hanya boleh Edit nama dan aturan bayar.
    
    public function batal()
    {
        $this->showForm = false;
    }
}