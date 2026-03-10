<?php

namespace App\Imports;

use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\AkademikTranskrip;
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
        // --- PRE-LOAD DATA (PERFORMANCE OPTIMIZATION) ---
        // Load data master ke memori sekali saja untuk menghindari ribuan query ke DB
        $tahunAkademikCache = TahunAkademik::all()->keyBy('kode_tahun');
        $skalaNilaiCache = SkalaNilai::all()->keyBy('huruf');

        // Load Mata Kuliah ke cache (karena jumlahnya biasanya tidak sebanyak mahasiswa)
        $mkCache = MataKuliah::all()->keyBy('kode_mk');

        // Untuk Mahasiswa, kita ambil NIM yang ada di chunk ini saja agar hemat RAM
        $nims = $rows->pluck('nim')->filter()->unique()->toArray();
        $mahasiswaCache = Mahasiswa::whereIn('nim', $nims)->get()->keyBy('nim');

        foreach ($rows as $index => $row) {
            $barisExcel = $index + 2;

            try {
                // 1. Validasi Kolom Wajib
                if (empty($row['nim']) || empty($row['kode_tahun']) || empty($row['kode_mk']) || empty($row['nilai_huruf'])) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Kolom wajib (NIM, Kode Tahun, Kode MK, Nilai Huruf) ada yang kosong."
                    ];
                    continue;
                }

                // 2. Ambil dari Cache
                $mahasiswa = $mahasiswaCache->get($row['nim']);
                $mk = $mkCache->get($row['kode_mk']);
                $tahun = $tahunAkademikCache->get($row['kode_tahun']);
                $skala = $skalaNilaiCache->get(strtoupper($row['nilai_huruf']));

                // 3. Validasi Ketersediaan Master Data
                if (!$mahasiswa) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Mahasiswa dengan NIM {$row['nim']} tidak ditemukan."
                    ];
                    continue;
                }
                if (!$tahun) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Kode Tahun {$row['kode_tahun']} tidak ditemukan."
                    ];
                    continue;
                }
                if (!$mk) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Kode MK {$row['kode_mk']} tidak ditemukan."
                    ];
                    continue;
                }
                if (!$skala) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Huruf Nilai {$row['nilai_huruf']} tidak terdaftar di Skala Nilai."
                    ];
                    continue;
                }

                // 4. Eksekusi Database dengan Transaksi
                DB::transaction(function () use ($mahasiswa, $tahun, $mk, $skala, $row) {

                    // A. Header KRS (Historis)
                    $krs = Krs::firstOrCreate(
                        [
                            'mahasiswa_id' => $mahasiswa->id,
                            'tahun_akademik_id' => $tahun->id,
                        ],
                        [
                            'id' => (string) Str::uuid(),
                            'status_krs' => 'DISETUJUI',
                            'tgl_krs' => now(),
                        ]
                    );

                    // B. Detail Nilai (Harden Snapshot & FK)
                    $detail = KrsDetail::updateOrCreate(
                        [
                            'krs_id' => $krs->id,
                            'mata_kuliah_id' => $mk->id, // Wajib diisi untuk data historis
                        ],
                        [
                            'id' => (string) Str::uuid(),
                            'jadwal_kuliah_id' => null,
                            'kode_mk_snapshot' => $mk->kode_mk,
                            'nama_mk_snapshot' => $mk->nama_mk,
                            'sks_snapshot' => $mk->sks_default,
                            'activity_type_snapshot' => $mk->activity_type ?? 'KULIAH',
                            'status_ambil' => 'B',
                            'nilai_angka' => $row['nilai_angka'] ?? $skala->batas_bawah,
                            'nilai_huruf' => $skala->huruf,
                            'nilai_indeks' => $skala->bobot_indeks,
                            'is_published' => 1,
                            'is_edom_filled' => 1,
                        ]
                    );

                    // C. SINKRONISASI TRANSKRIP (Manual trigger untuk memastikan update transkrip segera)
                    // Cari data transkrip eksis untuk cek logic 'Nilai Terbaik'
                    $existingTranskrip = AkademikTranskrip::where('mahasiswa_id', $mahasiswa->id)
                        ->where('mata_kuliah_id', $mk->id)
                        ->first();

                    if (!$existingTranskrip || (float)$skala->bobot_indeks >= (float)$existingTranskrip->nilai_indeks_final) {
                        AkademikTranskrip::updateOrCreate(
                            [
                                'mahasiswa_id' => $mahasiswa->id,
                                'mata_kuliah_id' => $mk->id,
                            ],
                            [
                                'krs_detail_id' => $detail->id,
                                'sks_diakui' => $mk->sks_default,
                                'nilai_angka_final' => $detail->nilai_angka,
                                'nilai_huruf_final' => $detail->nilai_huruf,
                                'nilai_indeks_final' => $detail->nilai_indeks,
                                'is_konversi' => false,
                            ]
                        );
                    }
                });

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $barisExcel,
                    'message' => "Gagal karena sistem - " . $e->getMessage()
                ];
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
