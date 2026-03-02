<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterMatakuliahTL extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Master Mata Kuliah Teknik Lingkungan...');
        
        // 1. Ambil Prodi TL (Sesuaikan dengan namespace model Prodi Anda)
        $prodi = DB::table('ref_prodi')->where('kode_prodi_internal', 'TL')->first();
        
        if (!$prodi) {
            $this->command->error("Prodi dengan kode 'TL' tidak ditemukan di tabel ref_prodi.");
            return;
        }

        // 2. Daftar Mata Kuliah
        // Note: Kode MK dan SKS merupakan nilai placeholder sementara
        $courses = [
            // Semester 1
            ['nama_mk' => 'Pendidikan Agama', 'sks_default' => 2, 'kode_mk' => 'TL101'],
            ['nama_mk' => 'Pendidikan Pancasila', 'sks_default' => 2, 'kode_mk' => 'TL102'],
            ['nama_mk' => 'Pendidikan Anti Korupsi', 'sks_default' => 2, 'kode_mk' => 'TL103'],
            ['nama_mk' => 'Bahasa Indonesia', 'sks_default' => 2, 'kode_mk' => 'TL104'],
            ['nama_mk' => 'Fisika Dasar 1', 'sks_default' => 3, 'kode_mk' => 'TL105'],
            ['nama_mk' => 'Biologi Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL106'],
            ['nama_mk' => 'Kimia Dasar 1', 'sks_default' => 3, 'kode_mk' => 'TL107'],
            ['nama_mk' => 'Matematika Dasar', 'sks_default' => 3, 'kode_mk' => 'TL108'],
            ['nama_mk' => 'Pengenalan Lingkungan Potensi Sumba Barat Daya', 'sks_default' => 2, 'kode_mk' => 'TL109'],

            // Semester 2
            ['nama_mk' => 'Kewarganegaraan', 'sks_default' => 2, 'kode_mk' => 'TL201'],
            ['nama_mk' => 'Gambar Teknik', 'sks_default' => 2, 'kode_mk' => 'TL202'],
            ['nama_mk' => 'Fisika Dasar II', 'sks_default' => 3, 'kode_mk' => 'TL203'],
            ['nama_mk' => 'Kimia Dasar II', 'sks_default' => 3, 'kode_mk' => 'TL204'],
            ['nama_mk' => 'Hukum dan Kebijakan Lingkungan', 'sks_default' => 2, 'kode_mk' => 'TL205'],
            ['nama_mk' => 'Mikrobiologi Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL206'],
            ['nama_mk' => 'Pengelolaan Sumber Daya Air', 'sks_default' => 3, 'kode_mk' => 'TL207'],
            ['nama_mk' => 'Pencemaran Udara', 'sks_default' => 3, 'kode_mk' => 'TL208'],

            // Semester 3
            ['nama_mk' => 'Perencanaan Struktur', 'sks_default' => 3, 'kode_mk' => 'TL301'],
            ['nama_mk' => 'Mekanika Tanah dan Pondasi', 'sks_default' => 3, 'kode_mk' => 'TL302'],
            ['nama_mk' => 'Kesehatan Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL303'],
            ['nama_mk' => 'Hidrolika', 'sks_default' => 3, 'kode_mk' => 'TL304'],
            ['nama_mk' => 'Perpetaan', 'sks_default' => 3, 'kode_mk' => 'TL305'],
            ['nama_mk' => 'Teknik Analisis pencemaran Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL306'],
            ['nama_mk' => 'Etika Lingkungan dan Profesi', 'sks_default' => 2, 'kode_mk' => 'TL307'],
            ['nama_mk' => 'Geohidrologi', 'sks_default' => 3, 'kode_mk' => 'TL308'],
            ['nama_mk' => 'Konservasi Sumber Daya Alam', 'sks_default' => 2, 'kode_mk' => 'TL309'],

            // Semester 4
            ['nama_mk' => 'Manajemen SDA dan Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL401'],
            ['nama_mk' => 'Sistem Penyaluran Air Limbah', 'sks_default' => 3, 'kode_mk' => 'TL402'],
            ['nama_mk' => 'Sistem Penyediaan Air Minum', 'sks_default' => 3, 'kode_mk' => 'TL403'],
            ['nama_mk' => 'Pengelolaan Sampah', 'sks_default' => 3, 'kode_mk' => 'TL404'],
            ['nama_mk' => 'Statistik Lingkungan', 'sks_default' => 2, 'kode_mk' => 'TL405'],
            ['nama_mk' => 'Kewirausahaan', 'sks_default' => 2, 'kode_mk' => 'TL406'],
            ['nama_mk' => 'Kesehatan dan Keselamatan Kerja', 'sks_default' => 2, 'kode_mk' => 'TL407'],
            ['nama_mk' => 'Sistem Manajemen Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL408'],
            ['nama_mk' => 'Pengendalian dan Emisi Ambien', 'sks_default' => 3, 'kode_mk' => 'TL409'],

            // Semester 5
            ['nama_mk' => 'Sanitasi Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL501'],
            ['nama_mk' => 'Unit Operasi Teknik Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL502'],
            ['nama_mk' => 'Unit Proses Teknik Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL503'],
            ['nama_mk' => 'Sistem Drainase Perkotaan', 'sks_default' => 3, 'kode_mk' => 'TL504'],
            ['nama_mk' => 'Metodologi Penelitian', 'sks_default' => 3, 'kode_mk' => 'TL505'],
            ['nama_mk' => 'Pencemaran Tanah Dan Air Tanah', 'sks_default' => 3, 'kode_mk' => 'TL506'],
            ['nama_mk' => 'Mitigasi Bencana', 'sks_default' => 2, 'kode_mk' => 'TL507'],
            ['nama_mk' => 'Teknologi Pengolahan Limbah Industri', 'sks_default' => 3, 'kode_mk' => 'TL508'],
            ['nama_mk' => 'Teknologi Hijau', 'sks_default' => 2, 'kode_mk' => 'TL509'],

            // Semester 6
            ['nama_mk' => 'Plambing dan Instrumentasi', 'sks_default' => 3, 'kode_mk' => 'TL601'],
            ['nama_mk' => 'Analisis Mengenai Dampak Lingkungan', 'sks_default' => 3, 'kode_mk' => 'TL602'],
            ['nama_mk' => 'Pengelolaan Limbah B3', 'sks_default' => 3, 'kode_mk' => 'TL603'],
            ['nama_mk' => 'Pengelolaan Limbah Industri', 'sks_default' => 3, 'kode_mk' => 'TL604'],
            ['nama_mk' => 'Pengendalian Pencemaran Air', 'sks_default' => 3, 'kode_mk' => 'TL605'],
            ['nama_mk' => 'Audit Lingkungan', 'sks_default' => 2, 'kode_mk' => 'TL606'],
            ['nama_mk' => 'Perencanaan Bangunan Pengelolaan Air Limbah', 'sks_default' => 3, 'kode_mk' => 'TL607'],
            ['nama_mk' => 'Perencanaan dan Pengelolaan Proyek', 'sks_default' => 3, 'kode_mk' => 'TL608'],
            ['nama_mk' => 'Teknologi Energi Terbarukan', 'sks_default' => 3, 'kode_mk' => 'TL609'],

            // Semester 7
            ['nama_mk' => 'Kerja Praktik', 'sks_default' => 2, 'kode_mk' => 'TL701', 'sks_lapangan' => 2, 'sks_tatap_muka' => 0],
            ['nama_mk' => 'Proposal', 'sks_default' => 2, 'kode_mk' => 'TL702'],

            // Semester 8
            ['nama_mk' => 'Seminar Proposal', 'sks_default' => 2, 'kode_mk' => 'TL801'],
            ['nama_mk' => 'SKRIPSI', 'sks_default' => 4, 'kode_mk' => 'TL802', 'sks_lapangan' => 4, 'sks_tatap_muka' => 0],
        ];

        // 3. Proses Insert Data menggunakan DB Facade (UpdateOrInsert)
        $now = Carbon::now();

        foreach ($courses as $course) {
            DB::table('master_mata_kuliahs')->updateOrInsert(
                [
                    // Kondisi Unique Key
                    'prodi_id' => $prodi->id,
                    'kode_mk'  => $course['kode_mk'],
                ],
                [
                    // Data yang di-insert/update
                    'nama_mk'        => $course['nama_mk'],
                    'sks_default'    => $course['sks_default'],
                    
                    // Jika ada nilai sks spesifik (praktek/lapangan) di array, gunakan itu, jika tidak asumsikan tatap muka
                    'sks_tatap_muka' => $course['sks_tatap_muka'] ?? $course['sks_default'],
                    'sks_praktek'    => $course['sks_praktek'] ?? 0,
                    'sks_lapangan'   => $course['sks_lapangan'] ?? 0,
                    
                    'jenis_mk'       => 'A',
                    'activity_type'  => 'REGULAR',
                    
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            );
        }

        $this->command->info('Seeding Master Mata Kuliah Teknik Lingkungan selesai!');
    }
}