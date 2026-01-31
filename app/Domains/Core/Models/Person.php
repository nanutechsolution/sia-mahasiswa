<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Gelar; // Import Model Gelar
use App\Domains\Core\Models\Gelar as ModelsGelar;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use SoftDeletes;

    protected $table = 'ref_person';
    
    protected $fillable = [
        'nama_lengkap', 'nik', 'email', 'no_hp', 
        'tanggal_lahir', 'jenis_kelamin', 'tempat_lahir',
    ];

    /**
     * RELASI KE GELAR (Many-to-Many)
     * Menggunakan tabel pivot trx_person_gelar
     */
    public function gelars()
    {
        // [FIX] Parameter pertama harus Model Class, bukan DB::table
        return $this->belongsToMany(ModelsGelar::class, 'trx_person_gelar', 'person_id', 'gelar_id')
            ->withPivot('urutan')
            ->orderBy('trx_person_gelar.urutan', 'asc');
    }

    /**
     * Accessor: Gabungan Nama + Gelar
     * Panggil: $person->nama_dengan_gelar
     */
    public function getNamaDenganGelarAttribute()
    {
        // Kita bisa gunakan relasi yang sudah diload untuk performa (jika eager loading)
        // atau query manual jika relasi belum diload.
        
        if ($this->relationLoaded('gelars')) {
            $gelars = $this->gelars;
        } else {
            // Fallback query manual jika relasi tidak di-eager load (untuk hemat memori list besar)
            $gelars = DB::table('trx_person_gelar as tpg')
                ->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')
                ->where('tpg.person_id', $this->id)
                ->orderBy('tpg.urutan', 'asc')
                ->select('rg.kode', 'rg.posisi')
                ->get();
        }

        $depan = $gelars->where('posisi', 'DEPAN')->pluck('kode')->implode(' ');
        $belakang = $gelars->where('posisi', 'BELAKANG')->pluck('kode')->implode(', ');

        $namaLengkap = $this->nama_lengkap;

        // Format: [Gelar Depan] [Nama] [, Gelar Belakang]
        $hasil = $depan ? ($depan . ' ' . $namaLengkap) : $namaLengkap;
        $hasil .= $belakang ? (', ' . $belakang) : '';

        return $hasil;
    }

    /**
     * Relasi ke Akun Login
     */
    public function user()
    {
        return $this->hasOne(User::class, 'person_id');
    }

    /**
     * Domain: Akademik (Dosen)
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'person_id');
    }

    /**
     * Domain: Akademik (Mahasiswa)
     */
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'person_id');
    }

    // Bisa tambah relasi jabatan dll

    // public function jabatans() { ... }
}