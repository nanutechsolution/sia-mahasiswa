<?php

namespace App\Domains\Akademik\Actions;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use Illuminate\Support\Facades\DB;

class HitungNilaiAkhirAction
{
    /**
     * Eksekusi perhitungan nilai angka ke huruf berdasarkan skala database.
     */
    public function execute(KrsDetail $detail)
    {
        // 1. Hitung Nilai Angka (Contoh Bobot: 30% Tugas, 30% UTS, 40% UAS)
        $angka = ($detail->nilai_tugas * 0.3) + ($detail->nilai_uts * 0.3) + ($detail->nilai_uas * 0.4);
        
        // 2. Cari Huruf & Indeks di Master Skala Nilai
        $skala = SkalaNilai::where('nilai_min', '<=', $angka)
            ->where('nilai_max', '>=', $angka)
            ->first();

        // Fallback jika tidak ditemukan (misal di bawah nilai terkecil)
        $huruf = $skala ? $skala->huruf : 'E';
        $indeks = $skala ? $skala->bobot_indeks : 0.00;

        // 3. Simpan ke database
        $detail->update([
            'nilai_angka' => $angka,
            'nilai_huruf' => $huruf,
            'nilai_indeks' => $indeks
        ]);

        return $detail;
    }

    /**
     * Hitung ulang IPS mahasiswa untuk satu semester tertentu.
     */
    public function hitungIps($krs)
    {
        if (!$krs) return;

        $details = KrsDetail::join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
            ->where('krs_id', $krs->id)
            ->where('is_published', true)
            ->select('krs_detail.*', 'master_mata_kuliahs.sks_default')
            ->get();

        $totalSks = 0;
        $totalMutu = 0;

        foreach ($details as $d) {
            $totalSks += $d->sks_default;
            $totalMutu += ($d->sks_default * $d->nilai_indeks);
        }

        $ips = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        RiwayatStatusMahasiswa::updateOrCreate(
            ['mahasiswa_id' => $krs->mahasiswa_id, 'tahun_akademik_id' => $krs->tahun_akademik_id],
            ['ips' => $ips, 'sks_semester' => $totalSks]
        );
    }
}