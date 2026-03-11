<?php

namespace App\Imports;

use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TagihanHistorisImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public $errors = [];
    public $successCount = 0;

    public function collection(Collection $rows)
    {
        // --- PRE-LOAD DATA (PERFORMANCE OPTIMIZATION) ---
        $tahunAkademikCache = TahunAkademik::all()->keyBy('kode_tahun');
        
        // Caching mahasiswa berdasarkan NIM yang ada di chunk ini saja
        $nims = $rows->pluck('nim')->filter()->unique()->toArray();
        $mahasiswaCache = Mahasiswa::whereIn('nim', $nims)->get()->keyBy('nim');
        
        $adminId = Auth::id();

        foreach ($rows as $index => $row) {
            $barisExcel = $index + 2; 

            try {
                // 1. Validasi Kolom Wajib
                if (empty($row['nim']) || empty($row['deskripsi']) || !isset($row['total_tagihan'])) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Kolom wajib (NIM, Deskripsi, Total Tagihan) ada yang kosong."
                    ];
                    continue;
                }

                // 2. Ambil dari Cache
                $mahasiswa = $mahasiswaCache->get($row['nim']);
                $tahun = !empty($row['kode_tahun']) ? $tahunAkademikCache->get($row['kode_tahun']) : null;

                // 3. Validasi Ketersediaan Master Data
                if (!$mahasiswa) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Mahasiswa dengan NIM {$row['nim']} tidak ditemukan."
                    ];
                    continue;
                }

                if (!empty($row['kode_tahun']) && !$tahun) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Kode Tahun {$row['kode_tahun']} tidak ditemukan di sistem."
                    ];
                    continue;
                }

                // Validasi Angka
                $totalTagihan = (float) $row['total_tagihan'];
                if ($totalTagihan <= 0) {
                    $this->errors[] = [
                        'row' => $barisExcel,
                        'message' => "Total tagihan harus lebih besar dari 0."
                    ];
                    continue;
                }

                // Generate Kode Transaksi Unik
                // Format: INV-LEGACY-{NIM}-{RANDOM}
                $kodeTransaksi = 'INV-LEGACY-' . $mahasiswa->nim . '-' . strtoupper(Str::random(4));
                // Jika di excel ada kode transaksi custom, gunakan itu
                if (!empty($row['kode_transaksi'])) {
                    $kodeTransaksi = trim($row['kode_transaksi']);
                }

                // 4. Eksekusi Database
                DB::transaction(function () use ($mahasiswa, $tahun, $row, $totalTagihan, $kodeTransaksi, $adminId) {
                    
                    TagihanMahasiswa::create([
                        'id' => (string) Str::uuid(),
                        'mahasiswa_id' => $mahasiswa->id,
                        'tahun_akademik_id' => $tahun ? $tahun->id : null,
                        'kode_transaksi' => $kodeTransaksi,
                        'deskripsi' => trim($row['deskripsi']),
                        'total_tagihan' => $totalTagihan,
                        'total_bayar' => 0, // Bypass Generated Column sisa_tagihan otomatis ngikut
                        'status_bayar' => 'BELUM',
                        'created_by' => $adminId,
                        'rincian_item' => json_encode([
                            'Tunggakan Historis Pra-SIAKAD' => $totalTagihan
                        ]),
                        'tenggat_waktu' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                });

                $this->successCount++;

            } catch (\Exception $e) {
                // Tangkap jika ada duplicate entry kode_transaksi
                $msg = str_contains($e->getMessage(), 'Duplicate entry') 
                    ? "Kode Transaksi {$row['kode_transaksi']} sudah pernah diinput." 
                    : $e->getMessage();

                $this->errors[] = [
                    'row' => $barisExcel,
                    'message' => "Gagal: " . $msg
                ];
            }
        }
    }

    public function chunkSize(): int
    {
        return 200; // Chunk lebih kecil agar RAM tidak bocor
    }
}