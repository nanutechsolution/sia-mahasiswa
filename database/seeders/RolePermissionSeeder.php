<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cache Permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Role Dasar
        $roles = [
            'superadmin', // IT / Maintenance / Dewa System
            'admin',      // Admin Umum
            'keuangan',   // Staff Keuangan (Tagihan, Validasi)
            'baak',       // Biro Akademik (Jadwal, Kurikulum)
            'lpm',        // Penjaminan Mutu
            'dosen',      // Pengajar (Nilai, Perwalian)
            'mahasiswa',  // Peserta Didik (KRS, KHS)
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Note: Logika assignment user dipindahkan ke UserSeeder.php
    }
}