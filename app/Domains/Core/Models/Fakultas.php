<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
     * Helper untuk mengambil Dekan Aktif saat ini dari modul HR
     */
    public function getDekanAttribute()
    {
        return PersonJabatan::with('person')
            ->whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'DEKAN'))
            ->where('fakultas_id', $this->id)
            ->whereDate('tanggal_mulai', '<=', now())
            ->where(
                fn($q) =>
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', now())
            )
            ->first()
            ?->person
            ?->nama_lengkap ?? '-';
    }
}
