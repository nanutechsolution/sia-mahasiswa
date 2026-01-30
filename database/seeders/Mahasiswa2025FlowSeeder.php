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

class Mahasiswa2025FlowSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai simulasi alur akademik Angkatan 2025...');

        // 1. SETUP TAHUN AKADEMIK (2025 Ganjil Lampau & 2025 Genap Aktif)
        $ta1 = TahunAkademik::updateOrCreate(['kode_tahun' => '20251'], [
            'nama_tahun' => 'Ganjil 2025/2026', 'semester' => 1, 'is_active' => false
        ]);
        $ta2 = TahunAkademik::updateOrCreate(['kode_tahun' => '20252'], [
            'nama_tahun' => 'Genap 2025/2026', 'semester' => 2, 'is_active' => true, 'buka_krs' => true
        ]);

        // 2. REFERENSI DASAR
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $kelas = ProgramKelas::where('kode_internal', 'REG')->first();
        $dosenPA = Dosen::first();

        // 3. IDENTITAS MAHASISWA (SSOT)
        $person = Person::create([
            'nama_lengkap' => 'Andi Pratama',
            'nik' => '3201002025000001',
            'email' => 'andi25@mhs.unmaris.ac.id',
            'jenis_kelamin' => 'L'
        ]);

        $user = User::create([
            'name' => $person->nama_lengkap,
            'username' => '2501001',
            'email' => $person->email,
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'person_id' => $person->id
        ]);
        $user->assignRole('mahasiswa');

        $mhs = Mahasiswa::create([
            'person_id' => $person->id,
            'nim' => '2501001',
            'angkatan_id' => 2025,
            'prodi_id' => $prodi->id,
            'program_kelas_id' => $kelas->id,
            'dosen_wali_id' => $dosenPA->id
        ]);

        // 4. KEUANGAN SEMESTER 1 (SET LUNAS)
        TagihanMahasiswa::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $ta1->id,
            'kode_transaksi' => 'INV-20251-001',
            'deskripsi' => 'Tagihan Semester 1 (Daftar Ulang)',
            'total_tagihan' => 7500000,
            'total_bayar' => 7500000, // Simulasi Lunas
            'status_bayar' => 'LUNAS',
            'rincian_item' => [['nama' => 'SPP Tetap', 'nominal' => 3000000], ['nama' => 'Uang Gedung', 'nominal' => 4500000]]
        ]);

        // 5. KRS & NILAI SEMESTER 1
        $krs1 = Krs::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $ta1->id,
            'status_krs' => 'DISETUJUI',
            'dosen_wali_id' => $dosenPA->id
        ]);

        // Tambah 2 MK contoh untuk Smt 1
        $mks = MataKuliah::where('prodi_id', $prodi->id)->limit(2)->get();
        foreach($mks as $mk) {
            $jadwal = JadwalKuliah::firstOrCreate([
                'tahun_akademik_id' => $ta1->id, 'mata_kuliah_id' => $mk->id, 'nama_kelas' => 'A'
            ], ['dosen_id' => $dosenPA->id, 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:00', 'ruang' => 'R1']);

            KrsDetail::create([
                'krs_id' => $krs1->id,
                'jadwal_kuliah_id' => $jadwal->id,
                'nilai_angka' => 85,
                'nilai_huruf' => 'A',
                'nilai_indeks' => 4.00,
                'is_published' => true // Agar muncul di transkrip
            ]);
        }

        // 6. RIWAYAT STATUS (IPS/IPK Smt 1)
        RiwayatStatusMahasiswa::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_akademik_id' => $ta1->id,
            'status_kuliah' => 'A',
            'ips' => 4.00,
            'ipk' => 4.00,
            'sks_semester' => 6,
            'sks_total' => 6
        ]);

        $this->command->info('Simulasi selesai. Andi (2501001) siap mengisi KRS Semester 2.');
    }
}