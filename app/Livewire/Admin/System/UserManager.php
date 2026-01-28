<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form State
    public $userId;
    public $name;
    public $username;
    public $email;
    public $password;
    public $selectedRoles = []; // Array untuk multiselect role
    public $is_active = true;

    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $users = User::with('roles')
            ->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('username', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            // Filter: Sembunyikan Mahasiswa & Dosen agar list tidak penuh
            // Kita fokus ke user staff/admin di menu ini
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['mahasiswa', 'dosen']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $allRoles = Role::whereNotIn('name', ['mahasiswa', 'dosen'])->get(); // Role internal saja

        return view('livewire.admin.system.user-manager', [
            'users' => $users,
            'allRoles' => $allRoles
        ]);
    }

    public function create()
    {
        $this->reset(['userId', 'name', 'username', 'email', 'password', 'selectedRoles', 'is_active']);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $user = User::find($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->is_active = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'username' => 'required|unique:users,username,' . $this->userId,
            'selectedRoles' => 'required|array|min:1',
        ];

        if (!$this->editMode) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
            // Simpan role utama di kolom 'role' native juga untuk query cepat (opsional/redundant tapi berguna)
            'role' => $this->selectedRoles[0] ?? 'staff' 
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editMode) {
            $user = User::find($this->userId);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        // Sync Roles (Spatie)
        $user->syncRoles($this->selectedRoles);

        $this->showForm = false;
        session()->flash('success', 'User berhasil disimpan.');
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user->hasRole('superadmin')) {
            session()->flash('error', 'Superadmin tidak boleh dihapus!');
            return;
        }
        $user->delete();
        session()->flash('success', 'User dihapus.');
    }
}