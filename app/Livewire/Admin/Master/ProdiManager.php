<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\Fakultas;
use Illuminate\Validation\Rule;

class ProdiManager extends Component
{
    use WithPagination;

    // Filter
    public $search = '';

    // Form State
    public $prodiId;
    public $fakultas_id;
    public $kode_prodi_dikti;
    public $kode_prodi_internal;
    public $nama_prodi;
    public $jenjang = 'S1';
    public $gelar_lulusan;
    public $format_nim = '{THN}{KODE}{NO:3}';
    public $is_paket = true; // Default paket
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
        $fakultas_list = Fakultas::all();

        $prodis = Prodi::with('fakultas')
            ->where(function ($q) {
                $q->where('nama_prodi', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_prodi_internal', 'like', '%' . $this->search . '%');
            })
            ->orderBy('fakultas_id', 'asc')
            ->orderBy('kode_prodi_internal', 'asc')
            ->paginate(10);

        return view('livewire.admin.master.prodi-manager', [
            'prodis' => $prodis,
            'fakultas_list' => $fakultas_list
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
        $p = Prodi::findOrFail($id);
        $this->prodiId = $id;
        $this->fakultas_id = $p->fakultas_id;
        $this->kode_prodi_dikti = $p->kode_prodi_dikti;
        $this->kode_prodi_internal = $p->kode_prodi_internal;
        $this->nama_prodi = $p->nama_prodi;
        $this->jenjang = $p->jenjang;
        $this->gelar_lulusan = $p->gelar_lulusan;
        $this->format_nim = $p->format_nim;
        $this->is_paket = (bool) $p->is_paket;
        $this->is_active = (bool) $p->is_active;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'fakultas_id' => 'required|exists:ref_fakultas,id',
            'nama_prodi' => 'required|string|max:100',
            'jenjang' => 'required|in:D3,D4,S1,S2,S3,PROFESI',
            'is_paket' => 'boolean',
            'is_active' => 'boolean',
        ];
        $message =
            [
                'fakultas_id.required' => 'Silakan pilih fakultas!',
                'nama_prodi.required' => 'Nama prodi tidak boleh kosong!',
            ];
        if ($this->editMode) {
            $rules['kode_prodi_internal'] = ['required', 'max:10', Rule::unique('ref_prodi')->ignore($this->prodiId)];
        } else {
            $rules['kode_prodi_internal'] = 'required|unique:ref_prodi,kode_prodi_internal|max:10';
        }

        $this->validate($rules, $message);

        $data = [
            'fakultas_id' => $this->fakultas_id,
            'kode_prodi_dikti' => $this->kode_prodi_dikti,
            'kode_prodi_internal' => strtoupper($this->kode_prodi_internal),
            'nama_prodi' => $this->nama_prodi,
            'jenjang' => $this->jenjang,
            'gelar_lulusan' => $this->gelar_lulusan,
            'format_nim' => $this->format_nim,
            'is_paket' => $this->is_paket,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            Prodi::find($this->prodiId)->update($data);
            session()->flash('success', 'Data Prodi berhasil diperbarui.');
        } else {
            Prodi::create($data);
            session()->flash('success', 'Prodi baru berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            Prodi::destroy($id);
            session()->flash('success', 'Prodi berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus: Prodi mungkin masih digunakan di data lain.');
        }
    }

    public function resetForm()
    {
        $this->reset(['prodiId', 'fakultas_id', 'kode_prodi_dikti', 'kode_prodi_internal', 'nama_prodi', 'gelar_lulusan', 'format_nim']);
        $this->jenjang = 'S1';
        $this->is_paket = true;
        $this->is_active = true;
        $this->format_nim = '{THN}{KODE}{NO:3}';
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}
