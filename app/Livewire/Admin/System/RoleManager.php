<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form State
    public $roleId;
    public $name;
    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $roles = Role::where('name', 'like', '%'.$this->search.'%')
            ->withCount('users') // Hitung jumlah user per role
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin.system.role-manager', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $this->reset(['name', 'roleId']);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $role = Role::findById($id);
        $this->roleId = $id;
        $this->name = $role->name;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|unique:roles,name,' . $this->roleId,
        ]);

        // Simpan nama role dalam huruf kecil agar konsisten
        $roleName = strtolower($this->name);

        if ($this->editMode) {
            $role = Role::findById($this->roleId);
            // Cegah ubah nama role inti
            if (in_array($role->name, ['superadmin', 'dosen', 'mahasiswa', 'admin']) && $role->name !== $roleName) {
                session()->flash('error', 'Nama role inti sistem tidak boleh diubah.');
                return;
            }
            $role->update(['name' => $roleName]);
        } else {
            Role::create(['name' => $roleName]);
        }

        $this->showForm = false;
        session()->flash('success', 'Role berhasil disimpan.');
    }

    public function delete($id)
    {
        // Cegah hapus role vital
        $role = Role::findById($id);
        if (in_array($role->name, ['superadmin', 'dosen', 'mahasiswa', 'admin'])) {
            session()->flash('error', 'Role inti tidak boleh dihapus.');
            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('error', 'Gagal hapus: Masih ada user yang menggunakan role ini.');
            return;
        }

        $role->delete();
        session()->flash('success', 'Role dihapus.');
    }
}