<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fakultas extends Model
{
    use SoftDeletes;

    protected $table = 'ref_fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'nama_dekan',
        'id_feeder'
    ];

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'fakultas_id');
    }
}