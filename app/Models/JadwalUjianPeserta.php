<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Akademik\Models\KrsDetail;

class JadwalUjianPeserta extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'jadwal_ujian_pesertas';

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = [
        'jadwal_ujian_id', 
        'krs_detail_id', 
        'status_kehadiran', 
        'nomor_kursi', 
        'waktu_check_in', 
        'catatan_pelanggaran'
    ];

    /**
     * Relasi ke Jadwal Ujian (Header)
     */
    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    /**
     * Relasi ke KRS Detail (Data Pengambilan MK Mahasiswa)
     */
    public function krsDetail()
    {
        return $this->belongsTo(KrsDetail::class, 'krs_detail_id');
    }
}