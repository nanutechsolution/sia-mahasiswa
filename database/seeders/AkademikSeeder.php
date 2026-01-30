<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Core\Models\Person;
use Illuminate\Support\Facades\Hash;

class AkademikSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding Data Akademik (MK, Kurikulum, Jadwal)...');

        // 1. Setup Referensi
        $prodiTi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $prodiSi = Prodi::where('kode_prodi_internal', 'SI')->first();
        
        if (!$prodiTi || !$prodiSi) {
            $this->command->error('Prodi TI atau SI belum ada. Jalankan MasterReferenceSeeder dulu.');
            return;
        }

        $taAktif = TahunAkademik::where('is_active', true)->first();
        $progReg = ProgramKelas::where('kode_internal', 'REG')->first();

        // 2. Setup Dosen Dummy (SSOT Architecture)
        $dosenA = $this->createDosen('Budi Santoso, M.Kom', '001001', $prodiTi->id);
        $dosenB = $this->createDosen('Siti Aminah, M.T', '002002', $prodiTi->id);
        $dosenC = $this->createDosen('Rudi Hartono, M.Cs', '003003', $prodiSi->id);

        // 3. Seeding Mata Kuliah & Kurikulum (TI)
        $this->seedKurikulumTI($prodiTi, $dosenA, $dosenB, $taAktif, $progReg);

        // 4. Seeding Mata Kuliah & Kurikulum (SI)
        $this->seedKurikulumSI($prodiSi, $dosenC, $taAktif, $progReg);
        
        $this->command->info('Seeding Akademik Selesai.');
    }

    private function createDosen($nama, $nidn, $prodiId)
    {
        // SSOT: Buat Person dulu
        $person = Person::firstOrCreate(['nama_lengkap' => $nama], ['created_at' => now()]);
        
        // Buat Data Akademik Dosen
        $dosen = Dosen::firstOrCreate(['nidn' => $nidn], [
            'person_id' => $person->id,
            'prodi_id' => $prodiId,
            'is_active' => true
        ]);

        // Buat User Login (dosen001 / password)
        $username = 'dosen'.$nidn;
        if (!User::where('username', $username)->exists()) {
            $user = User::create([
                'name' => $nama,
                'username' => $username,
                'email' => $username.'@lecturer.unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'is_active' => true,
                'person_id' => $person->id
            ]);
            $user->assignRole('dosen');
        }

        return $dosen;
    }

    private function seedKurikulumTI($prodi, $dosenA, $dosenB, $ta, $progReg)
    {
        // A. Buat Kurikulum
        $kurikulum = Kurikulum::firstOrCreate(
            ['nama_kurikulum' => 'Kurikulum TI 2024'],
            [
                'prodi_id' => $prodi->id, 
                'tahun_mulai' => 2024, 
                'id_semester_mulai' => '20241',
                'jumlah_sks_lulus' => 144,
                'is_active' => true
            ]
        );

        // B. Daftar MK Semester 1 & 3
        $mks = [
            ['kode' => 'TI-101', 'nama' => 'Algoritma Pemrograman', 'sks' => 4, 'smt' => 1, 'sifat' => 'W', 'dosen' => $dosenA],
            ['kode' => 'TI-102', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'smt' => 1, 'sifat' => 'W', 'dosen' => $dosenB],
            ['kode' => 'TI-103', 'nama' => 'Bahasa Inggris 1', 'sks' => 2, 'smt' => 1, 'sifat' => 'W', 'dosen' => null],
            ['kode' => 'TI-301', 'nama' => 'Basis Data', 'sks' => 4, 'smt' => 3, 'sifat' => 'W', 'dosen' => $dosenA],
            ['kode' => 'TI-302', 'nama' => 'Pemrograman Web', 'sks' => 4, 'smt' => 3, 'sifat' => 'W', 'dosen' => $dosenB],
        ];

        foreach ($mks as $data) {
            // 1. Create MK
            $mk = MataKuliah::firstOrCreate(
                ['kode_mk' => $data['kode'], 'prodi_id' => $prodi->id],
                [
                    'nama_mk' => $data['nama'],
                    'sks_default' => $data['sks'],
                    'sks_tatap_muka' => $data['sks'], // Default semua ke teori
                    'jenis_mk' => 'A'
                ]
            );

            // 2. Attach ke Kurikulum
            $kurikulum->mataKuliahs()->syncWithoutDetaching([
                $mk->id => [
                    'semester_paket' => $data['smt'],
                    'sks_tatap_muka' => $data['sks'],
                    'sks_praktek' => 0,
                    'sks_lapangan' => 0,
                    'sifat_mk' => $data['sifat']
                ]
            ]);

            // 3. Buat Jadwal (Jika ada dosen & TA aktif)
            if ($ta && $data['dosen']) {
                JadwalKuliah::firstOrCreate(
                    [
                        'tahun_akademik_id' => $ta->id,
                        'mata_kuliah_id' => $mk->id,
                        'nama_kelas' => 'A'
                    ],
                    [
                        'dosen_id' => $data['dosen']->id,
                        'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][rand(0,4)],
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '10:00',
                        'ruang' => 'R-LAB-' . rand(1,5),
                        'kuota_kelas' => 40,
                        'id_program_kelas_allow' => $progReg?->id
                    ]
                );
            }
        }
    }

    private function seedKurikulumSI($prodi, $dosenC, $ta, $progReg)
    {
        $kurikulum = Kurikulum::firstOrCreate(
            ['nama_kurikulum' => 'Kurikulum SI 2024'],
            ['prodi_id' => $prodi->id, 'tahun_mulai' => 2024, 'is_active' => true]
        );

        $mk = MataKuliah::firstOrCreate(
            ['kode_mk' => 'SI-101', 'prodi_id' => $prodi->id],
            ['nama_mk' => 'Pengantar Sistem Informasi', 'sks_default' => 3, 'jenis_mk' => 'A']
        );

        $kurikulum->mataKuliahs()->syncWithoutDetaching([
            $mk->id => ['semester_paket' => 1, 'sks_tatap_muka' => 3, 'sifat_mk' => 'W']
        ]);

        if ($ta) {
            JadwalKuliah::firstOrCreate(
                ['tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mk->id, 'nama_kelas' => 'A'],
                ['dosen_id' => $dosenC->id, 'hari' => 'Kamis', 'jam_mulai' => '10:00', 'jam_selesai' => '12:30', 'ruang' => 'R-301', 'kuota_kelas' => 35]
            );
        }
    }
}