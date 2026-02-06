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
        'activity_type_snapshot',
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

    /**
     * Definisi Tipe Aktivitas (Policy Constants)
     * Digunakan untuk standarisasi logika di seluruh aplikasi.
     */
    const TYPE_REGULAR = 'REGULAR';         // Perkuliahan rutin
    const TYPE_THESIS = 'THESIS';           // Skripsi / Tugas Akhir
    const TYPE_MBKM = 'MBKM';               // Magang / Pertukaran Pelajar
    const TYPE_CONTINUATION = 'CONTINUATION'; // Registrasi administratif (Smt Akhir)


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

    /**
     * Scope untuk memfilter aktivitas khusus (Misal: untuk laporan PDDikti)
     */
    public function scopeOnlyThesis($query)
    {
        return $query->where('activity_type_snapshot', 'THESIS');
    }

    public function scopeOnlyContinuation($query)
    {
        return $query->where('activity_type_snapshot', 'CONTINUATION');
    }


    /**
     * Helper: Cek apakah baris ini merupakan skripsi
     */
    public function isFinalProject(): bool
    {
        return $this->activity_type_snapshot === self::TYPE_THESIS;
    }

    /**
     * Helper: Cek apakah baris ini hanya registrasi lanjutan (tanpa kuliah rill)
     */
    public function isAdministrativeOnly(): bool
    {
        return $this->activity_type_snapshot === self::TYPE_CONTINUATION;
    }

    /**
     * Policy Decision: Ambil label deskriptif untuk laporan keuangan/akademik
     */
    public function getActivityLabelAttribute(): string
    {
        return match ($this->activity_type_snapshot) {
            self::TYPE_THESIS => 'Tugas Akhir / Skripsi',
            self::TYPE_MBKM => 'Program MBKM',
            self::TYPE_CONTINUATION => 'Registrasi Lanjutan',
            default => 'Perkuliahan Reguler',
        };
    }
}
