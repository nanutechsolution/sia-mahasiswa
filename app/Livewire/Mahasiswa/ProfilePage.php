<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Person;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Carbon\Carbon;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $user;
    public $person;
    public $mahasiswa;

    // Form Data Utama
    public $nama_lengkap, $email_pribadi, $nomor_hp, $nik, $jenis_kelamin;
    public $tempat_lahir, $tanggal_lahir;

    // Form Alamat & Keluarga (JSON)
    public $jalan, $dusun, $rt, $rw, $kelurahan, $kode_pos;
    public $nama_ayah, $nik_ayah, $nama_ibu, $nik_ibu;

    // Berkas & Foto (Properti yang menyebabkan error sebelumnya)
    public $photo_profil;
    public $berkas_ktp, $berkas_ijazah, $berkas_kk;

    // Form Keamanan
    public $current_password, $new_password, $new_password_confirmation;

    protected function messages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi sesuai identitas resmi.',
            'email_pribadi.required' => 'Alamat email aktif diperlukan untuk korespondensi.',
            'nik.required' => 'NIK wajib diisi sesuai KTP.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'new_password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'photo_profil.image' => 'File harus berupa gambar (JPG/PNG).',
            'photo_profil.max' => 'Ukuran gambar maksimal 1MB.',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user->person_id) {
            abort(403, 'Profil personil tidak terdeteksi (SSOT Error).');
        }

        $this->person = $this->user->person;
        $this->mahasiswa = $this->person->mahasiswa;

        // Load Data dari ref_person
        $this->nama_lengkap = $this->person->nama_lengkap;
        $this->email_pribadi = $this->person->email;
        $this->nomor_hp = $this->person->no_hp;
        $this->nik = $this->person->nik;
        $this->jenis_kelamin = $this->person->jenis_kelamin;

        // Load dari JSON data_tambahan
        $extra = $this->mahasiswa->data_tambahan ?? [];
        $this->tempat_lahir = $extra['tempat_lahir'] ?? null;
        
        if (!empty($extra['tanggal_lahir'])) {
            try { $this->tanggal_lahir = Carbon::parse($extra['tanggal_lahir'])->format('Y-m-d'); } 
            catch (\Exception $e) { $this->tanggal_lahir = null; }
        }
        
        $alamat = $extra['alamat_detail'] ?? [];
        $this->jalan = $alamat['jalan'] ?? '';
        $this->dusun = $alamat['dusun'] ?? '';
        $this->rt = $alamat['rt'] ?? '';
        $this->rw = $alamat['rw'] ?? '';
        $this->kelurahan = $alamat['kelurahan'] ?? '';
        $this->kode_pos = $alamat['kode_pos'] ?? '';

        $ortu = $extra['orang_tua'] ?? [];
        $this->nama_ayah = $ortu['nama_ayah'] ?? '';
        $this->nik_ayah = $ortu['nik_ayah'] ?? '';
        $this->nama_ibu = $ortu['nama_ibu'] ?? '';
        $this->nik_ibu = $ortu['nik_ibu'] ?? '';
    }

    // Hook untuk simpan foto otomatis saat di-upload
   // ... di dalam ProfilePage.php

public function updatedPhotoProfil()
{
    // 1. Validasi segera
    $this->validate([
        'photo_profil' => 'image|max:1024', // max 1MB
    ]);

    try {
        // 2. Simpan file ke disk public
        $path = $this->photo_profil->store('photos', 'public');

        // 3. Hapus foto lama jika ada di database & storage
        if ($this->person->photo_path) {
            Storage::disk('public')->delete($this->person->photo_path);
        }

        // 4. Update path di database
        $this->person->update([
            'photo_path' => $path
        ]);

        // 5. Reset property agar tidak menahan object file lama
        $this->reset('photo_profil');

        session()->flash('success', 'Foto profil berhasil diperbarui.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal mengunggah foto: ' . $e->getMessage());
    }
}

    public function uploadBerkas()
    {
        $this->validate([
            'berkas_ktp' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_ijazah' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_kk' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $extra = $this->mahasiswa->data_tambahan ?? [];

        if ($this->berkas_ktp) {
            $extra['path_ktp'] = $this->berkas_ktp->store('documents/ktp', 'public');
        }
        if ($this->berkas_ijazah) {
            $extra['path_ijazah'] = $this->berkas_ijazah->store('documents/ijazah', 'public');
        }
        if ($this->berkas_kk) {
            $extra['path_kk'] = $this->berkas_kk->store('documents/kk', 'public');
        }

        $this->mahasiswa->update(['data_tambahan' => $extra]);
        $this->reset(['berkas_ktp', 'berkas_ijazah', 'berkas_kk']);
        
        session()->flash('success', 'Dokumen pendukung berhasil diunggah.');
    }

    public function updateProfil()
    {
        $this->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email_pribadi' => 'required|email|unique:ref_person,email,' . $this->person->id,
            'nomor_hp' => 'required|numeric',
            'nik' => 'required|digits:16|unique:ref_person,nik,' . $this->person->id,
        ]);

        DB::beginTransaction();
        try {
            $this->person->update([
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email_pribadi,
                'no_hp' => $this->nomor_hp,
                'nik' => $this->nik,
                'jenis_kelamin' => $this->jenis_kelamin,
            ]);

            $this->user->update(['name' => $this->nama_lengkap, 'email' => $this->email_pribadi]);

            $extra = $this->mahasiswa->data_tambahan ?? [];
            $extra['tempat_lahir'] = $this->tempat_lahir;
            $extra['tanggal_lahir'] = $this->tanggal_lahir;
            $extra['alamat_detail'] = [
                'jalan' => $this->jalan, 'dusun' => $this->dusun,
                'rt' => $this->rt, 'rw' => $this->rw,
                'kelurahan' => $this->kelurahan, 'kode_pos' => $this->kode_pos
            ];
            $extra['orang_tua'] = [
                'nama_ayah' => $this->nama_ayah, 'nik_ayah' => $this->nik_ayah,
                'nama_ibu' => $this->nama_ibu, 'nik_ibu' => $this->nik_ibu
            ];

            $this->mahasiswa->update(['data_tambahan' => $extra]);

            DB::commit();
            session()->flash('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui profil.');
        }
    }

    public function updatePassword()
    {
        $this->validate(['current_password' => 'required', 'new_password' => 'required|min:6|confirmed']);

        if (!Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'Kata sandi lama tidak sesuai.');
            return;
        }

        $this->user->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_pw', 'Kata sandi berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.mahasiswa.profile-page');
    }
}