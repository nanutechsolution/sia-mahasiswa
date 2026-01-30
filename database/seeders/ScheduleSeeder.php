<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Person as ModelsPerson;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Referensi Prodi & Program Kelas
        $prodiTi = Prodi::where('kode_prodi_internal', 'TI')->first();
        if (!$prodiTi) return; // Safety check

        $progReg = ProgramKelas::where('kode_internal', 'REG')->first();
        $progEks = ProgramKelas::where('kode_internal', 'EKS')->first();
        $ta = DB::table('ref_tahun_akademik')->where('is_active', true)->first();

        // 2. Buat Data Dosen (SSOT: Person -> Dosen)
        // Cek atau buat Identitas Person
        $personDosen = ModelsPerson::firstOrCreate(
            ['nama_lengkap' => 'Dr. Code, M.Kom'],
            ['created_at' => now()]
        );

        // Buat data Akademik Dosen (trx_dosen)
        $dosen = Dosen::firstOrCreate(
            ['person_id' => $personDosen->id],
            [
                'prodi_id' => $prodiTi->id,
                'nidn' => '001002003',
                'is_active' => true,
                'created_at' => now()
            ]
        );
        
        // Hubungkan User 'dosen01' ke Person ini (Agar bisa login)
        $userDosen = User::where('username', 'dosen01')->first();
        if ($userDosen) {
            $userDosen->update(['person_id' => $personDosen->id]);
        }

        // 3. Setup Mata Kuliah
        // Ambil atau buat MK Algoritma
        $mkAlgo = MataKuliah::firstOrCreate(
            ['kode_mk' => 'TI-101', 'prodi_id' => $prodiTi->id],
            ['nama_mk' => 'Algoritma Pemrograman', 'sks_default' => 3, 'jenis_mk' => 'A']
        );

        // 4. Buat Jadwal Kuliah
        if ($ta) {
            // Jadwal A (Reguler)
            // Gunakan insertOrIgnore untuk mencegah duplikat UUID jika seeder dijalankan ulang
            if (!DB::table('jadwal_kuliah')->where('nama_kelas', 'A-Pagi')->exists()) {
                DB::table('jadwal_kuliah')->insert([
                    'id' => Str::uuid(),
                    'tahun_akademik_id' => $ta->id,
                    'mata_kuliah_id' => $mkAlgo->id,
                    'dosen_id' => $dosen->id,
                    'nama_kelas' => 'A-Pagi',
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:30:00',
                    'ruang' => 'R-101',
                    'kuota_kelas' => 40,
                    'id_program_kelas_allow' => $progReg?->id, // Khusus Reguler
                    'created_at' => now(),
                ]);
            }

            // Jadwal B (Ekstensi)
            if (!DB::table('jadwal_kuliah')->where('nama_kelas', 'B-Malam')->exists()) {
                DB::table('jadwal_kuliah')->insert([
                    'id' => Str::uuid(),
                    'tahun_akademik_id' => $ta->id,
                    'mata_kuliah_id' => $mkAlgo->id,
                    'dosen_id' => $dosen->id,
                    'nama_kelas' => 'B-Malam',
                    'hari' => 'Senin',
                    'jam_mulai' => '18:30:00',
                    'jam_selesai' => '21:00:00',
                    'ruang' => 'R-101',
                    'kuota_kelas' => 40,
                    'id_program_kelas_allow' => $progEks?->id, // Khusus Ekstensi
                    'created_at' => now(),
                ]);
            }
        }
    }
}