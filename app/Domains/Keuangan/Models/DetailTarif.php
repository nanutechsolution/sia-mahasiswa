<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTarif extends Model
{
    protected $table = 'keuangan_detail_tarif';

    protected $fillable = [
        'skema_tarif_id',
        'komponen_biaya_id',
        'nominal',
        'berlaku_semester'
    ];

    public function komponenBiaya()
    {
        return $this->belongsTo(\App\Domains\Keuangan\Models\KomponenBiaya::class, 'komponen_biaya_id');
    }
}