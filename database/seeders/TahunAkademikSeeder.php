<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Core\Models\TahunAkademik;
use Carbon\Carbon;

class TahunAkademikSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = 2017;
        $currentYear = date('Y');
        $endYear = $currentYear + 1; // Generate sampai tahun depan

        $this->command->info("Menjana Tahun Akademik dari {$startYear} hingga {$endYear}...");

        for ($year = $startYear; $year <= $endYear; $year++) {
            $nextYear = $year + 1;
            
            // 1. Semester Ganjil (Kode: YYYY1)
            // Cth: 20171 (Ganjil 2017/2018)
            // Periode: September - Januari
            TahunAkademik::updateOrCreate(
                ['kode_tahun' => $year . '1'],
                [
                    'nama_tahun' => "Ganjil {$year}/{$nextYear}",
                    'semester' => 1,
                    'tanggal_mulai' => "{$year}-09-01",
                    'tanggal_selesai' => "{$nextYear}-01-31",
                    'tgl_mulai_krs' => "{$year}-08-20",
                    'tgl_selesai_krs' => "{$year}-09-10",
                    'is_active' => false,
                    'buka_krs' => false,
                    'buka_input_nilai' => false,
                ]
            );

            // 2. Semester Genap (Kode: YYYY2)
            // Cth: 20172 (Genap 2017/2018)
            // Periode: Februari - Juli
            TahunAkademik::updateOrCreate(
                ['kode_tahun' => $year . '2'],
                [
                    'nama_tahun' => "Genap {$year}/{$nextYear}",
                    'semester' => 2,
                    'tanggal_mulai' => "{$nextYear}-02-01",
                    'tanggal_selesai' => "{$nextYear}-07-31",
                    'tgl_mulai_krs' => "{$nextYear}-01-20",
                    'tgl_selesai_krs' => "{$nextYear}-02-10",
                    'is_active' => false,
                    'buka_krs' => false,
                    'buka_input_nilai' => false,
                ]
            );

            // 3. Semester Pendek (Kode: YYYY3) - Opsional
            // Cth: 20173 (Pendek 2017/2018)
            // Periode: Agustus
            TahunAkademik::updateOrCreate(
                ['kode_tahun' => $year . '3'],
                [
                    'nama_tahun' => "Pendek {$year}/{$nextYear}",
                    'semester' => 3,
                    'tanggal_mulai' => "{$nextYear}-08-01",
                    'tanggal_selesai' => "{$nextYear}-08-31",
                    'tgl_mulai_krs' => "{$nextYear}-07-25",
                    'tgl_selesai_krs' => "{$nextYear}-08-05",
                    'is_active' => false,
                    'buka_krs' => false,
                    'buka_input_nilai' => false,
                ]
            );
        }

        // Set Tahun Akademik Aktif secara otomatis berdasarkan tanggal hari ini
        $today = Carbon::now();
        $activeTa = TahunAkademik::where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->orderBy('kode_tahun', 'desc') // Ambil yang paling relevan (misal Ganjil/Genap > Pendek jika overlap)
            ->first();

        if ($activeTa) {
            $activeTa->update([
                'is_active' => true,
                'buka_krs' => true // Default buka KRS jika semester aktif
            ]);
            $this->command->info("Semester Aktif diset ke: {$activeTa->nama_tahun} ({$activeTa->kode_tahun})");
        } else {
            // Fallback: Set semester terakhir sebagai aktif jika tidak ada tanggal yang cocok
            $lastTa = TahunAkademik::orderBy('kode_tahun', 'desc')->first();
            if ($lastTa) {
                $lastTa->update(['is_active' => true]);
                $this->command->info("Semester Aktif (Fallback) diset ke: {$lastTa->nama_tahun}");
            }
        }
    }
}