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
        'id_semester_mulai', // Ex: 20211
        'jumlah_sks_lulus',  // Ex: 145
        'jumlah_sks_wajib',  // Ex: 140
        'jumlah_sks_pilihan', // Ex: 5
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
            ->withPivot([
                'semester_paket',
                'sks_tatap_muka',
                'sks_praktek',
                'sks_lapangan',
                'sifat_mk',
                'prasyarat_mk_id',
                'min_nilai_prasyarat'
            ])
            ->orderBy('pivot_semester_paket');
    }
}
