<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;

class RefKomponenNilai extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'ref_komponen_nilai';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment)
     */
    protected $fillable = [
        'nama_komponen',
        'slug',
        'is_active',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke tabel jadwal_komponen_nilai (Kontrak Kuliah spesifik per jadwal)
     */
    public function jadwalKomponen()
    {
        return $this->hasMany(\App\Models\JadwalKomponenNilai::class, 'komponen_id');
    }

    /**
     * Relasi ke krs_detail_nilai (Nilai riil mahasiswa per komponen)
     */
    public function krsDetailNilai()
    {
        return $this->hasMany(KrsDetailNilai::class, 'komponen_id');
    }
}
