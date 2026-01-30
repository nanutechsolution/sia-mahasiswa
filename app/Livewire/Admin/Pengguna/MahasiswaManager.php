<?php

namespace App\Livewire\Admin\Pengguna;

use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MahasiswaManager extends Component
{
    use WithPagination;

    // Filter
    public $search = '';
    public $filterProdiId;
    public $filterAngkatan;

    // Form State
    public $mhsId;
    public $nim;
    public $nama_lengkap;
    public $angkatan_id;
    public $prodi_id;
    public $program_kelas_id;
    public $dosen_wali_id;
    public $email_pribadi;
    public $nomor_hp;
    public $password_baru;

    // STATE BARU: DISPENSASI
    public $bebas_keuangan = false;

    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        $this->filterAngkatan = date('Y');
        $this->angkatan_id = date('Y');
    }

    public function render()
    {
        // Ambil Data Dosen dengan Join ke Person untuk nama
        $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
            ->where('trx_dosen.is_active', true)
            ->orderBy('ref_person.nama_lengkap', 'asc')
            ->select('trx_dosen.id', 'ref_person.nama_lengkap as nama_lengkap_gelar')
            ->get();

        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();

        $mahasiswas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'dosenWali.person', 'person'])
            ->where(function ($q) {
                $q->whereRaw('LENGTH(nim) < 15')
                    ->where('nim', 'not like', '%PMB%');
            })
            ->when($this->filterProdiId, function ($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->when($this->filterAngkatan, function ($q) {
                $q->where('angkatan_id', $this->filterAngkatan);
            })
            ->where(function ($q) {
                // [FIX] Cari Nama di tabel Person (Relasi), bukan di tabel Mahasiswa
                $q->whereHas('person', function ($qPerson) {
                    $qPerson->where('nama_lengkap', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('nim', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nim', 'desc')
            ->paginate(10);

        return view('livewire.admin.pengguna.mahasiswa-manager', [
            'mahasiswas' => $mahasiswas,
            'prodis' => $prodis,
            'programKelasList' => $programKelasList,
            'angkatans' => $angkatans,
            'dosens' => $dosens
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
        $mhs = Mahasiswa::with(['user', 'person'])->find($id);
        $this->mhsId = $id;
        $this->nim = $mhs->nim;

        // Ambil data biodata dari Person (SSOT)
        $this->nama_lengkap = $mhs->person->nama_lengkap ?? $mhs->nama_lengkap; // Fallback jika migrasi belum sempurna
        $this->email_pribadi = $mhs->person->email ?? '';
        $this->nomor_hp = $mhs->person->no_hp ?? '';

        $this->angkatan_id = $mhs->angkatan_id;
        $this->prodi_id = $mhs->prodi_id;
        $this->program_kelas_id = $mhs->program_kelas_id;
        $this->dosen_wali_id = $mhs->dosen_wali_id;

        // Load Status Dispensasi dari JSON
        $this->bebas_keuangan = $mhs->data_tambahan['bebas_keuangan'] ?? false;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_lengkap' => 'required',
            'angkatan_id' => 'required|digits:4',
            'prodi_id' => 'required',
            'program_kelas_id' => 'required',
            'dosen_wali_id' => 'nullable',
            'bebas_keuangan' => 'boolean'
        ];

        if ($this->editMode) {
            $rules['nim'] = 'required|unique:mahasiswas,nim,' . $this->mhsId;
        } else {
            $rules['nim'] = 'required|unique:mahasiswas,nim';
            $rules['password_baru'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            // 1. Handle Person (Biodata Pusat - SSOT)
            if ($this->editMode) {
                $mhs = Mahasiswa::find($this->mhsId);
                $person = Person::updateOrCreate(
                    ['id' => $mhs->person_id],
                    [
                        'nama_lengkap' => $this->nama_lengkap,
                        'email' => $this->email_pribadi,
                        'no_hp' => $this->nomor_hp
                    ]
                );
            } else {
                $person = Person::create([
                    'nama_lengkap' => $this->nama_lengkap,
                    'email' => $this->email_pribadi,
                    'no_hp' => $this->nomor_hp,
                    'created_at' => now()
                ]);
            }

            // 2. Handle User Login
            if ($this->editMode) {
                $user = User::where('person_id', $person->id)->first();
                if (!$user && $mhs->user_id) {
                    // Fallback untuk data lama (kolom user_id di mahasiswa sudah dihapus di db, 
                    // tapi kita cari user via username=nim jika relasi person belum ada)
                    $user = User::where('username', $this->nim)->first();
                }

                if ($user) {
                    $user->name = $this->nama_lengkap;
                    $user->username = $this->nim;
                    if ($this->password_baru) {
                        $user->password = Hash::make($this->password_baru);
                    }
                    // Pastikan link ke person ada
                    if (!$user->person_id) $user->update(['person_id' => $person->id]);
                    $user->save();
                }
            } else {
                User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $this->nim,
                    'email' => $this->nim . '@student.unmaris.ac.id', // Email login default
                    'password' => Hash::make($this->password_baru),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'person_id' => $person->id // Link ke Person
                ]);
            }

            // 3. Handle Data Mahasiswa (Domain Akademik)
            $dataMhs = [
                'nim' => $this->nim,
                'person_id' => $person->id, // Link ke SSOT
                'nama_lengkap' => $this->nama_lengkap, // Cache nama (opsional, jika kolom masih ada)
                'angkatan_id' => $this->angkatan_id,
                'prodi_id' => $this->prodi_id,
                'program_kelas_id' => $this->program_kelas_id,
                'dosen_wali_id' => $this->dosen_wali_id ?: null,
            ];

            if (!$this->editMode) {
                $dataMhs['data_tambahan'] = ['bebas_keuangan' => $this->bebas_keuangan];
                Mahasiswa::create($dataMhs);
            } else {
                $mhs = Mahasiswa::find($this->mhsId);
                $currentData = $mhs->data_tambahan ?? [];
                $currentData['bebas_keuangan'] = $this->bebas_keuangan;
                $dataMhs['data_tambahan'] = $currentData;

                $mhs->update($dataMhs);
            }
        });

        session()->flash('success', 'Data Mahasiswa berhasil disimpan.');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $mhs = Mahasiswa::with('person.user')->find($id);

        // Hapus User Login (Soft Delete jika ada)
        if ($mhs->person && $mhs->person->user) {
            $mhs->person->user->delete();
        }

        // Hapus Data Mahasiswa
        $mhs->delete();

        session()->flash('success', 'Mahasiswa berhasil dihapus (Non-aktif).');
    }

    public function resetForm()
    {
        $this->reset(['mhsId', 'nim', 'nama_lengkap', 'email_pribadi', 'nomor_hp', 'password_baru', 'editMode', 'prodi_id', 'program_kelas_id', 'angkatan_id', 'dosen_wali_id', 'bebas_keuangan']);
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}
