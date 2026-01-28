<?php

namespace App\Domains\Mahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User; // Sesuaikan jika User ada di App\Models\User
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik; // Jika perlu relasi ke angkatan
use Spatie\Activitylog\Traits\LogsActivity;

class Mahasiswa extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'angkatan_id',
        'prodi_id',
        'program_kelas_id',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nik',
        'nomor_hp',
        'email_pribadi',
        'data_tambahan',
        'id_pd_feeder',
        'last_synced_at',
        'dosen_wali_id'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'data_tambahan' => 'array',
        'last_synced_at' => 'datetime',
    ];

    // logs activity
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['nim', 'nama_lengkap', 'prodi_id', 'program_kelas_id'])
            ->logOnlyDirty()
            ->useLogName('mahasiswa');
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
