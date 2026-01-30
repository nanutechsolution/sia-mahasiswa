<?php

namespace App\Domains\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganSaldoTransaction extends Model
{
    protected $table = 'keuangan_saldo_transactions';

    protected $fillable = [
        'saldo_id',
        'tipe', // IN (Masuk/Refund ke dompet), OUT (Keluar/Dipakai bayar/Cair tunai)
        'nominal',
        'referensi_id',
        'keterangan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    public function saldo()
    {
        return $this->belongsTo(KeuanganSaldo::class, 'saldo_id');
    }
}