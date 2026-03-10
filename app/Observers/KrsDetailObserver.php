<?php

namespace App\Observers;

use App\Domains\Akademik\Models\KrsDetail;
use App\Models\AkademikTranskrip;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KrsDetailObserver
{
    /**
     * Handle the KrsDetail "saved" event (Mencakup Created & Updated).
     */
    public function saved(KrsDetail $krsDetail): void
    {
        $this->syncTranskrip($krsDetail);
    }

    /**
     * Custom method untuk memproses pembaruan Transkrip (Materialized View)
     */
    private function syncTranskrip(KrsDetail $krsDetail): void
    {
        // 1. Validasi: Hanya proses jika statusnya sudah dipublikasi
        if (!$krsDetail->is_published) {
            return;
        }

        // 2. Ambil Mahasiswa ID
        // Cek objek relasi dulu, jika gagal gunakan Query Builder (lebih aman untuk background process)
        $mahasiswaId = $krsDetail->krs->mahasiswa_id ?? DB::table('krs')->where('id', $krsDetail->krs_id)->value('mahasiswa_id');
        
        // 3. Ambil Mata Kuliah ID
        // Cek kolom mata_kuliah_id dulu (pastikan sudah masuk $fillable), jika null cek ke tabel jadwal
        $mkId = $krsDetail->mata_kuliah_id;
        if (!$mkId && $krsDetail->jadwal_kuliah_id) {
            $mkId = DB::table('jadwal_kuliah')->where('id', $krsDetail->jadwal_kuliah_id)->value('mata_kuliah_id');
        }

        // Jika data krusial tetap tidak ditemukan, hentikan proses dan catat di log
        if (!$mkId || !$mahasiswaId) {
            Log::warning("Transkrip Gagal Sinkron: Metadata tidak lengkap.", [
                'krs_detail_id' => $krsDetail->id,
                'mk_id' => $mkId,
                'mhs_id' => $mahasiswaId
            ]);
            return;
        }

        // 4. Ambil data transkrip yang sudah ada (untuk cek retake)
        $existing = AkademikTranskrip::where('mahasiswa_id', $mahasiswaId)
            ->where('mata_kuliah_id', $mkId)
            ->first();

        /**
         * LOGIKA PEMBARUAN TRANSKRIP:
         * Simpan/Update jika:
         * 1. Belum pernah mengambil MK ini (tidak ada di transkrip)
         * 2. Nilai indeks yang baru LEBIH TINGGI atau SAMA (Retake/Perbaikan Nilai)
         */
        if (!$existing || (float)$krsDetail->nilai_indeks >= (float)$existing->nilai_indeks_final) {
            try {
                AkademikTranskrip::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'mata_kuliah_id' => $mkId,
                    ],
                    [
                        'krs_detail_id'      => $krsDetail->id,
                        'sks_diakui'         => $krsDetail->sks_snapshot,
                        'nilai_angka_final'  => $krsDetail->nilai_angka,
                        'nilai_huruf_final'  => $krsDetail->nilai_huruf,
                        'nilai_indeks_final' => $krsDetail->nilai_indeks,
                        'is_konversi'        => false,
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Database Error pada Tabel Transkrip: " . $e->getMessage());
            }
        }
    }
}