<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_person')->insert([
            [
                'nama_lengkap' => 'Dr. Andi Wijaya',
                'jenis_kelamin' => 'L',
                'created_at' => now(),
            ],
            [
                'nama_lengkap' => 'Prof. Budi Santoso',
                'jenis_kelamin' => 'L',
                'created_at' => now(),
            ],
            [
                'nama_lengkap' => 'Siti Aminah',
                'jenis_kelamin' => 'P',
                'created_at' => now(),
            ],
        ]);
    }
}
