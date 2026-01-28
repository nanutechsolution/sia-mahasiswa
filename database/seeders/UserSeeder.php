<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin (IT / Dewa System)
        $super = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'root@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'superadmin', // Native Column
                'is_active' => true,
            ]
        );
        $super->assignRole('superadmin'); // Spatie Role

        // 2. Admin Keuangan (Biro Keuangan)
        $keuangan = User::firstOrCreate(
            ['username' => 'admin_keuangan'],
            [
                'name' => 'Staff Keuangan',
                'email' => 'keuangan@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'keuangan',
                'is_active' => true,
            ]
        );
        $keuangan->assignRole('keuangan');

        // 3. Admin BAAK (Biro Administrasi Akademik)
        $baak = User::firstOrCreate(
            ['username' => 'admin_baak'],
            [
                'name' => 'Kepala BAAK',
                'email' => 'baak@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'baak',
                'is_active' => true,
            ]
        );
        $baak->assignRole('baak');

        // 4. Admin LPM (Lembaga Penjaminan Mutu - Optional)
        $lpm = User::firstOrCreate(
            ['username' => 'admin_lpm'],
            [
                'name' => 'Staff LPM',
                'email' => 'lpm@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'lpm',
                'is_active' => true,
            ]
        );
        $lpm->assignRole('lpm');

        // 5. Dosen (Dr. Code)
        $dosen = User::firstOrCreate(
            ['username' => 'dosen01'],
            [
                'name' => 'Dr. Code, M.Kom',
                'email' => 'dosen@unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'is_active' => true,
            ]
        );
        $dosen->assignRole('dosen');

        // 6. Mahasiswa Reguler (Budi)
        $mhs1 = User::firstOrCreate(
            ['username' => '2401001'],
            [
                'name' => 'Budi Santoso (Reguler)',
                'email' => 'budi@mhs.unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'is_active' => true,
            ]
        );
        $mhs1->assignRole('mahasiswa');

        // 7. Mahasiswa Ekstensi (Ani)
        $mhs2 = User::firstOrCreate(
            ['username' => '2401002'],
            [
                'name' => 'Ani Wijaya (Ekstensi)',
                'email' => 'ani@mhs.unmaris.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'is_active' => true,
            ]
        );
        $mhs2->assignRole('mahasiswa');
    }
}