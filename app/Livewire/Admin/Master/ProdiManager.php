<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\Fakultas;

class ProdiManager extends Component
{
    use WithPagination;

    public $search = '';
    
    public $prodiId;
    public $fakultas_id;
    public $kode_prodi_internal;
    public $kode_prodi_dikti;
    public $nama_prodi;
    public $jenjang = 'S1';
    public $gelar_lulusan;
    public $format_nim; // <-- Property Baru
    
    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $fakultas = Fakultas::all();
        $prodis = Prodi::with('fakultas')
            ->where('nama_prodi', 'like', '%'.$this->search.'%')
            ->orderBy('fakultas_id')
            ->orderBy('kode_prodi_internal')
            ->paginate(10);

        return view('livewire.admin.master.prodi-manager', [
            'prodis' => $prodis,
            'fakultas_list' => $fakultas
        ]);
    }

    public function create()
    {
        $this->reset(['prodiId', 'fakultas_id', 'kode_prodi_internal', 'kode_prodi_dikti', 'nama_prodi', 'gelar_lulusan', 'format_nim']);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $p = Prodi::find($id);
        $this->prodiId = $id;
        $this->fakultas_id = $p->fakultas_id;
        $this->kode_prodi_internal = $p->kode_prodi_internal;
        $this->kode_prodi_dikti = $p->kode_prodi_dikti;
        $this->nama_prodi = $p->nama_prodi;
        $this->jenjang = $p->jenjang;
        $this->gelar_lulusan = $p->gelar_lulusan;
        $this->format_nim = $p->format_nim; // <-- Load Data
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'fakultas_id' => 'required',
            'nama_prodi' => 'required',
            'jenjang' => 'required',
            'format_nim' => 'nullable|string', // Validasi
        ];

        if ($this->editMode) {
            $rules['kode_prodi_internal'] = 'required|unique:ref_prodi,kode_prodi_internal,' . $this->prodiId;
        } else {
            $rules['kode_prodi_internal'] = 'required|unique:ref_prodi,kode_prodi_internal';
        }

        $this->validate($rules);

        $data = [
            'fakultas_id' => $this->fakultas_id,
            'kode_prodi_internal' => $this->kode_prodi_internal,
            'kode_prodi_dikti' => $this->kode_prodi_dikti,
            'nama_prodi' => $this->nama_prodi,
            'jenjang' => $this->jenjang,
            'gelar_lulusan' => $this->gelar_lulusan,
            'format_nim' => $this->format_nim, // Simpan
        ];

        if ($this->editMode) {
            Prodi::find($this->prodiId)->update($data);
        } else {
            Prodi::create($data);
        }

        $this->showForm = false;
        session()->flash('success', 'Data Prodi & Format NIM berhasil disimpan.');
    }

    public function delete($id)
    {
        try {
            Prodi::destroy($id);
            session()->flash('success', 'Prodi dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus. Data prodi ini sudah dipakai.');
        }
    }
}