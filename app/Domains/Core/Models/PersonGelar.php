<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PersonGelar extends Model
{
    protected $table = 'trx_person_gelar';

    protected $fillable = [
        'person_id',
        'gelar_id',
    ];
}