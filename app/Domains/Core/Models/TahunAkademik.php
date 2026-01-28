<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TahunAkademik extends Model
{
    use LogsActivity;
    // Nama tabel sesuai database
    protected $table = 'ref_tahun_akademik';

    protected $fillable = [
        'kode_tahun',        // varchar(5)
        'nama_tahun',        // varchar(50)
        'semester',          // int: 1=Ganjil, 2=Genap, 3=Pendek
        'tanggal_mulai',     // date
        'tanggal_selesai',   // date
        'is_active',         // boolean
        'buka_krs',          // boolean
        'buka_input_nilai',  // boolean
        'tgl_mulai_krs',     // date (Kolom baru sesuai SQL)
        'tgl_selesai_krs',   // date (Kolom baru sesuai SQL)
    ];

    protected $casts = [
        'semester' => 'integer',
        'is_active' => 'boolean',
        'buka_krs' => 'boolean',
        'buka_input_nilai' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tgl_mulai_krs' => 'date',
        'tgl_selesai_krs' => 'date',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tahun_akademik')
            ->logOnly([
                'kode_tahun',
                'semester',
                'is_active',
                'buka_krs',
                'buka_input_nilai',
                'tgl_mulai_krs',
                'tgl_selesai_krs'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Tahun Akademik {$this->kode_tahun} {$eventName}";
    }


    public function isKrsOpen(): bool
    {
        return $this->buka_krs
            && now()->between($this->tgl_mulai_krs, $this->tgl_selesai_krs);
    }

    public function isInputNilaiOpen(): bool
    {
        return $this->buka_input_nilai
            && now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }
}
