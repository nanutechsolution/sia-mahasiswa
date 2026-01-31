<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;

class KurikulumMappingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Referensi Prodi
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();
        if (!$prodi) {
            $this->command->error('Prodi TI tidak ditemukan. Pastikan FakultasProdiSeeder sudah dijalankan.');
            return;
        }

        // 2. Buat Header Kurikulum Utama
        $kurikulum = Kurikulum::updateOrCreate(
            ['prodi_id' => $prodi->id, 'nama_kurikulum' => 'Kurikulum TI 2024 - Merdeka Belajar'],
            [
                'tahun_mulai' => 2024,
                'id_semester_mulai' => '20241',
                'is_active' => true,
                'jumlah_sks_lulus' => 144,
            ]
        );

        // 3. Daftar Mapping Mata Kuliah ke Kurikulum (Smt 1-8)
        $mappings = [
            // SEMESTER 1
            ['kode' => 'TI101', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI102', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI103', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI104', 'smt' => 1, 'sifat' => 'W'],
            ['kode' => 'TI105', 'smt' => 1, 'sifat' => 'W'],

            // SEMESTER 2
            ['kode' => 'TI201', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI202', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI203', 'smt' => 2, 'sifat' => 'W'],
            ['kode' => 'TI204', 'smt' => 2, 'sifat' => 'W', 'syarat' => 'TI105'], // Struktur Data butuh Algo I
            ['kode' => 'TI205', 'smt' => 2, 'sifat' => 'W'],

            // SEMESTER 3
            ['kode' => 'TI301', 'smt' => 3, 'sifat' => 'W', 'syarat' => 'TI204'],
            ['kode' => 'TI302', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI303', 'smt' => 3, 'sifat' => 'W'],
            ['kode' => 'TI304', 'smt' => 3, 'sifat' => 'W'],

            // SEMESTER 4
            ['kode' => 'TI401', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI402', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI403', 'smt' => 4, 'sifat' => 'W'],
            ['kode' => 'TI404', 'smt' => 4, 'sifat' => 'W'],

            // SEMESTER 5
            ['kode' => 'TI501', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI502', 'smt' => 5, 'sifat' => 'W'],
            ['kode' => 'TI503', 'smt' => 5, 'sifat' => 'P'], // Pilihan
            ['kode' => 'TI504', 'smt' => 5, 'sifat' => 'W'],

            // SEMESTER 6
            ['kode' => 'TI601', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI602', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI603', 'smt' => 6, 'sifat' => 'W'],
            ['kode' => 'TI604', 'smt' => 6, 'sifat' => 'W'],

            // SEMESTER 7
            ['kode' => 'TI701', 'smt' => 7, 'sifat' => 'W'],
            ['kode' => 'TI702', 'smt' => 7, 'sifat' => 'W'],
            ['kode' => 'TI703', 'smt' => 7, 'sifat' => 'W'],
            ['kode' => 'TI704', 'smt' => 7, 'sifat' => 'W'],

            // SEMESTER 8
            ['kode' => 'TI801', 'smt' => 8, 'sifat' => 'W', 'syarat' => 'TI701'], // Skripsi butuh Metopen
        ];

        $this->command->info('Memetakan mata kuliah ke kurikulum...');

        foreach ($mappings as $map) {
            $mk = MataKuliah::where('kode_mk', $map['kode'])->where('prodi_id', $prodi->id)->first();

            if (!$mk) {
                $this->command->warn("Skip: Kode MK {$map['kode']} tidak ditemukan di master.");
                continue;
            }

            // Cari ID Prasyarat jika ada
            $prasyaratId = null;
            if (isset($map['syarat'])) {
                $preMk = MataKuliah::where('kode_mk', $map['syarat'])->first();
                $prasyaratId = $preMk ? $preMk->id : null;
            }

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
                    'sifat_mk' => $map['sifat'],
                    'prasyarat_mk_id' => $prasyaratId,
                    'min_nilai_prasyarat' => 'D',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        // 4. Hitung ulang akumulasi SKS di Header Kurikulum
        $stats = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $kurikulum->id)
            ->selectRaw("
                SUM(CASE WHEN sifat_mk = 'W' THEN (sks_tatap_muka + sks_praktek + sks_lapangan) ELSE 0 END) as wajib,
                SUM(CASE WHEN sifat_mk = 'P' THEN (sks_tatap_muka + sks_praktek + sks_lapangan) ELSE 0 END) as pilihan
            ")->first();

        $kurikulum->update([
            'jumlah_sks_wajib' => $stats->wajib ?? 0,
            'jumlah_sks_pilihan' => $stats->pilihan ?? 0
        ]);

        $this->command->info('Mapping selesai. Total ' . count($mappings) . ' MK telah dimasukkan.');
    }
}
