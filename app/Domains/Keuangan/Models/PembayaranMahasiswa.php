<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PembayaranMahasiswa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'pembayaran_mahasiswas';

    protected $fillable = [
        'tagihan_id',
        'nominal_bayar',
        'tanggal_bayar',
        'metode_pembayaran',   // MANUAL, GATEWAY
        'bukti_bayar_path',    // Path gambar bukti
        'keterangan_pengirim', // Nama pengirim (opsional)
        'status_verifikasi',   // PENDING, VALID, INVALID
        'verified_by',
        'verified_at',
        'catatan_verifikasi'
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'verified_at' => 'datetime',
        // Casting decimal agar tidak dianggap string
        'nominal_bayar' => 'decimal:2',
    ];

    // log activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nominal_bayar', 'status_verifikasi', 'verified_by', 'verified_at'])
            ->logOnlyDirty()
            ->useLogName('keuangan');
    }

    // ==========================================
    // RELASI
    // ==========================================

    /**
     * Relasi ke Tagihan Induk
     */
    public function tagihan()
    {
        return $this->belongsTo(TagihanMahasiswa::class, 'tagihan_id');
    }

    /**
     * Relasi ke Admin yang memverifikasi
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
