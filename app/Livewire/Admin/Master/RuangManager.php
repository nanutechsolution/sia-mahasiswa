<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Models\RefRuang;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class RuangManager extends Component
{
    /**
     * Kita menghapus WithPagination dan $search karena 
     * fungsi tersebut sudah ditangani secara otomatis oleh RuangTable (PowerGrid).
     */

    // UI State
    public $showForm = false;
    public $editMode = false;

    // Form Fields
    public $ruangId;
    public $kode_ruang;
    public $nama_ruang;
    public $kapasitas = 40;
    public $is_active = true;

    private function canManage(string $permission): bool
    {
        return auth()->user()->hasRole('superadmin') || auth()->user()->can($permission);
    }

    /**
     * Render sekarang hanya menampilkan view statis.
     * Tabel datanya dipanggil di dalam Blade menggunakan <livewire:ruang-table />
     */
    public function render()
    {
        return view('livewire.admin.master.ruang-manager');
    }

    public function create()
    {
        if (!$this->canManage('create_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

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
        if (!$this->canManage('edit_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        $ruang = RefRuang::findOrFail($id);

        $this->ruangId = $ruang->id;
        $this->kode_ruang = $ruang->kode_ruang;
        $this->nama_ruang = $ruang->nama_ruang;
        $this->kapasitas = $ruang->kapasitas;
        $this->is_active = $ruang->is_active;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function batal()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save()
    {
        $permissionNeeded = $this->editMode ? 'edit_ruang' : 'create_ruang';
        if (!$this->canManage($permissionNeeded)) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        $rules = [
            'nama_ruang' => 'required|string|max:100',
            'kapasitas'  => 'required|integer|min:1',
            'is_active'  => 'boolean'
        ];

        $rules['kode_ruang'] = $this->editMode 
            ? ['required', 'string', 'max:20', Rule::unique('ref_ruang', 'kode_ruang')->ignore($this->ruangId)]
            : ['required', 'string', 'max:20', 'unique:ref_ruang,kode_ruang'];

        $this->validate($rules);

        try {
            DB::transaction(function () {
                RefRuang::updateOrCreate(
                    ['id' => $this->ruangId],
                    [
                        'kode_ruang' => strtoupper($this->kode_ruang),
                        'nama_ruang' => $this->nama_ruang,
                        'kapasitas'  => $this->kapasitas,
                        'is_active'  => $this->is_active,
                    ]
                );
            });

            // Memberitahu PowerGrid untuk refresh data setelah simpan
            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: 'Data ruangan berhasil disimpan.');

            $this->batal();

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menyimpan data.');
        }
    }

    public function resetForm()
    {
        $this->reset(['ruangId', 'kode_ruang', 'nama_ruang', 'kapasitas', 'is_active', 'editMode']);
        $this->resetValidation();
        $this->kapasitas = 40;
        $this->is_active = true;
    }
}