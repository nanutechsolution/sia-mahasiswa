<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomponenBiayaSeeder extends Seeder
{
    public function run(): void
    {
        $komponen = [
            // 1. Biaya Awal Masuk (Hanya sekali bayar di awal)
            ['nama_komponen' => 'Biaya Pendaftaran/Registrasi', 'tipe_biaya' => 'SEKALI'],
            ['nama_komponen' => 'Biaya PKKMB', 'tipe_biaya' => 'SEKALI'],
            ['nama_komponen' => 'Pembayaran Jas Almamater', 'tipe_biaya' => 'SEKALI'],
            ['nama_komponen' => 'Pembayaran Seragam Kuliah', 'tipe_biaya' => 'SEKALI'],

            // 2. Biaya Rutin (Setiap Semester)
            ['nama_komponen' => 'Pembayaran SPP', 'tipe_biaya' => 'TETAP'],
            ['nama_komponen' => 'Biaya Extrakulikuler', 'tipe_biaya' => 'TETAP'], // Asumsi iuran kemahasiswaan

            // 3. Biaya Insidental (Hanya semester tertentu atau kondisional)
            ['nama_komponen' => 'Pembayaran Seragam Lapangan', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Pembayaran Praktekum Laboratorium', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Pembayaran Praktekum Lapangan', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Pembayaran Uji Kompetensi', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Biaya KKN/PKL', 'tipe_biaya' => 'INSIDENTAL'], // Semester 6/7
            ['nama_komponen' => 'Biaya Skripsi dan Tugas Akhir', 'tipe_biaya' => 'INSIDENTAL'], // Semester Akhir
            ['nama_komponen' => 'Biaya Yudisium', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Biaya Wisuda', 'tipe_biaya' => 'INSIDENTAL'],
            ['nama_komponen' => 'Biaya Lain-lain', 'tipe_biaya' => 'INSIDENTAL'],
        ];

        foreach ($komponen as $item) {
            DB::table('keuangan_komponen_biaya')->updateOrInsert(
                ['nama_komponen' => $item['nama_komponen']], // Kunci pengecekan agar tidak duplikat
                [
                    'tipe_biaya' => $item['tipe_biaya'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}