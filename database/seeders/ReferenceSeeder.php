<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================
         * 1. PROGRAM KELAS
         * =========================
         */
        $reg = DB::table('ref_program_kelas')->insertGetId([
            'nama_program' => 'Reguler Pagi',
            'kode_internal' => 'REG',
            'is_active' => true,
            'created_at' => now(),
        ]);

        $eks = DB::table('ref_program_kelas')->insertGetId([
            'nama_program' => 'Ekstensi Malam',
            'kode_internal' => 'EKS',
            'is_active' => true,
            'created_at' => now(),
        ]);

        /**
         * =========================
         * 2. ANGKATAN & TAHUN AKADEMIK
         * =========================
         */
        DB::table('ref_angkatan')->insert([
            ['id_tahun' => 2024, 'batas_tahun_studi' => 2031, 'is_active_pmb' => false],
            ['id_tahun' => 2025, 'batas_tahun_studi' => 2032, 'is_active_pmb' => true],
        ]);

        DB::table('ref_tahun_akademik')->insert([
            'kode_tahun' => '20241',
            'nama_tahun' => 'Ganjil 2024/2025',
            'semester' => 1,
            'is_active' => true,
            'buka_krs' => true,
            'created_at' => now(),
        ]);

        /**
         * =========================
         * 3. FAKULTAS
         * =========================
         */
        /**
         * =========================
         * FAKULTAS
         * =========================
         */
        $ft = DB::table('ref_fakultas')->insertGetId([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'created_at' => now(),
        ]);

        $fkes = DB::table('ref_fakultas')->insertGetId([
            'kode_fakultas' => 'FKES',
            'nama_fakultas' => 'Fakultas Kesehatan',
            'created_at' => now(),
        ]);

        $fkip = DB::table('ref_fakultas')->insertGetId([
            'kode_fakultas' => 'FKIP',
            'nama_fakultas' => 'Fakultas Keguruan',
            'created_at' => now(),
        ]);

        $feb = DB::table('ref_fakultas')->insertGetId([
            'kode_fakultas' => 'FEB',
            'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',
            'created_at' => now(),
        ]);


        /**
         * =========================
         * 4. PROGRAM STUDI
         * =========================
         */
        $prodi = [
            [
                'fakultas_id' => $feb,
                'kode_prodi_internal' => 'BD',
                'nama_prodi' => 'Bisnis Digital',
                'jenjang' => 'S1',
            ],
            [
                'fakultas_id' => $ft,
                'kode_prodi_internal' => 'TL',
                'nama_prodi' => 'Teknik Lingkungan',
                'jenjang' => 'S1',
            ],
            [
                'fakultas_id' => $ft,
                'kode_prodi_internal' => 'TI',
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
            ],
            [
                'fakultas_id' => $feb,
                'kode_prodi_internal' => 'MI',
                'nama_prodi' => 'Manajemen Informatika',
                'jenjang' => 'D3',
            ],
            [
                'fakultas_id' => $fkes,
                'kode_prodi_internal' => 'ARS',
                'nama_prodi' => 'Administrasi Rumah Sakit',
                'jenjang' => 'S1',
            ],
            [
                'fakultas_id' => $fkip,
                'kode_prodi_internal' => 'PTI',
                'nama_prodi' => 'Pendidikan Teknologi Informasi',
                'jenjang' => 'S1',
            ],
            [
                'fakultas_id' => $fkes,
                'kode_prodi_internal' => 'K3',
                'nama_prodi' => 'Keselamatan dan Kesehatan Kerja',
                'jenjang' => 'S1',
            ],
        ];

        foreach ($prodi as $item) {
            DB::table('ref_prodi')->insert([
                ...$item,
                'is_active' => true,
                'created_at' => now(),
            ]);
        }
    }
}
