<?php

namespace App\Livewire\Admin\Pengguna;

use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; 
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    // --- REAL-TIME NIM GENERATOR HOOKS ---

    // Trigger otomatis saat Prodi dipilih di Form
    public function updatedProdiId()
    {
        $this->fillNimOtomatis();
    }

    // Trigger otomatis saat Angkatan dipilih di Form
    public function updatedAngkatanId()
    {
        $this->fillNimOtomatis();
    }

    // Logika Pengisi NIM
    public function fillNimOtomatis()
    {
        // Hanya jalan jika mode Input Baru (bukan Edit) dan kedua field (Prodi & Angkatan) terisi
        if (!$this->editMode && $this->prodi_id && $this->angkatan_id) {
            $generated = $this->generateNim($this->prodi_id, $this->angkatan_id);
            if ($generated) {
                $this->nim = $generated;
            }
        }
    }

    /**
     * Generate NIM otomatis berdasarkan Format Prodi
     */
    private function generateNim($prodiId, $angkatan)
    {
        $prodi = Prodi::find($prodiId);
        if (!$prodi) return null;

        // Ambil Format, default: {THN}{KODE}{NO:3}
        $format = $prodi->format_nim ?? '{THN}{KODE}{NO:3}';
        
        // 1. Replace Variable Statis
        $thn2 = substr($angkatan, 2, 2); // 24
        $thn4 = $angkatan; // 2024
        $kode = $prodi->kode_prodi_internal;

        $prefix = str_replace(
            ['{THN}', '{TAHUN}', '{KODE}', '{INTERNAL}'],
            [$thn2, $thn4, $kode, $kode],
            $format
        );

        // 2. Handle Sequence {NO:x}
        // Cari pattern {NO:angka}
        if (preg_match('/\{NO:(\d+)\}/', $prefix, $matches)) {
            $length = (int) $matches[1]; // Panjang digit, misal 3
            $patternToken = $matches[0]; // String "{NO:3}"
            
            // Base prefix adalah string sebelum token NO
            $basePrefix = explode($patternToken, $prefix)[0];

            // Cari NIM terakhir dengan prefix ini di DB
            $lastMhs = Mahasiswa::where('nim', 'like', $basePrefix . '%')
                ->whereRaw("LENGTH(nim) = " . (strlen($basePrefix) + $length)) // Pastikan panjangnya pas
                ->orderBy('nim', 'desc')
                ->first();

            $nextNo = 1;
            if ($lastMhs) {
                // Ambil angka belakangnya
                $currentNoStr = substr($lastMhs->nim, strlen($basePrefix), $length);
                if (is_numeric($currentNoStr)) {
                    $nextNo = intval($currentNoStr) + 1;
                }
            }

            // Format nomor urut dengan leading zero
            $noStr = str_pad($nextNo, $length, '0', STR_PAD_LEFT);
            
            // Gabungkan kembali
            return str_replace($patternToken, $noStr, $prefix);
        }

        // Jika format tidak mengandung {NO:x}, kembalikan prefix (manual fallback)
        return $prefix; 
    }

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
            fputcsv($handle, ['NIM', 'Nama Lengkap', 'NIK', 'Email', 'No HP', 'Kode Prodi', 'Kode Kelas (REG/EKS)', 'Tahun Angkatan', 'Jenis Kelamin (L/P)']);
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
        fgetcsv($file); 

        $countSuccess = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
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

                $prodi = Prodi::where('kode_prodi_internal', $kodeProdi)->first();
                $kelas = ProgramKelas::where('kode_internal', $kodeKelas)->first();

                if (!$prodi || !$kelas) {
                    continue; 
                }

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
                    $person->update(['email' => $email, 'no_hp' => $hp]);
                }

                $user = User::where('username', $nim)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $nama,
                        'username' => $nim,
                        'email' => $email ?: $nim.'@student.unmaris.ac.id',
                        'password' => Hash::make($nim),
                        'role' => 'mahasiswa',
                        'is_active' => true,
                        'person_id' => $person->id
                    ]);
                    $user->assignRole('mahasiswa');
                }

                Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'person_id' => $person->id,
                        'prodi_id' => $prodi->id,
                        'program_kelas_id' => $kelas->id,
                        'angkatan_id' => $angkatan,
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
        $dosens = Dosen::with('person')
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($d) => $d->person->nama_lengkap ?? '')
            ->values(); 

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
        
        if ($this->editMode) {
             $rules['nim'] = ['required', Rule::unique('mahasiswas')->ignore($this->mhsId)];
        } else {
             $rules['nim'] = 'required|unique:mahasiswas,nim';
             $rules['password_baru'] = 'required|min:6'; 
        }
        
        $this->validate($rules);

        DB::transaction(function () {
            if ($this->editMode) {
                $mhs = Mahasiswa::find($this->mhsId);
                $person = Person::updateOrCreate(['id' => $mhs->person_id], ['nama_lengkap' => $this->nama_lengkap, 'email' => $this->email_pribadi, 'no_hp' => $this->nomor_hp]);
            } else {
                $person = Person::create(['nama_lengkap' => $this->nama_lengkap, 'email' => $this->email_pribadi, 'no_hp' => $this->nomor_hp, 'created_at' => now()]);
            }

            $user = User::where('person_id', $person->id)->first();
            if (!$user) {
                $user = User::create(['name' => $this->nama_lengkap, 'username' => $this->nim, 'email' => $this->nim . '@student.unmaris.ac.id', 'password' => Hash::make($this->password_baru ?? $this->nim), 'role' => 'mahasiswa', 'is_active' => true, 'person_id' => $person->id]);
                $user->assignRole('mahasiswa');
            } else {
                $user->update(['name' => $this->nama_lengkap, 'username' => $this->nim]);
                if($this->password_baru) $user->update(['password' => Hash::make($this->password_baru)]);
            }

            $dataMhs = ['nim' => $this->nim, 'person_id' => $person->id, 'angkatan_id' => $this->angkatan_id, 'prodi_id' => $this->prodi_id, 'program_kelas_id' => $this->program_kelas_id, 'dosen_wali_id' => $this->dosen_wali_id ?: null];
            
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

        session()->flash('success', 'Data Mahasiswa berhasil disimpan. NIM: ' . $this->nim);
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
        $this->angkatan_id = date('Y'); // Reset ke tahun ini
    }
    public function batal() { $this->showForm = false; $this->showImportModal = false; $this->resetForm(); }
}