<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramKelas extends Model
{
    use SoftDeletes;

    // WAJIB: Definisikan nama tabel yang benar
    protected $table = 'ref_program_kelas';

    protected $fillable = [
        'nama_program',
        'kode_internal',
        'id_jenis_kelas_feeder',
        'min_pembayaran_persen',
        'is_active',
        'deskripsi'
    ];
}