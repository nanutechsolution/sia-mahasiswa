<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Core\Models\Fakultas;

class FakultasManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form
    public $fakultasId;
    public $kode_fakultas;
    public $nama_fakultas;
    // public $nama_dekan; // Dihapus

    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $data = Fakultas::where('nama_fakultas', 'like', '%'.$this->search.'%')
            ->orderBy('kode_fakultas', 'asc')
            ->paginate(10);

        return view('livewire.admin.master.fakultas-manager', ['fakultas' => $data]);
    }

    public function create()
    {
        $this->reset(['kode_fakultas', 'nama_fakultas', 'fakultasId']);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $f = Fakultas::find($id);
        $this->fakultasId = $id;
        $this->kode_fakultas = $f->kode_fakultas;
        $this->nama_fakultas = $f->nama_fakultas;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_fakultas' => 'required',
        ];

        if ($this->editMode) {
            $rules['kode_fakultas'] = 'required|unique:ref_fakultas,kode_fakultas,' . $this->fakultasId;
        } else {
            $rules['kode_fakultas'] = 'required|unique:ref_fakultas,kode_fakultas';
        }

        $this->validate($rules);

        $data = [
            'kode_fakultas' => $this->kode_fakultas,
            'nama_fakultas' => $this->nama_fakultas,
        ];

        if ($this->editMode) {
            Fakultas::find($this->fakultasId)->update($data);
        } else {
            Fakultas::create($data);
        }

        $this->showForm = false;
        session()->flash('success', 'Data Fakultas berhasil disimpan.');
    }

    public function delete($id)
    {
        try {
            Fakultas::destroy($id);
            session()->flash('success', 'Fakultas dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus. Masih ada Prodi di fakultas ini.');
        }
    }
}