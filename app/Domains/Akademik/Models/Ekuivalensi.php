<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Domains\Core\Models\Prodi;

class Ekuivalensi extends Model
{
    protected $table = 'akademik_ekuivalensi';

    protected $fillable = [
        'prodi_id',
        'mk_asal_id',
        'mk_tujuan_id',
        'nomor_sk',
        'keterangan',
        'is_active',
        'created_by'
    ];

    public function prodi() { return $this->belongsTo(Prodi::class); }
    
    // Mata Kuliah di Kurikulum Mahasiswa
    public function mataKuliahAsal() { return $this->belongsTo(MataKuliah::class, 'mk_asal_id'); }
    
    // Mata Kuliah yang tersedia di Jadwal
    public function mataKuliahTujuan() { return $this->belongsTo(MataKuliah::class, 'mk_tujuan_id'); }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}