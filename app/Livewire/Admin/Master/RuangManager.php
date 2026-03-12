<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefRuang;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class RuangManager extends Component
{
    use WithPagination;

    // UI State
    public $search = '';
    public $showForm = false;
    public $editMode = false;

    // Form Fields
    public $ruangId;
    public $kode_ruang;
    public $nama_ruang;
    public $kapasitas = 40;
    public $is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $ruangan = RefRuang::where('kode_ruang', 'like', "%{$this->search}%")
            ->orWhere('nama_ruang', 'like', "%{$this->search}%")
            ->orderBy('kode_ruang', 'asc')
            ->paginate(12);

        return view('livewire.admin.master.ruang-manager', [
            'ruangan' => $ruangan
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }
    #[On('openEditForm')]
    public function openEditForm($id)
    {
        $this->edit($id);
    }
    public function edit($id)
    {
        $ruang = RefRuang::findOrFail($id);

        $this->ruangId = $ruang->id;
        $this->kode_ruang = $ruang->kode_ruang;
        $this->nama_ruang = $ruang->nama_ruang;
        $this->kapasitas = $ruang->kapasitas;
        $this->is_active = $ruang->is_active;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_ruang' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ];

        if ($this->editMode) {
            $rules['kode_ruang'] = ['required', 'string', 'max:20', Rule::unique('ref_ruang')->ignore($this->ruangId)];
        } else {
            $rules['kode_ruang'] = 'required|string|max:20|unique:ref_ruang,kode_ruang';
        }

        $this->validate($rules);

        RefRuang::updateOrCreate(
            ['id' => $this->ruangId],
            [
                'kode_ruang' => strtoupper($this->kode_ruang),
                'nama_ruang' => $this->nama_ruang,
                'kapasitas' => $this->kapasitas,
                'is_active' => $this->is_active,
            ]
        );
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data ruangan kelas berhasil disimpan.'
        ]);

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive($id)
    {
        $ruang = RefRuang::findOrFail($id);
        $ruang->update(['is_active' => !$ruang->is_active]);

        $this->dispatch('swal:success', [
            'title' => 'Status Diperbarui',
            'text' => "Status ruangan {$ruang->kode_ruang} telah diubah."
        ]);
    }


    public function resetForm()
    {
        $this->reset(['ruangId', 'kode_ruang', 'nama_ruang', 'kapasitas', 'is_active', 'editMode']);
        $this->resetValidation();
        $this->kapasitas = 40;
        $this->is_active = true;
    }
}
