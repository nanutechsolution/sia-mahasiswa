<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use SoftDeletes;

    protected $table = 'ref_person';

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'email',
        'no_hp',
        'tanggal_lahir',
        'jenis_kelamin',
        'tempat_lahir',
        'photo_path'
    ];

    /**
     * RELASI KE GELAR (Many-to-Many)
     * Menggunakan tabel pivot trx_person_gelar
     */
    public function gelars()
    {
        return $this->belongsToMany(Gelar::class, 'trx_person_gelar', 'person_id', 'gelar_id')
            ->withPivot('urutan')
            ->orderBy('trx_person_gelar.urutan', 'asc');
    }

  // 2. Accessor perakit gelar dan nama
    public function getNamaBergelarAttribute()
    {
        // Tarik semua gelar dari relasi
        $gelars = $this->gelars;
        // 1. FILTER GELAR DEPAN
        // Mengambil semua gelar yang posisinya 'DEPAN' (Bisa 1 atau lebih)
        // Misal: ['Prof.', 'Dr.', 'Ir.'] -> digabung spasi jadi "Prof. Dr. Ir. "
        $gelarDepanArr = $gelars->where('posisi', 'DEPAN')->pluck('kode')->toArray();
        $gelarDepanStr = !empty($gelarDepanArr) ? implode(' ', $gelarDepanArr) . ' ' : '';
        // 2. FILTER GELAR BELAKANG
        // Mengambil semua gelar yang posisinya 'BELAKANG' (Bisa 2, 3, atau lebih)
        // Misal: ['S.Kom.', 'M.T.'] -> digabung koma+spasi jadi ", S.Kom., M.T."
        $gelarBelakangArr = $gelars->where('posisi', 'BELAKANG')->pluck('kode')->toArray();
        $gelarBelakangStr = !empty($gelarBelakangArr) ? ', ' . implode(', ', $gelarBelakangArr) : '';

        // 3. GABUNGKAN SEMUANYA
        // Hasil: "Prof. Dr. Ir. Budi Santoso, S.Kom., M.T."
        return $gelarDepanStr . $this->nama_lengkap . $gelarBelakangStr;
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
