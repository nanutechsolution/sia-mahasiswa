<?php

namespace Database\Seeders;

use App\Models\User;
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
            // 1. Role harus dibuat duluan
            RolePermissionSeeder::class,

            // 2. Baru buat user & assign role
            UserSeeder::class,
            // 1. Master Core & Akademik
            ReferenceSeeder::class,
            AcademicMasterSeeder::class,

            // 2. Master Keuangan (Skema Tarif)
            // FinanceMasterSeeder::class,

            KomponenBiayaSeeder::class,
            // 3. User & Mahasiswa (Aktor Utama)
            MahasiswaSeeder::class,

            // 4. Transaksi Akademik (Jadwal)
            ScheduleSeeder::class,
            SkalaNilaiSeeder::class,
            // 5. Simulasi Keuangan (Tagihan & Bayar)
            // FinanceTransactionSeeder::class,
            MasterReferenceSeeder::class,
            PersonSeeder::class,
            TrxPersonJabatanSeeder::class,
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
