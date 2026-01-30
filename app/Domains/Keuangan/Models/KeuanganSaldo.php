<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Domains\Mahasiswa\Models\Mahasiswa;

class KeuanganSaldo extends Model
{
    use HasUuids;
    protected $table = 'keuangan_saldos';
    protected $guarded = ['id'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
    public function transactions()
    {
        return $this->hasMany(KeuanganSaldoTransaction::class, 'saldo_id');
    }
}
