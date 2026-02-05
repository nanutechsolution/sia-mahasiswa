<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class MasterMatakuliahTISeeder extends Seeder
{
    /**
     * Seeder untuk Master Mata Kuliah S1 Teknik Informatika
     * Berdasarkan Dokumen: Kurikulum MBKM UNMARIS 2023-2028
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah Teknik Informatika (MBKM 2023-2028)...');

        // 1. Ambil Prodi Teknik Informatika
        // Pastikan kode 'TI' sesuai dengan data di tabel ref_prodi
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'TI' tidak ditemukan. Harap jalankan FakultasProdiSeeder terlebih dahulu.");
            return;
        }

        // 2. Daftar Mata Kuliah Lengkap (Semester 1 - 8)
        // Format Jenis MK: 
        // A = Wajib Nasional/Universitas
        // B = Wajib Program Studi
        // C = Pilihan / MBKM
        // D = Tugas Akhir / Skripsi

        $courses = [
            // --- SEMESTER 1 (20 SKS) ---
            ['kode' => 'TI-101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-102', 'nama' => 'Pancasila & Kewarganegaraan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-103', 'nama' => 'Bahasa Inggris I', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-104', 'nama' => 'Kalkulus I', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-105', 'nama' => 'Logika Informatika', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-106', 'nama' => 'Algoritma & Pemrograman I', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-107', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-108', 'nama' => 'Fisika Dasar', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 2 (21 SKS) ---
            ['kode' => 'TI-201', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-202', 'nama' => 'Bahasa Inggris II', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-203', 'nama' => 'Kalkulus II', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-204', 'nama' => 'Aljabar Linier', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-205', 'nama' => 'Algoritma & Pemrograman II', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-206', 'nama' => 'Struktur Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-207', 'nama' => 'Sistem Digital', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 3 (22 SKS) ---
            ['kode' => 'TI-301', 'nama' => 'Statistika & Probabilitas', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-302', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-303', 'nama' => 'Organisasi & Arsitektur Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-304', 'nama' => 'Sistem Basis Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-305', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-306', 'nama' => 'Sistem Operasi', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-307', 'nama' => 'Desain Web Dasar', 'sks' => 2, 'tm' => 1, 'p' => 1, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 4 (23 SKS) ---
            ['kode' => 'TI-401', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-402', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-403', 'nama' => 'Analisis Desain Algoritma', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-404', 'nama' => 'Pemrograman Web Lanjut', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-405', 'nama' => 'Teori Bahasa & Automata', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-406', 'nama' => 'Interaksi Manusia & Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-407', 'nama' => 'Riset Operasi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-408', 'nama' => 'Kecerdasan Buatan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 5 (21 SKS) ---
            ['kode' => 'TI-501', 'nama' => 'Metodologi Penelitian', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-502', 'nama' => 'Pemrograman Mobile', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-503', 'nama' => 'Keamanan Sistem Informasi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-504', 'nama' => 'Sistem Terdistribusi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-505', 'nama' => 'Grafika Komputer', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'], // Pilihan
            ['kode' => 'TI-506', 'nama' => 'Teknopreneurship', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'TI-507', 'nama' => 'Pengolahan Citra Digital', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'], // Pilihan

            // --- SEMESTER 6 (20 SKS - Fokus MBKM / Lapangan) ---
            ['kode' => 'TI-601', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'A'],
            ['kode' => 'TI-602', 'nama' => 'Kerja Praktek (KP)', 'sks' => 2, 'tm' => 0, 'p' => 0, 'l' => 2, 'jenis' => 'B'],
            ['kode' => 'TI-603', 'nama' => 'Etika Profesi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'TI-604', 'nama' => 'Sistem Penunjang Keputusan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'C'],
            ['kode' => 'TI-605', 'nama' => 'Data Mining', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'],
            ['kode' => 'TI-606', 'nama' => 'Cloud Computing', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'],
            ['kode' => 'TI-607', 'nama' => 'Internet of Things (IoT)', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'],

            // --- SEMESTER 7 (10 SKS - Persiapan Akhir) ---
            ['kode' => 'TI-701', 'nama' => 'Seminar Proposal', 'sks' => 2, 'tm' => 1, 'p' => 0, 'l' => 1, 'jenis' => 'D'],
            ['kode' => 'TI-702', 'nama' => 'Machine Learning', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'],
            ['kode' => 'TI-703', 'nama' => 'Big Data Analytics', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'C'],
            ['kode' => 'TI-704', 'nama' => 'Manajemen Proyek TI', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 8 (6 SKS) ---
            ['kode' => 'TI-801', 'nama' => 'Skripsi / Tugas Akhir', 'sks' => 6, 'tm' => 0, 'p' => 0, 'l' => 6, 'jenis' => 'D'],
        ];

        // 3. Eksekusi Penyimpanan ke Database
        $count = 0;
        foreach ($courses as $c) {
            MataKuliah::updateOrCreate(
                [
                    'prodi_id' => $prodi->id,
                    'kode_mk' => $c['kode']
                ],
                [
                    'nama_mk' => $c['nama'],
                    'sks_default' => $c['sks'],
                    'sks_tatap_muka' => $c['tm'],
                    'sks_praktek' => $c['p'],
                    'sks_lapangan' => $c['l'],
                    'jenis_mk' => $c['jenis'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $count++;
        }

        $this->command->info("Berhasil menambahkan {$count} Master Mata Kuliah untuk Teknik Informatika.");
    }
}
