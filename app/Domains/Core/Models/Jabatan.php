<?php

namespace  App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'ref_jabatan';

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'jenis',
        'is_active',
    ];

    public function personJabatans()
    {
        return $this->hasMany(PersonJabatan::class, 'jabatan_id');
    }
}
