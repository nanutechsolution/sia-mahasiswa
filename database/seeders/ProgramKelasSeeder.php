<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Core\Models\ProgramKelas;

class ProgramKelasSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk Program Kelas.
     */
    public function run(): void
    {
        $programs = [
            [
                'kode_internal' => 'REG',
                'nama_program' => 'Reguler',
                'min_pembayaran_persen' => 25, // Mahasiswa reguler wajib bayar minimal 25% untuk isi KRS
                'is_active' => true,
                'deskripsi' => 'Program perkuliahan jam kerja (Pagi/Siang).'
            ],
            [
                'kode_internal' => 'EKS',
                'nama_program' => 'Ekstensi',
                'min_pembayaran_persen' => 50, // Mahasiswa ekstensi wajib bayar minimal 50% untuk isi KRS
                'is_active' => true,
                'deskripsi' => 'Program perkuliahan luar jam kerja (Malam) untuk karyawan.'
            ],
        ];

        foreach ($programs as $prog) {
            ProgramKelas::updateOrCreate(
                ['kode_internal' => $prog['kode_internal']],
                [
                    'nama_program' => $prog['nama_program'],
                    'min_pembayaran_persen' => $prog['min_pembayaran_persen'],
                    'is_active' => $prog['is_active'],
                    'deskripsi' => $prog['deskripsi'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Seeder Program Kelas (Reguler & Ekstensi) berhasil dijalankan.');
    }
}
