<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\TahunAkademik;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class JadwalKuliahSeeder extends Seeder
{
    public function run()
    {
        $tahunId = SistemHelper::idTahunAktif();
        $kurikulum = Kurikulum::where('is_active', true)->first();
        $dosenIds = Dosen::pluck('id')->toArray();

        if (!$kurikulum || empty($dosenIds)) {
            $this->command->error("Pastikan data Kurikulum Aktif dan Dosen sudah tersedia!");
            return;
        }

        // Ambil MK yang terdaftar di kurikulum tersebut
        $mks = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $kurikulum->id)
            ->limit(10) // Ambil 10 MK saja sebagai sampel
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $ruangList = ['R.101', 'R.102', 'R.201', 'LAB-A', 'LAB-B'];
        
        // Slot Waktu (Agar tidak bentrok, kita tentukan slot statis)
        $slotWaktu = [
            ['07:30', '10:00'],
            ['10:15', '12:45'],
            ['13:00', '15:30'],
            ['15:45', '18:15'],
        ];

        $count = 0;
        foreach ($mks as $mk) {
            foreach (['A', 'B'] as $kelas) {
                // Cari kombinasi hari, ruang, dan jam yang belum dipakai
                $isCreated = false;
                
                // Shuffle agar sebaran acak
                shuffle($hariList);
                shuffle($ruangList);
                shuffle($slotWaktu);

                foreach ($hariList as $hari) {
                    foreach ($slotWaktu as $waktu) {
                        foreach ($ruangList as $ruang) {
                            $dosenId = $dosenIds[array_rand($dosenIds)];

                            // Cek bentrok di database (Ruang atau Dosen pada waktu yang sama)
                            $bentrok = JadwalKuliah::where('tahun_akademik_id', $tahunId)
                                ->where('hari', $hari)
                                ->where('jam_mulai', $waktu[0])
                                ->where(function($q) use ($ruang, $dosenId) {
                                    $q->where('ruang', $ruang)
                                      ->orWhere('dosen_id', $dosenId);
                                })
                                ->exists();

                            if (!$bentrok) {
                                JadwalKuliah::create([
                                    'tahun_akademik_id' => $tahunId,
                                    'kurikulum_id'      => $kurikulum->id,
                                    'mata_kuliah_id'    => $mk->mata_kuliah_id,
                                    'dosen_id'          => $dosenId,
                                    'nama_kelas'        => $kelas,
                                    'hari'              => $hari,
                                    'jam_mulai'         => $waktu[0],
                                    'jam_selesai'       => $waktu[1],
                                    'ruang'             => $ruang,
                                    'kuota_kelas'       => 40,
                                ]);
                                
                                $isCreated = true;
                                $count++;
                                break 3; // Keluar dari loop ruang, waktu, dan hari
                            }
                        }
                    }
                }
            }
        }

        $this->command->info("Berhasil membuat {$count} jadwal kuliah tanpa bentrok.");
    }
}