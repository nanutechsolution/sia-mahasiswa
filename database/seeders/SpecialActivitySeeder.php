<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class SpecialActivitySeeder extends Seeder
{
    public function run(): void
    {
        $prodis = Prodi::all();

        foreach ($prodis as $prodi) {
            // 1. Mata Kuliah Skripsi (Mode: THESIS)
            MataKuliah::updateOrCreate(
                ['kode_mk' => $prodi->kode_prodi_internal . '-TA', 'prodi_id' => $prodi->id],
                [
                    'nama_mk' => 'Skripsi / Tugas Akhir',
                    'sks_default' => 6,
                    'jenis_mk' => 'D',
                    'activity_type' => 'THESIS'
                ]
            );

            // 2. Mata Kuliah Registrasi (Mode: CONTINUATION)
            // Digunakan agar mahasiswa tetap tercatat "Aktif" di PDDikti tanpa beban SKS rill
            MataKuliah::updateOrCreate(
                ['kode_mk' => 'REG-ADM', 'prodi_id' => $prodi->id],
                [
                    'nama_mk' => 'Registrasi Administrasi (Menunggu Kelulusan)',
                    'sks_default' => 0,
                    'jenis_mk' => 'A',
                    'activity_type' => 'CONTINUATION'
                ]
            );

            // 3. Mata Kuliah MBKM (Mode: MBKM)
            MataKuliah::updateOrCreate(
                ['kode_mk' => 'MBKM-INT', 'prodi_id' => $prodi->id],
                [
                    'nama_mk' => 'Magang Industri (MBKM)',
                    'sks_default' => 20,
                    'jenis_mk' => 'C',
                    'activity_type' => 'MBKM'
                ]
            );
        }
    }
}