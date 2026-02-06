<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdomQuestionnaireSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk instrumen kuesioner EDOM (Evaluasi Dosen oleh Mahasiswa).
     * Instrumen ini dirancang berdasarkan standar mutu kriteria pendidikan tinggi.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding instrumen kuesioner EDOM...');

        // 1. Bersihkan data lama agar urutan ID tetap konsisten
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lpm_kuisioner_pertanyaan')->truncate();
        DB::table('lpm_kuisioner_kelompok')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Data Kelompok Kuesioner (Aspek Penilaian)
        $groups = [
            ['id' => 1, 'nama' => 'Kompetensi Pedagogik (Proses Belajar Mengajar)', 'urut' => 1],
            ['id' => 2, 'nama' => 'Kompetensi Profesional (Penguasaan Keilmuan)', 'urut' => 2],
            ['id' => 3, 'nama' => 'Kompetensi Kepribadian (Sikap & Kewibawaan)', 'urut' => 3],
            ['id' => 4, 'nama' => 'Kompetensi Sosial (Komunikasi & Interaksi)', 'urut' => 4],
        ];

        foreach ($groups as $g) {
            DB::table('lpm_kuisioner_kelompok')->insert([
                'id' => $g['id'],
                'nama_kelompok' => $g['nama'],
                'urutan' => $g['urut'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Data Butir Pertanyaan (Instrumen)
        $questions = [
            // --- KELOMPOK 1: PEDAGOGIK ---
            [
                'gid' => 1, 
                'teks' => 'Kesiapan dosen dalam memberikan perkuliahan (RPS/Kontrak Kuliah disampaikan di awal).',
                'urut' => 1
            ],
            [
                'gid' => 1, 
                'teks' => 'Kemampuan dosen dalam menjelaskan materi perkuliahan secara sistematis dan terstruktur.',
                'urut' => 2
            ],
            [
                'gid' => 1, 
                'teks' => 'Efektivitas penggunaan media/teknologi pembelajaran (Slide, E-Learning, Video).',
                'urut' => 3
            ],
            [
                'gid' => 1, 
                'teks' => 'Dosen memberikan kesempatan bertanya dan berdiskusi secara aktif di kelas.',
                'urut' => 4
            ],
            [
                'gid' => 1, 
                'teks' => 'Dosen memberikan umpan balik (feedback) yang membangun atas tugas atau hasil ujian.',
                'urut' => 5
            ],

            // --- KELOMPOK 2: PROFESIONAL ---
            [
                'gid' => 2, 
                'teks' => 'Kedalaman dan keluasan penguasaan materi keilmuan yang diajarkan oleh dosen.',
                'urut' => 1
            ],
            [
                'gid' => 2, 
                'teks' => 'Kemampuan dosen mengaitkan teori dengan contoh kasus nyata di dunia kerja/lapangan.',
                'urut' => 2
            ],
            [
                'gid' => 2, 
                'teks' => 'Kemampuan dosen menjawab pertanyaan mahasiswa secara ilmiah dan meyakinkan.',
                'urut' => 3
            ],

            // --- KELOMPOK 3: KEPRIBADIAN ---
            [
                'gid' => 3, 
                'teks' => 'Kedisiplinan waktu kehadiran dosen dalam memulai dan mengakhiri sesi perkuliahan.',
                'urut' => 1
            ],
            [
                'gid' => 3, 
                'teks' => 'Kewibawaan, kerapian penampilan, dan ketegasan dosen dalam memimpin kelas.',
                'urut' => 2
            ],
            [
                'gid' => 3, 
                'teks' => 'Objektivitas dan transparansi dosen dalam memberikan penilaian hasil belajar.',
                'urut' => 3
            ],

            // --- KELOMPOK 4: SOSIAL ---
            [
                'gid' => 4, 
                'teks' => 'Kesantunan dan keramahan dosen dalam berkomunikasi dengan mahasiswa.',
                'urut' => 1
            ],
            [
                'gid' => 4, 
                'teks' => 'Sikap dosen dalam menghargai perbedaan pendapat dan latar belakang mahasiswa.',
                'urut' => 2
            ],
            [
                'gid' => 4, 
                'teks' => 'Kemudahan dalam menghubungi dosen untuk keperluan konsultasi akademik di luar jam kuliah.',
                'urut' => 3
            ],
        ];

        foreach ($questions as $q) {
            DB::table('lpm_kuisioner_pertanyaan')->insert([
                'kelompok_id' => $q['gid'],
                'bunyi_pertanyaan' => $q['teks'],
                'tipe_skala' => '1-4', // Skala: Buruk, Cukup, Baik, Sangat Baik
                'is_required' => true,
                'urutan' => $q['urut'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seed instrumen EDOM selesai. ' . count($questions) . ' butir kuesioner siap digunakan.');
    }
}