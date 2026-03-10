<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkademikTranskrip extends Model
{
    /**
     * Nama tabel didefinisikan secara eksplisit.
     */
    protected $table = 'akademik_transkrip';

    /**
     * Kolom yang diizinkan untuk diisi (Mass Assignment).
     */
    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'krs_detail_id',
        'sks_diakui',
        'nilai_angka_final',
        'nilai_huruf_final',
        'nilai_indeks_final',
        'is_konversi',
    ];

    /**
     * Relasi ke Mahasiswa.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(\App\Domains\Mahasiswa\Models\Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke Master Mata Kuliah.
     */
    public function mataKuliah()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\MataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * 
     * Tanpa relasi ini, sistem tidak bisa menarik data 'Tahun Akademik' untuk pengelompokan semester.
     */
    public function krsDetail()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\KrsDetail::class, 'krs_detail_id');
    }
}