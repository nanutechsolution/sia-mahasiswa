<?php

namespace App\Livewire\Admin\Pengguna;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\Dosen;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenManager extends Component
{
    use WithPagination;

    // Filter
    public $search = '';
    public $filterProdiId;

    // Form State
    public $dosenId;
    public $nidn;
    public $nama_lengkap_gelar;
    public $homebase_prodi_id;
    public $is_active = true;
    public $password_baru; // Opsional saat edit

    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        //
    }

    public function render()
    {
        $prodis = Prodi::all();

        $dosens = Dosen::with(['user']) // Dosen belum tentu punya relasi prodi di model, cek model Dosen nanti
            ->when($this->filterProdiId, function($q) {
                $q->where('homebase_prodi_id', $this->filterProdiId);
            })
            ->where(function($q) {
                $q->where('nama_lengkap_gelar', 'like', '%'.$this->search.'%')
                  ->orWhere('nidn', 'like', '%'.$this->search.'%');
            })
            ->orderBy('nama_lengkap_gelar', 'asc')
            ->paginate(10);

        return view('livewire.admin.pengguna.dosen-manager', [
            'dosens' => $dosens,
            'prodis' => $prodis
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
        $dosen = Dosen::with('user')->find($id);
        $this->dosenId = $id;
        $this->nidn = $dosen->nidn;
        $this->nama_lengkap_gelar = $dosen->nama_lengkap_gelar;
        $this->homebase_prodi_id = $dosen->homebase_prodi_id;
        $this->is_active = $dosen->is_active;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_lengkap_gelar' => 'required',
            'homebase_prodi_id' => 'required',
        ];

        if ($this->editMode) {
            // NIDN boleh kosong, tapi jika ada harus unik (kecuali diri sendiri)
            if ($this->nidn) {
                $rules['nidn'] = 'unique:dosens,nidn,' . $this->dosenId;
            }
        } else {
            if ($this->nidn) {
                $rules['nidn'] = 'unique:dosens,nidn';
            }
            $rules['password_baru'] = 'required|min:6'; // Wajib password awal
        }

        $this->validate($rules);

        // Username Login: Gunakan NIDN jika ada, atau generate random prefix 'dosen.'
        $usernameLogin = $this->nidn ? $this->nidn : 'dosen.' . strtolower(str_replace(' ', '', substr($this->nama_lengkap_gelar, 0, 5))) . rand(100, 999);

        DB::transaction(function () use ($usernameLogin) {
            // 1. Handle User Login
            if ($this->editMode) {
                $dosen = Dosen::find($this->dosenId);
                $user = User::find($dosen->user_id);
                
                // Update User info
                if ($user) {
                    $user->name = $this->nama_lengkap_gelar;
                    // Username update hanya jika NIDN berubah dan valid
                    if ($this->nidn) {
                        $user->username = $this->nidn;
                    }
                    if ($this->password_baru) {
                        $user->password = Hash::make($this->password_baru);
                    }
                    $user->save();
                }
            } else {
                // Create User Baru
                $user = User::create([
                    'name' => $this->nama_lengkap_gelar,
                    'username' => $usernameLogin,
                    'email' => $usernameLogin . '@lecturer.unmaris.ac.id', // Fake email
                    'password' => Hash::make($this->password_baru),
                    'role' => 'dosen',
                    'is_active' => true
                ]);
            }

            // 2. Handle Data Dosen
            $dataDosen = [
                'nidn' => $this->nidn,
                'nama_lengkap_gelar' => $this->nama_lengkap_gelar,
                'homebase_prodi_id' => $this->homebase_prodi_id,
                'is_active' => $this->is_active,
            ];

            if (!$this->editMode) {
                $dataDosen['user_id'] = $user->id;
                Dosen::create($dataDosen);
            } else {
                Dosen::find($this->dosenId)->update($dataDosen);
            }
        });

        session()->flash('success', 'Data Dosen berhasil disimpan.');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $dosen = Dosen::find($id);
        // Hapus User login juga
        if ($dosen->user_id) {
            User::where('id', $dosen->user_id)->delete();
        }
        $dosen->delete();
        
        session()->flash('success', 'Dosen berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['dosenId', 'nidn', 'nama_lengkap_gelar', 'password_baru', 'editMode', 'is_active']);
        $this->is_active = true;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}