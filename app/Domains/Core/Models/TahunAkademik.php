<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    // Nama tabel sesuai database
    protected $table = 'ref_tahun_akademik';

    protected $fillable = [
        'kode_tahun',        // varchar(5)
        'nama_tahun',        // varchar(50)
        'semester',          // int: 1=Ganjil, 2=Genap, 3=Pendek
        'tanggal_mulai',     // date
        'tanggal_selesai',   // date
        'is_active',         // boolean
        'buka_krs',          // boolean
        'buka_input_nilai',  // boolean
        'tgl_mulai_krs',     // date (Kolom baru sesuai SQL)
        'tgl_selesai_krs',   // date (Kolom baru sesuai SQL)
    ];

    protected $casts = [
        'semester' => 'integer',
        'is_active' => 'boolean',
        'buka_krs' => 'boolean',
        'buka_input_nilai' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tgl_mulai_krs' => 'date',
        'tgl_selesai_krs' => 'date',
    ];
}