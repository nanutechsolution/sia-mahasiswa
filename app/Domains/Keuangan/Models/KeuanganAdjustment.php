<?php
namespace App\Domains\Keuangan\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KeuanganAdjustment extends Model
{
    use HasUuids;
    protected $table = 'keuangan_adjustments';
    protected $guarded = ['id'];

    public function tagihan() { return $this->belongsTo(TagihanMahasiswa::class, 'tagihan_id'); }
    public function creator() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
}