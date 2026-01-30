<?php

namespace App\Domains\Mahasiswa\Models;

use App\Domains\Core\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User; // Sesuaikan jika User ada di App\Models\User
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik; // Jika perlu relasi ke angkatan
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Mahasiswa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'person_id',
        'nim',
        'angkatan_id',
        'prodi_id',
        'program_kelas_id',
        'dosen_wali_id',
        'data_tambahan',
        'id_pd_feeder',
        'last_synced_at'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'data_tambahan' => 'array',
        'last_synced_at' => 'datetime',
    ];


    // logs activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('mahasiswa')
            ->logOnly([
                'nim',
                'nama_lengkap',
                'prodi_id',
                'program_kelas_id',
                'dosen_wali_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Mahasiswa {$this->nim} {$eventName}";
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    // Relasi user diakses via Person
    public function getUserAttribute()
    {
        return $this->person ? $this->person->user : null;
    }
    // ==========================================
    // RELASI UTAMA (YANG MENYEBABKAN ERROR)
    // ==========================================

    /**
     * Relasi ke Program Kelas (Reguler/Ekstensi)
     * Kunci segregasi jadwal & biaya.
     */
    public function programKelas()
    {
        // Parameter: (ModelTujuan, Foreign_Key_Local, Primary_Key_Tujuan)
        return $this->belongsTo(ProgramKelas::class, 'program_kelas_id', 'id');
    }

    /**
     * Relasi ke Program Studi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    /**
     * Relasi ke User Login
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ==========================================
    // RELASI PENDUKUNG (Future Use)
    // ==========================================

    /**
     * Relasi ke Tagihan (Untuk cek lunas/belum)
     */
    public function tagihan()
    {
        return $this->hasMany(\App\Domains\Keuangan\Models\TagihanMahasiswa::class, 'mahasiswa_id', 'id');
    }

    /**
     * Relasi ke KRS
     */
    public function krs()
    {
        return $this->hasMany(\App\Domains\Akademik\Models\Krs::class, 'mahasiswa_id', 'id');
    }

    /**
     * Relasi ke Tahun Akademik (Angkatan)
     */
    public function angkatan()
    {
        return $this->belongsTo(TahunAkademik::class, 'angkatan_id', 'id_tahun');
    }

    /**
     * Relasi ke Dosen Wali
     */
    public function dosenWali()
    {
        return $this->belongsTo(\App\Domains\Akademik\Models\Dosen::class, 'dosen_wali_id', 'id');
    }


    /**
     * Relasi ke Riwayat Status Mahasiswa
     */
    public function riwayatStatus()
    {
        return $this->hasMany(\App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa::class, 'mahasiswa_id', 'id');
    }
}
