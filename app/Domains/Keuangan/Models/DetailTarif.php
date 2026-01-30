<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DetailTarif extends Model
{
    use LogsActivity;

    protected $table = 'keuangan_detail_tarif';

    protected $fillable = [
        'skema_tarif_id',
        'komponen_biaya_id',
        'nominal',
        'berlaku_semester'
    ];

    /**
     * Konfigurasi activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('keuangan_detail_tarif')
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return match ($eventName) {
            'created' => 'Menambahkan detail tarif',
            'updated' => 'Mengubah detail tarif',
            'deleted' => 'Menghapus detail tarif',
            default => "Detail tarif {$eventName}",
        };
    }

    public function komponenBiaya()
    {
        return $this->belongsTo(
            \App\Domains\Keuangan\Models\KomponenBiaya::class,
            'komponen_biaya_id'
        );
    }
}
