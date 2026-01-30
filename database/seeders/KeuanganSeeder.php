<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Keuangan\Models\KomponenBiaya;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Keuangan\Models\DetailTarif;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Komponen
        $spp = KomponenBiaya::create(['nama_komponen' => 'SPP Tetap', 'tipe_biaya' => 'TETAP']);
        $gedung = KomponenBiaya::create(['nama_komponen' => 'Uang Gedung', 'tipe_biaya' => 'SEKALI']);
        $almamater = KomponenBiaya::create(['nama_komponen' => 'Jas Almamater', 'tipe_biaya' => 'SEKALI']);

        // 2. Skema Tarif Reguler TI 2024
        $prodi = Prodi::where('kode_prodi_internal', 'TI')->first();
        $reguler = ProgramKelas::where('kode_internal', 'REG')->first();

        $skema = SkemaTarif::create([
            'nama_skema' => 'Paket Reguler TI 2024',
            'angkatan_id' => 2024,
            'prodi_id' => $prodi->id,
            'program_kelas_id' => $reguler->id,
        ]);

        DetailTarif::create(['skema_tarif_id' => $skema->id, 'komponen_biaya_id' => $spp->id, 'nominal' => 3000000]);
        DetailTarif::create(['skema_tarif_id' => $skema->id, 'komponen_biaya_id' => $gedung->id, 'nominal' => 5000000, 'berlaku_semester' => 1]);
        DetailTarif::create(['skema_tarif_id' => $skema->id, 'komponen_biaya_id' => $almamater->id, 'nominal' => 250000, 'berlaku_semester' => 1]);
    }
}