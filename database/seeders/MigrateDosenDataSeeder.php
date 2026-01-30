<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateDosenDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data dari tabel lama 'dosens'
        // Kita menggunakan query builder raw agar tidak tergantung pada Model yang mungkin sudah berubah
        $oldDosens = DB::table('dosens')->get();

        if ($oldDosens->isEmpty()) {
            $this->command->info('Tidak ada data dosen lama untuk dimigrasi.');
            return;
        }

        $this->command->info('Memulai migrasi ' . $oldDosens->count() . ' data dosen...');

        DB::transaction(function () use ($oldDosens) {
            foreach ($oldDosens as $old) {
                // 2. Buat Data Personil Baru (ref_person)
                // Ini menjadi pusat biodata
                $personId = DB::table('ref_person')->insertGetId([
                    'nama_lengkap' => $old->nama_lengkap_gelar, // Pindahkan nama
                    'nik' => null, // Data lama belum punya NIK, biarkan null
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 3. Masukkan ke Struktur Baru (trx_dosen)
                // CRITICAL: Kita paksa pakai ID lama ($old->id) agar relasi ke Jadwal/KRS/PA tidak putus!
                DB::table('trx_dosen')->updateOrInsert(
                    ['id' => $old->id], // Cek berdasarkan UUID lama
                    [
                        'person_id' => $personId,
                        'prodi_id' => $old->homebase_prodi_id ?? 1, // Fallback ke ID 1 jika null
                        'nidn' => $old->nidn,
                        'is_active' => $old->is_active,
                        'created_at' => $old->created_at,
                        'updated_at' => $old->updated_at,
                    ]
                );
            }
        });
        
        $this->command->info('Migrasi data dosen selesai! Relasi ID lama berhasil dipertahankan.');
    }
}