<?php

namespace App\Domains\Akademik\Actions;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Models\AkademikTranskrip;
use Illuminate\Support\Facades\DB;

/**
 * Class HitungNilaiAkhirAction
 * Bertanggung jawab untuk kalkulasi Nilai Akhir (NA) dan Indeks Prestasi (IPS/IPK).
 */
class HitungNilaiAkhirAction
{
    /**
     * Mengeksekusi perhitungan Nilai Akhir (NA) berdasarkan bobot dinamis Kontrak Kuliah Dosen.
     * * @param KrsDetail $detail
     * @return KrsDetail
     */
    public function execute(KrsDetail $detail)
    {
        // 1. Data historis/konversi yang tidak memiliki jadwal di-skip kalkulasi komponennya.
        // Nilai akhir biasanya diinput manual secara langsung.
        if (!$detail->jadwal_kuliah_id) {
            return $detail;
        }

        // 2. Ambil Definisi Bobot dari KONTRAK KULIAH DOSEN (Tabel jadwal_komponen_nilai)
        $configs = DB::table('jadwal_komponen_nilai')
            ->where('jadwal_kuliah_id', $detail->jadwal_kuliah_id)
            ->get();

        // 3. Fallback ke Template Kurikulum jika dosen belum melakukan sinkronisasi
        if ($configs->isEmpty()) {
            // Ambil kurikulum_id dari jadwal
            $kurikulumId = DB::table('jadwal_kuliah')
                ->where('id', $detail->jadwal_kuliah_id)
                ->value('kurikulum_id');

            if ($kurikulumId) {
                $configs = DB::table('kurikulum_komponen_nilai')
                    ->where('kurikulum_id', $kurikulumId)
                    ->get();
            }
        }

        if ($configs->isEmpty()) {
            return $detail; // Tidak ada konfigurasi bobot, lewati perhitungan
        }

        // 4. Ambil Nilai Rill Mahasiswa yang diinput dosen per komponen
        $nilaiInputs = DB::table('krs_detail_nilai')
            ->where('krs_detail_id', $detail->id)
            ->pluck('nilai_angka', 'komponen_id');

        $totalNilaiAngka = 0;

        // 5. Kalkulasi: (Nilai Mentah * (Bobot % / 100))
        foreach ($configs as $config) {
            $nilaiMhs = $nilaiInputs[$config->komponen_id] ?? 0;
            $totalNilaiAngka += ($nilaiMhs * ($config->bobot_persen / 100));
        }

        // 6. Konversi Nilai Angka ke Huruf & Indeks (SSOT dari Master Skala Nilai)
        $skala = SkalaNilai::where('nilai_min', '<=', $totalNilaiAngka)
            ->where('nilai_max', '>=', $totalNilaiAngka)
            ->first();

        $huruf = $skala ? $skala->huruf : 'E';
        $indeks = $skala ? $skala->bobot_indeks : 0.00;

        // 7. Update ringkasan nilai ke tabel krs_detail
        $detail->update([
            'nilai_angka' => $totalNilaiAngka,
            'nilai_huruf' => $huruf,
            'nilai_indeks' => $indeks
        ]);

        return $detail;
    }

    /**
     * Menghitung ulang Indeks Prestasi Semester (IPS) & Kumulatif (IPK) Mahasiswa.
     * Dipanggil secara otomatis saat Dosen mem-publish nilai, atau sinkronisasi background.
     * * @param mixed $krs
     * @return void
     */
    public function hitungIps($krs)
    {
        if (!$krs) return;

        // ==========================================
        // A. HITUNG INDEKS PRESTASI SEMESTER (IPS)
        // ==========================================
        // PERBAIKAN: Gunakan sks_snapshot secara langsung. Tidak perlu JOIN ke tabel jadwal/master_mk.
        $detailsSemester = KrsDetail::where('krs_id', $krs->id)
            ->where('is_published', true)
            ->get();

        $totalSksSemester = 0;
        $totalMutuSemester = 0;

        foreach ($detailsSemester as $d) {
            $sks = (int) ($d->sks_snapshot ?? 0);
            $totalSksSemester += $sks;
            $totalMutuSemester += ($sks * $d->nilai_indeks);
        }

        $ips = $totalSksSemester > 0 ? round($totalMutuSemester / $totalSksSemester, 2) : 0;

        // ==========================================
        // B. HITUNG INDEKS PRESTASI KUMULATIF (IPK)
        // ==========================================
        // PERBAIKAN: Tarik data cepat dari Materialized View Transkrip 
        // (Tabel ini sudah otomatis hanya menyimpan nilai terbaik)
        $transkrip = AkademikTranskrip::where('mahasiswa_id', $krs->mahasiswa_id)->get();
        $totalSksKumulatif = $transkrip->sum('sks_diakui');
        $totalMutuKumulatif = $transkrip->sum(fn($i) => $i->sks_diakui * $i->nilai_indeks_final);

        $ipk = $totalSksKumulatif > 0 ? round($totalMutuKumulatif / $totalSksKumulatif, 2) : 0;

        // ==========================================
        // C. SIMPAN KE RECORD SSOT MAHASISWA
        // ==========================================
        RiwayatStatusMahasiswa::updateOrCreate(
            [
                'mahasiswa_id'      => $krs->mahasiswa_id,
                'tahun_akademik_id' => $krs->tahun_akademik_id
            ],
            [
                'ips'           => $ips,
                'ipk'           => $ipk, // Sinkronisasi otomatis IPK terbaru
                'sks_semester'  => $totalSksSemester,
                'sks_total'     => $totalSksKumulatif,
                'status_kuliah' => 'A', // Otomatis set Aktif jika nilai dipublish
                'updated_at'    => now()
            ]
        );
    }
}
