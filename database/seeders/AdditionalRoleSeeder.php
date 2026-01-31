<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdditionalRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * KONFIGURASI ROLE & PERMISSION (FUTURE-PROOF)
         * -------------------------------------------------------------------------
         * Cukup tambahkan modul baru di array ini. Tidak perlu ubah logika di bawah.
         * Format: 'nama_role' => ['permission_1', 'permission_2']
         */
        $roleMap = [
            // Superadmin: Memiliki akses ke SELURUH permission yang ada di sistem
            'superadmin' => ['*'], 

            // Admin: Operator utama sistem
            'admin' => [
                'akses_modul_akademik',
                'akses_modul_keuangan',
                'akses_modul_system',
            ],

            // BARA: Bagian Administrasi, Registrasi & Akademik
            'bara' => [
                'akses_modul_akademik',
            ],

            // BAUK: Bagian Administrasi Umum & Keuangan
            'bauk' => [
                'akses_modul_keuangan',
            ],

            // User Role: Default tanpa permission khusus panel admin
            'dosen' => [],
            'mahasiswa' => [],
            
            // --- AREA PENGEMBANGAN MASA DEPAN (Cukup Uncomment/Tambah) ---
            // 'pustakawan' => ['akses_modul_perpustakaan'],
            // 'aset'       => ['akses_modul_inventaris'],
            // 'alumni'     => ['akses_modul_karir'],
        ];

        // 2. EKSEKUSI OTOMATIS (JANGAN DIUBAH)
        
        // A. Kumpulkan semua permission unik dari konfigurasi diatas
        // (Mengambil semua value array, membuang tanda '*', dan membuatnya unik)
        $allPermissions = collect($roleMap)
            ->flatten()
            ->reject(fn($p) => $p === '*')
            ->unique()
            ->values();

        // B. Buat Permission di Database jika belum ada
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // C. Buat Role dan Sinkronisasi Permission
        foreach ($roleMap as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if (in_array('*', $permissions)) {
                // Jika superadmin, beri semua permission yang ada di database
                $role->syncPermissions(Permission::all());
            } else {
                // Jika role biasa, beri permission spesifik sesuai konfigurasi
                // syncPermissions() akan menghapus permission lama yang tidak ada di list (Clean Slate)
                $role->syncPermissions($permissions);
            }
        }
        
        $this->command->info('Role & Permission berhasil disinkronisasi dengan konfigurasi master.');
    }
}