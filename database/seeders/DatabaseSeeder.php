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
            SkalaNilaiSeeder::class,
            KomponenBiayaSeeder::class,
            UserSeeder::class,
            KurikulumMappingSeeder::class,
        ]);
    }
}
