<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterMatakuliahMISeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah Manajemen Informatika...');

        // 1. Ambil Prodi MI (Sesuaikan kode internalnya, asumsi 'MI')
        $prodi = DB::table('ref_prodi')->where('kode_prodi_internal', 'MI')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'MI' tidak ditemukan di tabel ref_prodi.");
            return;
        }

        $now = Carbon::now();

        // 2. Daftar Mata Kuliah dari Dokumen PDF
        $courses = [
            // SEMESTER 1
            ['kode' => 'MI1110001', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI1110002', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI1110003', 'nama' => 'Pengantar Teknologi Informatika', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI1110004', 'nama' => 'Akuntansi I', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI1110005', 'nama' => 'Algoritma dan Struktur Data', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI1110006', 'nama' => 'Dasar Manajemen Bisnis', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI1110007', 'nama' => 'Praktek Algoritma dan Struktur Data', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI1110008', 'nama' => 'Praktek Aplikasi Perkantoran', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI1110009', 'nama' => 'Bahasa Inggris I', 'sks' => 2, 'ket' => 'Teori'],

            // SEMESTER 2
            ['kode' => 'MI2110010', 'nama' => 'Pendidikan Pancasila', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI2110011', 'nama' => 'Kewarganegaraan', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI2110012', 'nama' => 'Sistem Operasi', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI2110013', 'nama' => 'Basis Data', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI2110014', 'nama' => 'Statistik', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI2110015', 'nama' => 'Praktek Basis Data', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI2110016', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI2110017', 'nama' => 'Praktek Pemrograman Berorientasi Objek', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI2110018', 'nama' => 'Bahasa Inggris II', 'sks' => 2, 'ket' => 'Teori'],

            // SEMESTER 3
            ['kode' => 'MI3110019', 'nama' => 'Analisa dan Perancangan Sistem Informasi', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI3110020', 'nama' => 'Pemrograman Web', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI3110021', 'nama' => 'Praktek Pemrograman Web', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI3110022', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI3110023', 'nama' => 'Praktek Jaringan Komputer', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI3110024', 'nama' => 'Kewirausahaan', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI3110025', 'nama' => 'Akuntansi II', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI3110026', 'nama' => 'Interaksi Manusia dan Komputer', 'sks' => 3, 'ket' => 'Teori'],

            // SEMESTER 4
            ['kode' => 'MI4110030', 'nama' => 'Pemrograman Web Lanjut', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI4110031', 'nama' => 'Praktek Pemrograman Web Lanjut', 'sks' => 2, 'ket' => 'Praktek'],
            ['kode' => 'MI4110032', 'nama' => 'Sistem Informasi Perbankan', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI4110033', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI4110034', 'nama' => 'Sistem Informasi Akuntansi', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI4110035', 'nama' => 'Sistem Informasi Manajemen', 'sks' => 3, 'ket' => 'Teori'],
            ['kode' => 'MI4110036', 'nama' => 'Etika Profesi', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI4110037', 'nama' => 'Manajemen Proyek', 'sks' => 2, 'ket' => 'Teori'],
            ['kode' => 'MI4110038', 'nama' => 'Teknik Penulisan Ilmiah', 'sks' => 2, 'ket' => 'Teori'],

            // SEMESTER 5 & 6 (MBKM / Tugas Akhir)
            ['kode' => 'MI5110039', 'nama' => 'Jalur Pilihan (PKL)*', 'sks' => 20, 'ket' => 'Praktek'],
            ['kode' => 'MI6110040', 'nama' => 'Jalur Pilihan (Magang)*', 'sks' => 20, 'ket' => 'Praktek'],
            ['kode' => 'MI6110041', 'nama' => 'Tugas Akhir', 'sks' => 4, 'ket' => 'Praktek'],
        ];

        foreach ($courses as $course) {
            $isPraktek = ($course['ket'] === 'Praktek');

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

        $this->command->info('Seeding Mata Kuliah Manajemen Informatika berhasil!');
    }
}
