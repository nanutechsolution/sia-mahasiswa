<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Actions\GenerateTagihanMassalAction;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\Prodi;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use App\Domains\Core\Models\ProgramKelas;

class MahasiswaFullSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $reguler = ProgramKelas::where('kode_internal', 'REG')->first();
        $taAktif = TahunAkademik::where('is_active', true)->first(); // 20241
        $dosenWali = Dosen::first();

        // 1. Buat Mahasiswa: Ahmad (Semester 1)
        $person = Person::create(['nama_lengkap' => 'Ahmad Dahlan', 'email' => 'ahmad@mhs.ac.id', 'jenis_kelamin' => 'L']);
        $user = User::create([
            'username' => '2401001',
            'email' => 'ahmad@mhs.ac.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'person_id' => $person->id,
            'name' => 'Ahmad Dahlan'
        ]);
        $user->assignRole('mahasiswa');

        $mhs = Mahasiswa::create([
            'person_id' => $person->id,
            'nim' => '2401001',
            'angkatan_id' => 2024,
            'prodi_id' => $prodi->id,
            'program_kelas_id' => $reguler->id,
            'dosen_wali_id' => $dosenWali->id
        ]);

        // 2. Generate Tagihan
        $action = new GenerateTagihanMassalAction();
        $action->execute($taAktif->id, 2024, $prodi->id);

        // 3. Bayar Tagihan (LUNAS)
        $tagihan = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)->first();
        if ($tagihan) {
            $tagihan->update(['status_bayar' => 'LUNAS', 'total_bayar' => $tagihan->total_tagihan]);

            // Set Status Aktif
            RiwayatStatusMahasiswa::create([
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taAktif->id,
                'status_kuliah' => 'A',
                'ips' => 0,
                'ipk' => 0
            ]);
        }
    }
}
