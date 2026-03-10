<?php

namespace App\Models;

use App\Domains\Akademik\Models\RefKomponenNilai;
use Illuminate\Database\Eloquent\Model;

class JadwalKomponenNilai extends Model
{
     protected $table = 'jadwal_komponen_nilai';

    protected $fillable = [
        'jadwal_kuliah_id',
        'komponen_id',
        'bobot_persen',
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    public function komponen()
    {
        return $this->belongsTo(RefKomponenNilai::class, 'komponen_id');
    }
}
