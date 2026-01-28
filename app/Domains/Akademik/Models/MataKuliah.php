<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Core\Models\Prodi;

class MataKuliah extends Model
{
    use SoftDeletes;

    protected $table = 'master_mata_kuliahs';

    protected $fillable = [
        'prodi_id',
        'kode_mk',
        'nama_mk',
        'sks_default',    // Total SKS (Contoh: 4)
        'sks_tatap_muka', // Default Teori (Contoh: 2)
        'sks_praktek',    // Default Praktek (Contoh: 2)
        'sks_lapangan',   // Default Lapangan (Contoh: 0)
        'jenis_mk'
    ];

    /**
     * Relasi ke Program Studi
     * Wajib ada karena dipanggil menggunakan ->with('prodi')
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}