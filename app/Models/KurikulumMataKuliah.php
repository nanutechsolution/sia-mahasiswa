<?php


namespace App\Models;

use App\Domains\Akademik\Models\MataKuliah;
use Illuminate\Database\Eloquent\Model;

class KurikulumMataKuliah extends Model
{
    // Definisikan nama tabel secara eksplisit karena tidak mengikuti konvensi plural Laravel
    protected $table = 'kurikulum_mata_kuliah';

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     * kurikulum_id ditambahkan untuk mendukung create() pada Livewire Component.
     */
    protected $fillable = [
        'kurikulum_id',
        'mata_kuliah_id',
        'semester_paket',
        'sks_tatap_muka',
        'sks_praktek',
        'sks_lapangan',
        'sifat_mk',
    ];

    /**
     * Relasi Many-to-Many ke Mata Kuliah Prasyarat melalui tabel pivot baru.
     */
    public function prasyarats()
    {
        return $this->belongsToMany(
            MataKuliah::class, 
            'kurikulum_mk_prasyarat', 
            'kurikulum_mk_id', 
            'prasyarat_mk_id'
        )->withPivot('min_nilai_huruf');
    }
}