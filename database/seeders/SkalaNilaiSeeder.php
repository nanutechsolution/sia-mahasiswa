<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Akademik\Models\SkalaNilai;

class SkalaNilaiSeeder extends Seeder
{
    /**
     * Jalankan database seeder untuk standar skala nilai.
     */
    public function run(): void
    {
        $data = [
            [
                'huruf' => 'A',
                'bobot_indeks' => 4.00,
                'nilai_min' => 80.00,
                'nilai_max' => 100.00,
                'is_lulus' => true,
            ],
            [
                'huruf' => 'B',
                'bobot_indeks' => 3.00,
                'nilai_min' => 70.00,
                'nilai_max' => 79.99,
                'is_lulus' => true,
            ],
            [
                'huruf' => 'C',
                'bobot_indeks' => 2.00,
                'nilai_min' => 60.00,
                'nilai_max' => 69.99,
                'is_lulus' => true,
            ],
            [
                'huruf' => 'D',
                'bobot_indeks' => 1.00,
                'nilai_min' => 45.00,
                'nilai_max' => 59.99,
                'is_lulus' => true,
            ],
            [
                'huruf' => 'E',
                'bobot_indeks' => 0.00,
                'nilai_min' => 0.00,
                'nilai_max' => 44.99,
                'is_lulus' => false,
            ],
        ];

        foreach ($data as $item) {
            SkalaNilai::updateOrCreate(
                ['huruf' => $item['huruf']], // Kunci pencarian
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Skala nilai berhasil di-generate.');
    }
}