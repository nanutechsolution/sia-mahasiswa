<?php

namespace  App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Gelar extends Model
{
    protected $table = 'ref_gelar';

    protected $fillable = [
        'kode',
        'nama',
        'jenjang',
        'posisi',
    ];
}