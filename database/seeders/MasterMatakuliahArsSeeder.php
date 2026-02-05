<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class MasterMatakuliahArsSeeder extends Seeder
{
    /**
     * Seeder untuk Master Mata Kuliah S1 Administrasi Rumah Sakit (ARS)
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah ARS...');

        // 1. Ambil Prodi ARS
        $prodi = Prodi::where('kode_prodi_internal', 'ARS')->first();

        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'ARS' tidak ditemukan.");
            return;
        }

        // 2. Daftar Mata Kuliah
        // A = Wajib Nasional
        // B = Wajib Prodi (Keahlian)
        // C = Pilihan
        // D = Tugas Akhir/Skripsi
        
        $courses = [
            // --- SEMESTER 1 (Fondasi) ---
            ['kode' => 'ARS-101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'ARS-102', 'nama' => 'Pancasila & Kewarganegaraan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'ARS-103', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'ARS-104', 'nama' => 'Pengantar Ilmu Kesehatan Masyarakat', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-105', 'nama' => 'Dasar-Dasar Manajemen', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-106', 'nama' => 'Terminologi Medis', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'], // Penting untuk ARS
            ['kode' => 'ARS-107', 'nama' => 'Anatomi & Fisiologi Dasar', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-108', 'nama' => 'Aplikasi Komputer Dasar', 'sks' => 2, 'tm' => 1, 'p' => 1, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 2 (Dasar Keahlian) ---
            ['kode' => 'ARS-201', 'nama' => 'Bahasa Inggris Kesehatan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'A'],
            ['kode' => 'ARS-202', 'nama' => 'Manajemen Rumah Sakit Dasar', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-203', 'nama' => 'Etika dan Hukum Kesehatan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-204', 'nama' => 'K3 Rumah Sakit (K3RS)', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-205', 'nama' => 'Psikologi Kesehatan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-206', 'nama' => 'Dasar Akuntansi', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-207', 'nama' => 'Komunikasi Efektif', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 3 (Manajemen Fungsional) ---
            ['kode' => 'ARS-301', 'nama' => 'Manajemen SDM Rumah Sakit', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-302', 'nama' => 'Manajemen Pemasaran RS', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-303', 'nama' => 'Manajemen Keuangan RS', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-304', 'nama' => 'Manajemen Rekam Medis & Informasi Kesehatan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-305', 'nama' => 'Epidemiologi', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-306', 'nama' => 'Sistem Informasi Manajemen RS (SIMRS)', 'sks' => 4, 'tm' => 2, 'p' => 2, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 4 (Manajemen Operasional) ---
            ['kode' => 'ARS-401', 'nama' => 'Manajemen Logistik Medis & Non-Medis', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-402', 'nama' => 'Akuntansi Rumah Sakit', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-403', 'nama' => 'Manajemen Mutu Pelayanan Kesehatan', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-404', 'nama' => 'Manajemen Fasilitas & Keselamatan RS', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-405', 'nama' => 'Asuransi Kesehatan & JKN', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-406', 'nama' => 'Biostatistik', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-407', 'nama' => 'Manajemen Farmasi RS', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],

            // --- SEMESTER 5 (Manajemen Lanjut & Riset) ---
            ['kode' => 'ARS-501', 'nama' => 'Metodologi Penelitian Kesehatan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-502', 'nama' => 'Manajemen Risiko & Patient Safety', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-503', 'nama' => 'Manajemen Unit Rawat Jalan & Inap', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-504', 'nama' => 'Perpajakan Rumah Sakit', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-505', 'nama' => 'Auditing Medis', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-506', 'nama' => 'Kewirausahaan Kesehatan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'A'],

            // --- SEMESTER 6 (Strategi & Kebijakan) ---
            ['kode' => 'ARS-601', 'nama' => 'Akreditasi Rumah Sakit', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-602', 'nama' => 'Perencanaan Strategis RS (Renstra)', 'sks' => 3, 'tm' => 3, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-603', 'nama' => 'Analisis Kebijakan Kesehatan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-604', 'nama' => 'Studi Kelayakan Bisnis RS', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-605', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'A'],

            // --- SEMESTER 7 (Praktik Lapangan) ---
            ['kode' => 'ARS-701', 'nama' => 'Magang / Praktik Kerja Lapangan', 'sks' => 4, 'tm' => 0, 'p' => 0, 'l' => 4, 'jenis' => 'B'], // Magang di RS
            ['kode' => 'ARS-702', 'nama' => 'Seminar Proposal', 'sks' => 2, 'tm' => 1, 'p' => 1, 'l' => 0, 'jenis' => 'D'],
            ['kode' => 'ARS-703', 'nama' => 'Leadership & Pengambilan Keputusan', 'sks' => 2, 'tm' => 2, 'p' => 0, 'l' => 0, 'jenis' => 'B'],
            ['kode' => 'ARS-704', 'nama' => 'Digital Marketing Layanan Kesehatan', 'sks' => 3, 'tm' => 2, 'p' => 1, 'l' => 0, 'jenis' => 'C'], // Pilihan

            // --- SEMESTER 8 (Tugas Akhir) ---
            ['kode' => 'ARS-801', 'nama' => 'Skripsi / Tugas Akhir', 'sks' => 6, 'tm' => 0, 'p' => 0, 'l' => 6, 'jenis' => 'D'],
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

        $this->command->info("Berhasil menambahkan {$count} Master Mata Kuliah untuk Administrasi Rumah Sakit.");
    }
}