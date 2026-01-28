<?php

namespace App\Domains\Akademik\Actions;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;

class HitungNilaiAkhirAction
{
    /**
     * Hitung Nilai Akhir satu mata kuliah
     */
    public function execute(KrsDetail $detail)
    {
        // 1. Hitung Nilai Angka (Bobot: Tugas 30%, UTS 30%, UAS 40%)
        // Gunakan nilai 0 jika null agar kalkulasi tidak error
        $tugas = $detail->nilai_tugas ?? 0;
        $uts   = $detail->nilai_uts ?? 0;
        $uas   = $detail->nilai_uas ?? 0;

        $angka = ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);

        // 2. Konversi ke Huruf & Indeks (Skala 4.0)
        if ($angka >= 85)     { $huruf = 'A';  $indeks = 4.00; }
        elseif ($angka >= 80) { $huruf = 'A-'; $indeks = 3.75; }
        elseif ($angka >= 75) { $huruf = 'B+'; $indeks = 3.50; }
        elseif ($angka >= 70) { $huruf = 'B';  $indeks = 3.00; }
        elseif ($angka >= 65) { $huruf = 'B-'; $indeks = 2.75; }
        elseif ($angka >= 60) { $huruf = 'C+'; $indeks = 2.50; }
        elseif ($angka >= 55) { $huruf = 'C';  $indeks = 2.00; }
        elseif ($angka >= 40) { $huruf = 'D';  $indeks = 1.00; }
        else                  { $huruf = 'E';  $indeks = 0.00; }

        // 3. Simpan
        $detail->update([
            'nilai_angka'  => $angka,
            'nilai_huruf'  => $huruf,
            'nilai_indeks' => $indeks
        ]);

        return $detail;
    }

    /**
     * Hitung IPS (Indeks Prestasi Semester) Mahasiswa
     */
    public function hitungIps(Krs $krs)
    {
        // Refresh relasi untuk mendapatkan data nilai terbaru
        $krs->load('details.jadwalKuliah.mataKuliah');
        
        $details = $krs->details;

        $totalSks = 0;
        $totalMutu = 0; // SKS * Indeks

        foreach ($details as $mk) {
            // Skip jika mata kuliah atau jadwal terhapus
            if (!$mk->jadwalKuliah || !$mk->jadwalKuliah->mataKuliah) continue;

            $sks = $mk->jadwalKuliah->mataKuliah->sks_default;
            
            $totalSks += $sks;
            $totalMutu += ($sks * $mk->nilai_indeks);
        }

        $ips = ($totalSks > 0) ? ($totalMutu / $totalSks) : 0;

        // Update Riwayat Status (Feeder Requirement)
        RiwayatStatusMahasiswa::updateOrCreate(
            [
                'mahasiswa_id' => $krs->mahasiswa_id, 
                'tahun_akademik_id' => $krs->tahun_akademik_id
            ],
            [
                'status_kuliah' => 'A', // Aktif
                'sks_semester' => $totalSks,
                'ips' => $ips,
                // Pastikan nilai default agar tidak error SQL
                'sks_total' => $totalSks, // Sementara samakan dulu
                'ipk' => $ips, // Sementara samakan dulu
            ]
        );
    }
}