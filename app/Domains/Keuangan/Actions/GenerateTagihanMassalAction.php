<?php

namespace App\Domains\Keuangan\Actions;

use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class GenerateTagihanMassalAction
{
    public function execute($tahunAkademikId, $angkatanId, $prodiId = null)
    {
        // 1. Cari Mahasiswa Target
        $query = Mahasiswa::query()
            ->where('angkatan_id', $angkatanId);
            
        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }
        
        $mahasiswas = $query->get();
        $stats = ['sukses' => 0, 'skip' => 0, 'errors' => []];

        DB::beginTransaction();
        try {
            foreach ($mahasiswas as $mhs) {
                // 2. Ambil Skema Tarif Terbaru
                $skema = SkemaTarif::with('details.komponenBiaya')
                    ->where('angkatan_id', $mhs->angkatan_id)
                    ->where('prodi_id', $mhs->prodi_id)
                    ->where('program_kelas_id', $mhs->program_kelas_id)
                    ->first();

                if (!$skema) {
                    $stats['errors'][] = "Skema tarif tidak ditemukan untuk NIM {$mhs->nim}";
                    continue;
                }

                // 3. LOGIKA DIFFERENTIAL BILLING (Tagihan Selisih)
                
                // A. Hitung apa saja yang SUDAH ditagihkan sebelumnya di semester ini
                $existingTagihans = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $tahunAkademikId)
                    ->get();

                $sudahDitagih = []; // Map: Nama Komponen => Total Nominal Terbit
                
                foreach ($existingTagihans as $inv) {
                    if (is_array($inv->rincian_item)) {
                        foreach ($inv->rincian_item as $item) {
                            $nama = $item['nama'];
                            $nominal = $item['nominal'];
                            if (!isset($sudahDitagih[$nama])) $sudahDitagih[$nama] = 0;
                            $sudahDitagih[$nama] += $nominal;
                        }
                    }
                }

                // B. Bandingkan dengan Skema saat ini
                $invoiceBaruItems = [];
                $totalInvoiceBaru = 0;

                foreach ($skema->details as $detail) {
                    $namaKomponen = $detail->komponenBiaya->nama_komponen;
                    $targetNominal = $detail->nominal;
                    
                    // Berapa yang sudah ditagih untuk komponen ini?
                    $billedNominal = $sudahDitagih[$namaKomponen] ?? 0;

                    // Jika target lebih besar dari yang sudah ditagih, tagihkan selisihnya
                    if ($targetNominal > $billedNominal) {
                        $selisih = $targetNominal - $billedNominal;
                        
                        $invoiceBaruItems[] = [
                            'nama' => $namaKomponen,
                            'nominal' => $selisih
                        ];
                        $totalInvoiceBaru += $selisih;
                    }
                }

                // 4. Jika tidak ada selisih, Skip
                if ($totalInvoiceBaru <= 0) {
                    $stats['skip']++;
                    continue;
                }

                // --- [PERBAIKAN] BUAT DESKRIPSI DINAMIS ---
                // Ambil nama item dari rincian baru
                $itemNames = array_column($invoiceBaruItems, 'nama');
                $deskripsiList = implode(', ', $itemNames);
                
                // Potong jika terlalu panjang
                if (strlen($deskripsiList) > 100) {
                    $deskripsiList = substr($deskripsiList, 0, 97) . '...';
                }
                
                // Tentukan Prefix: "Tagihan Susulan" jika sudah ada tagihan sebelumnya, "Tagihan" jika baru
                $prefix = $existingTagihans->count() > 0 ? "Tagihan Susulan: " : "Tagihan: ";
                $deskripsiFinal = $prefix . $deskripsiList;

                // 5. Buat Invoice
                TagihanMahasiswa::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademikId,
                    'kode_transaksi' => 'INV-' . $tahunAkademikId . '-' . $mhs->nim . '-' . rand(1000, 9999), 
                    'deskripsi' => $deskripsiFinal, // Gunakan deskripsi dinamis
                    'total_tagihan' => $totalInvoiceBaru,
                    'total_bayar' => 0,
                    'status_bayar' => 'BELUM',
                    'rincian_item' => $invoiceBaruItems
                ]);

                $stats['sukses']++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $stats;
    }
}