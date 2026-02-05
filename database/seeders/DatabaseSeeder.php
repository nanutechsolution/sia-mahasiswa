<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdditionalRoleSeeder::class,
            TahunAkademikSeeder::class,
            FakultasProdiSeeder::class,
            AturanSksSeeder::class,
            RefJabatanSeeder::class,
            SkalaNilaiSeeder::class,
            KomponenBiayaSeeder::class,
            UserSeeder::class,
            ProgramKelasSeeder::class,
            MasterMatakuliahTISeeder::class,
            KurikulumTISeeder::class,
            MasterMatakuliahPtiSeeder::class,
            MasterMatakuliahArsSeeder::class,
            MasterMatakuliahBisnisDigitalSeeder::class,
            KomponenNilaiSeeder::class,
            // KurikulumAutoSeeder::class,
            // KurikulumMappingSeeder::class,
            RealDosenSeeder::class,
            RealMahasiswaSeeder::class,
        ]);
    }
}
