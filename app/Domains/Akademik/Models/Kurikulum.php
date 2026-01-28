<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Core\Models\Prodi;

class Kurikulum extends Model
{
    protected $table = 'master_kurikulums';

    protected $fillable = [
        'prodi_id',
        'nama_kurikulum',
        'tahun_mulai',
        'is_active'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    // Relasi Many-to-Many ke MK via Pivot
    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'kurikulum_mata_kuliah', 'kurikulum_id', 'mata_kuliah_id')
            ->withPivot('semester_paket', 'sks_tatap_muka', 'sks_praktek', 'sks_lapangan', 'sifat_mk')
            ->orderBy('pivot_semester_paket');
    }
}