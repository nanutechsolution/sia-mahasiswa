<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SiakadSpmisIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Tahun Akademik Aktif (Cek jika sudah ada)
        $tahun = DB::table('ref_tahun_akademik')->where('kode_tahun', '20251')->first();
        if (!$tahun) {
            $tahunId = DB::table('ref_tahun_akademik')->insertGetId([
                'kode_tahun' => '20251',
                'nama_tahun' => '2025/2026 Ganjil',
                'semester' => 1,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        } else {
            $tahunId = $tahun->id;
        }

        // 2. DATA REFERENSI (Prasyarat untuk tabel Mahasiswa & Dosen)
        
        // Cek/Buat Angkatan 2024
        if (!DB::table('ref_angkatan')->where('id_tahun', 2024)->exists()) {
            DB::table('ref_angkatan')->insert([
                'id_tahun' => 2024,
                'is_active_pmb' => 1,
                'created_at' => now(),
            ]);
        }

        // Cek/Buat Fakultas (ID: 1)
        if (!DB::table('ref_fakultas')->where('id', 1)->exists()) {
            DB::table('ref_fakultas')->insert([
                'id' => 1,
                'kode_fakultas' => 'FTI',
                'nama_fakultas' => 'Fakultas Teknologi Informasi',
                'created_at' => now(),
            ]);
        }

        // Cek/Buat Prodi (ID: 1)
        if (!DB::table('ref_prodi')->where('id', 1)->exists()) {
            DB::table('ref_prodi')->insert([
                'id' => 1,
                'fakultas_id' => 1,
                'kode_prodi_internal' => 'INF',
                'nama_prodi' => 'Informatika',
                'jenjang' => 'S1',
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }

        // Cek/Buat Program Kelas (ID: 1)
        if (!DB::table('ref_program_kelas')->where('id', 1)->exists()) {
            DB::table('ref_program_kelas')->insert([
                'id' => 1,
                'nama_program' => 'Reguler A',
                'kode_internal' => 'REG-A',
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }

        // 3. Buat Data Person untuk Mahasiswa (Cek berdasarkan NIK)
        $nikMhs = '1234567890123456';
        $personMhs = DB::table('ref_person')->where('nik', $nikMhs)->first();
        if (!$personMhs) {
            $personMhsId = DB::table('ref_person')->insertGetId([
                'nama_lengkap' => 'Mahasiswa Tester',
                'nik' => $nikMhs,
                'email' => 'mhs@unmaris.ac.id',
                'created_at' => now(),
            ]);
        } else {
            $personMhsId = $personMhs->id;
        }

        // Data Person untuk Dosen
        $nikDosen = '9876543210987654';
        $personDosen = DB::table('ref_person')->where('nik', $nikDosen)->first();
        if (!$personDosen) {
            $personDosenId = DB::table('ref_person')->insertGetId([
                'nama_lengkap' => 'Dosen Tester, S.Kom., M.T.',
                'nik' => $nikDosen,
                'email' => 'dosen@unmaris.ac.id',
                'created_at' => now(),
            ]);
        } else {
            $personDosenId = $personDosen->id;
        }

        // 4. Buat User untuk Login (NIM: 2024001) - Cek berdasarkan username
        $usernameMhs = '2024001';
        $userMhs = DB::table('users')->where('username', $usernameMhs)->first();
        if (!$userMhs) {
            $mhsUuid = (string) Str::uuid();
            DB::table('users')->insert([
                'id' => $mhsUuid,
                'person_id' => $personMhsId,
                'name' => 'Mahasiswa Tester',
                'username' => $usernameMhs,
                'email' => 'mhs@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'is_active' => 1,
                'created_at' => now(),
            ]);
        } else {
            $mhsUuid = $userMhs->id;
        }

        // 5. Daftarkan di tabel mahasiswas (Cek NIM)
        if (!DB::table('mahasiswas')->where('nim', $usernameMhs)->exists()) {
            DB::table('mahasiswas')->insert([
                'id' => $mhsUuid,
                'person_id' => $personMhsId,
                'nim' => $usernameMhs,
                'angkatan_id' => 2024,
                'prodi_id' => 1, 
                'program_kelas_id' => 1,
                'created_at' => now(),
            ]);
        }

        // 6. Buat Data Dosen (Cek NIDN)
        $nidnDosen = '0808080801';
        $dosen = DB::table('trx_dosen')->where('nidn', $nidnDosen)->first();
        if (!$dosen) {
            $dosenUuid = (string) Str::uuid();
            DB::table('trx_dosen')->insert([
                'id' => $dosenUuid,
                'person_id' => $personDosenId,
                'prodi_id' => 1,
                'nidn' => $nidnDosen,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        } else {
            $dosenUuid = $dosen->id;
        }

        // 7. Buat Mata Kuliah (Cek Kode MK)
        $kodeMk = 'MK001';
        $mk = DB::table('master_mata_kuliahs')->where('kode_mk', $kodeMk)->first();
        if (!$mk) {
            $mkId = DB::table('master_mata_kuliahs')->insertGetId([
                'prodi_id' => 1,
                'kode_mk' => $kodeMk,
                'nama_mk' => 'Pemrograman Web Integrasi',
                'sks_default' => 3,
                'created_at' => now(),
            ]);
        } else {
            $mkId = $mk->id;
        }

        // 8. Buat Jadwal Kuliah & Plot Dosen
        $jadwal = DB::table('jadwal_kuliah')
            ->where('tahun_akademik_id', $tahunId)
            ->where('mata_kuliah_id', $mkId)
            ->first();

        if (!$jadwal) {
            $jadwalUuid = (string) Str::uuid();
            DB::table('jadwal_kuliah')->insert([
                'id' => $jadwalUuid,
                'tahun_akademik_id' => $tahunId,
                'mata_kuliah_id' => $mkId,
                'nama_kelas' => 'A',
                'kuota_kelas' => 40,
                'created_at' => now(),
            ]);

            DB::table('jadwal_kuliah_dosen')->insert([
                'jadwal_kuliah_id' => $jadwalUuid,
                'dosen_id' => $dosenUuid,
                'is_koordinator' => 1,
                'created_at' => now(),
            ]);
        } else {
            $jadwalUuid = $jadwal->id;
        }

        // 9. Buat Header KRS (Cek Mahasiswa & Tahun)
        $krs = DB::table('krs')
            ->where('mahasiswa_id', $mhsUuid)
            ->where('tahun_akademik_id', $tahunId)
            ->first();

        if (!$krs) {
            $krsUuid = (string) Str::uuid();
            DB::table('krs')->insert([
                'id' => $krsUuid,
                'mahasiswa_id' => $mhsUuid,
                'tahun_akademik_id' => $tahunId,
                'status_krs' => 'VALID',
                'created_at' => now(),
            ]);
        } else {
            $krsUuid = $krs->id;
        }

        // 10. Isi Detail KRS (Cek jika sudah ada detail MK tersebut di KRS)
        if (!DB::table('krs_detail')->where('krs_id', $krsUuid)->where('mata_kuliah_id', $mkId)->exists()) {
            DB::table('krs_detail')->insert([
                'krs_id' => $krsUuid,
                'jadwal_kuliah_id' => $jadwalUuid,
                'mata_kuliah_id' => $mkId,
                'created_at' => now(),
            ]);
        }
    }
}