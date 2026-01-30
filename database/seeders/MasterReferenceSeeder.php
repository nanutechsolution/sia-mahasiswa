<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterReferenceSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================
         * MASTER ROLE PERSON
         * =========================
         */
        DB::table('ref_person_role')->insert([
            [
                'kode_role' => 'DOSEN',
                'nama_role' => 'Dosen',
                'created_at' => now(),
            ],
            [
                'kode_role' => 'PEGAWAI',
                'nama_role' => 'Pegawai',
                'created_at' => now(),
            ],
            [
                'kode_role' => 'MAHASISWA',
                'nama_role' => 'Mahasiswa',
                'created_at' => now(),
            ],
            [
                'kode_role' => 'ADMIN',
                'nama_role' => 'Administrator Sistem',
                'created_at' => now(),
            ],
        ]);

        /**
         * =========================
         * MASTER GELAR AKADEMIK
         * =========================
         */
        DB::table('ref_gelar')->insert([
            ['kode' => 'S.Kom', 'nama' => 'Sarjana Komputer', 'jenjang' => 'S1', 'posisi' => 'BELAKANG'],
            ['kode' => 'M.T', 'nama' => 'Magister Teknik', 'jenjang' => 'S2', 'posisi' => 'BELAKANG'],
            ['kode' => 'Dr', 'nama' => 'Doktor', 'jenjang' => 'S3', 'posisi' => 'DEPAN'],
            ['kode' => 'Prof', 'nama' => 'Profesor', 'jenjang' => 'PROFESI', 'posisi' => 'DEPAN'],
        ]);

        /**
         * =========================
         * MASTER JABATAN
         * =========================
         */
        /**
         * =========================
         * MASTER JABATAN
         * =========================
         */
        DB::table('ref_jabatan')->insert([
            // ===== STRUKTURAL =====
            [
                'kode_jabatan' => 'REKTOR',
                'nama_jabatan' => 'Rektor',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'WR1',
                'nama_jabatan' => 'Wakil Rektor I',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'WR2',
                'nama_jabatan' => 'Wakil Rektor II',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'DEKAN',
                'nama_jabatan' => 'Dekan',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'WD1',
                'nama_jabatan' => 'Wakil Dekan I',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'KAPRODI',
                'nama_jabatan' => 'Ketua Program Studi',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'SEKPRODI',
                'nama_jabatan' => 'Sekretaris Program Studi',
                'jenis' => 'STRUKTURAL',
                'created_at' => now(),
            ],

            // ===== FUNGSIONAL DOSEN =====
            [
                'kode_jabatan' => 'ASISTEN_AHLI',
                'nama_jabatan' => 'Asisten Ahli',
                'jenis' => 'FUNGSIONAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'LEKTOR',
                'nama_jabatan' => 'Lektor',
                'jenis' => 'FUNGSIONAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'LEKTOR_KEPALA',
                'nama_jabatan' => 'Lektor Kepala',
                'jenis' => 'FUNGSIONAL',
                'created_at' => now(),
            ],
            [
                'kode_jabatan' => 'GURU_BESAR',
                'nama_jabatan' => 'Guru Besar',
                'jenis' => 'FUNGSIONAL',
                'created_at' => now(),
            ],
        ]);
    }
}
