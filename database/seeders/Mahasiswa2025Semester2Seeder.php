<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Person;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;

class Mahasiswa2025Semester2Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menyiapkan data simulasi Semester 2 (Genap 2025)...');

        // 1. SETUP PERIODE AKADEMIK
        // Semester 1 (Ganjil - Lampau)
        $ta1 = TahunAkademik::updateOrCreate(['kode_tahun' => '20251'], [
            'nama_tahun' => 'Ganjil 2025/2026', 'semester' => 1, 'is_active' => false
        ]);
        // Semester 2 (Genap - Sekarang Aktif & Buka KRS)
        $ta2 = TahunAkademik::updateOrCreate(['kode_tahun' => '20252'], [
            'nama_tahun' => 'Genap 2025/2026', 'semester' => 2, 'is_active' => true, 'buka_krs' => true,
            'tanggal_mulai' => now(), 'tanggal_selesai' => now()->addMonths(6)
        ]);

        // 2. REFERENSI DASAR
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $kelas = ProgramKelas::where('kode_internal', 'REG')->first();
        $dosenPA = Dosen::first();

        // 3. MAHASISWA & USER (SSOT)
        $person = Person::create([
            'nama_lengkap' => 'Rizky Ramadhan',
            'nik' => '3201002025990001',
            'email' => 'rizky25@mhs.unmaris.ac.id',
            'jenis_kelamin' => 'L'
        ]);

        $user = User::create([
            'name' => $person->nama_lengkap,
            'username' => '2501999',
            'email' => $person->email,
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'person_id' => $person->id
        ]);

        $mhs = Mahasiswa::create([
            'person_id' => $person->id,
            'nim' => '2501999',
            'angkatan_id' => 2025,
            'prodi_id' => $prodi->id,
            'program_kelas_id' => $kelas->id,
            'dosen_wali_id' => $dosenPA->id
        ]);

        // 4. KEUANGAN SEMESTER 1 (SET LUNAS - SYARAT KRS SMT 2)
        TagihanMahasiswa::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $ta1->id,
            'kode_transaksi' => 'INV-20251-PAID',
            'deskripsi' => 'Tagihan Semester 1 (Lunas)',
            'total_tagihan' => 5000000,
            'total_bayar' => 5000000, 
            'status_bayar' => 'LUNAS',
            'rincian_item' => [['nama' => 'SPP Tetap', 'nominal' => 5000000]]
        ]);

        // 5. DATA AKADEMIK SEMESTER 1 (HISTORI & NILAI)
        $krs1 = Krs::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $ta1->id,
            'status_krs' => 'DISETUJUI',
            'dosen_wali_id' => $dosenPA->id
        ]);

        // MK Semester 1
        $mkS1 = MataKuliah::firstOrCreate(['kode_mk' => 'MK-101'], ['nama_mk' => 'Dasar Pemrograman', 'sks_default' => 3, 'prodi_id' => $prodi->id]);
        
        $jadwal1 = JadwalKuliah::create([
            'id' => Str::uuid(), 'tahun_akademik_id' => $ta1->id, 'mata_kuliah_id' => $mkS1->id, 
            'dosen_id' => $dosenPA->id, 'nama_kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:00', 'ruang' => 'R1'
        ]);

        KrsDetail::create([
            'krs_id' => $krs1->id, 'jadwal_kuliah_id' => $jadwal1->id,
            'nilai_angka' => 88, 'nilai_huruf' => 'A', 'nilai_indeks' => 4.00, 'is_published' => true
        ]);

        // 6. RIWAYAT STATUS SEMESTER 1 (IPS/IPK)
        RiwayatStatusMahasiswa::create([
            'mahasiswa_id' => $mhs->id, 'tahun_akademik_id' => $ta1->id, 'status_kuliah' => 'A',
            'ips' => 4.00, 'ipk' => 4.00, 'sks_semester' => 3, 'sks_total' => 3
        ]);

        // 7. SETUP JADWAL BARU SEMESTER 2 (PENAWARAN KRS)
        $mkS2 = MataKuliah::firstOrCreate(['kode_mk' => 'MK-201'], ['nama_mk' => 'Struktur Data', 'sks_default' => 4, 'prodi_id' => $prodi->id]);
        
        JadwalKuliah::create([
            'id' => Str::uuid(),
            'tahun_akademik_id' => $ta2->id,
            'mata_kuliah_id' => $mkS2->id,
            'dosen_id' => $dosenPA->id,
            'nama_kelas' => 'A',
            'hari' => 'Rabu',
            'jam_mulai' => '10:00',
            'jam_selesai' => '13:00',
            'ruang' => 'LAB-COMP',
            'kuota_kelas' => 40
        ]);

        $this->command->info('Sukses: Mahasiswa Rizky siap melakukan KRS Semester 2 karena S1 sudah LUNAS dan NILAI keluar.');
    }
}