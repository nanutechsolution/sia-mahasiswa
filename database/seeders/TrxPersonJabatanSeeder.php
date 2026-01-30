<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrxPersonJabatanSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Ambil ID master
         * (biasakan pakai query, bukan hardcode)
         */
        $dekan   = DB::table('ref_jabatan')->where('kode_jabatan', 'DEKAN')->value('id');
        $kaprodi = DB::table('ref_jabatan')->where('kode_jabatan', 'KAPRODI')->value('id');
        $lektor  = DB::table('ref_jabatan')->where('kode_jabatan', 'LEKTOR')->value('id');

        $ft = DB::table('ref_fakultas')->where('kode_fakultas', 'FT')->value('id');
        $ti = DB::table('ref_prodi')->where('kode_prodi_internal', 'TI')->value('id');

        /**
         * Ambil person
         */
        $dekanId = DB::table('ref_jabatan')
            ->where('kode_jabatan', 'DEKAN')
            ->value('id');

        $kaprodiId = DB::table('ref_jabatan')
            ->where('kode_jabatan', 'KAPRODI')
            ->value('id');

        $dekanPersonId = DB::table('ref_person')
            ->where('nama_lengkap', 'Dr. Andi Wijaya')
            ->value('id');

        $kaprodiPersonId = DB::table('ref_person')
            ->where('nama_lengkap', 'Prof. Budi Santoso')
            ->value('id');

        /**
         * Insert jabatan
         */
        DB::table('trx_person_jabatan')->insert([
            [
                'person_id' => $dekanPersonId,
                'jabatan_id' => $dekanId,
                'fakultas_id' => 1,
                'prodi_id' => null,
                'tanggal_mulai' => '2024-01-01',
                'tanggal_selesai' => null,
                'created_at' => now(),
            ],
            [
                'person_id' => $kaprodiPersonId,
                'jabatan_id' => $kaprodiId,
                'fakultas_id' => 1,
                'prodi_id' => 3,
                'tanggal_mulai' => '2023-08-01',
                'tanggal_selesai' => null,
                'created_at' => now(),
            ],
        ]);
    }
}
