<?php

namespace App\Domains\Core\Models;

use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Prodi extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'ref_prodi';

    protected $fillable = [
        'fakultas_id',
        'kode_prodi_dikti',
        'kode_prodi_internal',
        'nama_prodi',
        'jenjang',
        'gelar_lulusan',
        'format_nim',
        'last_nim_seq',
        'id_feeder',
        'is_active',
        'is_paket'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_prodi', 'jenjang', 'is_active', 'kode_prodi_internal'])
            ->logOnlyDirty()
            ->useLogName('master-data');
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
    // Relasi ke tabel trx_person_jabatan
    public function kaprodiAktif()
    {
        return $this->hasOne(PersonJabatan::class, 'prodi_id')
            ->whereHas('jabatan', fn($q) => $q->where('kode_jabatan', 'KAPRODI'))
            ->whereDate('tanggal_mulai', '<=', now())
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', now());
            });
    }

    // Accessor untuk merender nama bergelar
    public function getNamaKaprodiAttribute()
    {
        // Pastikan model Person sudah punya accessor getNamaBergelarAttribute
        return $this->kaprodiAktif?->person?->nama_bergelar ?? '-';
    }

    /**
     * Relasi ke Mata Kuliah
     * Sesuaikan nama fungsi ini dengan yang dipanggil di withCount()
     */
    public function prodis(): HasMany
    {
        // Pastikan namespace model MataKuliah sudah benar
        return $this->hasMany(MataKuliah::class, 'prodi_id');
    }

    /**
     * Relasi ke Kurikulum
     */
    public function kurikulums(): HasMany
    {
        return $this->hasMany(Kurikulum::class, 'prodi_id');
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'prodi_id');
    }
}
