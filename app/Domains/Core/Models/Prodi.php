<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\DB; // Tambahkan DB

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

    /**
     * Helper untuk mengambil Kaprodi Aktif saat ini dari modul HR
     */
    public function getKaprodiAttribute()
    {
        $today = date('Y-m-d');
        
        $pj = DB::table('trx_person_jabatan as pj')
            ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
            ->join('ref_person as p', 'pj.person_id', '=', 'p.id')
            ->where('j.kode_jabatan', 'KAPRODI') // Pastikan kode jabatan di master adalah 'KAPRODI'
            ->where('pj.prodi_id', $this->id) // Filter spesifik ID Prodi ini
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')
                  ->orWhere('pj.tanggal_selesai', '>=', $today);
            })
            ->select('p.nama_lengkap')
            ->first();

        return $pj ? $pj->nama_lengkap : '-';
    }
}