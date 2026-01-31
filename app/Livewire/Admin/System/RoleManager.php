<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RoleManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form State
    public $roleId;
    public $name;
    public $guard_name = 'web';
    
    // [BARU] State untuk Checkbox Permission
    public $selectedPermissions = []; 
    
    // UI State
    public $showForm = false;
    public $editMode = false;

    public function updatedSearch() { $this->resetPage(); }

    public function render()
    {
        $roles = Role::where('name', 'like', '%'.$this->search.'%')
            ->withCount('users')
            ->with('permissions') // Load permission yang dimiliki
            ->orderBy('name', 'asc')
            ->paginate(10);

        // Ambil semua permission untuk ditampilkan di form (Group by nama depan agar rapi)
        // Contoh: "akademik_read", "akademik_write" jadi grup "Akademik"
        $allPermissions = Permission::orderBy('name')->get();

        return view('livewire.admin.system.role-manager', [
            'roles' => $roles,
            'allPermissions' => $allPermissions
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
        $role = Role::find($id);
        
        if (!$role) {
            session()->flash('error', 'Role tidak ditemukan.');
            return;
        }

        $this->roleId = $id;
        $this->name = $role->name;
        
        // [BARU] Load permission yang sudah dicentang sebelumnya
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->roleId)],
            'selectedPermissions' => 'array'
        ]);

        DB::transaction(function () {
            if ($this->editMode) {
                $role = Role::find($this->roleId);
                // Proteksi nama superadmin
                if ($role->name === 'superadmin' && $this->name !== 'superadmin') {
                     session()->flash('error', 'Nama Role Superadmin tidak boleh diubah.');
                     return;
                }
                $role->update(['name' => $this->name]);
            } else {
                $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);
            }

            // [BARU] Sync Permission (Update Hak Akses)
            // Superadmin selalu punya semua akses (bypass), tapi untuk role lain kita sync manual
            if ($role->name !== 'superadmin') {
                $role->syncPermissions($this->selectedPermissions);
            }
        });

        session()->flash('success', 'Role & Hak Akses berhasil disimpan.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if (!$role) return;

        $protectedRoles = ['superadmin', 'admin', 'dosen', 'mahasiswa'];
        if (in_array($role->name, $protectedRoles)) {
            session()->flash('error', 'Role sistem utama tidak dapat dihapus.');
            return;
        }
        
        if ($role->users_count > 0) {
            session()->flash('error', 'Role ini masih dipakai user. Hapus usernya dulu.');
            return;
        }

        $role->delete();
        session()->flash('success', 'Role berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['roleId', 'name', 'showForm', 'editMode', 'selectedPermissions']);
    }
    
    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}