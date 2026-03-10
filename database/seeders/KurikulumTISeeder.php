<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use App\Models\KurikulumMataKuliah; // Gunakan model pivot

class KurikulumTISeeder extends Seeder
{
    /**
     * Seeder untuk Pemetaan Kurikulum TI 2023-2028 (MBKM)
     * Diperbarui untuk mendukung Arsitektur Many-to-Many Prasyarat.
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

        // 2. Buat/Update Header Kurikulum
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
            ['kode' => 'TI-203', 'smt' => 2, 'sifat' => 'W', 'pre' => ['TI-104']], // Kalkulus II
            ['kode' => 'TI-204', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI-205', 'smt' => 2, 'sifat' => 'W', 'pre' => ['TI-106']], // Alpro II
            ['kode' => 'TI-206', 'smt' => 2, 'sifat' => 'W', 'pre' => ['TI-106']], // Struktur Data
            ['kode' => 'TI-207', 'smt' => 2, 'sifat' => 'W'],

            // --- SEMESTER 3 ---
            ['kode' => 'TI-301', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-302', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-303', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-304', 'smt' => 3, 'sifat' => 'W', 'pre' => ['TI-206']], // Basis Data
            ['kode' => 'TI-305', 'smt' => 3, 'sifat' => 'W', 'pre' => ['TI-205']], // PBO
            ['kode' => 'TI-306', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI-307', 'smt' => 3, 'sifat' => 'W'],

            // --- SEMESTER 4 ---
            ['kode' => 'TI-401', 'smt' => 4, 'sifat' => 'W', 'pre' => ['TI-305']], // RPL
            ['kode' => 'TI-402', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-403', 'smt' => 4, 'sifat' => 'W', 'pre' => ['TI-206']], // Desain Algoritma
            ['kode' => 'TI-404', 'smt' => 4, 'sifat' => 'W', 'pre' => ['TI-307']], // Web Lanjut
            ['kode' => 'TI-405', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-406', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-407', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI-408', 'smt' => 4, 'sifat' => 'W'],

            // --- SEMESTER 5 ---
            ['kode' => 'TI-501', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-502', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-503', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-504', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-505', 'smt' => 5, 'sifat' => 'P'], 
            ['kode' => 'TI-506', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI-507', 'smt' => 5, 'sifat' => 'P'],

            // --- SEMESTER 6 ---
            ['kode' => 'TI-601', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-602', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-603', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI-604', 'smt' => 6, 'sifat' => 'P'],

            // --- SEMESTER 7 & 8 ---
            ['kode' => 'TI-701', 'smt' => 7, 'sifat' => 'W', 'pre' => ['TI-501']], // Sempro
            ['kode' => 'TI-801', 'smt' => 8, 'sifat' => 'W', 'pre' => ['TI-701']], // Skripsi
        ];

        // 4. Proses Sinkronisasi
        $totalWajib = 0;
        $totalPilihan = 0;

        foreach ($mappings as $map) {
            $mk = MataKuliah::where('kode_mk', $map['kode'])->where('prodi_id', $prodi->id)->first();
            
            if (!$mk) {
                $this->command->warn("Mata kuliah {$map['kode']} tidak ditemukan.");
                continue;
            }

            // A. Simpan ke Tabel Kurikulum Mata Kuliah (Tanpa kolom prasyarat yang sudah dihapus)
            $kurMk = KurikulumMataKuliah::updateOrCreate(
                [
                    'kurikulum_id' => $kurikulum->id,
                    'mata_kuliah_id' => $mk->id
                ],
                [
                    'semester_paket' => $map['smt'],
                    'sks_tatap_muka' => $mk->sks_tatap_muka,
                    'sks_praktek'    => $mk->sks_praktek,
                    'sks_lapangan'   => $mk->sks_lapangan,
                    'sifat_mk'       => $map['sifat'],
                ]
            );

            // B. Sinkronisasi Prasyarat (Many-to-Many)
            if (isset($map['pre']) && !empty($map['pre'])) {
                $syncData = [];
                foreach ($map['pre'] as $preKode) {
                    $preMk = MataKuliah::where('kode_mk', $preKode)->where('prodi_id', $prodi->id)->first();
                    if ($preMk) {
                        $syncData[$preMk->id] = ['min_nilai_huruf' => 'D'];
                    }
                }
                // Update tabel pivot kurikulum_mk_prasyarat
                $kurMk->prasyarats()->sync($syncData);
            }

            // Akumulasi SKS
            $sks = $mk->sks_default;
            if ($map['sifat'] == 'W') $totalWajib += $sks;
            else $totalPilihan += $sks;
        }

        // 5. Update Header
        $kurikulum->update([
            'jumlah_sks_wajib' => $totalWajib,
            'jumlah_sks_pilihan' => $totalPilihan
        ]);

        $this->command->info("Penyusunan Kurikulum TI Selesai. Total: {$totalWajib} SKS Wajib & {$totalPilihan} SKS Pilihan.");
    }
}