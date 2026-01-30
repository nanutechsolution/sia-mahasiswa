<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Person;
use App\Models\User;
use App\Domains\Akademik\Models\Dosen;
use Carbon\Carbon;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $user;
    public $person;
    public $dosen;

    // Form Data Utama (ref_person)
    public $nama_lengkap, $email_pribadi, $nomor_hp, $nik, $jenis_kelamin;
    public $tempat_lahir, $tanggal_lahir;

    // Form Alamat & Detail (JSON data_tambahan di trx_dosen)
    public $jalan, $dusun, $rt, $rw, $kelurahan, $kode_pos;
    
    // Berkas & Foto
    public $photo_profil;
    public $berkas_ktp, $berkas_ijazah_terakhir, $berkas_serdos;

    // Form Keamanan
    public $current_password, $new_password, $new_password_confirmation;

    protected function messages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap dan gelar wajib diisi.',
            'email_pribadi.required' => 'Alamat email aktif diperlukan.',
            'nik.required' => 'NIK wajib diisi sesuai KTP.',
            'nik.digits' => 'NIK harus 16 digit.',
            'new_password.min' => 'Kata sandi minimal 6 karakter.',
            'photo_profil.image' => 'Berkas harus berupa gambar.',
            'photo_profil.max' => 'Ukuran maksimal 1MB.',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user->person_id) {
            abort(403, 'Profil personil tidak terdeteksi.');
        }

        $this->person = $this->user->person;
        $this->dosen = $this->person->dosen;

        // Load Data dari ref_person
        $this->nama_lengkap = $this->person->nama_lengkap;
        $this->email_pribadi = $this->person->email;
        $this->nomor_hp = $this->person->no_hp;
        $this->nik = $this->person->nik;
        $this->jenis_kelamin = $this->person->jenis_kelamin;

        // Load dari JSON data_tambahan di tabel dosen
        $extra = $this->dosen->data_tambahan ?? [];
        $this->tempat_lahir = $extra['tempat_lahir'] ?? null;
        
        if (!empty($extra['tanggal_lahir'])) {
            try { $this->tanggal_lahir = Carbon::parse($extra['tanggal_lahir'])->format('Y-m-d'); } 
            catch (\Exception $e) { $this->tanggal_lahir = null; }
        }
        
        $alamat = $extra['alamat_detail'] ?? [];
        $this->jalan = $alamat['jalan'] ?? '';
        $this->kelurahan = $alamat['kelurahan'] ?? '';
        $this->kode_pos = $alamat['kode_pos'] ?? '';
    }

    public function updatedPhotoProfil()
    {
        $this->validate(['photo_profil' => 'image|max:1024']);
        $path = $this->photo_profil->store('photos/dosen', 'public');
        
        if ($this->person->photo_path) {
            Storage::disk('public')->delete($this->person->photo_path);
        }

        $this->person->update(['photo_path' => $path]);
        session()->flash('success', 'Foto profil berhasil diperbarui.');
    }

    public function uploadBerkas()
    {
        $this->validate([
            'berkas_ktp' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_ijazah_terakhir' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_serdos' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $extra = $this->dosen->data_tambahan ?? [];

        if ($this->berkas_ktp) {
            $extra['path_ktp'] = $this->berkas_ktp->store('documents/dosen/ktp', 'public');
        }
        if ($this->berkas_ijazah_terakhir) {
            $extra['path_ijazah'] = $this->berkas_ijazah_terakhir->store('documents/dosen/ijazah', 'public');
        }
        if ($this->berkas_serdos) {
            $extra['path_serdos'] = $this->berkas_serdos->store('documents/dosen/serdos', 'public');
        }

        $this->dosen->update(['data_tambahan' => $extra]);
        $this->reset(['berkas_ktp', 'berkas_ijazah_terakhir', 'berkas_serdos']);
        
        session()->flash('success', 'Dokumen pendukung dosen berhasil diunggah.');
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

            $extra = $this->dosen->data_tambahan ?? [];
            $extra['tempat_lahir'] = $this->tempat_lahir;
            $extra['tanggal_lahir'] = $this->tanggal_lahir;
            $extra['alamat_detail'] = [
                'jalan' => $this->jalan,
                'kelurahan' => $this->kelurahan, 
                'kode_pos' => $this->kode_pos
            ];

            $this->dosen->update(['data_tambahan' => $extra]);

            DB::commit();
            session()->flash('success', 'Profil Dosen berhasil diperbarui.');
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
        return view('livewire.dosen.profile-page');
    }
}