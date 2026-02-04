<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domains\Core\Models\Prodi;
use Carbon\Carbon;

class LpmModuleSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk Modul Lembaga Penjaminan Mutu (LPM).
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data modul LPM (SPMI & AMI)...');

        // 1. SEEDING MASTER STANDAR MUTU (PPEPP - Penetapan)
        $standars = [
            [
                'kode_standar' => 'STD-AKD-01',
                'nama_standar' => 'Standar Kompetensi Lulusan',
                'kategori' => 'AKADEMIK',
                'pernyataan_standar' => 'Lulusan harus memiliki IPK minimal 3.25 dan masa studi maksimal 4.5 tahun.',
                'target_pencapaian' => 100,
                'satuan' => '%'
            ],
            [
                'kode_standar' => 'STD-SDM-01',
                'nama_standar' => 'Standar Dosen dan Tenaga Kependidikan',
                'kategori' => 'NON-AKADEMIK',
                'pernyataan_standar' => 'Minimal 40% dari total dosen tetap harus berkualifikasi Doktor (S3).',
                'target_pencapaian' => 40,
                'satuan' => '%'
            ],
            [
                'kode_standar' => 'STD-PPM-01',
                'nama_standar' => 'Standar Hasil Penelitian',
                'kategori' => 'AKADEMIK',
                'pernyataan_standar' => 'Setiap dosen tetap wajib mempublikasikan minimal 1 karya ilmiah per tahun di jurnal terakreditasi.',
                'target_pencapaian' => 100,
                'satuan' => '%'
            ],
        ];

        foreach ($standars as $std) {
            DB::table('lpm_standars')->updateOrInsert(
                ['kode_standar' => $std['kode_standar']],
                array_merge($std, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. SEEDING INDIKATOR KINERJA UTAMA (IKU)
        $stdLulusan = DB::table('lpm_standars')->where('kode_standar', 'STD-AKD-01')->first();
        $stdDosen = DB::table('lpm_standars')->where('kode_standar', 'STD-SDM-01')->first();

        $indikators = [
            [
                'standar_id' => $stdLulusan->id,
                'nama_indikator' => 'Rata-rata IPK Lulusan',
                'slug' => 'rata-rata-ipk-lulusan',
                'bobot' => 40.00,
                'sumber_data_siakad' => 'riwayat_status_mahasiswas.ips'
            ],
            [
                'standar_id' => $stdDosen->id,
                'nama_indikator' => 'Persentase Dosen S3',
                'slug' => 'persentase-dosen-s3',
                'bobot' => 30.00,
                'sumber_data_siakad' => 'trx_person_gelar.gelar_id'
            ],
        ];

        foreach ($indikators as $ind) {
            DB::table('lpm_indikators')->updateOrInsert(
                ['slug' => $ind['slug']],
                array_merge($ind, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 3. SEEDING REPOSITORI DOKUMEN MUTU
        $dokumens = [
            [
                'nama_dokumen' => 'Kebijakan SPMI UNMARIS 2024',
                'jenis' => 'KEBIJAKAN',
                'file_path' => 'lpm/docs/kebijakan_spmi_2024.pdf',
                'versi' => '2.1',
                'tgl_berlaku' => '2024-01-01'
            ],
            [
                'nama_dokumen' => 'Manual Penetapan Standar Mutu',
                'jenis' => 'MANUAL',
                'file_path' => 'lpm/docs/manual_penetapan.pdf',
                'versi' => '1.0',
                'tgl_berlaku' => '2024-02-15'
            ],
            [
                'nama_dokumen' => 'Formulir Evaluasi Dosen (EDOM)',
                'jenis' => 'FORMULIR',
                'file_path' => 'lpm/docs/form_edom.docx',
                'versi' => '3.0',
                'tgl_berlaku' => '2025-01-10'
            ],
        ];

        foreach ($dokumens as $dok) {
            DB::table('lpm_dokumens')->updateOrInsert(
                ['nama_dokumen' => $dok['nama_dokumen']],
                array_merge($dok, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 4. SEEDING PERIODE AMI (Audit Mutu Internal)
        $periode = DB::table('lpm_ami_periodes')->updateOrInsert(
            ['nama_periode' => 'AMI Siklus Akademik 2025/2026'],
            [
                'tgl_mulai' => '2026-02-01',
                'tgl_selesai' => '2026-03-31',
                'status' => 'ON-GOING',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        $periodeId = DB::table('lpm_ami_periodes')->where('nama_periode', 'AMI Siklus Akademik 2025/2026')->value('id');

        // 5. SEEDING TEMUAN AUDIT (AMI Findings)
        $prodiTI = Prodi::where('kode_prodi_internal', 'TI')->first();
        
        if ($prodiTI) {
            $findings = [
                [
                    'periode_id' => $periodeId,
                    'prodi_id' => $prodiTI->id,
                    'standar_id' => $stdLulusan->id,
                    'auditor_name' => 'Drs. Auditor Senior, M.Si',
                    'klasifikasi' => 'KTS_MINOR',
                    'deskripsi_temuan' => 'Data tracer study tahun 2024 belum mencapai target responden 80%.',
                    'akar_masalah' => 'Kurangnya koordinasi dengan ikatan alumni.',
                    'rencana_tindak_lanjut' => 'Mengadakan temu alumni per prodi setiap 6 bulan.',
                    'deadline_perbaikan' => Carbon::now()->addDays(30),
                    'is_closed' => false
                ],
                [
                    'periode_id' => $periodeId,
                    'prodi_id' => $prodiTI->id,
                    'standar_id' => $stdDosen->id,
                    'auditor_name' => 'Drs. Auditor Senior, M.Si',
                    'klasifikasi' => 'OB',
                    'deskripsi_temuan' => 'Rasio dosen dan mahasiswa mulai mendekati ambang batas maksimal.',
                    'akar_masalah' => 'Lonjakan mahasiswa baru di prodi TI.',
                    'rencana_tindak_lanjut' => 'Rekrutmen dosen tetap baru di semester ganjil mendatang.',
                    'deadline_perbaikan' => Carbon::now()->addMonths(3),
                    'is_closed' => false
                ]
            ];

            foreach ($findings as $f) {
                DB::table('lpm_ami_findings')->insert(
                    array_merge($f, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        $this->command->info('Seeding LPM selesai! Dashboard sekarang memiliki data visual.');
    }
}