<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Validation\Rule;

class ProgramKelasManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form State
    public $programId;
    public $kode_internal;
    public $nama_program;
    public $min_pembayaran_persen = 50; // Default 50%
    public $is_active = true;

    // UI State
    public $showForm = false;
    public $editMode = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $programs = ProgramKelas::where(function ($q) {
            $q->where('nama_program', 'like', '%' . $this->search . '%')
                ->orWhere('kode_internal', 'like', '%' . $this->search . '%');
        })
            ->orderBy('kode_internal', 'asc')
            ->paginate(10);

        return view('livewire.admin.master.program-kelas-manager', [
            'programs' => $programs
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $p = ProgramKelas::findOrFail($id);
        $this->programId = $id;
        $this->kode_internal = $p->kode_internal;
        $this->nama_program = $p->nama_program;
        $this->min_pembayaran_persen = $p->min_pembayaran_persen;
        $this->is_active = (bool) $p->is_active;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_program' => 'required|string|max:100',
            'min_pembayaran_persen' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ];

        if ($this->editMode) {
            $rules['kode_internal'] = ['required', 'max:10', Rule::unique('ref_program_kelas')->ignore($this->programId)];
        } else {
            $rules['kode_internal'] = 'required|unique:ref_program_kelas,kode_internal|max:10';
        }

        $this->validate($rules);

        $data = [
            'kode_internal' => strtoupper($this->kode_internal),
            'nama_program' => $this->nama_program,
            'min_pembayaran_persen' => $this->min_pembayaran_persen,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            ProgramKelas::find($this->programId)->update($data);
            session()->flash('success', 'Program Kelas berhasil diperbarui.');
        } else {
            ProgramKelas::create($data);
            session()->flash('success', 'Program Kelas baru berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            ProgramKelas::destroy($id);
            session()->flash('success', 'Program Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus: Program ini sedang digunakan oleh mahasiswa/tagihan.');
        }
    }

    public function resetForm()
    {
        $this->reset(['programId', 'kode_internal', 'nama_program', 'showForm', 'editMode']);
        $this->min_pembayaran_persen = 50;
        $this->is_active = true;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}
