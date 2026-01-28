<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkemaTarif extends Model
{
    use SoftDeletes;
    
    protected $table = 'keuangan_skema_tarif';

    protected $fillable = [
        'nama_skema', 'angkatan_id', 'prodi_id', 'program_kelas_id', 'is_active'
    ];

    // Relasi ke Detail (Nominal)
    public function details()
    {
        return $this->hasMany(DetailTarif::class, 'skema_tarif_id');
    }
}