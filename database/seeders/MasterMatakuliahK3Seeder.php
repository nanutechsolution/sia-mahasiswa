<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterMatakuliahK3Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah K3...');

        // 1. Ambil Prodi K3 (Asumsi kode internal 'K3')
        $prodi = DB::table('ref_prodi')->where('kode_prodi_internal', 'K3')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'K3' tidak ditemukan di tabel ref_prodi.");
            return;
        }

        $now = Carbon::now();

        // 2. Daftar Mata Kuliah dari Dokumen Kurikulum K3
        $courses = [
            // SEMESTER 1
            ['kode' => 'K3101', 'nama' => 'Pendidikan Agama', 'sks' => 2],
            ['kode' => 'K3102', 'nama' => 'Pendidikan Pancasila', 'sks' => 2],
            ['kode' => 'K3103', 'nama' => 'Bahasa Indonesia', 'sks' => 2],
            ['kode' => 'K3104', 'nama' => 'Fisika Dasar', 'sks' => 2],
            ['kode' => 'K3105', 'nama' => 'Biologi Dasar', 'sks' => 2],
            ['kode' => 'K3106', 'nama' => 'Kimia Dasar', 'sks' => 2],
            ['kode' => 'K3107', 'nama' => 'Matematika Dasar', 'sks' => 2],
            ['kode' => 'K3108', 'nama' => 'Pengantar K3', 'sks' => 2],
            ['kode' => 'K3109', 'nama' => 'Wawasan Kebangsaan & Anti Korupsi', 'sks' => 2],

            // SEMESTER 2
            ['kode' => 'K3201', 'nama' => 'Pendidikan Kewarganegaraan', 'sks' => 2],
            ['kode' => 'K3202', 'nama' => 'Bahasa Inggris', 'sks' => 2],
            ['kode' => 'K3203', 'nama' => 'Anatomi Fisiologi', 'sks' => 2],
            ['kode' => 'K3204', 'nama' => 'Dasar Kesehatan Masyarakat', 'sks' => 2],
            ['kode' => 'K3205', 'nama' => 'Dasar Ilmu Gizi', 'sks' => 2],
            ['kode' => 'K3206', 'nama' => 'Psikologi Industri', 'sks' => 2],
            ['kode' => 'K3207', 'nama' => 'Dasar Toksikologi', 'sks' => 2],
            ['kode' => 'K3208', 'nama' => 'Hukum dan Perundang-undangan K3', 'sks' => 2],

            // SEMESTER 3
            ['kode' => 'K3301', 'nama' => 'Kesehatan Kerja', 'sks' => 3],
            ['kode' => 'K3302', 'nama' => 'Keselamatan Kerja', 'sks' => 3],
            ['kode' => 'K3303', 'nama' => 'Higene Industri Dasar', 'sks' => 3],
            ['kode' => 'K3304', 'nama' => 'Ergonomi Dasar', 'sks' => 3],
            ['kode' => 'K3305', 'nama' => 'Promosi K3', 'sks' => 2],
            ['kode' => 'K3306', 'nama' => 'Statistik Deskriptif', 'sks' => 2],
            ['kode' => 'K3307', 'nama' => 'Epidemiologi Dasar', 'sks' => 2],

            // SEMESTER 4
            ['kode' => 'K3401', 'nama' => 'Manajemen Risiko K3', 'sks' => 3],
            ['kode' => 'K3402', 'nama' => 'Sistem Manajemen K3 (SMK3)', 'sks' => 3],
            ['kode' => 'K3403', 'nama' => 'Kesehatan Lingkungan Kerja', 'sks' => 2],
            ['kode' => 'K3404', 'nama' => 'Pencegahan dan Penanggulangan Kebakaran', 'sks' => 3],
            ['kode' => 'K3405', 'nama' => 'Gizi Kerja', 'sks' => 2],
            ['kode' => 'K3406', 'nama' => 'Statistik Inferensial', 'sks' => 2],
            ['kode' => 'K3407', 'nama' => 'Metodologi Penelitian', 'sks' => 2],

            // SEMESTER 5
            ['kode' => 'K3501', 'nama' => 'Audit K3', 'sks' => 2],
            ['kode' => 'K3502', 'nama' => 'K3 Konstruksi', 'sks' => 2],
            ['kode' => 'K3503', 'nama' => 'K3 Pertambangan', 'sks' => 2],
            ['kode' => 'K3504', 'nama' => 'K3 Rumah Sakit', 'sks' => 2],
            ['kode' => 'K3505', 'nama' => 'Manajemen Tanggap Darurat', 'sks' => 2],
            ['kode' => 'K3506', 'nama' => 'Toksikologi Industri', 'sks' => 2],
            ['kode' => 'K3507', 'nama' => 'Peralatan dan Mesin Produksi', 'sks' => 2],

            // SEMESTER 6 (Pilihan MBKM)
            ['kode' => 'K3601', 'nama' => 'K3 Kimia', 'sks' => 2],
            ['kode' => 'K3602', 'nama' => 'K3 Listrik', 'sks' => 2],
            ['kode' => 'K3603', 'nama' => 'Praktek Kerja Lapangan (PKL)', 'sks' => 4],
            ['kode' => 'K3604', 'nama' => 'Seminar Usulan Penelitian (SUP)', 'sks' => 2],

            // SEMESTER 7 & 8
            ['kode' => 'K3701', 'nama' => 'Magang MBKM', 'sks' => 20],
            ['kode' => 'K3801', 'nama' => 'Skripsi', 'sks' => 4],
        ];

        foreach ($courses as $course) {
            // Tentukan apakah masuk sks_praktek atau tatap muka
            $isPraktek = str_contains(strtolower($course['nama']), 'praktek') ||
                str_contains(strtolower($course['nama']), 'magang') ||
                str_contains(strtolower($course['nama']), 'pkl');

            DB::table('master_mata_kuliahs')->updateOrInsert(
                [
                    'prodi_id' => $prodi->id,
                    'kode_mk'  => $course['kode'],
                ],
                [
                    'nama_mk'        => $course['nama'],
                    'sks_default'    => $course['sks'],
                    'sks_tatap_muka' => $isPraktek ? 0 : $course['sks'],
                    'sks_praktek'    => $isPraktek ? $course['sks'] : 0,
                    'sks_lapangan'   => 0,
                    'jenis_mk'       => 'A',
                    'activity_type'  => 'REGULAR',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            );
        }

        $this->command->info('Seeding Mata Kuliah K3 berhasil!');
    }
}
