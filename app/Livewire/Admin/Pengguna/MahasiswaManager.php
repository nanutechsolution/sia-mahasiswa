<?php

namespace App\Livewire\Admin\Pengguna;

use App\Domains\Akademik\Models\Dosen;
use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
    public $angkatan_id; // Tahun Masuk
    public $prodi_id;
    public $program_kelas_id;
    public $status_awal = 'B'; // B=Baru, P=Pindahan
    public $email_pribadi;
    public $nomor_hp;
    public $password_baru; // Opsional saat edit

    public $showForm = false;
    public $editMode = false;

    public $dosen_wali_id;

    public function mount()
    {
        $this->filterAngkatan = date('Y');
        $this->angkatan_id = date('Y');
    }

    public function render()
    {
        $dosens = Dosen::orderBy('nama_lengkap_gelar')->get();
        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();

        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();

        $mahasiswas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'dosenWali', 'tagihan']) // Load tagihan untuk cek lunas
            ->when($this->filterProdiId, function ($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->when($this->filterAngkatan, function ($q) {
                $q->where('angkatan_id', $this->filterAngkatan);
            })
            ->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
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

    // --- FITUR BARU: GENERATE NIM ---
    public function generateNimResmi($id)
    {
        DB::transaction(function () use ($id) {
            $mhs = Mahasiswa::lockForUpdate()->find($id);
            $prodi = Prodi::lockForUpdate()->find($mhs->prodi_id);

            // 1. Generate NIM Baru
            $format = $prodi->format_nim ?? '{TAHUN}{KODE}{NO:4}';
            $nextSeq = $prodi->last_nim_seq + 1;

            $newNim = str_replace('{TAHUN}', $mhs->angkatan_id, $format);
            $newNim = str_replace('{THN}', substr($mhs->angkatan_id, 2, 2), $newNim);
            $newNim = str_replace('{KODE}', $prodi->kode_prodi_dikti ?? '00000', $newNim);
            $newNim = str_replace('{INTERNAL}', $prodi->kode_prodi_internal, $newNim);

            if (preg_match('/\{NO:(\d+)\}/', $newNim, $matches)) {
                $length = $matches[1];
                $number = str_pad($nextSeq, $length, '0', STR_PAD_LEFT);
                $newNim = str_replace($matches[0], $number, $newNim);
            } else {
                $newNim .= str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            // 2. Update Data Mahasiswa
            $oldNim = $mhs->nim; // Simpan NIM lama (No Pendaftaran) untuk log/history jika perlu
            $mhs->update([
                'nim' => $newNim,
                'data_tambahan' => array_merge($mhs->data_tambahan ?? [], ['nim_lama' => $oldNim])
            ]);

            // 3. Update User Login
            if ($mhs->user_id) {
                $user = User::find($mhs->user_id);
                $user->update([
                    'username' => $newNim,
                    'password' => Hash::make($newNim) // Reset password jadi NIM baru agar mudah
                ]);
            }

            // 4. Update Counter Prodi
            $prodi->update(['last_nim_seq' => $nextSeq]);
        });

        session()->flash('success', 'NIM Resmi berhasil digenerate. Username login berubah menjadi NIM baru.');
    }

    // ... (Fungsi create, edit, save, delete, resetForm, batal TETAP SAMA seperti sebelumnya) ...
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $mhs = Mahasiswa::with('user')->find($id);
        $this->mhsId = $id;
        $this->nim = $mhs->nim;
        $this->nama_lengkap = $mhs->nama_lengkap;
        $this->angkatan_id = $mhs->angkatan_id;
        $this->prodi_id = $mhs->prodi_id;
        $this->program_kelas_id = $mhs->program_kelas_id;
        $this->dosen_wali_id = $mhs->dosen_wali_id;
        $this->email_pribadi = $mhs->email_pribadi;
        $this->nomor_hp = $mhs->nomor_hp;

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
        ];

        if ($this->editMode) {
            $rules['nim'] = 'required|unique:mahasiswas,nim,' . $this->mhsId;
        } else {
            $rules['nim'] = 'required|unique:mahasiswas,nim';
            $rules['password_baru'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            if ($this->editMode) {
                $mhs = Mahasiswa::find($this->mhsId);
                $user = User::find($mhs->user_id);

                if ($user) {
                    $user->name = $this->nama_lengkap;
                    $user->username = $this->nim;
                    if ($this->password_baru) {
                        $user->password = Hash::make($this->password_baru);
                    }
                    $user->save();
                }
            } else {
                $user = User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $this->nim,
                    'email' => $this->nim . '@student.unmaris.ac.id',
                    'password' => Hash::make($this->password_baru),
                    'role' => 'mahasiswa',
                    'is_active' => true
                ]);
            }

            $dataMhs = [
                'nim' => $this->nim,
                'nama_lengkap' => $this->nama_lengkap,
                'angkatan_id' => $this->angkatan_id,
                'prodi_id' => $this->prodi_id,
                'program_kelas_id' => $this->program_kelas_id,
                'dosen_wali_id' => $this->dosen_wali_id ?: null,
                'email_pribadi' => $this->email_pribadi,
                'nomor_hp' => $this->nomor_hp,
            ];

            if (!$this->editMode) {
                $dataMhs['user_id'] = $user->id;
                Mahasiswa::create($dataMhs);
            } else {
                Mahasiswa::find($this->mhsId)->update($dataMhs);
            }
        });

        session()->flash('success', 'Data Mahasiswa berhasil disimpan.');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $mhs = Mahasiswa::find($id);
        if ($mhs->user_id) {
            User::where('id', $mhs->user_id)->delete();
        }
        $mhs->delete();

        session()->flash('success', 'Mahasiswa berhasil dihapus (Non-aktif).');
    }

    public function resetForm()
    {
        $this->reset(['mhsId', 'nim', 'nama_lengkap', 'email_pribadi', 'nomor_hp', 'password_baru', 'editMode', 'prodi_id', 'program_kelas_id', 'angkatan_id', 'dosen_wali_id']);
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}
