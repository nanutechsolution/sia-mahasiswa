<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class MataKuliahSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk Master Mata Kuliah (TI).
     */
    public function run(): void
    {
        // Ambil ID Prodi TI sebagai target
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();

        if (!$prodi) {
            $this->command->error('Prodi TI tidak ditemukan. Pastikan FakultasProdiSeeder sudah dijalankan.');
            return;
        }

        $data = [
            // SEMESTER 1
            ['kode' => 'TI101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI102', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI103', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI104', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI105', 'nama' => 'Algoritma & Pemrograman I', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 2
            ['kode' => 'TI201', 'nama' => 'Kalkulus', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI202', 'nama' => 'Bahasa Inggris', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI203', 'nama' => 'Organisasi & Arsitektur Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI204', 'nama' => 'Struktur Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI205', 'nama' => 'Fisika Dasar', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 3
            ['kode' => 'TI301', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI302', 'nama' => 'Sistem Basis Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI303', 'nama' => 'Statistika & Probabilitas', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI304', 'nama' => 'Aljabar Linier', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 4
            ['kode' => 'TI401', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI402', 'nama' => 'Sistem Operasi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI403', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI404', 'nama' => 'Analisis & Desain Algoritma', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 5
            ['kode' => 'TI501', 'nama' => 'Pemrograman Web', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI502', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI503', 'nama' => 'Interaksi Manusia & Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI504', 'nama' => 'Keamanan Informasi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 6
            ['kode' => 'TI601', 'nama' => 'Pemrograman Mobile', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI602', 'nama' => 'Sistem Terdistribusi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI603', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'A'],
            ['kode' => 'TI604', 'nama' => 'Etika Profesi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],

            // SEMESTER 7
            ['kode' => 'TI701', 'nama' => 'Metodologi Penelitian', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI702', 'nama' => 'Kerja Praktek / Magang', 'sks' => 3, 'tm' => 0, 'p' => 0, 'l' => 3, 'jenis' => 'B'],
            ['kode' => 'TI703', 'nama' => 'Kewirausahaan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI704', 'nama' => 'Manajemen Proyek TI', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // SEMESTER 8
            ['kode' => 'TI801', 'nama' => 'Skripsi / Tugas Akhir', 'sks' => 6, 'tm' => 0, 'p' => 0, 'l' => 6, 'jenis' => 'D'],
        ];

        foreach ($data as $item) {
            MataKuliah::updateOrCreate(
                [
                    'prodi_id' => $prodi->id,
                    'kode_mk' => $item['kode']
                ],
                [
                    'nama_mk' => $item['nama'],
                    'sks_default' => $item['sks'],
                    'sks_tatap_muka' => $item['tm'],
                    'sks_praktek' => $item['p'],
                    'sks_lapangan' => $item['l'],
                    'jenis_mk' => $item['jenis'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Seeder Master Mata Kuliah (Semester 1-8) berhasil dijalankan.');
    }
}