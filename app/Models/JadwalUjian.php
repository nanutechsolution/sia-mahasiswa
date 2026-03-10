<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    use HasUuids;

    protected $table = 'jadwal_ujians';

    protected $fillable = [
        'jadwal_kuliah_id',
        'jenis_ujian',
        'tanggal_ujian',
        'jam_mulai',
        'jam_selesai',
        'ruang_id',
        'metode_ujian',
        'keterangan',
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    public function ruang()
    {
        return $this->belongsTo(RefRuang::class, 'ruang_id');
    }

    public function pengawas()
    {
        return $this->hasMany(JadwalUjianPengawas::class, 'jadwal_ujian_id');
    }

    public function peserta()
    {
        return $this->hasMany(JadwalUjianPeserta::class, 'jadwal_ujian_id');
    }
}
