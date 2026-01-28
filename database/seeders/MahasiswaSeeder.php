<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Akademik\Models\Dosen;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Referensi Data
        $prodiTi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $progReg = ProgramKelas::where('kode_internal', 'REG')->first();
        $progEks = ProgramKelas::where('kode_internal', 'EKS')->first();
        $dosenWali = Dosen::first(); 

        if (!$prodiTi || !$progReg) {
            // Safety check jika master data belum ada
            return;
        }

        // === MAHASISWA 1: REGULER (Si Budi) ===
        // Cari User Budi yang dibuat UserSeeder
        $userBudi = User::where('username', '2401001')->first();
        
        if ($userBudi) {
            Mahasiswa::firstOrCreate(
                ['nim' => '2401001'], // Kunci pencarian
                [
                    'user_id' => $userBudi->id,
                    'nama_lengkap' => $userBudi->name,
                    'angkatan_id' => 2024,
                    'prodi_id' => $prodiTi->id,
                    'program_kelas_id' => $progReg->id,
                    'dosen_wali_id' => $dosenWali?->id,
                    'created_at' => now(),
                ]
            );
        }

        // === MAHASISWA 2: EKSTENSI (Si Ani) ===
        $userAni = User::where('username', '2401002')->first();
        
        if ($userAni) {
            Mahasiswa::firstOrCreate(
                ['nim' => '2401002'],
                [
                    'user_id' => $userAni->id,
                    'nama_lengkap' => $userAni->name,
                    'angkatan_id' => 2024,
                    'prodi_id' => $prodiTi->id,
                    'program_kelas_id' => $progEks->id,
                    'dosen_wali_id' => $dosenWali?->id,
                    'created_at' => now(),
                ]
            );
        }

        // Setup Riwayat Status Aktif untuk Semester Ini
        $ta = DB::table('ref_tahun_akademik')->where('is_active', true)->first();
        if ($ta) {
            $mhsBudi = Mahasiswa::where('nim', '2401001')->first();
            if ($mhsBudi) {
                DB::table('riwayat_status_mahasiswas')->updateOrInsert(
                    ['mahasiswa_id' => $mhsBudi->id, 'tahun_akademik_id' => $ta->id],
                    ['status_kuliah' => 'A', 'created_at' => now()]
                );
            }
            
            $mhsAni = Mahasiswa::where('nim', '2401002')->first();
            if ($mhsAni) {
                DB::table('riwayat_status_mahasiswas')->updateOrInsert(
                    ['mahasiswa_id' => $mhsAni->id, 'tahun_akademik_id' => $ta->id],
                    ['status_kuliah' => 'A', 'created_at' => now()]
                );
            }
        }
    }
}