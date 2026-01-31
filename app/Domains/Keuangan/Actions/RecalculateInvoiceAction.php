<?php

namespace App\Domains\Keuangan\Actions;

use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction;
use Illuminate\Support\Facades\DB;

class RecalculateInvoiceAction
{
    public function execute(TagihanMahasiswa $tagihan)
    {
        DB::transaction(function () use ($tagihan) {
            // 1. Ambil Data Transaksi
            // Total Diskon/Beasiswa
            $totalAdjustment = $tagihan->adjustments()->sum('nominal');

            // Total Uang Tunai Masuk (Hanya yang VERIFIED)
            $totalBayarCash = $tagihan->pembayarans()
                ->where('status_verifikasi', 'VERIFIED')
                ->sum('nominal_bayar');

            // Total Uang yang SUDAH PERNAH dipindahkan ke Dompet/Saldo dari tagihan ini
            // (Penting agar tidak double entry saat hitung ulang)
            $totalSudahDiRefund = KeuanganSaldoTransaction::where('referensi_id', $tagihan->kode_transaksi)
                ->where('tipe', 'IN')
                ->sum('nominal');

            // 2. Hitung Matematika Murni
            // Tagihan Awal (DB) dianggap Kontrak Awal
            $kewajibanBersih = $tagihan->total_tagihan - $totalAdjustment;
            if ($kewajibanBersih < 0) $kewajibanBersih = 0; // Tidak mungkin minus

            // Posisi Keuangan: (Yang Wajib Dibayar) - (Yang Sudah Dibayar Cash)
            // Jika positif = Kurang Bayar (Hutang)
            // Jika negatif = Lebih Bayar (Piutang Mahasiswa)
            $posisiKeuangan = $kewajibanBersih - $totalBayarCash;

            // 3. Logika Penanganan Saldo (Enterprise Logic)
            // Kita cek: Apakah "Lebih Bayar" ini sudah diamankan ke dompet belum?

            // Rumus: (Posisi Keuangan saat ini) + (Uang yang sudah kita amankan ke dompet)
            // Contoh Kasus:
            // Tagihan 0 (kena beasiswa), Bayar 5jt. Posisi = -5jt.
            // SudahDiRefund = 0.
            // Cek: -5jt + 0 = -5jt. (Artinya masih ada 5jt yang belum masuk dompet).

            $selisihBelumTerproses = $posisiKeuangan + $totalSudahDiRefund;

            // Jika hasilnya NEGATIF, berarti ada uang nganggur yang BELUM masuk dompet
            if ($selisihBelumTerproses < -1) { // Pakai -1 untuk toleransi koma
                $nominalRefundBaru = abs($selisihBelumTerproses);

                // Masukkan ke Dompet
                $saldo = KeuanganSaldo::firstOrCreate(['mahasiswa_id' => $tagihan->mahasiswa_id]);
                $saldo->increment('saldo', $nominalRefundBaru);
                $saldo->update(['last_updated_at' => now()]);

                // Catat Log
                KeuanganSaldoTransaction::create([
                    'saldo_id' => $saldo->id,
                    'tipe' => 'IN',
                    'nominal' => $nominalRefundBaru,
                    'referensi_id' => $tagihan->kode_transaksi,
                    'keterangan' => 'Deposit otomatis: Kelebihan bayar / Beasiswa Susulan'
                ]);

                // Update tracker lokal agar perhitungan status di bawah akurat
                $totalSudahDiRefund += $nominalRefundBaru;
            }

            // 4. Update Status Tagihan Akhir
            // Sisa tagihan di database tidak boleh minus. Jika minus, artinya 0 (Lunas/Lebih).
            // Tapi karena uang lebihnya sudah kita pindah ke dompet, maka sisa riilnya 0.

            $sisaFinal = $posisiKeuangan;

            // Jika posisi keuangan negatif (lebih bayar), dan sudah diamankan ke refund,
            // maka secara administratif sisa tagihan adalah 0 (Lunas).
            if ($sisaFinal < 0) $sisaFinal = 0;

            $status = 'BELUM';
            if ($sisaFinal <= 0) {
                $status = 'LUNAS';
            } elseif ($totalBayarCash > 0) {
                $status = 'CICIL';
            }

            // Simpan perubahan ke Database
            $tagihan->update([
                'total_bayar' => $totalBayarCash, // Catat total cash yang masuk
                'sisa_tagihan' => $sisaFinal,
                'status_bayar' => $status
            ]);
        });
    }
}
