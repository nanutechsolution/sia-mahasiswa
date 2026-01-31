<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Core\Models\Prodi;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\Kurikulum;

class KurikulumAutoSeeder extends Seeder
{
    /**
     * Otomatis membuat kurikulum dan memetakan semua MK yang ada di database.
     */
    public function run(): void
    {
        $this->command->info('Memulai Auto-Generate Kurikulum dari Master Mata Kuliah...');

        // 1. Ambil semua prodi
        $prodis = Prodi::all();

        foreach ($prodis as $prodi) {
            
            // 2. Ambil semua MK milik prodi ini
            $mks = MataKuliah::where('prodi_id', $prodi->id)->get();

            if ($mks->isEmpty()) {
                $this->command->warn("Skip: Prodi {$prodi->nama_prodi} tidak memiliki mata kuliah master.");
                continue;
            }

            $this->command->info("Memproses Kurikulum: {$prodi->nama_prodi}...");

            // 3. Buat Header Kurikulum
            $kurikulum = Kurikulum::updateOrCreate(
                [
                    'prodi_id' => $prodi->id,
                    'nama_kurikulum' => "Kurikulum {$prodi->kode_prodi_internal} 2025 (Otomatis)",
                ],
                [
                    'tahun_mulai' => 2025,
                    'id_semester_mulai' => '20251',
                    'is_active' => true,
                    'jumlah_sks_lulus' => 144, // Default S1
                ]
            );

            $totalWajib = 0;
            $totalPilihan = 0;
            $countMk = 0;

            // 4. Masukkan Setiap MK ke Kurikulum
            foreach ($mks as $mk) {
                // --- LOGIKA CERDAS: TEBAK SEMESTER DARI KODE ---
                // Contoh: TI101 -> 1, T1203 -> 2, MK801 -> 8
                // Mengambil digit pertama yang ditemukan dalam string kode
                preg_match('/\d/', $mk->kode_mk, $matches);
                $semesterPrediksi = isset($matches[0]) ? (int)$matches[0] : 1;
                
                // Normalisasi: Jika 0 ubah ke 1, jika > 8 biarkan (mungkin semester 9)
                if ($semesterPrediksi == 0) $semesterPrediksi = 1;

                // --- LOGIKA CERDAS: SIFAT MK ---
                // A/B/D = Wajib, C = Pilihan
                $sifat = ($mk->jenis_mk == 'C') ? 'P' : 'W';

                DB::table('kurikulum_mata_kuliah')->updateOrInsert(
                    [
                        'kurikulum_id' => $kurikulum->id,
                        'mata_kuliah_id' => $mk->id
                    ],
                    [
                        'semester_paket' => $semesterPrediksi,
                        'sks_tatap_muka' => $mk->sks_tatap_muka,
                        'sks_praktek' => $mk->sks_praktek,
                        'sks_lapangan' => $mk->sks_lapangan,
                        'sifat_mk' => $sifat,
                        'min_nilai_prasyarat' => 'D',
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );

                // Hitung total SKS
                if ($sifat == 'W') {
                    $totalWajib += $mk->sks_default;
                } else {
                    $totalPilihan += $mk->sks_default;
                }
                $countMk++;
            }

            // 5. Update Total SKS di Header
            $kurikulum->update([
                'jumlah_sks_wajib' => $totalWajib,
                'jumlah_sks_pilihan' => $totalPilihan
            ]);

            $this->command->info("  -> Selesai. Total {$countMk} MK (Wajib: {$totalWajib} SKS, Pilihan: {$totalPilihan} SKS)");
        }
    }
}