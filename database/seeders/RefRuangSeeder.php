<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefRuangSeeder extends Seeder
{
    /**
     * Jalankan database seeds untuk data referensi ruangan.
     */
    public function run(): void
    {
        $ruangan = [
            // Gedung A (Rektorat & Administrasi)
            ['kode_ruang' => 'A.101', 'nama_ruang' => 'Aula Utama Gedung A', 'kapasitas' => 300, 'is_active' => true],
            ['kode_ruang' => 'A.201', 'nama_ruang' => 'Ruang Rapat Senat', 'kapasitas' => 50, 'is_active' => true],
            ['kode_ruang' => 'A.202', 'nama_ruang' => 'Ruang Teori Rektorat', 'kapasitas' => 40, 'is_active' => true],
            
            // Gedung B (Fakultas Teknik / Ilmu Komputer)
            ['kode_ruang' => 'B.101', 'nama_ruang' => 'Ruang Kelas Teori B.1', 'kapasitas' => 40, 'is_active' => true],
            ['kode_ruang' => 'B.102', 'nama_ruang' => 'Ruang Kelas Teori B.2', 'kapasitas' => 40, 'is_active' => true],
            ['kode_ruang' => 'B.201', 'nama_ruang' => 'Ruang Kelas Teori B.3', 'kapasitas' => 45, 'is_active' => true],
            ['kode_ruang' => 'L.KOM.01', 'nama_ruang' => 'Laboratorium Komputer Dasar', 'kapasitas' => 30, 'is_active' => true],
            ['kode_ruang' => 'L.KOM.02', 'nama_ruang' => 'Laboratorium Jaringan & Cyber Security', 'kapasitas' => 25, 'is_active' => true],
            ['kode_ruang' => 'L.KOM.03', 'nama_ruang' => 'Laboratorium Multimedia & Design', 'kapasitas' => 30, 'is_active' => true],
            
            // Gedung C (Fakultas Umum)
            ['kode_ruang' => 'C.101', 'nama_ruang' => 'Ruang Kelas Besar C.1', 'kapasitas' => 80, 'is_active' => true],
            ['kode_ruang' => 'C.102', 'nama_ruang' => 'Ruang Micro Teaching', 'kapasitas' => 20, 'is_active' => true],
            ['kode_ruang' => 'C.201', 'nama_ruang' => 'Ruang Teori C.3', 'kapasitas' => 40, 'is_active' => true],
            
            // Laboratorium Terpadu & Lapangan
            ['kode_ruang' => 'LAB.FIS', 'nama_ruang' => 'Laboratorium Fisika', 'kapasitas' => 35, 'is_active' => true],
            ['kode_ruang' => 'LAB.KIM', 'nama_ruang' => 'Laboratorium Kimia', 'kapasitas' => 35, 'is_active' => true],
            ['kode_ruang' => 'LPG.01', 'nama_ruang' => 'Lapangan Olahraga Utama', 'kapasitas' => 100, 'is_active' => true],
        ];

        foreach ($ruangan as $r) {
            DB::table('ref_ruang')->updateOrInsert(
                ['kode_ruang' => $r['kode_ruang']],
                [
                    'nama_ruang' => $r['nama_ruang'],
                    'kapasitas' => $r['kapasitas'],
                    'is_active' => $r['is_active']
                ]
            );
        }
    }
}