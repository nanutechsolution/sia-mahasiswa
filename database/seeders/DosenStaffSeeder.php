<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use App\Domains\Core\Models\Prodi;

class DosenStaffSeeder extends Seeder
{
    public function run(): void
    {
        $prodiTi = Prodi::where('kode_prodi_internal', 'TI')->first();

        // === 1. SUPER ADMIN (Tanpa Personil Akademik) ===
        $personAdmin = Person::firstOrCreate(['nama_lengkap' => 'Administrator Sistem'], ['email' => 'admin@unmaris.ac.id']);
        $this->createUser($personAdmin, 'superadmin', 'superadmin');

        // === 2. DOSEN 1 (Kaprodi) ===
        $personDosen1 = Person::firstOrCreate(['nama_lengkap' => 'Budi Santoso'], ['nik' => '1234567890', 'jenis_kelamin' => 'L']);
        $this->assignGelar($personDosen1, ['S.Kom', 'M.Kom']);

        // Buat Data Dosen
        $dosen1 = Dosen::firstOrCreate(['nidn' => '0412018801'], [
            'person_id' => $personDosen1->id,
            'prodi_id' => $prodiTi->id,
            'is_active' => true
        ]);

        $this->createUser($personDosen1, 'dosen01', 'dosen'); // Login Dosen

        // Assign Jabatan Kaprodi
        $jabKaprodi = DB::table('ref_jabatan')->where('kode_jabatan', 'KAPRODI')->first();
        DB::table('trx_person_jabatan')->insert([
            'person_id' => $personDosen1->id,
            'jabatan_id' => $jabKaprodi->id,
            'prodi_id' => $prodiTi->id,
            'tanggal_mulai' => '2024-01-01',
            'created_at' => now()
        ]);

        // === 3. DOSEN 2 (Dosen Biasa) ===
        $personDosen2 = Person::firstOrCreate(['nama_lengkap' => 'Siti Aminah'], ['jenis_kelamin' => 'P']);
        $this->assignGelar($personDosen2, ['S.T', 'M.T']);

        Dosen::firstOrCreate(['nidn' => '0412019902'], [
            'person_id' => $personDosen2->id,
            'prodi_id' => $prodiTi->id,
            'is_active' => true
        ]);
        $this->createUser($personDosen2, 'dosen02', 'dosen');

        // === 4. STAFF KEUANGAN ===
        $personKeu = Person::firstOrCreate(['nama_lengkap' => 'Staff Keuangan'], ['email' => 'keuangan@unmaris.ac.id']);
        $this->createUser($personKeu, 'akses_modul_keuangan', 'bauk');

        // === 5. STAFF BAAK ===
        $personBaak = Person::firstOrCreate(['nama_lengkap' => 'Kepala BAAK'], ['email' => 'baak@unmaris.ac.id']);
        $this->createUser($personBaak, 'akses_modul_akademik', 'bara');

        // Assign Jabatan KaBAAK
        $jabBaak = DB::table('ref_jabatan')->where('kode_jabatan', 'KABAAK')->first();
        DB::table('trx_person_jabatan')->insert([
            'person_id' => $personBaak->id,
            'jabatan_id' => $jabBaak->id,
            'tanggal_mulai' => '2024-01-01',
            'created_at' => now()
        ]);
    }

    private function createUser($person, $username, $role)
    {
        $user = User::firstOrCreate(['username' => $username], [
            'name' => $person->nama_lengkap,
            'email' => $person->email ?? $username . '@sys.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
            'person_id' => $person->id
        ]);
        $user->assignRole($role);
    }

    private function assignGelar($person, $kodeGelars)
    {
        foreach ($kodeGelars as $index => $kode) {
            $gelar = DB::table('ref_gelar')->where('kode', $kode)->first();
            if ($gelar) {
                DB::table('trx_person_gelar')->updateOrInsert(
                    ['person_id' => $person->id, 'gelar_id' => $gelar->id],
                    ['urutan' => $index + 1]
                );
            }
        }
    }
}
