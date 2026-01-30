<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PersonRole extends Model
{
    protected $table = 'ref_person_role';

    protected $fillable = [
        'kode_role',
        'nama_role',
    ];
}
