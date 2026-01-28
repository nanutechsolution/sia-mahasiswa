<?php


namespace Database\Seeders;

use App\Domains\Core\Models\ProgramKelas; 
use App\Domains\Core\Models\TahunAkademik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Program Kelas (KUNCI UTAMA)
        $reg = DB::table('ref_program_kelas')->insertGetId([
            'nama_program' => 'Reguler Pagi',
            'kode_internal' => 'REG',
            'is_active' => true,
            'created_at' => now(),
        ]);

        $eks = DB::table('ref_program_kelas')->insertGetId([
            'nama_program' => 'Ekstensi Malam',
            'kode_internal' => 'EKS',
            'is_active' => true,
            'created_at' => now(),
        ]);

        // 2. Angkatan & Tahun Akademik
        DB::table('ref_angkatan')->insert([
            ['id_tahun' => 2024, 'batas_tahun_studi' => 2031, 'is_active_pmb' => false],
            ['id_tahun' => 2025, 'batas_tahun_studi' => 2032, 'is_active_pmb' => true],
        ]);

        DB::table('ref_tahun_akademik')->insert([
            'kode_tahun' => '20241',
            'nama_tahun' => 'Ganjil 2024/2025',
            'semester' => 1,
            'is_active' => true, // Semester Aktif Sekarang
            'buka_krs' => true,
            'created_at' => now(),
        ]);

        // 3. Fakultas & Prodi
        $ft = DB::table('ref_fakultas')->insertGetId([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'created_at' => now(),
        ]);

        DB::table('ref_prodi')->insert([
            'fakultas_id' => $ft,
            'kode_prodi_internal' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'is_active' => true,
            'created_at' => now(),
        ]);
    }
}