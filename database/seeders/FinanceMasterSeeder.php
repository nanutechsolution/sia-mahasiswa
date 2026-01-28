<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Komponen Biaya
        $idSppTetap = DB::table('keuangan_komponen_biaya')->insertGetId([
            'nama_komponen' => 'SPP Tetap Semester',
            'tipe_biaya' => 'TETAP',
            'created_at' => now(),
        ]);
        
        $idSks = DB::table('keuangan_komponen_biaya')->insertGetId([
            'nama_komponen' => 'Biaya SKS',
            'tipe_biaya' => 'SKS',
            'created_at' => now(),
        ]);

        // Ambil ID referensi
        $prodiTi = DB::table('ref_prodi')->where('kode_prodi_internal', 'TI')->value('id');
        $progReg = DB::table('ref_program_kelas')->where('kode_internal', 'REG')->value('id');
        $progEks = DB::table('ref_program_kelas')->where('kode_internal', 'EKS')->value('id');

        // 2. Skema Tarif: REGULER (Mahal di SPP, Gratis SKS)
        $skemaReg = DB::table('keuangan_skema_tarif')->insertGetId([
            'nama_skema' => 'Tarif TI Reguler 2024',
            'angkatan_id' => 2024,
            'prodi_id' => $prodiTi,
            'program_kelas_id' => $progReg,
            'created_at' => now(),
        ]);

        DB::table('keuangan_detail_tarif')->insert([
            ['skema_tarif_id' => $skemaReg, 'komponen_biaya_id' => $idSppTetap, 'nominal' => 5000000, 'created_at' => now()],
            ['skema_tarif_id' => $skemaReg, 'komponen_biaya_id' => $idSks, 'nominal' => 0, 'created_at' => now()], // Paket SKS Gratis
        ]);

        // 3. Skema Tarif: EKSTENSI (Murah di SPP, Bayar per SKS)
        $skemaEks = DB::table('keuangan_skema_tarif')->insertGetId([
            'nama_skema' => 'Tarif TI Ekstensi 2024',
            'angkatan_id' => 2024,
            'prodi_id' => $prodiTi,
            'program_kelas_id' => $progEks,
            'created_at' => now(),
        ]);

        DB::table('keuangan_detail_tarif')->insert([
            ['skema_tarif_id' => $skemaEks, 'komponen_biaya_id' => $idSppTetap, 'nominal' => 1500000, 'created_at' => now()],
            ['skema_tarif_id' => $skemaEks, 'komponen_biaya_id' => $idSks, 'nominal' => 150000, 'created_at' => now()], // 150rb per SKS
        ]);
    }
}