<?php

namespace App\Livewire\Admin\Pengguna;

use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // WAJIB untuk upload
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaManager extends Component
{
    use WithPagination, WithFileUploads;

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
    public $bebas_keuangan = false;

    // Import State
    public $fileImport;
    public $showImportModal = false;
    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        $this->filterAngkatan = date('Y');
        $this->angkatan_id = date('Y');
    }

    public function updatedSearch() { $this->resetPage(); }

    // --- IMPORT CSV FEATURE ---

    public function openImport()
    {
        $this->reset(['fileImport']);
        $this->showImportModal = true;
    }

    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            // Header CSV
            fputcsv($handle, ['NIM', 'Nama Lengkap', 'NIK', 'Email', 'No HP', 'Kode Prodi', 'Kode Kelas (REG/EKS)', 'Tahun Angkatan', 'Jenis Kelamin (L/P)']);
            // Contoh Data
            fputcsv($handle, ['241001', 'Ahmad Dahlan', '3201123456789001', 'ahmad@gmail.com', '08123456789', 'TI', 'REG', '2024', 'L']);
            fclose($handle);
        }, 'template_import_mahasiswa.csv');
    }

    public function processImport()
    {
        $this->validate([
            'fileImport' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $this->fileImport->getRealPath();
        $file = fopen($path, 'r');
        fgetcsv($file); // Skip Header

        $countSuccess = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                // Validasi jumlah kolom minimal
                if (count($row) < 9) continue;

                $nim        = trim($row[0]);
                $nama       = trim($row[1]);
                $nik        = trim($row[2]);
                $email      = trim($row[3]);
                $hp         = trim($row[4]);
                $kodeProdi  = trim($row[5]);
                $kodeKelas  = trim($row[6]);
                $angkatan   = trim($row[7]);
                $gender     = strtoupper(trim($row[8])) == 'P' ? 'P' : 'L';

                // 1. Cari Relasi ID
                $prodi = Prodi::where('kode_prodi_internal', $kodeProdi)->first();
                $kelas = ProgramKelas::where('kode_internal', $kodeKelas)->first();

                if (!$prodi || !$kelas) {
                    // Skip jika prodi/kelas tidak valid
                    continue; 
                }

                // 2. Handle Person (SSOT)
                // Cek apakah orang ini sudah ada di sistem (by NIK)
                $person = Person::where('nik', $nik)->first();
                
                if (!$person) {
                    $person = Person::create([
                        'nama_lengkap' => $nama,
                        'nik' => $nik,
                        'email' => $email,
                        'no_hp' => $hp,
                        'jenis_kelamin' => $gender,
                        'created_at' => now()
                    ]);
                } else {
                    // Update data kontak jika sudah ada
                    $person->update(['email' => $email, 'no_hp' => $hp]);
                }

                // 3. Handle User Login
                $user = User::where('username', $nim)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $nama,
                        'username' => $nim,
                        'email' => $email ?: $nim.'@student.unmaris.ac.id',
                        'password' => Hash::make($nim), // Default password = NIM
                        'role' => 'mahasiswa',
                        'is_active' => true,
                        'person_id' => $person->id
                    ]);
                    $user->assignRole('mahasiswa');
                }

                // 4. Handle Data Akademik Mahasiswa
                Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'person_id' => $person->id,
                        'prodi_id' => $prodi->id,
                        'program_kelas_id' => $kelas->id,
                        'angkatan_id' => $angkatan,
                        // Dosen Wali dikosongkan dulu, nanti di-plotting
                        'dosen_wali_id' => null, 
                        'data_tambahan' => ['bebas_keuangan' => false]
                    ]
                );

                $countSuccess++;
            }
            DB::commit();
            session()->flash('success', "Berhasil mengimport $countSuccess data mahasiswa.");
            $this->showImportModal = false;
            $this->reset(['fileImport']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
        fclose($file);
    }

    public function render()
    {
        // ... (Query Render tetap sama seperti sebelumnya) ...
        $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
            ->where('trx_dosen.is_active', true)
            ->orderBy('ref_person.nama_lengkap', 'asc')
            ->select('trx_dosen.id', 'ref_person.nama_lengkap as nama_lengkap_gelar')
            ->get();

        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();

        $mahasiswas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'dosenWali.person', 'person'])
            ->where(function($q) {
                $q->whereRaw('LENGTH(nim) < 15')
                  ->where('nim', 'not like', '%PMB%');
            })
            ->when($this->filterProdiId, function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->when($this->filterAngkatan, function($q) {
                $q->where('angkatan_id', $this->filterAngkatan);
            })
            ->where(function($q) {
                $q->whereHas('person', function($qp) {
                    $qp->where('nama_lengkap', 'like', '%'.$this->search.'%');
                })
                ->orWhere('nim', 'like', '%'.$this->search.'%');
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

    // ... (Sisa fungsi CRUD create, edit, save, delete, resetForm, batal TETAP SAMA) ...
    
    public function create() { $this->resetForm(); $this->showForm = true; $this->editMode = false; }

    public function edit($id) {
        $mhs = Mahasiswa::with(['user', 'person'])->find($id);
        $this->mhsId = $id;
        $this->nim = $mhs->nim;
        $this->nama_lengkap = $mhs->person->nama_lengkap ?? $mhs->nama_lengkap; 
        $this->email_pribadi = $mhs->person->email ?? '';
        $this->nomor_hp = $mhs->person->no_hp ?? '';
        $this->angkatan_id = $mhs->angkatan_id;
        $this->prodi_id = $mhs->prodi_id;
        $this->program_kelas_id = $mhs->program_kelas_id;
        $this->dosen_wali_id = $mhs->dosen_wali_id;
        $this->bebas_keuangan = $mhs->data_tambahan['bebas_keuangan'] ?? false;
        $this->editMode = true; $this->showForm = true;
    }

    public function save() {
        $rules = [ 'nama_lengkap' => 'required', 'angkatan_id' => 'required|digits:4', 'prodi_id' => 'required', 'program_kelas_id' => 'required' ];
        if ($this->editMode) { $rules['nim'] = 'required|unique:mahasiswas,nim,' . $this->mhsId; } 
        else { $rules['nim'] = 'required|unique:mahasiswas,nim'; $rules['password_baru'] = 'required|min:6'; }
        $this->validate($rules);

        DB::transaction(function () {
            // Logic Save SSOT
            if ($this->editMode) {
                $mhs = Mahasiswa::find($this->mhsId);
                $person = Person::updateOrCreate(['id' => $mhs->person_id], ['nama_lengkap' => $this->nama_lengkap, 'email' => $this->email_pribadi, 'no_hp' => $this->nomor_hp]);
            } else {
                $person = Person::create(['nama_lengkap' => $this->nama_lengkap, 'email' => $this->email_pribadi, 'no_hp' => $this->nomor_hp, 'created_at' => now()]);
            }

            // User Logic
            $user = User::where('person_id', $person->id)->first();
            if (!$user) {
                $user = User::create(['name' => $this->nama_lengkap, 'username' => $this->nim, 'email' => $this->nim . '@student.unmaris.ac.id', 'password' => Hash::make($this->password_baru ?? $this->nim), 'role' => 'mahasiswa', 'is_active' => true, 'person_id' => $person->id]);
                $user->assignRole('mahasiswa');
            } else {
                $user->update(['name' => $this->nama_lengkap, 'username' => $this->nim]);
                if($this->password_baru) $user->update(['password' => Hash::make($this->password_baru)]);
            }

            // Mahasiswa Logic
            $dataMhs = ['nim' => $this->nim, 'person_id' => $person->id, 'angkatan_id' => $this->angkatan_id, 'prodi_id' => $this->prodi_id, 'program_kelas_id' => $this->program_kelas_id, 'dosen_wali_id' => $this->dosen_wali_id ?: null];
            if (!$this->editMode) { $dataMhs['data_tambahan'] = ['bebas_keuangan' => $this->bebas_keuangan]; Mahasiswa::create($dataMhs); }
            else { $mhs = Mahasiswa::find($this->mhsId); $currentData = $mhs->data_tambahan ?? []; $currentData['bebas_keuangan'] = $this->bebas_keuangan; $dataMhs['data_tambahan'] = $currentData; $mhs->update($dataMhs); }
        });

        session()->flash('success', 'Data Mahasiswa berhasil disimpan.');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id) {
        $mhs = Mahasiswa::find($id);
        if ($mhs->person && $mhs->person->user) $mhs->person->user->delete();
        $mhs->delete();
        session()->flash('success', 'Mahasiswa dihapus.');
    }
    
    public function resetForm() {
        $this->reset(['mhsId', 'nim', 'nama_lengkap', 'email_pribadi', 'nomor_hp', 'password_baru', 'editMode', 'prodi_id', 'program_kelas_id', 'angkatan_id', 'dosen_wali_id', 'bebas_keuangan', 'fileImport']);
    }
    public function batal() { $this->showForm = false; $this->showImportModal = false; $this->resetForm(); }
}