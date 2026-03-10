<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUjianPengawas extends Model
{
    protected $table = 'jadwal_ujian_pengawas';
    protected $fillable = ['jadwal_ujian_id', 'person_id', 'peran'];

    public function person()
    {
        return $this->belongsTo(\App\Domains\Core\Models\Person::class, 'person_id');
    }
}
