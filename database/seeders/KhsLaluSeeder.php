<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Person;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Core\Models\TahunAkademik;
use Illuminate\Support\Str;

class KhsLaluSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding KHS Semester Lalu...');

        // 1. Ambil Mahasiswa Target (Budi)
        // Pastikan Budi sudah ada (dari MahasiswaSeeder)
        // Kita cari berdasarkan NIM di tabel Mahasiswa
        $mhs = Mahasiswa::where('nim', '2024000000001')->first();
        
        if (!$mhs) {
            $this->command->error('Mahasiswa dengan NIM 2024000000001 tidak ditemukan. Jalankan MahasiswaSeeder dulu.');
            return;
        }

        // 2. Buat Semester Lalu (Ganjil 2023/2024 -> Kode 20231)
        // Asumsi semester sekarang 20241
        $taLalu = TahunAkademik::firstOrCreate(
            ['kode_tahun' => '20231'],
            [
                'nama_tahun' => 'Ganjil 2023/2024',
                'semester' => 1,
                'is_active' => false,
                'buka_krs' => false,
                'tanggal_mulai' => '2023-09-01',
                'tanggal_selesai' => '2024-01-31'
            ]
        );

        // 3. Ambil Dosen (Dr. Code)
        $dosen = Dosen::first(); 
        if (!$dosen) {
            $this->command->error('Data Dosen belum ada.');
            return;
        }

        // 4. Buat Mata Kuliah Semester 1 (Jika belum ada)
        $prodiId = $mhs->prodi_id;
        
        $mkList = [
            ['kode' => 'DU-101', 'nama' => 'Pendidikan Agama', 'sks' => 2, 'nilai' => 'A', 'indeks' => 4.0],
            ['kode' => 'DU-102', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'nilai' => 'B', 'indeks' => 3.0],
            ['kode' => 'TI-101', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 3, 'nilai' => 'A', 'indeks' => 4.0],
            ['kode' => 'TI-102', 'nama' => 'Algoritma & Pemrograman Dasar', 'sks' => 4, 'nilai' => 'B', 'indeks' => 3.0],
            ['kode' => 'TI-103', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'nilai' => 'C', 'indeks' => 2.0],
        ];

        // 5. Buat KRS Header
        $krs = Krs::firstOrCreate(
            [
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taLalu->id
            ],
            [
                'status_krs' => 'DISETUJUI', // Langsung setujui
                'tgl_krs' => '2023-09-05',
                'dosen_wali_id' => $mhs->dosen_wali_id ?? $dosen->id
            ]
        );

        $totalSks = 0;
        $totalMutu = 0;

        foreach ($mkList as $data) {
            // A. Create MK
            $mk = MataKuliah::firstOrCreate(
                ['kode_mk' => $data['kode'], 'prodi_id' => $prodiId],
                ['nama_mk' => $data['nama'], 'sks_default' => $data['sks'], 'jenis_mk' => 'A']
            );

            // B. Create Jadwal (Dummy/Hantu untuk history)
            $jadwal = JadwalKuliah::firstOrCreate(
                [
                    'tahun_akademik_id' => $taLalu->id,
                    'mata_kuliah_id' => $mk->id,
                    'nama_kelas' => 'A-Lalu'
                ],
                [
                    'dosen_id' => $dosen->id,
                    'hari' => 'Senin', // Dummy
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:00:00',
                    'ruang' => 'Virtual',
                    'kuota_kelas' => 50
                ]
            );

            // C. Create KRS Detail (Nilai)
            KrsDetail::updateOrCreate(
                [
                    'krs_id' => $krs->id,
                    'jadwal_kuliah_id' => $jadwal->id
                ],
                [
                    'status_ambil' => 'B', // Baru
                    'nilai_huruf' => $data['nilai'],
                    'nilai_indeks' => $data['indeks'],
                    'nilai_angka' => ($data['indeks'] * 25), // Estimasi kasar
                    'is_published' => true // Wajib true agar muncul di KHS/Transkrip
                ]
            );

            $totalSks += $data['sks'];
            $totalMutu += ($data['sks'] * $data['indeks']);
        }

        // 6. Hitung IPS & Simpan Riwayat
        $ips = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        RiwayatStatusMahasiswa::updateOrCreate(
            [
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taLalu->id
            ],
            [
                'status_kuliah' => 'A', // Aktif
                'ips' => $ips,
                'ipk' => $ips, // Karena semester 1, IPK = IPS
                'sks_semester' => $totalSks,
                'sks_total' => $totalSks
            ]
        );

        $this->command->info("Sukses! KHS Semester Lalu untuk {$mhs->nim} telah dibuat.");
        $this->command->info("IPS: " . number_format($ips, 2) . " | SKS: $totalSks");
    }
}