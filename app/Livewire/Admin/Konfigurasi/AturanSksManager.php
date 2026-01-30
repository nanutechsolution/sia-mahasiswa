<?php

namespace App\Livewire\Admin\Konfigurasi;

use Livewire\Component;
use App\Domains\Akademik\Models\AturanSks;

class AturanSksManager extends Component
{
    public $aturanId;
    public $min_ips;
    public $max_ips;
    public $max_sks;

    public $showForm = false;
    public $editMode = false;

    protected $rules = [
        'min_ips' => 'required|numeric|min:0|max:4',
        'max_ips' => 'required|numeric|min:0|max:4|gte:min_ips',
        'max_sks' => 'required|integer|min:1|max:30',
    ];

    public function render()
    {
        $aturan = AturanSks::orderBy('min_ips', 'asc')->get();
        return view('livewire.admin.konfigurasi.aturan-sks-manager', [
            'aturan' => $aturan
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
        $data = AturanSks::find($id);
        $this->aturanId = $id;
        $this->min_ips = $data->min_ips;
        $this->max_ips = $data->max_ips;
        $this->max_sks = $data->max_sks;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'min_ips' => $this->min_ips,
            'max_ips' => $this->max_ips,
            'max_sks' => $this->max_sks,
        ];

        if ($this->editMode) {
            AturanSks::find($this->aturanId)->update($data);
            session()->flash('success', 'Aturan SKS berhasil diperbarui.');
        } else {
            AturanSks::create($data);
            session()->flash('success', 'Aturan SKS berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        AturanSks::destroy($id);
        session()->flash('success', 'Aturan SKS dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['aturanId', 'min_ips', 'max_ips', 'max_sks', 'showForm', 'editMode']);
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}
