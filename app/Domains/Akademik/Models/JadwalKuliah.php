<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\TahunAkademik;
use App\Models\RefRuang;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JadwalKuliah extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'jadwal_kuliah';

    protected $fillable = [
        'tahun_akademik_id',
        'kurikulum_id',
        'mata_kuliah_id',
        'nama_kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang_id', // Menggunakan relasi ke ref_ruang
        'kuota_kelas',
        'id_program_kelas_allow'
    ];

    /**
     * Konfigurasi Spatie Activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'tahun_akademik_id',
                'mata_kuliah_id',
                'nama_kelas',
                'hari',
                'jam_mulai',
                'jam_selesai',
                'ruang_id',
                'kuota_kelas',
                'id_program_kelas_allow',
                'deleted_at',
            ])
            ->useLogName('Jadwal Kuliah')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Deskripsi audit log disesuaikan untuk Team Teaching
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $mk = $this->mataKuliah->nama_mk ?? '-';
        $kelas = $this->nama_kelas ?? '-';
        return "Jadwal Kuliah [MK: {$mk}, Kelas: {$kelas}] telah di {$eventName}.";
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    // Relasi Many-to-Many untuk Team Teaching
    public function dosens()
    {
        return $this->belongsToMany(Dosen::class, 'jadwal_kuliah_dosen', 'jadwal_kuliah_id', 'dosen_id')
            ->withPivot('is_koordinator', 'rencana_tatap_muka')
            ->withTimestamps();
    }

    public function ruang()
    {
        return $this->belongsTo(RefRuang::class, 'ruang_id');
    }

    public function programKelasAllow()
    {
        return $this->belongsTo(ProgramKelas::class, 'id_program_kelas_allow');
    }

    public function krsDetails()
    {
        return $this->hasMany(KrsDetail::class, 'jadwal_kuliah_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function sesi()
    {
        return $this->hasMany(PerkuliahanSesi::class, 'jadwal_kuliah_id')->orderBy('pertemuan_ke', 'asc');
    }

    public function komponenNilai()
    {
        return $this->hasMany(\App\Models\JadwalKomponenNilai::class, 'jadwal_kuliah_id');
    }

    // Relasi ke Jadwal Ujian
    public function ujiana()
    {
        return $this->hasMany(\App\Models\JadwalUjian::class, 'jadwal_kuliah_id');
    }
}
