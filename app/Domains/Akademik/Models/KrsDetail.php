<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class KrsDetail extends Model
{
    use LogsActivity;
    protected $table = 'krs_detail';

    protected $fillable = [
        'krs_id',
        'jadwal_kuliah_id',
        'kode_mk_snapshot', // Snapshot Kode
        'nama_mk_snapshot', // Snapshot Nama
        'sks_snapshot',      // Snapshot SKS
        'ekuivalensi_id',   // Referensi SK Penyetaraan
        'status_ambil',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_angka',
        'nilai_huruf',
        'nilai_indeks',
        'is_published'
    ];

    // log activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nilai_tugas', 'nilai_uts', 'nilai_uas', 'nilai_angka', 'nilai_huruf', 'nilai_indeks', 'is_published'])
            ->logOnlyDirty()
            ->useLogName('akademik');
    }

    /**
     * Relasi ke Header KRS (Parent)
     * Digunakan untuk mengetahui Mahasiswa pemilik nilai ini.
     */
    public function krs()
    {
        return $this->belongsTo(Krs::class, 'krs_id', 'id');
    }
    // Relasi ke Jadwal untuk mengambil Nama MK, Hari, Jam
    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_kuliah_id');
    }
}
