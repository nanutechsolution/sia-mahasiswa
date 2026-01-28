<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Setup Dosen & MK Basic
        $dosenId = Str::uuid();
        DB::table('dosens')->insert(['id' => $dosenId, 'nama_lengkap_gelar' => 'Dr. Code, M.Kom', 'created_at' => now()]);

        $prodiTi = DB::table('ref_prodi')->where('kode_prodi_internal', 'TI')->value('id');
        // $mkId = DB::table('master_mata_kuliahs')->insertGetId([
        //     'prodi_id' => $prodiTi,
        //     'kode_mk' => 'TI-101', 'nama_mk' => 'Algoritma Pemrograman', 'sks_default' => 3, 'created_at' => now()
        // ]);

        $mkId = DB::table('master_mata_kuliahs')->where('kode_mk', 'TI-101')->value('id');

        // Setup Jadwal
        $taId = DB::table('ref_tahun_akademik')->where('is_active', true)->value('id');
        $progReg = DB::table('ref_program_kelas')->where('kode_internal', 'REG')->value('id');
        $progEks = DB::table('ref_program_kelas')->where('kode_internal', 'EKS')->value('id');

        // 1. KELAS A (REGULER ONLY) - Senin Pagi
        DB::table('jadwal_kuliah')->insert([
            'id' => Str::uuid(),
            'tahun_akademik_id' => $taId,
            'mata_kuliah_id' => $mkId,
            'dosen_id' => $dosenId,
            'nama_kelas' => 'A-Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:30:00',
            'ruang' => 'R-101',
            'id_program_kelas_allow' => $progReg, // <--- KUNCI: HANYA REGULER
            'created_at' => now(),
        ]);

        // 2. KELAS B (EKSTENSI ONLY) - Senin Malam
        DB::table('jadwal_kuliah')->insert([
            'id' => Str::uuid(),
            'tahun_akademik_id' => $taId,
            'mata_kuliah_id' => $mkId,
            'dosen_id' => $dosenId,
            'nama_kelas' => 'B-Malam',
            'hari' => 'Senin',
            'jam_mulai' => '18:30:00',
            'jam_selesai' => '21:00:00',
            'ruang' => 'R-101',
            'id_program_kelas_allow' => $progEks, // <--- KUNCI: HANYA EKSTENSI
            'created_at' => now(),
        ]);
    }
}
