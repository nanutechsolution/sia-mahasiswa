<?php

namespace Database\Seeders;

use App\Domains\Core\Models\Person as ModelsPerson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Helpers\SistemHelper;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

class RealMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai import data Mahasiswa Real dari CSV...');

        $csvPath = database_path('csv/mahasiswa_real.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("❌ File tidak ditemukan: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        $headerFound = false;

        $countImport = 0;
        $countSkip   = 0;
        $countError  = 0;
        $limit       = 100; // Batasi import 100 mahasiswa

        $prodis = Prodi::pluck('id', 'nama_prodi');

        $kelasReguler = ProgramKelas::firstOrCreate(
            ['kode_internal' => 'REG'],
            [
                'nama_program' => 'Reguler Pagi',
                'min_pembayaran_persen' => 25,
                'is_active' => true
            ]
        );
        $kelasRegulerId = $kelasReguler->id;

        $taAktifId = SistemHelper::idTahunAktif(); 

        $progress = new ProgressBar($this->command->getOutput(), $limit);
        $progress->setFormat(" %current%/%max% [%bar%] %percent:3s%% | %message%");
        $progress->start();

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false && $countImport < $limit) {

                // Deteksi header
                if (!$headerFound) {
                    $rowStr = implode(',', $row);
                    if (stripos($rowStr, 'NIM') !== false && stripos($rowStr, 'Nama') !== false) {
                        $headerFound = true;
                    }
                    continue;
                }

                if (count($row) < 5 || empty(trim($row[1]))) {
                    $countSkip++;
                    $progress->setMessage("⚠ [SKIP] Baris kosong / invalid");
                    $progress->advance();
                    continue;
                }

                try {
                    // --- Mapping CSV ---
                    $nim          = $this->cleanString($row[1]);
                    $nik          = $this->cleanNumber($row[2]);
                    $nama         = $this->cleanString($row[3]);
                    $prodiCsv     = $this->cleanString($row[4]);
                    $tglMasuk     = $this->cleanString($row[5]);
                    $angkatan     = (int) $this->cleanNumber($row[6]);
                    $jenisDaftar  = $this->cleanString($row[7]);
                    $biayaMasuk   = $this->cleanNumber($row[8]);
                    $gender       = strtoupper(trim($row[9])) == 'P' ? 'P' : 'L';
                    $ttlRaw       = $this->cleanString($row[10]);
                    $agama        = $this->cleanString($row[11]);
                    $alamat       = $this->cleanString($row[12] ?? '');

                    // Cek NIM duplikat
                    if (Mahasiswa::where('nim', $nim)->exists()) {
                        $countSkip++;
                        $progress->setMessage("⚠ [SKIP] NIM {$nim} sudah ada");
                        $progress->advance();
                        continue;
                    }

                    // Pastikan Tahun Angkatan ada
                    if ($angkatan > 1900) {
                        DB::table('ref_angkatan')->updateOrInsert(
                            ['id_tahun' => $angkatan],
                            ['updated_at' => now(), 'created_at' => now()]
                        );
                    } else {
                        $angkatan = (int) date('Y');
                        DB::table('ref_angkatan')->updateOrInsert(['id_tahun' => $angkatan]);
                    }

                    // Mapping Prodi
                    $prodiId = $prodis[$prodiCsv] ?? null;
                    if (!$prodiId) {
                        $cleanName = trim(str_replace(['S1','D3','D4'], '', $prodiCsv));
                        $prodiDb = Prodi::where('nama_prodi','like',"%$cleanName%")->first();
                        $prodiId = $prodiDb ? $prodiDb->id : Prodi::first()->id ?? 1;
                    }

                    // Parsing TTL
                    $tempatLahir = null;
                    $tglLahir = null;
                    if (str_contains($ttlRaw, ',')) {
                        $parts = explode(',', $ttlRaw);
                        $tempatLahir = trim($parts[0]);
                        try {
                            $dateStr = str_ireplace(
                                ['Mei','Agustus','Oktober','Desember','Januari','Februari','Maret','April','Juni','Juli','September','November'],
                                ['May','August','October','December','January','February','March','April','June','July','September','November'],
                                trim($parts[1] ?? '')
                            );
                            $tglLahir = Carbon::parse($dateStr)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $tglLahir = null;
                        }
                    } elseif (!empty($ttlRaw)) {
                        $tempatLahir = $ttlRaw;
                    }

                    // --- Simpan Person ---
                    if (ModelsPerson::where('nik',$nik)->exists() || empty($nik)) {
                        $nik = rand(1000000000000000, 9999999999999999);
                    }

                    $person = ModelsPerson::firstOrCreate(
                        ['nama_lengkap' => $nama],
                        [
                            'nik' => $nik,
                            'jenis_kelamin' => $gender,
                            'tempat_lahir' => $tempatLahir,
                            'tanggal_lahir' => $tglLahir,
                            'email' => $nim.'@student.unmaris.ac.id',
                            'no_hp' => '081234567890',
                            'updated_at' => now()
                        ]
                    );

                    // --- Simpan Mahasiswa ---
                    $mhs = Mahasiswa::create([
                        'person_id' => $person->id,
                        'nim' => $nim,
                        'angkatan_id' => $angkatan,
                        'prodi_id' => $prodiId,
                        'program_kelas_id' => $kelasRegulerId,
                        'dosen_wali_id' => null,
                        'data_tambahan' => [
                            'agama' => $agama,
                            'alamat_detail' => ['jalan'=>$alamat],
                            'jalur_masuk' => $jenisDaftar,
                            'tgl_masuk' => $tglMasuk
                        ]
                    ]);

                    // --- Buat User Login ---
                    if (!User::where('username',$nim)->exists()) {
                        $user = User::create([
                            'name' => $nama,
                            'username' => $nim,
                            'email' => $person->email,
                            'password' => Hash::make($nim),
                            'role' => 'mahasiswa',
                            'is_active' => true,
                            'person_id' => $person->id
                        ]);
                        $user->assignRole('mahasiswa');
                    }

                    // --- Tagihan Awal ---
                    // if ($biayaMasuk > 0 && $taAktifId) {
                    //     TagihanMahasiswa::create([
                    //         'mahasiswa_id' => $mhs->id,
                    //         'tahun_akademik_id' => $taAktifId,
                    //         'kode_transaksi' => 'INV-AWAL-'.$nim,
                    //         'deskripsi' => 'Biaya Masuk / Daftar Ulang',
                    //         'total_tagihan' => $biayaMasuk,
                    //         'total_bayar' => 0,
                    //         'sisa_tagihan' => $biayaMasuk,
                    //         'status_bayar' => 'BELUM',
                    //         'rincian_item' => [['nama'=>'Biaya Masuk','nominal'=>$biayaMasuk]]
                    //     ]);
                    // }

                    $countImport++;
                    $progress->setMessage("✅ [SUKSES] {$nim} | {$nama} | {$prodiCsv} | Angkatan: {$angkatan}");
                } catch (\Exception $e) {
                    $countError++;
                    $progress->setMessage("❌ [ERROR] Baris {$countImport}: ".$e->getMessage());
                }

                $progress->advance();
            }

            DB::commit();
            $progress->finish();
            $this->command->line("\n🟢 Import selesai. Total SUKSES: {$countImport}, SKIP: {$countSkip}, ERROR: {$countError}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ GAGAL SYSTEM: ".$e->getMessage());
        }

        fclose($file);
    }

    private function cleanNumber($val)
    {
        $val = trim((string)$val);
        if (empty($val) || strtolower($val) == 'null' || $val == '-') return null;
        if (preg_match('/E\+/i', $val)) {
            $val = number_format((float)$val,0,'','');
        }
        $val = preg_replace('/[^0-9]/','',$val);
        return $val === '' ? null : $val;
    }

    private function cleanString($val)
    {
        if (empty($val) || $val == '-') return '';
        return trim(iconv('UTF-8','UTF-8//IGNORE',$val));
    }
}
