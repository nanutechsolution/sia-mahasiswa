<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Prodi TI (dari ReferenceSeeder)
        $prodiTi = DB::table('ref_prodi')->where('kode_prodi_internal', 'TI')->value('id');

        // 2. Insert MASTER MATA KULIAH (Bank Data MK)
        // Kita buat beberapa MK untuk simulasi semester 1 & 2
        $mkData = [
            [
                'prodi_id' => $prodiTi,
                'kode_mk' => 'TI-101',
                'nama_mk' => 'Algoritma Pemrograman',
                'sks_default' => 3,
                'jenis_mk' => 'A', // Wajib Nasional
                'created_at' => now(),
            ],
            [
                'prodi_id' => $prodiTi,
                'kode_mk' => 'TI-102',
                'nama_mk' => 'Basis Data Dasar',
                'sks_default' => 3,
                'jenis_mk' => 'A',
                'created_at' => now(),
            ],
            [
                'prodi_id' => $prodiTi,
                'kode_mk' => 'TI-103',
                'nama_mk' => 'Bahasa Inggris Teknik',
                'sks_default' => 2,
                'jenis_mk' => 'A',
                'created_at' => now(),
            ],
            [
                'prodi_id' => $prodiTi,
                'kode_mk' => 'TI-104',
                'nama_mk' => 'Matematika Diskrit',
                'sks_default' => 3,
                'jenis_mk' => 'A',
                'created_at' => now(),
            ],
        ];

        DB::table('master_mata_kuliahs')->insert($mkData);

        // Ambil ID MK yang baru diinsert untuk relasi kurikulum
        $idAlgo = DB::table('master_mata_kuliahs')->where('kode_mk', 'TI-101')->value('id');
        $idBasdat = DB::table('master_mata_kuliahs')->where('kode_mk', 'TI-102')->value('id');
        $idInggris = DB::table('master_mata_kuliahs')->where('kode_mk', 'TI-103')->value('id');
        $idMatdisk = DB::table('master_mata_kuliahs')->where('kode_mk', 'TI-104')->value('id');

        // 3. Insert HEADER KURIKULUM
        $kurikulumId = DB::table('master_kurikulums')->insertGetId([
            'prodi_id' => $prodiTi,
            'nama_kurikulum' => 'Kurikulum 2024 - Merdeka Belajar',
            'tahun_mulai' => 2024,
            'is_active' => true,
            'created_at' => now(),
        ]);

        // 4. Insert ISI KURIKULUM (Pivot Table)
        // Menentukan MK ini diambil di semester berapa
        $pivotData = [
            [
                'kurikulum_id' => $kurikulumId,
                'mata_kuliah_id' => $idAlgo,
                'semester_paket' => 1,
                'sks_tatap_muka' => 3,
                'sifat_mk' => 'W', // Wajib
                'created_at' => now(),
            ],
            [
                'kurikulum_id' => $kurikulumId,
                'mata_kuliah_id' => $idInggris,
                'semester_paket' => 1,
                'sks_tatap_muka' => 2,
                'sifat_mk' => 'W',
                'created_at' => now(),
            ],
            [
                'kurikulum_id' => $kurikulumId,
                'mata_kuliah_id' => $idMatdisk,
                'semester_paket' => 1,
                'sks_tatap_muka' => 3,
                'sifat_mk' => 'W',
                'created_at' => now(),
            ],
            [
                'kurikulum_id' => $kurikulumId,
                'mata_kuliah_id' => $idBasdat,
                'semester_paket' => 2, // Ini semester 2
                'sks_tatap_muka' => 3,
                'sifat_mk' => 'W',
                'created_at' => now(),
            ],
        ];

        DB::table('kurikulum_mata_kuliah')->insert($pivotData);
    }
}