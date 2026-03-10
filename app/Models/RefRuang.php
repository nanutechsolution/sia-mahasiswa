<?php

namespace App\Models;

use App\Domains\Akademik\Models\JadwalKuliah;
use Illuminate\Database\Eloquent\Model;

class RefRuang extends Model
{
    protected $table = 'ref_ruang';

    // Matikan timestamps karena tidak dibuat di migration
    public $timestamps = false;

    protected $fillable = [
        'kode_ruang',
        'nama_ruang',
        'kapasitas',
        'is_active',
    ];

    public function jadwalKuliahs()
    {
        return $this->hasMany(JadwalKuliah::class, 'ruang_id');
    }
}
