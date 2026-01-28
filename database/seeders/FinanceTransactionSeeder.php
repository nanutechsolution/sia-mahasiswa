<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Data Budi (Reguler)
        $budi = DB::table('mahasiswas')->where('nim', '2401001')->first();
        $taId = DB::table('ref_tahun_akademik')->where('is_active', true)->value('id');

        // 1. GENERATE TAGIHAN BUDI (Otomatis ambil tarif Reguler 5jt)
        $tagihanId = Str::uuid();
        DB::table('tagihan_mahasiswas')->insert([
            'id' => $tagihanId,
            'mahasiswa_id' => $budi->id,
            'tahun_akademik_id' => $taId,
            'kode_transaksi' => 'INV-20241-001',
            'deskripsi' => 'SPP Semester Ganjil 2024/2025',
            'total_tagihan' => 5000000,
            'total_bayar' => 5000000, // LUNAS
            'status_bayar' => 'LUNAS',
            'created_at' => now(),
        ]);

        // 2. PEMBAYARAN BUDI (Lunas)
        DB::table('pembayaran_mahasiswas')->insert([
            'id' => Str::uuid(),
            'tagihan_id' => $tagihanId,
            'nominal_bayar' => 5000000,
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'MANUAL',
            'status_verifikasi' => 'VALID', // Sudah diverifikasi Admin
            'verified_at' => now(),
            'created_at' => now(),
        ]);

        // Ambil Data Ani (Ekstensi)
        $ani = DB::table('mahasiswas')->where('nim', '2401002')->first();

        // 3. GENERATE TAGIHAN ANI (Tarif Ekstensi 1.5jt)
        // Ani belum bayar sama sekali
        DB::table('tagihan_mahasiswas')->insert([
            'id' => Str::uuid(),
            'mahasiswa_id' => $ani->id,
            'tahun_akademik_id' => $taId,
            'kode_transaksi' => 'INV-20241-002',
            'deskripsi' => 'SPP Semester Ganjil 2024/2025',
            'total_tagihan' => 1500000,
            'total_bayar' => 0,
            'status_bayar' => 'BELUM', // BELUM LUNAS
            'created_at' => now(),
        ]);
    }
}