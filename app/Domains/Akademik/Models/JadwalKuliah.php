<?php

namespace App\Domains\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\TahunAkademik;
use App\Models\Dosen; // Sesuaikan namespace Dosen Anda (App\Models\Dosen atau App\Domains\Akademik\Models\Dosen)

class JadwalKuliah extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'jadwal_kuliah'; // Fix nama tabel

    protected $fillable = [
        'tahun_akademik_id',
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

    // Relasi yang dibutuhkan Livewire KrsPage
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen()
    {
        // Pastikan model Dosen sudah dibuat, biasanya di App\Domains\Akademik\Models\Dosen
        // Atau jika masih default: App\Models\Dosen
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
}
