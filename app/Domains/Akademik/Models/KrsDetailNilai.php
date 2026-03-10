<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;

class KrsDetailNilai extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'krs_detail_nilai';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment)
     */
    protected $fillable = [
        'krs_detail_id',
        'komponen_id',
        'nilai_angka',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'nilai_angka' => 'decimal:2',
    ];

    /**
     * Relasi ke KRS Detail (Data pengambilan kelas mahasiswa)
     */
    public function krsDetail()
    {
        return $this->belongsTo(KrsDetail::class, 'krs_detail_id');
    }

    /**
     * Relasi ke Master Komponen Nilai (Tugas, UTS, UAS, dll)
     */
    public function komponen()
    {
        return $this->belongsTo(RefKomponenNilai::class, 'komponen_id');
    }
}
