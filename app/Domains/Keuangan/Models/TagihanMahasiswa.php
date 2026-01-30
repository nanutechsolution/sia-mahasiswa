<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TagihanMahasiswa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'tagihan_mahasiswas';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'kode_transaksi',
        'deskripsi',
        'total_tagihan',
        'total_bayar',
        'status_bayar', // BELUM, CICIL, LUNAS
        'tenggat_waktu',
        'rincian_item', // JSON
        'is_lock'
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'rincian_item' => 'array',
        'tenggat_waktu' => 'date',
        'sisa_tagihan' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['total_tagihan', 'status_bayar', 'total_bayar'])
            ->logOnlyDirty()
            ->useLogName('keuangan');
    }

    // ==========================================
    // RELASI UTAMA (SOLUSI ERROR ANDA)
    // ==========================================

    /**
     * Relasi ke Pembayaran (One to Many)
     * Satu tagihan bisa dicicil berkali-kali
     */
    public function pembayarans()
    {
        return $this->hasMany(PembayaranMahasiswa::class, 'tagihan_id', 'id');
    }

    // ==========================================
    // RELASI LAINNYA
    // ==========================================

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    // Virtual Attribute Helper
    public function getSisaTagihanAttribute()
    {
        return $this->total_tagihan - $this->total_bayar;
    }

    public function adjustments()
    {
        return $this->hasMany(KeuanganAdjustment::class, 'tagihan_id');
    }
}
