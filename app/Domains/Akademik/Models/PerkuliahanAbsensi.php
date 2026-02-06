<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerkuliahanAbsensi extends Model
{
    protected $table = 'perkuliahan_absensi';
    
    // Konfigurasi UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = ['id'];

    // Casting JSON otomatis ke Array agar mudah diakses
    // Contoh: $absensi->bukti_validasi['ip']
    protected $casts = [
        'waktu_check_in' => 'datetime',
        'bukti_validasi' => 'array', 
        'is_manual_update' => 'boolean',
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

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(PerkuliahanSesi::class, 'perkuliahan_sesi_id');
    }

    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KrsDetail::class, 'krs_detail_id');
    }

    // --- SCOPES (Query Filters) ---

    public function scopeHadir(Builder $query): void
    {
        $query->where('status_kehadiran', 'H');
    }

    public function scopeTidakHadir(Builder $query): void
    {
        $query->whereIn('status_kehadiran', ['A', 'I', 'S']);
    }

    public function scopeTerlambat(Builder $query): void
    {
        $query->where('status_kehadiran', 'T');
    }
    
    // --- ACCESSORS (Helpers) ---

    // Helper label status human-readable
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_kehadiran) {
            'H' => 'Hadir',
            'I' => 'Ijin',
            'S' => 'Sakit',
            'A' => 'Alpha',
            'T' => 'Terlambat',
            default => 'Tidak Diketahui',
        };
    }

    // Helper warna badge untuk UI (Tailwind classes color name)
    public function getStatusColorAttribute(): string
    {
        return match($this->status_kehadiran) {
            'H' => 'emerald', // bg-emerald-100 text-emerald-800
            'I' => 'blue',    // bg-blue-100 text-blue-800
            'S' => 'amber',   // bg-amber-100 text-amber-800
            'A' => 'rose',    // bg-rose-100 text-rose-800
            'T' => 'orange',  // bg-orange-100 text-orange-800
            default => 'slate',
        };
    }

    // Abstraksi Data JSON: Device
    public function getDeviceAttribute(): string
    {
        return $this->bukti_validasi['device'] ?? 'Unknown Device';
    }

    // Abstraksi Data JSON: Koordinat (jika pakai GPS)
    public function getKoordinatAttribute(): ?string
    {
        if (isset($this->bukti_validasi['lat']) && isset($this->bukti_validasi['long'])) {
            return $this->bukti_validasi['lat'] . ', ' . $this->bukti_validasi['long'];
        }
        return null;
    }

    // Abstraksi Data JSON: IP Address
    public function getIpAddressAttribute(): string
    {
        return $this->bukti_validasi['ip'] ?? '-';
    }

    // Cek apakah absen dilakukan tepat waktu (misal: max 15 menit setelah jadwal mulai)
    public function getIsOnTimeAttribute(): bool
    {
        if (!$this->waktu_check_in || !$this->sesi) return false;

        $jadwalMulai = $this->sesi->waktu_mulai_rencana;
        // Toleransi 15 menit
        return $this->waktu_check_in->lte($jadwalMulai->copy()->addMinutes(15));
    }
}   