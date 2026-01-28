<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkalaNilai extends Model
{
    use SoftDeletes;

    protected $table = 'ref_skala_nilai';

    protected $fillable = [
        'huruf', 
        'bobot_indeks', 
        'nilai_min', 
        'nilai_max', 
        'is_lulus'
    ];
}