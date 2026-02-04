<?php

namespace App\Domains\Akademik\Actions;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use Illuminate\Support\Facades\DB;

/**
 * Class HitungNilaiAkhirAction
 * Bertanggung jawab untuk kalkulasi Nilai Akhir (NA) dan Indeks Prestasi Semester (IPS).
 */
class HitungNilaiAkhirAction
{
    /**
     * Mengeksekusi perhitungan Nilai Akhir (NA) berdasarkan bobot dinamis kurikulum.
     * * @param KrsDetail $detail
     * @return KrsDetail
     */
    public function execute(KrsDetail $detail)
    {
        // 1. Identifikasi Kurikulum yang digunakan matakuliah tersebut
        $kurikulumId = DB::table('kurikulum_mata_kuliah')
            ->where('mata_kuliah_id', $detail->jadwalKuliah->mata_kuliah_id)
            ->value('kurikulum_id');

        if (!$kurikulumId) {
            // Jika kurikulum tidak ditemukan, kita tidak bisa menghitung bobot secara otomatis
            return $detail;
        }

        // 2. Ambil Definisi Bobot (Konfigurasi Admin: Aktif 10%, Tugas 15%, dll)
        $configs = DB::table('kurikulum_komponen_nilai')
            ->where('kurikulum_id', $kurikulumId)
            ->get();

        if ($configs->isEmpty()) {
            // Jika admin belum mengatur bobot untuk kurikulum ini
            return $detail;
        }

        // 3. Ambil Nilai Rill Mahasiswa yang diinput dosen (dari tabel krs_detail_nilai)
        $nilaiInputs = DB::table('krs_detail_nilai')
            ->where('krs_detail_id', $detail->id)
            ->pluck('nilai_angka', 'komponen_id');

        $totalNilaiAngka = 0;

        // 4. Kalkulasi: (Nilai Mentah * (Bobot / 100))
        foreach ($configs as $config) {
            $nilaiMhs = $nilaiInputs[$config->komponen_id] ?? 0;
            $totalNilaiAngka += ($nilaiMhs * ($config->bobot_persen / 100));
        }

        // 5. Konversi Nilai Angka ke Huruf & Indeks (SSOT dari Master Skala Nilai)
        // Contoh: 85 -> A (4.00), 70 -> B (3.00)
        $skala = SkalaNilai::where('nilai_min', '<=', $totalNilaiAngka)
            ->where('nilai_max', '>=', $totalNilaiAngka)
            ->first();

        // Fallback jika tidak ditemukan skala yang cocok (default E)
        $huruf = $skala ? $skala->huruf : 'E';
        $indeks = $skala ? $skala->bobot_indeks : 0.00;

        // 6. Update ringkasan nilai ke tabel krs_detail
        $detail->update([
            'nilai_angka' => $totalNilaiAngka,
            'nilai_huruf' => $huruf,
            'nilai_indeks' => $indeks
        ]);

        return $detail;
    }

    /**
     * Menghitung ulang Indeks Prestasi Semester (IPS) mahasiswa.
     * Dipanggil setelah Dosen mempublikasikan seluruh nilai (Publish).
     * * @param mixed $krs
     * @return void
     */
    public function hitungIps($krs)
    {
        if (!$krs) return;

        // Ambil semua detail KRS yang sudah dipublikasikan di semester ini
        // Kita join ke master matakuliah untuk mendapatkan bobot SKS-nya
        $details = KrsDetail::join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
            ->where('krs_id', $krs->id)
            ->where('is_published', true)
            ->select('krs_detail.*', 'master_mata_kuliahs.sks_default')
            ->get();

        $totalSks = 0;
        $totalMutu = 0;

        foreach ($details as $d) {
            $sks = (int) ($d->sks_default ?? 0);
            $totalSks += $sks;
            
            // Mutu per matakuliah = SKS * Bobot Indeks (misal: 3 SKS * 4.00 = 12.0)
            $totalMutu += ($sks * $d->nilai_indeks);
        }

        // IPS = Total Mutu / Total SKS
        $ips = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        // Simpan ke Riwayat Status Mahasiswa (Tabel SSOT Keaktifan Semester)
        RiwayatStatusMahasiswa::updateOrCreate(
            [
                'mahasiswa_id'    => $krs->mahasiswa_id, 
                'tahun_akademik_id' => $krs->tahun_akademik_id
            ],
            [
                'ips'           => $ips,
                'sks_semester'  => $totalSks,
                'status_kuliah' => 'A', // Otomatis set Aktif jika nilai dipublish
                'updated_at'    => now()
            ]
        );
    }
}