<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomponenBiaya extends Model
{
    use SoftDeletes;

    protected $table = 'keuangan_komponen_biaya';

    protected $fillable = [
        'nama_komponen',
        'tipe_biaya', // TETAP, SKS, SEKALI, INSIDENTAL
        'is_active'
    ];
}