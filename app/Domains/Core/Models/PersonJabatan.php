<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PersonJabatan extends Model
{
    protected $table = 'trx_person_jabatan';

    protected $fillable = [
        'person_id',
        'jabatan_id',
        'fakultas_id',
        'prodi_id',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    /**
     * RELATIONS
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    /**
     * SCOPES
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('tanggal_selesai');
    }

    public function isAktif(): bool
    {
        return is_null($this->tanggal_selesai);
    }
}