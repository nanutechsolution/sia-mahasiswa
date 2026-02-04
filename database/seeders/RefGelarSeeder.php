<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefGelarSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [

            // =====================
            // GELAR VOKASI
            // =====================
            ['kode' => 'A.Md', 'nama' => 'Ahli Madya', 'posisi' => 'BELAKANG', 'jenjang' => 'D3'],
            ['kode' => 'S.Tr', 'nama' => 'Sarjana Terapan', 'posisi' => 'BELAKANG', 'jenjang' => 'D4'],

            // =====================
            // SARJANA
            // =====================
            ['kode' => 'S.Ag', 'nama' => 'Sarjana Agama', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.E', 'nama' => 'Sarjana Ekonomi', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Ei', 'nama' => 'Sarjana Ekonomi Islam', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.H', 'nama' => 'Sarjana Hukum', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Hum', 'nama' => 'Sarjana Humaniora', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Kom', 'nama' => 'Sarjana Komputer', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.M', 'nama' => 'Sarjana Manajemen', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.P', 'nama' => 'Sarjana Pendidikan', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Pd', 'nama' => 'Sarjana Pendidikan', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Psi', 'nama' => 'Sarjana Psikologi', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Si', 'nama' => 'Sarjana Sains', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.Sos', 'nama' => 'Sarjana Sosial', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'S.T', 'nama' => 'Sarjana Teknik', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],

            // =====================
            // MAGISTER
            // =====================
            ['kode' => 'M.Ag', 'nama' => 'Magister Agama', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.E', 'nama' => 'Magister Ekonomi', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.H', 'nama' => 'Magister Hukum', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.Hum', 'nama' => 'Magister Humaniora', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.Kom', 'nama' => 'Magister Komputer', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.M', 'nama' => 'Magister Manajemen', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.Pd', 'nama' => 'Magister Pendidikan', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.Psi', 'nama' => 'Magister Psikologi', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.Si', 'nama' => 'Magister Sains', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'M.T', 'nama' => 'Magister Teknik', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],

            // =====================
            // DOKTOR
            // =====================
            ['kode' => 'Dr', 'nama' => 'Doktor', 'posisi' => 'DEPAN', 'jenjang' => 'S3'],
            ['kode' => 'Dr.', 'nama' => 'Doktor', 'posisi' => 'DEPAN', 'jenjang' => 'S3'],

            // =====================
            // PROFESI
            // =====================
            ['kode' => 'Ir', 'nama' => 'Insinyur', 'posisi' => 'DEPAN', 'jenjang' => 'PROFESI'],
            ['kode' => 'dr', 'nama' => 'Dokter', 'posisi' => 'DEPAN', 'jenjang' => 'PROFESI'],
            ['kode' => 'drg', 'nama' => 'Dokter Gigi', 'posisi' => 'DEPAN', 'jenjang' => 'PROFESI'],
            ['kode' => 'Ns', 'nama' => 'Ners', 'posisi' => 'BELAKANG', 'jenjang' => 'PROFESI'],
            ['kode' => 'Apt', 'nama' => 'Apoteker', 'posisi' => 'BELAKANG', 'jenjang' => 'PROFESI'],
            ['kode' => 'Ak', 'nama' => 'Akuntan', 'posisi' => 'BELAKANG', 'jenjang' => 'PROFESI'],
            ['kode' => 'CPA', 'nama' => 'Certified Public Accountant', 'posisi' => 'BELAKANG', 'jenjang' => 'PROFESI'],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('ref_gelar')->insertOrIgnore($data);
    }
}
