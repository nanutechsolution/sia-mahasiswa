<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PerkuliahanSesi extends Model
{
    protected $table = 'perkuliahan_sesi';
    
    // Konfigurasi UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_mulai_rencana' => 'datetime',
        'waktu_mulai_realisasi' => 'datetime',
        'waktu_selesai_realisasi' => 'datetime',
    ];

    /**
     * Boot function to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // --- RELATIONS ---

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(PerkuliahanAbsensi::class, 'perkuliahan_sesi_id');
    }
}