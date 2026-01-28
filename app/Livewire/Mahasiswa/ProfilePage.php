<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $user;
    public $mahasiswa;

    // Form Kontak & Domisili (Sesuai Feeder)
    public $email_pribadi;
    public $nomor_hp;
    public $jalan;
    public $dusun;
    public $rt;
    public $rw;
    public $kelurahan;
    public $kode_pos;
    public $jenis_tinggal;
    public $alat_transportasi;

    // Form Data Orang Tua (Sesuai Feeder)
    public $nik_ayah;
    public $nama_ayah;
    public $tanggal_lahir_ayah;
    public $pendidikan_ayah;
    public $pekerjaan_ayah;
    public $penghasilan_ayah;

    public $nik_ibu;
    public $nama_ibu;
    public $tanggal_lahir_ibu;
    public $pendidikan_ibu;
    public $pekerjaan_ibu;
    public $penghasilan_ibu;

    public $nama_wali;
    
    // Form Password
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Form Berkas
    public $file_ktp;
    public $file_kk;
    public $file_ijazah;
    public $file_foto;
    
    public $existing_files = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->mahasiswa = Mahasiswa::where('user_id', $this->user->id)->firstOrFail();

        // 1. Load Data Utama
        $this->email_pribadi = $this->mahasiswa->email_pribadi;
        $this->nomor_hp = $this->mahasiswa->nomor_hp;
        
        // 2. Load Data Tambahan (Alamat Detail & Feeder Info)
        $data = $this->mahasiswa->data_tambahan ?? [];
        
        // Alamat
        $alamat = $data['alamat_detail'] ?? [];
        $this->jalan = $alamat['jalan'] ?? ($data['alamat'] ?? ''); // Fallback ke alamat lama
        $this->dusun = $alamat['dusun'] ?? '';
        $this->rt = $alamat['rt'] ?? '';
        $this->rw = $alamat['rw'] ?? '';
        $this->kelurahan = $alamat['kelurahan'] ?? '';
        $this->kode_pos = $alamat['kode_pos'] ?? '';
        $this->jenis_tinggal = $alamat['jenis_tinggal'] ?? '';
        $this->alat_transportasi = $alamat['alat_transportasi'] ?? '';

        // Orang Tua
        $ortu = $data['orang_tua'] ?? [];
        
        // Ayah
        $this->nik_ayah = $ortu['nik_ayah'] ?? '';
        $this->nama_ayah = $ortu['nama_ayah'] ?? '';
        $this->tanggal_lahir_ayah = $ortu['tanggal_lahir_ayah'] ?? '';
        $this->pendidikan_ayah = $ortu['pendidikan_ayah'] ?? '';
        $this->pekerjaan_ayah = $ortu['pekerjaan_ayah'] ?? '';
        $this->penghasilan_ayah = $ortu['penghasilan_ayah'] ?? '';

        // Ibu
        $this->nik_ibu = $ortu['nik_ibu'] ?? '';
        $this->nama_ibu = $ortu['nama_ibu'] ?? '';
        $this->tanggal_lahir_ibu = $ortu['tanggal_lahir_ibu'] ?? '';
        $this->pendidikan_ibu = $ortu['pendidikan_ibu'] ?? '';
        $this->pekerjaan_ibu = $ortu['pekerjaan_ibu'] ?? '';
        $this->penghasilan_ibu = $ortu['penghasilan_ibu'] ?? '';

        $this->nama_wali = $ortu['nama_wali'] ?? '';

        // Files
        $this->existing_files = $data['dokumen'] ?? [];
    }

    public function updateProfile()
    {
        $this->validate([
            'email_pribadi' => 'required|email',
            'nomor_hp' => 'required|numeric',
            'jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|numeric',
            'rw' => 'nullable|numeric',
            'nik_ayah' => 'nullable|numeric|digits:16',
            'nik_ibu' => 'nullable|numeric|digits:16',
        ]);

        $dataTambahan = $this->mahasiswa->data_tambahan ?? [];
        
        // Simpan Struktur Alamat Detail
        $dataTambahan['alamat_detail'] = [
            'jalan' => $this->jalan,
            'dusun' => $this->dusun,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'kelurahan' => $this->kelurahan,
            'kode_pos' => $this->kode_pos,
            'jenis_tinggal' => $this->jenis_tinggal,
            'alat_transportasi' => $this->alat_transportasi,
        ];
        
        // Simpan Struktur Orang Tua Detail
        $dataTambahan['orang_tua'] = [
            // Ayah
            'nik_ayah' => $this->nik_ayah,
            'nama_ayah' => $this->nama_ayah,
            'tanggal_lahir_ayah' => $this->tanggal_lahir_ayah,
            'pendidikan_ayah' => $this->pendidikan_ayah, // Simpan ID atau Nama (sesuai value select option)
            'pekerjaan_ayah' => $this->pekerjaan_ayah,
            'penghasilan_ayah' => $this->penghasilan_ayah,
            
            // Ibu
            'nik_ibu' => $this->nik_ibu,
            'nama_ibu' => $this->nama_ibu,
            'tanggal_lahir_ibu' => $this->tanggal_lahir_ibu,
            'pendidikan_ibu' => $this->pendidikan_ibu,
            'pekerjaan_ibu' => $this->pekerjaan_ibu,
            'penghasilan_ibu' => $this->penghasilan_ibu,
            
            'nama_wali' => $this->nama_wali,
        ];

        // Flat field untuk kompatibilitas lama (opsional)
        $dataTambahan['alamat'] = $this->jalan . ', RT ' . $this->rt . '/RW ' . $this->rw . ', ' . $this->kelurahan; 

        $this->mahasiswa->update([
            'email_pribadi' => $this->email_pribadi,
            'nomor_hp' => $this->nomor_hp,
            'data_tambahan' => $dataTambahan
        ]);

        $this->user->update(['email' => $this->email_pribadi]);

        session()->flash('success_profile', 'Data profil & keluarga berhasil diperbarui sesuai standar Feeder.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'Password lama tidak sesuai.');
            return;
        }

        $this->user->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password berhasil diubah.');
    }

    public function uploadDokumen($jenis)
    {
        $rules = [
            "file_{$jenis}" => 'required|mimes:pdf,jpg,jpeg,png|max:2048', 
        ];
        $this->validate($rules);

        $file = $this->{"file_{$jenis}"};
        $filename = "{$jenis}_" . $this->mahasiswa->nim . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen-mahasiswa/' . $this->mahasiswa->nim, $filename, 'public');

        $dataTambahan = $this->mahasiswa->data_tambahan ?? [];
        $dokumen = $dataTambahan['dokumen'] ?? [];
        
        $dokumen[$jenis] = $path;
        $dataTambahan['dokumen'] = $dokumen;

        $this->mahasiswa->update(['data_tambahan' => $dataTambahan]);
        
        $this->existing_files = $dokumen;
        $this->reset("file_{$jenis}");
        
        session()->flash('success_file', "Dokumen " . strtoupper($jenis) . " berhasil diunggah.");
    }

    public function render()
    {
        return view('livewire.mahasiswa.profile-page');
    }
}