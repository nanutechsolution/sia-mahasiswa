<?php

namespace App\Domains\Keuangan\Actions;

use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecalculateInvoiceAction
{
    public function execute(TagihanMahasiswa $tagihan)
    {
        DB::transaction(function () use ($tagihan) {
            // 1. Ambil Data Transaksi
            $totalTagihan = $tagihan->total_tagihan;
            $totalAdjustment = $tagihan->adjustments()->sum('nominal'); // Total Diskon/Beasiswa
            
            // Total Uang Tunai (Cash) Masuk
            $totalBayarCash = $tagihan->pembayarans()
                ->where('status_verifikasi', 'VALID')
                ->sum('nominal_bayar');

            // Total Uang yang SUDAH PERNAH dipindahkan ke Dompet dari tagihan ini
            $totalSudahDiRefund = KeuanganSaldoTransaction::where('referensi_id', $tagihan->kode_transaksi)
                ->where('tipe', 'IN')
                ->sum('nominal');

            // ==========================================
            // 2. Akuntansi Saldo Lebih (Surplus)
            // ==========================================
            // Total Hak = Uang Cash + Beasiswa
            $totalHak = $totalBayarCash + $totalAdjustment;
            $totalLebihBayar = 0;
            
            if ($totalHak > $totalTagihan) {
                $totalLebihBayar = $totalHak - $totalTagihan;
            }

            // Apakah ada surplus baru yang belum dimasukkan ke dompet?
            $refundBaru = $totalLebihBayar - $totalSudahDiRefund;

            if ($refundBaru > 0) {
                // Masukkan Surplus ke Dompet Mahasiswa
                $saldo = KeuanganSaldo::firstOrCreate(
                    ['mahasiswa_id' => $tagihan->mahasiswa_id],
                    ['id' => (string) Str::uuid(), 'saldo' => 0]
                );
                
                $saldo->increment('saldo', $refundBaru);
                $saldo->update(['last_updated_at' => now()]);

                // Catat Log Transaksi Masuk
                KeuanganSaldoTransaction::create([
                    'saldo_id' => $saldo->id,
                    'tipe' => 'IN',
                    'nominal' => $refundBaru,
                    'referensi_id' => $tagihan->kode_transaksi,
                    'keterangan' => 'Deposit otomatis: Kelebihan bayar / Beasiswa'
                ]);

                $totalSudahDiRefund += $refundBaru;
            }

            // ==========================================
            // 3. Update Status Tagihan Akhir
            // ==========================================
            // Maksimal uang cash yang boleh diakui oleh tagihan ini (Mencegah bug UI "Lebih Bayar")
            $maksimalCashDibutuhkan = max(0, $totalTagihan - $totalAdjustment);
            $bayarDiakui = min($totalBayarCash, $maksimalCashDibutuhkan);

            $sisaFinal = $maksimalCashDibutuhkan - $bayarDiakui;

            $status = 'BELUM';
            if ($sisaFinal <= 0) {
                $status = 'LUNAS';
            } elseif ($bayarDiakui > 0) {
                $status = 'CICIL';
            }

            // Simpan posisi terbaru ke tagihan
            $tagihan->update([
                'total_bayar'  => $bayarDiakui, 
                'status_bayar' => $status
            ]);
        });
    }
}