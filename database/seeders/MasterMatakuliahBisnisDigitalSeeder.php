<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class MasterMatakuliahBisnisDigitalSeeder extends Seeder
{
    /**
     * Seeder untuk Master Mata Kuliah S1 Bisnis Digital (BD)
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah Bisnis Digital...');

        // 1. Ambil Prodi BD
        $prodi = Prodi::where('kode_prodi_internal', 'BD')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'BD' tidak ditemukan.");
            return;
        }

        // 2. Daftar Mata Kuliah
        // A = Wajib Nasional
        // B = Wajib Prodi (Keahlian)
        // C = Pilihan
        // D = Tugas Akhir/Skripsi
        
        $courses = [
            // --- SEMESTER 1 (Fondasi Bisnis & TI) ---
            ['kode' => 'BD-101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'BD-102', 'nama' => 'Pancasila & Kewarganegaraan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'BD-103', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'BD-104', 'nama' => 'Pengantar Bisnis', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-105', 'nama' => 'Pengantar Akuntansi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-106', 'nama' => 'Dasar-Dasar Manajemen', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-107', 'nama' => 'Algoritma & Pemrograman Dasar', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-108', 'nama' => 'Matematika Bisnis', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 2 (Ekonomi Digital) ---
            ['kode' => 'BD-201', 'nama' => 'Bahasa Inggris Bisnis', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'BD-202', 'nama' => 'Ekonomi Mikro & Makro', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-203', 'nama' => 'Pemasaran Digital (Digital Marketing)', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-204', 'nama' => 'Statistika Bisnis', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-205', 'nama' => 'Manajemen Keuangan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-206', 'nama' => 'Sistem Informasi Manajemen', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-207', 'nama' => 'Hukum Bisnis & Regulasi Digital', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 3 (Analisis & Strategi) ---
            ['kode' => 'BD-301', 'nama' => 'Perilaku Konsumen di Era Digital', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-302', 'nama' => 'E-Commerce & Marketplace', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-303', 'nama' => 'Analisis Data Bisnis', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-304', 'nama' => 'Manajemen Operasional', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-305', 'nama' => 'Desain Web & User Experience (UI/UX)', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-306', 'nama' => 'Manajemen Sumber Daya Manusia', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-307', 'nama' => 'Kewirausahaan Digital (Technopreneurship)', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 4 (Teknologi Finansial & Inovasi) ---
            ['kode' => 'BD-401', 'nama' => 'Financial Technology (Fintech)', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-402', 'nama' => 'Manajemen Rantai Pasok (Supply Chain)', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-403', 'nama' => 'Riset Pemasaran', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-404', 'nama' => 'Manajemen Hubungan Pelanggan (CRM)', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-405', 'nama' => 'Ekonomi Kreatif', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-406', 'nama' => 'Komunikasi Bisnis & Negosiasi', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-407', 'nama' => 'Etika Bisnis & CSR', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],

            // --- SEMESTER 5 (Pengembangan Bisnis & Big Data) ---
            ['kode' => 'BD-501', 'nama' => 'Metodologi Penelitian Bisnis', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-502', 'nama' => 'Manajemen Strategik', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-503', 'nama' => 'Big Data & Business Intelligence', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-504', 'nama' => 'Start-up Business Development', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-505', 'nama' => 'Keamanan Siber untuk Bisnis', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'C'], // Pilihan
            ['kode' => 'BD-506', 'nama' => 'Analisis Investasi & Portofolio', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'C'], // Pilihan

            // --- SEMESTER 6 (MBKM / Magang) ---
            ['kode' => 'BD-601', 'nama' => 'Magang Industri / Praktik Bisnis', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'B'],
            ['kode' => 'BD-602', 'nama' => 'Proyek Kewirausahaan', 'sks' => 3, 'tm' => 0, 'p' => 3, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-603', 'nama' => 'Studi Kelayakan Bisnis', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-604', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'A'],

            // --- SEMESTER 7 (Tugas Akhir Awal) ---
            ['kode' => 'BD-701', 'nama' => 'Seminar Proposal Bisnis', 'sks' => 2, 'tm' => 1, 'p' => 1, 'l' => 0, 'jenis' => 'D'],
            ['kode' => 'BD-702', 'nama' => 'Manajemen Risiko Bisnis', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-703', 'nama' => 'Kepemimpinan Digital (Digital Leadership)', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'BD-704', 'nama' => 'Pemasaran Media Sosial', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'C'], // Pilihan

            // --- SEMESTER 8 (Tugas Akhir) ---
            ['kode' => 'BD-801', 'nama' => 'Skripsi / Tugas Akhir', 'sks' => 6, 'tm' => 0, 'p' => 0, 'l' => 6, 'jenis' => 'D'],
        ];

        // 3. Eksekusi Penyimpanan
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

        $this->command->info("Berhasil menambahkan {$count} Master Mata Kuliah untuk Bisnis Digital.");
    }
}