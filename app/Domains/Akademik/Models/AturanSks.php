<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;

class AturanSks extends Model
{
    protected $table = 'ref_aturan_sks';
    protected $fillable = ['min_ips', 'max_ips', 'max_sks'];
}