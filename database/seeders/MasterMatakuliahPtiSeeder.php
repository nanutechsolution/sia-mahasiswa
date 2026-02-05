<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class MasterMatakuliahPtiSeeder extends Seeder
{
    /**
     * Seeder untuk Master Mata Kuliah S1 Pendidikan Teknologi Informasi (PTI)
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah PTI...');

        // 1. Ambil Prodi PTI
        $prodi = Prodi::where('kode_prodi_internal', 'PTI')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'PTI' tidak ditemukan.");
            return;
        }

        // 2. Daftar Mata Kuliah (Kombinasi Kependidikan & TI)
        // A = Wajib Nasional
        // B = Wajib Prodi (Keahlian & Kependidikan)
        // C = Pilihan
        // D = Tugas Akhir/PLP
        
        $courses = [
            // --- SEMESTER 1 (Paket Dasar) ---
            ['kode' => 'PTI-101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'PTI-102', 'nama' => 'Pancasila', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'PTI-103', 'nama' => 'Pengantar Pendidikan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-104', 'nama' => 'Perkembangan Peserta Didik', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-105', 'nama' => 'Matematika Dasar', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-106', 'nama' => 'Algoritma & Pemrograman Dasar', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-107', 'nama' => 'Bahasa Inggris Profesi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],

            // --- SEMESTER 2 ---
            ['kode' => 'PTI-201', 'nama' => 'Kewarganegaraan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'PTI-202', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'PTI-203', 'nama' => 'Belajar dan Pembelajaran', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-204', 'nama' => 'Profesi Kependidikan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-205', 'nama' => 'Struktur Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-206', 'nama' => 'Arsitektur Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-207', 'nama' => 'Fisika Dasar', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 3 ---
            ['kode' => 'PTI-301', 'nama' => 'Kurikulum & Pembelajaran TI', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-302', 'nama' => 'Strategi Pembelajaran TI', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-303', 'nama' => 'Basis Data', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-304', 'nama' => 'Sistem Operasi', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-305', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-306', 'nama' => 'Statistika Pendidikan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 4 ---
            ['kode' => 'PTI-401', 'nama' => 'Evaluasi Pembelajaran TI', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-402', 'nama' => 'Media Pembelajaran Berbasis TIK', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'], // MK Kependidikan
            ['kode' => 'PTI-403', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-404', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-405', 'nama' => 'Pemrograman Web', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-406', 'nama' => 'Desain Grafis & Multimedia', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 5 ---
            ['kode' => 'PTI-501', 'nama' => 'Metodologi Penelitian Pendidikan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-502', 'nama' => 'Interaksi Manusia dan Komputer', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-503', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-504', 'nama' => 'Multimedia Pembelajaran Interaktif', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-505', 'nama' => 'Pengembangan E-Learning', 'sks' => 3, 'tm' => 1, 'p' => 2, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-506', 'nama' => 'Kewirausahaan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],

            // --- SEMESTER 6 (Magang/KKN) ---
            ['kode' => 'PTI-601', 'nama' => 'Microteaching', 'sks' => 2, 'tm' => 0, 'p' => 2, 'l' => 0, 'jenis' => 'B'], // Praktek Mengajar
            ['kode' => 'PTI-602', 'nama' => 'Keamanan Jaringan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-603', 'nama' => 'Pemrograman Mobile', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-604', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'A'],

            // --- SEMESTER 7 (PLP/Skripsi Awal) ---
            ['kode' => 'PTI-701', 'nama' => 'Pengenalan Lapangan Persekolahan (PLP)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'B'], // Magang Sekolah
            ['kode' => 'PTI-702', 'nama' => 'Seminar Proposal Skripsi', 'sks' => 2, 'tm' => 1, 'p' => 1, 'l' => 0, 'jenis' => 'D'],
            ['kode' => 'PTI-703', 'nama' => 'Etika Profesi Keguruan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'PTI-704', 'nama' => 'Data Mining Pendidikan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'], // Pilihan

            // --- SEMESTER 8 ---
            ['kode' => 'PTI-801', 'nama' => 'Skripsi', 'sks' => 6, 'tm' => 0, 'p' => 0, 'l' => 6, 'jenis' => 'D'],
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

        $this->command->info("Berhasil menambahkan {$count} Master Mata Kuliah untuk PTI.");
    }
}