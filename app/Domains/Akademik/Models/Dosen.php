<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Dosen extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'nidn',
        'nama_lengkap_gelar',
        'homebase_prodi_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // RELASI
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
