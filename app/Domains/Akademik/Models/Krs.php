<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\TahunAkademik; // Pastikan import ini ada
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Krs extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'tgl_krs',
        'status_krs',
        'dosen_wali_id'
    ];

    protected $casts = [
        'tgl_krs' => 'datetime',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function details()
    {
        return $this->hasMany(KrsDetail::class, 'krs_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('krs')
            ->logOnly([
                'mahasiswa_id',
                'tahun_akademik_id',
                'status_krs',
                'dosen_wali_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "KRS {$this->mahasiswa_id} {$eventName}";
    }
}
