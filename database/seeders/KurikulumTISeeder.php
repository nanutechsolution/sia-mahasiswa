<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;

class KurikulumTISeeder extends Seeder
{
    /**
     * Seeder untuk Pemetaan Kurikulum TI 2023-2028 (MBKM)
     * Mengatur distribusi mata kuliah per semester dan prasyaratnya.
     */
    public function run(): void
    {
        $this->command->info('Memproses Pemetaan Kurikulum TI 2023-2028...');

        // 1. Ambil Prodi Teknik Informatika
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();

        if (!$prodi) {
            $this->command->error("Prodi TI tidak ditemukan. Jalankan MasterMatakuliahSeeder terlebih dahulu.");
            return;
        }

        // 2. Buat Header Kurikulum
        $kurikulum = Kurikulum::updateOrCreate(
            [
                'prodi_id' => $prodi->id,
                'nama_kurikulum' => 'Kurikulum MBKM TI 2023'
            ],
            [
                'tahun_mulai' => 2023,
                'id_semester_mulai' => '20231',
                'jumlah_sks_lulus' => 150,
                'is_active' => true,
            ]
        );

        // 3. Definisi Struktur Mata Kuliah per Semester
        // Format: [Kode MK, Semester Paket, Sifat (W=Wajib, P=Pilihan), Kode Prasyarat (Opsional)]
        $mappings = [
            // --- SEMESTER 1 ---
            ['kode' => 'TI-101', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-102', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-103', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-104', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-105', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-106', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-107', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI-108', 'smt' => 1, 'sifat' => 'W'],

            // --- SEMESTER 2 ---
            ['kode' => 'TI-201', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI-202', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI-203', 'smt' => 2, 'sifat' => 'W', 'pre' => 'TI-104'], // Kalkulus II butuh Kalkulus I
            ['kode' => 'TI-204', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI-205', 'smt' => 2, 'sifat' => 'W', 'pre' => 'TI-106'], // Alpro II butuh Alpro I
            ['kode' => 'TI-206', 'smt' => 2, 'sifat' => 'W', 'pre' => 'TI-106'], // Struktur Data butuh Alpro I
            ['kode' => 'TI-207', 'smt' => 2, 'sifat' => 'W'],

            // --- SEMESTER 3 ---
            ['kode' => 'TI-301', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-302', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-303', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-304', 'smt' => 3, 'sifat' => 'W', 'pre' => 'TI-206'], // Basis Data butuh Struktur Data
            ['kode' => 'TI-305', 'smt' => 3, 'sifat' => 'W', 'pre' => 'TI-205'], // PBO butuh Alpro II
            ['kode' => 'TI-306', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-307', 'smt' => 3, 'sifat' => 'W'],

            // --- SEMESTER 4 ---
            ['kode' => 'TI-401', 'smt' => 4, 'sifat' => 'W', 'pre' => 'TI-305'], // RPL butuh PBO
            ['kode' => 'TI-402', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-403', 'smt' => 4, 'sifat' => 'W', 'pre' => 'TI-206'], // Desain Algoritma butuh Struktur Data
            ['kode' => 'TI-404', 'smt' => 4, 'sifat' => 'W', 'pre' => 'TI-307'], // Web Lanjut butuh Web Dasar
            ['kode' => 'TI-405', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-406', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-407', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-408', 'smt' => 4, 'sifat' => 'W'],

            // --- SEMESTER 5 ---
            ['kode' => 'TI-501', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-502', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-503', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-504', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-505', 'smt' => 5, 'sifat' => 'P'], // Grafika (Pilihan)
            ['kode' => 'TI-506', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-507', 'smt' => 5, 'sifat' => 'P'], // Citra Digital (Pilihan)

            // --- SEMESTER 6 (MBKM) ---
            ['kode' => 'TI-601', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-602', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-603', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-604', 'smt' => 6, 'sifat' => 'P'],
            ['kode' => 'TI-605', 'smt' => 6, 'sifat' => 'P'],
            ['kode' => 'TI-606', 'smt' => 6, 'sifat' => 'P'],
            ['kode' => 'TI-607', 'smt' => 6, 'sifat' => 'P'],

            // --- SEMESTER 7 & 8 ---
            ['kode' => 'TI-701', 'smt' => 7, 'sifat' => 'W', 'pre' => 'TI-501'], // Sempro butuh Metopen
            ['kode' => 'TI-702', 'smt' => 7, 'sifat' => 'P'],
            ['kode' => 'TI-703', 'smt' => 7, 'sifat' => 'P'],
            ['kode' => 'TI-704', 'smt' => 7, 'sifat' => 'W'],
            ['kode' => 'TI-801', 'smt' => 8, 'sifat' => 'W', 'pre' => 'TI-701'], // Skripsi butuh Sempro
        ];

        // 4. Proses Sinkronisasi ke Database
        $totalWajib = 0;
        $totalPilihan = 0;

        foreach ($mappings as $map) {
            $mk = MataKuliah::where('kode_mk', $map['kode'])->where('prodi_id', $prodi->id)->first();
            
            if (!$mk) {
                $this->command->warn("Mata kuliah {$map['kode']} tidak ditemukan di master. Pastikan MasterMatakuliahSeeder sudah jalan.");
                continue;
            }

            // Cari ID Prasyarat jika didefinisikan
            $prasyaratId = null;
            if (isset($map['pre'])) {
                $preMk = MataKuliah::where('kode_mk', $map['pre'])->where('prodi_id', $prodi->id)->first();
                $prasyaratId = $preMk ? $preMk->id : null;
            }

            // Masukkan ke Tabel Pivot kurikulum_mata_kuliah
            DB::table('kurikulum_mata_kuliah')->updateOrInsert(
                [
                    'kurikulum_id' => $kurikulum->id,
                    'mata_kuliah_id' => $mk->id
                ],
                [
                    'semester_paket' => $map['smt'],
                    'sks_tatap_muka' => $mk->sks_tatap_muka,
                    'sks_praktek' => $mk->sks_praktek,
                    'sks_lapangan' => $mk->sks_lapangan,
                    'sifat_mk' => $map['sifat'], // W / P
                    'prasyarat_mk_id' => $prasyaratId,
                    'min_nilai_prasyarat' => 'D', // Default minimal D agar bisa ambil lanjutannya
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            // Akumulasi SKS untuk Header
            $sks = $mk->sks_default;
            if ($map['sifat'] == 'W') $totalWajib += $sks;
            else $totalPilihan += $sks;
        }

        // 5. Update Total SKS di Header Kurikulum
        $kurikulum->update([
            'jumlah_sks_wajib' => $totalWajib,
            'jumlah_sks_pilihan' => $totalPilihan
        ]);

        $this->command->info("Penyusunan Kurikulum TI Selesai. Total: {$totalWajib} SKS Wajib & {$totalPilihan} SKS Pilihan.");
    }
}