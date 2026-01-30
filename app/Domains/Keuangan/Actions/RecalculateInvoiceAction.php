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
            // 1. Hitung Total Adjustment (Potongan/Beasiswa)
            $totalAdjustment = $tagihan->adjustments()->sum('nominal');

            // 2. Hitung Kewajiban Bersih (Total Awal - Diskon)
            // Asumsi: total_tagihan di DB sudah merupakan hasil akhir, 
            // jadi kita perlu logika untuk menyimpan 'total_asli' jika ingin audit trail sempurna.
            // Untuk simplifikasi saat ini, kita anggap adjustment mengurangi beban sisa.

            // 3. Hitung Total Pembayaran Masuk (Cash)
            $totalBayar = $tagihan->pembayarans()
                ->where('status_verifikasi', 'VALID')
                ->sum('nominal_bayar');

            // 4. Kalkulasi Ulang Sisa
            // Rumus: (Tagihan Awal - Adjustment) - Bayar
            // Namun karena struktur table adjustment terpisah, kita hitung manual
            // Kita asumsikan total_tagihan di database adalah nilai AWAL sebelum diskon.

            $kewajibanBersih = $tagihan->total_tagihan - $totalAdjustment;
            if ($kewajibanBersih < 0) $kewajibanBersih = 0;

            $sisaTagihan = $kewajibanBersih - $totalBayar;
            $status = 'BELUM';

            if ($sisaTagihan <= 0) {
                $status = 'LUNAS';
            } elseif ($totalBayar > 0) {
                $status = 'CICIL';
            }

            // 5. Handle LEBIH BAYAR (Deposit ke Saldo)
            if ($sisaTagihan < 0) {
                $lebihBayar = abs($sisaTagihan); // Jadi positif

                // Masukkan ke Dompet (KeuanganSaldo)
                $saldo = KeuanganSaldo::firstOrCreate(['mahasiswa_id' => $tagihan->mahasiswa_id]);
                $saldo->increment('saldo', $lebihBayar);
                $saldo->update(['last_updated_at' => now()]);

                // Catat Log Mutasi
                KeuanganSaldoTransaction::create([
                    'saldo_id' => $saldo->id,
                    'tipe' => 'IN',
                    'nominal' => $lebihBayar,
                    'referensi_id' => $tagihan->kode_transaksi,
                    'keterangan' => 'Refund otomatis dari kelebihan bayar tagihan'
                ]);

                // Set sisa tagihan jadi 0 (karena kelebihannya sudah dipindah ke dompet)
                $sisaTagihan = 0;
            }

            // 6. Update Tagihan di DB
            $tagihan->update([
                'total_bayar' => $totalBayar, // Refresh total bayar riil
                'sisa_tagihan' => $sisaTagihan,
                'status_bayar' => $status
            ]);
        });
    }
}
