<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Core\Models\Prodi;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MataKuliah extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'master_mata_kuliahs';

    protected $fillable = [
        'prodi_id',
        'kode_mk',
        'nama_mk',
        'sks_default',    // Total SKS (Contoh: 4)
        'sks_tatap_muka', // Default Teori (Contoh: 2)
        'sks_praktek',    // Default Praktek (Contoh: 2)
        'sks_lapangan',   // Default Lapangan (Contoh: 0)
        'jenis_mk'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('mata_kuliah')
            ->logOnly([
                'kode_mk',
                'nama_mk',
                'sks_default',
                'jenis_mk'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relasi ke Program Studi
     * Wajib ada karena dipanggil menggunakan ->with('prodi')
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
