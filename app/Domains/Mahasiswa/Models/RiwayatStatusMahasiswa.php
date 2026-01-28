<?php

namespace App\Domains\Mahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Core\Models\TahunAkademik;

class RiwayatStatusMahasiswa extends Model
{
    // Nama tabel sesuai migration Fase 2
    protected $table = 'riwayat_status_mahasiswas';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'status_kuliah', // A=Aktif, C=Cuti, N=Non-Aktif
        'ips',           // Indeks Prestasi Semester
        'ipk',           // Indeks Prestasi Kumulatif
        'sks_semester',  // SKS diambil semester ini
        'sks_total',     // Total SKS lulus
        'nomor_sk'       // Opsional (untuk Cuti/Yudisium)
    ];

    protected $casts = [
        'ips' => 'decimal:2',
        'ipk' => 'decimal:2',
        'sks_semester' => 'integer',
        'sks_total' => 'integer',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }
}