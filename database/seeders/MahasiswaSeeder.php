<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person as ModelsPerson;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Referensi Data
        $prodiTi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $progReg = ProgramKelas::where('kode_internal', 'REG')->first();
        $progEks = ProgramKelas::where('kode_internal', 'EKS')->first();
        $dosenWali = Dosen::first(); 

        if (!$prodiTi || !$progReg) {
            $this->command->error('Data Prodi atau Program Kelas belum ada. Jalankan ReferenceSeeder dulu.');
            return;
        }

        // === MAHASISWA 1: REGULER (Si Budi) ===
        $this->seedMahasiswa(
            '2401001', 
            'Budi Santoso (Reguler)', 
            'budi@mhs.unmaris.ac.id',
            $prodiTi, 
            $progReg, 
            $dosenWali
        );

        // === MAHASISWA 2: EKSTENSI (Si Ani) ===
        $this->seedMahasiswa(
            '2401002', 
            'Ani Wijaya (Ekstensi)', 
            'ani@mhs.unmaris.ac.id',
            $prodiTi, 
            $progEks, 
            $dosenWali
        );
    }

    private function seedMahasiswa($nim, $nama, $email, $prodi, $programKelas, $dosenWali)
    {
        // A. Buat/Cari Identitas Person (Pusat Data)
        $person = ModelsPerson::firstOrCreate(
            ['nama_lengkap' => $nama], // Cari berdasarkan nama
            [
                'email' => $email,
                'jenis_kelamin' => 'L', // Default dummy
                'created_at' => now(),
            ]
        );

        // B. Hubungkan User Login ke Person (Update kolom person_id di users)
        $user = User::where('username', $nim)->first();
        if ($user) {
            $user->update(['person_id' => $person->id]);
        }

        // C. Buat Data Akademik Mahasiswa (Link ke Person, HANYA DATA AKADEMIK)
        // Perhatikan: Kita TIDAK memasukkan 'nama_lengkap', 'email_pribadi' dll 
        // ke tabel mahasiswas karena kolom tersebut sudah dihapus (SSOT).
        $mhs = Mahasiswa::firstOrCreate(
            ['nim' => $nim],
            [
                'person_id' => $person->id, // Link ke identitas
                'angkatan_id' => 2024,
                'prodi_id' => $prodi->id,
                'program_kelas_id' => $programKelas->id,
                'dosen_wali_id' => $dosenWali?->id,
                // 'data_tambahan' bisa diisi jika perlu
                'created_at' => now(),
            ]
        );

        // D. Setup Riwayat Status Aktif untuk Semester Ini
        $ta = DB::table('ref_tahun_akademik')->where('is_active', true)->first();
        if ($ta) {
            DB::table('riwayat_status_mahasiswas')->updateOrInsert(
                ['mahasiswa_id' => $mhs->id, 'tahun_akademik_id' => $ta->id],
                [
                    'status_kuliah' => 'A', // Aktif
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}