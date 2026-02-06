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
    public $is_paket = true; 
    public $is_active = true;
    public $kaprodi;

    // UI State
    public $showForm = false;
    public $editMode = false;

    // Custom Messages
    protected $messages = [
        'fakultas_id.required' => 'Fakultas wajib dipilih.',
        'fakultas_id.exists' => 'Fakultas tidak valid.',
        'nama_prodi.required' => 'Nama Program Studi wajib diisi.',
        'kode_prodi_internal.required' => 'Kode Internal wajib diisi.',
        'kode_prodi_internal.unique' => 'Kode Internal sudah digunakan.',
        'kode_prodi_internal.max' => 'Kode Internal maksimal 10 karakter.',
        'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $fakultas_list = Fakultas::orderBy('nama_fakultas')->get();

        $prodis = Prodi::with('fakultas')
            ->where(function ($q) {
                $q->where('nama_prodi', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_prodi_internal', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_prodi_dikti', 'like', '%' . $this->search . '%');
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
        $this->kaprodi = $p->kaprodi;
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
            'gelar_lulusan' => 'nullable|string|max:50',
            'kaprodi' => 'nullable|string|max:100',
            'format_nim' => 'nullable|string',
            'kode_prodi_dikti' => 'nullable|string|max:10',
        ];

        if ($this->editMode) {
            $rules['kode_prodi_internal'] = ['required', 'max:10', Rule::unique('ref_prodi')->ignore($this->prodiId)];
        } else {
            $rules['kode_prodi_internal'] = 'required|unique:ref_prodi,kode_prodi_internal|max:10';
        }

        $this->validate($rules);

        $data = [
            'fakultas_id' => $this->fakultas_id,
            'kode_prodi_dikti' => trim($this->kode_prodi_dikti),
            'kode_prodi_internal' => strtoupper(trim($this->kode_prodi_internal)),
            'nama_prodi' => trim($this->nama_prodi),
            'jenjang' => $this->jenjang,
            'gelar_lulusan' => trim($this->gelar_lulusan),
            'kaprodi' => trim($this->kaprodi),
            'format_nim' => trim($this->format_nim),
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

        $this->batal();
    }

    public function delete($id)
    {
        try {
            Prodi::destroy($id);
            session()->flash('success', 'Prodi berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus: Prodi mungkin masih memiliki data mahasiswa atau kurikulum.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'prodiId', 'fakultas_id', 'kode_prodi_dikti', 'kode_prodi_internal', 
            'nama_prodi', 'gelar_lulusan', 'format_nim', 'kaprodi'
        ]);
        $this->jenjang = 'S1';
        $this->is_paket = true;
        $this->is_active = true;
        $this->format_nim = '{THN}{KODE}{NO:3}';
        $this->resetErrorBag();
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}