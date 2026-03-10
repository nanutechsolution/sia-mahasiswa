<?php

namespace App\Imports;

use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class NilaiHistorisImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public $errors = [];
    public $successCount = 0;

    public function collection(Collection $rows)
    {
        // Cache master data di memori untuk mempercepat query dalam loop
        // Ini sangat berguna jika data yang diimport ribuan
        $tahunAkademikCache = TahunAkademik::all()->keyBy('kode_tahun');
        $skalaNilaiCache = SkalaNilai::all()->keyBy('huruf');
        
        // Loop setiap baris di Excel
        foreach ($rows as $index => $row) {
            $barisExcel = $index + 2; // +2 karena index mulai dari 0 dan baris 1 adalah header

            try {
                // 1. Validasi Kolom Wajib
                if (empty($row['nim']) || empty($row['kode_tahun']) || empty($row['kode_mk']) || empty($row['nilai_huruf'])) {
                    $this->errors[] = "Baris $barisExcel: Kolom wajib (NIM, Kode Tahun, Kode MK, Nilai Huruf) ada yang kosong.";
                    continue;
                }

                // 2. Cari Data Master
                $mahasiswa = Mahasiswa::where('nim', $row['nim'])->first();
                $mk = MataKuliah::where('kode_mk', $row['kode_mk'])->first();
                
                $tahun = $tahunAkademikCache->get($row['kode_tahun']);
                $skala = $skalaNilaiCache->get(strtoupper($row['nilai_huruf']));

                // Cek ketersediaan data master
                if (!$mahasiswa) {
                    $this->errors[] = "Baris $barisExcel: Mahasiswa dengan NIM {$row['nim']} tidak ditemukan.";
                    continue;
                }
                if (!$tahun) {
                    $this->errors[] = "Baris $barisExcel: Tahun Akademik {$row['kode_tahun']} tidak ditemukan.";
                    continue;
                }
                if (!$mk) {
                    $this->errors[] = "Baris $barisExcel: Mata Kuliah {$row['kode_mk']} tidak ditemukan.";
                    continue;
                }
                if (!$skala) {
                    $this->errors[] = "Baris $barisExcel: Nilai Huruf {$row['nilai_huruf']} tidak valid/tidak ada di master.";
                    continue;
                }

                // 3. Eksekusi Database menggunakan Transaction
                DB::transaction(function () use ($mahasiswa, $tahun, $mk, $skala, $row) {
                    
                    // A. Buat atau Cari Header KRS
                    // Kita cari berdasarkan mahasiswa dan tahun akademik
                    $krs = Krs::firstOrCreate(
                        [
                            'mahasiswa_id' => $mahasiswa->id,
                            'tahun_akademik_id' => $tahun->id,
                        ],
                        [
                            'id' => Str::uuid()->toString(),
                            'status_krs' => 'DISETUJUI', // Langsung disetujui
                            'tgl_krs' => now(),
                        ]
                    );

                    // B. Masukkan Detail Nilai
                    // Cek dulu apakah MK ini sudah ada di KRS semester ini (mencegah duplikat)
                    $existingDetail = KrsDetail::where('krs_id', $krs->id)
                                             ->where('kode_mk_snapshot', $mk->kode_mk)
                                             ->first();

                    if ($existingDetail) {
                        // Jika sudah ada, update nilainya saja
                        $existingDetail->update([
                            'nilai_angka' => $row['nilai_angka'] ?? 0,
                            'nilai_huruf' => $skala->huruf,
                            'nilai_indeks' => $skala->bobot_indeks,
                            'is_published' => 1,
                        ]);
                    } else {
                        // Jika belum ada, buat baru
                        KrsDetail::create([
                            'krs_id' => $krs->id,
                            'jadwal_kuliah_id' => null, // Data historis tidak butuh jadwal
                            'kode_mk_snapshot' => $mk->kode_mk,
                            'nama_mk_snapshot' => $mk->nama_mk,
                            'sks_snapshot' => $mk->sks_default,
                            'activity_type_snapshot' => $mk->activity_type,
                            'status_ambil' => 'B', // B = Baru, U = Ulang
                            'nilai_angka' => $row['nilai_angka'] ?? 0,
                            'nilai_huruf' => $skala->huruf,
                            'nilai_indeks' => $skala->bobot_indeks,
                            'is_published' => 1, // Langsung muncul di transkrip
                            'is_edom_filled' => 1, // Anggap sudah isi edom agar tidak nyangkut
                        ]);
                    }
                });

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Baris $barisExcel: Terjadi kesalahan sistem - " . $e->getMessage();
            }
        }
    }

    // Membaca per 500 baris agar RAM server tidak penuh jika import 10.000 data
    public function chunkSize(): int
    {
        return 500;
    }
}