<?php

namespace App\Domains\Keuangan\Actions;

use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerateTagihanMassalAction
{
    /**
     * Generate tagihan untuk satu angkatan/prodi tertentu.
     * Menggunakan logika Differential Billing (Hanya tagih selisih jika ada kenaikan).
     */
    public function execute($tahunAkademikId, $angkatanId, $prodiId = null)
    {
        // 1. Cari Mahasiswa Target (Aktif)
        $query = Mahasiswa::query()
            ->where('angkatan_id', $angkatanId);

        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        // Eager load untuk performa
        $mahasiswas = $query->with(['programKelas', 'prodi'])->get();

        $stats = ['sukses' => 0, 'skip' => 0, 'errors' => []];
        $userId = Auth::id();
        DB::beginTransaction();
        try {
            foreach ($mahasiswas as $mhs) {
                // 2. Ambil Skema Tarif Terbaru yang Cocok
                $skema = SkemaTarif::with('details.komponenBiaya')
                    ->where('angkatan_id', $mhs->angkatan_id)
                    ->where('prodi_id', $mhs->prodi_id)
                    ->where('program_kelas_id', $mhs->program_kelas_id)
                    ->first();

                if (!$skema) {
                    // Jangan error, catat saja dan lanjut ke mhs berikutnya
                    $stats['errors'][] = "Skema tarif tidak ditemukan untuk NIM {$mhs->nim} (Prodi: {$mhs->prodi->nama_prodi})";
                    continue;
                }

                // 3. HITUNG TOTAL TARGET (Berapa seharusnya dia bayar semester ini?)
                $totalTarget = 0;
                $rincianTarget = [];

                foreach ($skema->details as $detail) {
                    $totalTarget += $detail->nominal;
                    $rincianTarget[$detail->komponenBiaya->nama_komponen] = $detail->nominal;
                }

                // 4. CEK TOTAL EKSISTING (Berapa yang SUDAH ditagihkan sebelumnya?)
                $existingTagihans = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $tahunAkademikId)
                    ->get();

                $totalSudahDitagih = $existingTagihans->sum('total_tagihan');

                // 5. HITUNG KEKURANGAN (Selisih Global)
                $kekuranganBayar = $totalTarget - $totalSudahDitagih;

                // Jika kekurangan <= 0, artinya sudah sesuai/lunas target -> SKIP
                if ($kekuranganBayar <= 0) {
                    $stats['skip']++;
                    continue;
                }

                // 6. SUSUN RINCIAN TAGIHAN BARU (Smart Detail)
                // Kita coba deteksi komponen apa yang belum tertagih
                $rincianBaru = [];
                $tempTotal = 0;

                // Map apa saja yang sudah pernah ditagih
                $komponenExisting = [];
                foreach ($existingTagihans as $inv) {
                    if (is_array($inv->rincian_item)) {
                        foreach ($inv->rincian_item as $item) {
                            $nama = $item['nama'];
                            $komponenExisting[$nama] = ($komponenExisting[$nama] ?? 0) + $item['nominal'];
                        }
                    }
                }

                // Jika tidak ada data rincian lama (kasus migrasi/seeder), anggap "Penyesuaian Biaya"
                if ($totalSudahDitagih > 0 && empty($komponenExisting)) {
                    $rincianBaru[] = [
                        'nama' => 'Penyesuaian / Kekurangan Biaya',
                        'nominal' => $kekuranganBayar
                    ];
                } else {
                    // Bandingkan per komponen
                    foreach ($rincianTarget as $namaKomponen => $nominalTarget) {
                        $sudah = $komponenExisting[$namaKomponen] ?? 0;

                        if ($nominalTarget > $sudah) {
                            $selisihItem = $nominalTarget - $sudah;

                            // Safety Cap: Jangan sampai item melebihi total kekurangan global
                            if (($tempTotal + $selisihItem) > $kekuranganBayar) {
                                $selisihItem = $kekuranganBayar - $tempTotal;
                            }

                            if ($selisihItem > 0) {
                                $rincianBaru[] = [
                                    'nama' => $namaKomponen,
                                    'nominal' => $selisihItem
                                ];
                                $tempTotal += $selisihItem;
                            }
                        }
                    }

                    // Fallback jika loop komponen gagal memenuhi kuota selisih
                    if ($tempTotal < $kekuranganBayar) {
                        $sisa = $kekuranganBayar - $tempTotal;
                        $rincianBaru[] = ['nama' => 'Biaya Tambahan Lainnya', 'nominal' => $sisa];
                    }
                }

                // Buat Deskripsi Dinamis
                $descNames = implode(', ', array_column($rincianBaru, 'nama'));
                if (strlen($descNames) > 50) $descNames = substr($descNames, 0, 47) . '...';
                $deskripsiFinal = ($totalSudahDitagih > 0 ? "Tagihan Susulan: " : "Tagihan: ") . $descNames;

                // 7. BUAT INVOICE
                TagihanMahasiswa::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademikId,
                    'kode_transaksi' => 'INV-' . $tahunAkademikId . '-' . $mhs->nim . '-' . rand(1000, 9999),
                    'deskripsi' => $deskripsiFinal,
                    'total_tagihan' => $kekuranganBayar,
                    'total_bayar' => 0,
                    'status_bayar' => 'BELUM',
                    'rincian_item' => $rincianBaru,
                    'created_by' => $userId
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
