<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AturanSksSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_aturan_sks')->insert([
            ['min_ips' => 3.00, 'max_ips' => 4.00, 'max_sks' => 24, 'created_at' => now()],
            ['min_ips' => 2.50, 'max_ips' => 2.99, 'max_sks' => 21, 'created_at' => now()],
            ['min_ips' => 2.00, 'max_ips' => 2.49, 'max_sks' => 18, 'created_at' => now()],
            ['min_ips' => 1.50, 'max_ips' => 1.99, 'max_sks' => 15, 'created_at' => now()],
            ['min_ips' => 0.00, 'max_ips' => 1.49, 'max_sks' => 12, 'created_at' => now()],
        ]);
    }
}