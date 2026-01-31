<?php

namespace App\Livewire\Admin\System;

use App\Domains\Core\Models\Person as ModelsPerson;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserManager extends Component
{
    use WithPagination;

    // Filter
    public $search = '';
    
    // Form State (Create/Edit)
    public $userId;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role = 'mahasiswa';
    public $is_active = true;
    
    // SSOT Linking (Pencarian Personil)
    public $person_id;
    public $searchPerson = '';
    public $selectedPersonName = '';

    // UI State
    public $showForm = false;
    public $editMode = false;
    
    // Reset Password Modal State
    public $showResetModal = false;
    public $resetUserId;
    public $resetUsername;
    public $new_password_reset;

    public function updatedSearch() { $this->resetPage(); }

    public function render()
    {
        $users = User::with('person')
            ->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('username', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Query untuk dropdown personil (hanya dijalankan jika form terbuka dan ada input)
        $persons = [];
        if ($this->showForm && strlen($this->searchPerson) >= 2) {
            $persons = ModelsPerson::where('nama_lengkap', 'like', '%'.$this->searchPerson.'%')
                ->orWhere('nik', 'like', '%'.$this->searchPerson.'%')
                ->orderBy('nama_lengkap')
                ->limit(5)
                ->get();
        }

        return view('livewire.admin.system.user-manager', [
            'users' => $users,
            'persons' => $persons
        ]);
    }

    // --- CRUD METHODS ---

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $user = User::with('person')->find($id);
        
        if (!$user) {
            session()->flash('error', 'User tidak ditemukan.');
            return;
        }

        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;
        
        $this->person_id = $user->person_id;
        $this->selectedPersonName = $user->person->nama_lengkap ?? '';
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($this->userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'role' => 'required',
        ];

        // Password wajib hanya saat create
        if (!$this->editMode) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $data = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'person_id' => $this->person_id ?: null,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->editMode) {
                User::find($this->userId)->update($data);
            } else {
                $user = User::create($data);
                $user->assignRole($this->role); // Sync Spatie Role
            }
        });

        session()->flash('success', 'Data Pengguna berhasil disimpan.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        if ($id == auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
            return;
        }
        
        User::destroy($id);
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }

    // --- RESET PASSWORD METHODS ---

    public function openResetPassword($id)
    {
        $user = User::find($id);
        if (!$user) return;

        $this->resetUserId = $id;
        $this->resetUsername = $user->name;
        $this->new_password_reset = '';
        
        $this->showResetModal = true;
        $this->showForm = false; // Pastikan form edit tertutup
    }

    public function processResetPassword()
    {
        $this->validate([
            'new_password_reset' => 'required|min:6'
        ], ['new_password_reset.required' => 'Password baru wajib diisi.']);

        $user = User::find($this->resetUserId);
        $user->update(['password' => Hash::make($this->new_password_reset)]);
        
        session()->flash('success', 'Password untuk ' . $this->resetUsername . ' berhasil direset.');
        $this->closeResetModal();
    }

    public function closeResetModal()
    {
        $this->showResetModal = false;
        $this->reset(['resetUserId', 'resetUsername', 'new_password_reset']);
    }

    // --- HELPERS ---

    public function selectPerson($id, $nama)
    {
        $this->person_id = $id;
        $this->selectedPersonName = $nama;
        $this->searchPerson = ''; // Clear search to close dropdown
        
        // Auto-fill nama jika kosong
        if (empty($this->name)) {
            $this->name = $nama;
        }
    }

    public function resetForm()
    {
        $this->reset([
            'userId', 'name', 'username', 'email', 'password', 'role', 
            'is_active', 'person_id', 'searchPerson', 'selectedPersonName'
        ]);
        $this->role = 'mahasiswa';
        $this->is_active = true;
        $this->editMode = false;
    }
    
    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}