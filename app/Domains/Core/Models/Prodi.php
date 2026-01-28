<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Prodi extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'ref_prodi';
    protected $fillable = [
        'fakultas_id',
        'kode_prodi_dikti',
        'kode_prodi_internal',
        'nama_prodi',
        'jenjang',
        'gelar_lulusan',
        'format_nim',
        'last_nim_seq',
        'id_feeder',
        'is_active'
    ];

    // log activity

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_prodi', 'format_nim'])
            ->logOnlyDirty()
            ->useLogName('master-data');
    }
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
}
