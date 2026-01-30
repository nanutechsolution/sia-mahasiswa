<?php

namespace App\Domains\Akademik\Models;

use App\Domains\Core\Models\Person as ModelsPerson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Core\Models\Prodi;

class Dosen extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'trx_dosen';

    protected $fillable = [
        'person_id',
        'prodi_id',
        'nidn',
        'nuptk',
        'jenis_dosen',
        'asal_institusi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function person()
    {
        return $this->belongsTo(ModelsPerson::class, 'person_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    // Helper untuk akses user login via Person (SSOT)
    public function getUserAttribute()
    {
        return $this->person ? $this->person->user : null;
    }


    /**
     * UPDATE ACCESSOR INI
     * Agar $dosen->nama_lengkap_gelar otomatis memunculkan gelar
     */
        public function getNamaLengkapGelarAttribute()
    {
        if ($this->person) {
            // Panggil accessor baru yang kita buat di Model Person
            return $this->person->nama_dengan_gelar;
        }
        return '-';
    }
}
