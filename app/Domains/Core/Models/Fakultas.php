<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fakultas extends Model
{
    use SoftDeletes;

    protected $table = 'ref_fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'id_feeder'
    ];

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'fakultas_id');
    }
    /**
     * 1. RELASI: Mengambil data Jabatan Dekan yang sedang aktif
     */
    public function dekanAktif()
    {
        return $this->hasOne(PersonJabatan::class, 'fakultas_id')
            ->whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'DEKAN'))
            ->whereDate('tanggal_mulai', '<=', now())
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', now());
            });
    }

    public function getDekanAttribute()
    {
        $person = $this->dekanAktif?->person;
        
        // Panggil atribut nama_bergelar dari model Person
        return $person ? $person->nama_bergelar : '-';
    }
}
