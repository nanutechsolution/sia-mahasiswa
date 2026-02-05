<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\TahunAkademik;
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
        'dosen_id',
        'nama_kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang',
        'kuota_kelas',
        'id_program_kelas_allow'
    ];
    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
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
                'dosen_id',
                'nama_kelas',
                'hari',
                'jam_mulai',
                'jam_selesai',
                'ruang',
                'kuota_kelas',
                'id_program_kelas_allow',
                'deleted_at',
            ])
            ->useLogName('Jadwal Kuliah')
            ->logOnlyDirty() // hanya perubahan yang dicatat
            ->dontSubmitEmptyLogs(); // jangan log kalau tidak ada perubahan
    }

    /**
     * Custom description untuk event
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        $mk = $this->mataKuliah->nama_mk ?? '-';
        $dosen = $this->dosen->person->nama_lengkap ?? '-';
        $kelas = $this->nama_kelas ?? '-';

        return "Jadwal Kuliah [MK: {$mk}, Kelas: {$kelas}, Dosen: {$dosen}] telah di {$eventName}.";
    }
    // Relasi yang dibutuhkan Livewire KrsPage
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\Dosen::class, 'dosen_id');
    }

    public function programKelasAllow()
    {
        return $this->belongsTo(ProgramKelas::class, 'id_program_kelas_allow');
    }

    /**
     * Relasi ke Detail KRS (Untuk menghitung peserta kelas)
     */
    public function krsDetails()
    {
        return $this->hasMany(KrsDetail::class, 'jadwal_kuliah_id');
    }

    /**
     * Relasi ke Tahun Akademik (Untuk Kop Surat PDF)
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }



    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }
}
