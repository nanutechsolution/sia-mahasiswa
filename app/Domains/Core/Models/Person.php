<?php

namespace  App\Domains\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'ref_person';

    protected $fillable = [
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'nik',
    ];

    /**
     * RELATIONS
     */
    public function jabatans()
    {
        return $this->hasMany(PersonJabatan::class, 'person_id');
    }

    // user
    public function user()
    {
        return $this->hasOne(User::class, 'person_id');
    }

    public function gelars()
    {
        // Relasi Many-to-Many ke tabel ref_gelar melalui trx_person_gelar
        return $this->belongsToMany(\Illuminate\Support\Facades\DB::table('ref_gelar'), 'trx_person_gelar', 'person_id', 'gelar_id')
            ->using(new class extends \Illuminate\Database\Eloquent\Relations\Pivot {
                protected $table = 'trx_person_gelar';
            })
            ->withPivot('urutan')
            ->orderBy('trx_person_gelar.urutan', 'asc');
    }

    /**
     * Accessor untuk Nama Lengkap + Gelar
     * Panggil dengan: $person->nama_dengan_gelar
     */
    public function getNamaDenganGelarAttribute()
    {
        // Ambil gelar via Query Builder agar lebih ringan/pasti jika model Gelar belum dibuat
        $gelars = \Illuminate\Support\Facades\DB::table('trx_person_gelar as tpg')
            ->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')
            ->where('tpg.person_id', $this->id)
            ->orderBy('tpg.urutan', 'asc')
            ->get();

        $depan = $gelars->where('posisi', 'DEPAN')->pluck('kode')->implode(' ');
        $belakang = $gelars->where('posisi', 'BELAKANG')->pluck('kode')->implode(', ');

        $namaLengkap = $this->nama_lengkap;

        // Gabung: [Depan] [Nama] [, Belakang]
        $hasil = $depan ? ($depan . ' ' . $namaLengkap) : $namaLengkap;
        $hasil .= $belakang ? (', ' . $belakang) : '';

        return $hasil;
    }

    public function dosen()
    {
        return $this->hasOne(\App\Domains\Akademik\Models\Dosen::class, 'person_id');
    }

    // mahasiswa
    public function mahasiswa()
    {
        return $this->hasOne(\App\Domains\Mahasiswa\Models\Mahasiswa::class, 'person_id');
    }
}
