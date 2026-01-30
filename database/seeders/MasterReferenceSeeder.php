<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Core\Models\Fakultas;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Akademik\Models\SkalaNilai;

class MasterReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // 0. [FIX] ISI DATA ANGKATAN TERLEBIH DAHULU
        // Ini wajib ada sebelum Skema Tarif atau Mahasiswa dibuat
        $years = range(2020, 2030); // Generate tahun 2020 s.d 2030
        foreach ($years as $year) {
            DB::table('ref_angkatan')->updateOrInsert(
                ['id_tahun' => $year],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 1. Tahun Akademik
        TahunAkademik::updateOrCreate(['kode_tahun' => '20232'], ['nama_tahun' => 'Genap 2023/2024', 'semester' => 2, 'is_active' => false]);
        TahunAkademik::updateOrCreate(['kode_tahun' => '20241'], ['nama_tahun' => 'Ganjil 2024/2025', 'semester' => 1, 'is_active' => true, 'buka_krs' => true]);

        // 2. Fakultas & Prodi
        $ft = Fakultas::firstOrCreate(['kode_fakultas' => 'FT'], ['nama_fakultas' => 'Fakultas Teknik']);
        $fe = Fakultas::firstOrCreate(['kode_fakultas' => 'FE'], ['nama_fakultas' => 'Fakultas Ekonomi']);

        Prodi::firstOrCreate(['kode_prodi_internal' => 'TI'], [
            'fakultas_id' => $ft->id,
            'kode_prodi_dikti' => '55201',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'gelar_lulusan' => 'S.Kom',
            'format_nim' => '{TAHUN}55201{NO:4}'
        ]);
        
        Prodi::firstOrCreate(['kode_prodi_internal' => 'SI'], [
            'fakultas_id' => $ft->id,
            'kode_prodi_dikti' => '55202',
            'nama_prodi' => 'Sistem Informasi',
            'jenjang' => 'S1',
            'gelar_lulusan' => 'S.Kom',
            'format_nim' => '{TAHUN}55202{NO:4}'
        ]);

        // 3. Program Kelas
        ProgramKelas::firstOrCreate(['kode_internal' => 'REG'], ['nama_program' => 'Reguler Pagi', 'min_pembayaran_persen' => 25]);
        ProgramKelas::firstOrCreate(['kode_internal' => 'EKS'], ['nama_program' => 'Ekstensi Malam', 'min_pembayaran_persen' => 50]);

        // 4. Skala Nilai
        $skala = [
            ['huruf' => 'A', 'bobot_indeks' => 4.00, 'nilai_min' => 85, 'nilai_max' => 100, 'is_lulus' => true],
            ['huruf' => 'B', 'bobot_indeks' => 3.00, 'nilai_min' => 70, 'nilai_max' => 84.99, 'is_lulus' => true],
            ['huruf' => 'C', 'bobot_indeks' => 2.00, 'nilai_min' => 55, 'nilai_max' => 69.99, 'is_lulus' => true],
            ['huruf' => 'D', 'bobot_indeks' => 1.00, 'nilai_min' => 40, 'nilai_max' => 54.99, 'is_lulus' => true],
            ['huruf' => 'E', 'bobot_indeks' => 0.00, 'nilai_min' => 0, 'nilai_max' => 39.99, 'is_lulus' => false],
        ];
        foreach($skala as $s) SkalaNilai::updateOrCreate(['huruf' => $s['huruf']], $s);

        // 5. HR Master (Jabatan & Gelar & Role)
        DB::table('ref_jabatan')->insertOrIgnore([
            ['kode_jabatan' => 'REKTOR', 'nama_jabatan' => 'Rektor', 'jenis' => 'STRUKTURAL', 'is_active' => true],
            ['kode_jabatan' => 'DEKAN', 'nama_jabatan' => 'Dekan Fakultas', 'jenis' => 'STRUKTURAL', 'is_active' => true],
            ['kode_jabatan' => 'KAPRODI', 'nama_jabatan' => 'Ketua Program Studi', 'jenis' => 'STRUKTURAL', 'is_active' => true],
            ['kode_jabatan' => 'KABAAK', 'nama_jabatan' => 'Kepala BAAK', 'jenis' => 'STRUKTURAL', 'is_active' => true],
        ]);

        DB::table('ref_gelar')->insertOrIgnore([
            ['kode' => 'S.Kom', 'nama' => 'Sarjana Komputer', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'M.Kom', 'nama' => 'Magister Komputer', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
            ['kode' => 'Dr.', 'nama' => 'Doktor', 'posisi' => 'DEPAN', 'jenjang' => 'S3'],
            ['kode' => 'S.T', 'nama' => 'Sarjana Teknik', 'posisi' => 'BELAKANG', 'jenjang' => 'S1'],
            ['kode' => 'M.T', 'nama' => 'Magister Teknik', 'posisi' => 'BELAKANG', 'jenjang' => 'S2'],
        ]);
        
        DB::table('ref_person_role')->insertOrIgnore([
            ['kode_role' => 'DOSEN', 'nama_role' => 'Tenaga Pengajar'],
            ['kode_role' => 'MAHASISWA', 'nama_role' => 'Peserta Didik'],
            ['kode_role' => 'STAFF', 'nama_role' => 'Tenaga Kependidikan'],
        ]);
    }
}