<?php

namespace App\Helpers;

use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Keuangan\Models\DetailTarif;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SistemHelper
{
    /**
     * Ambil ID Tahun Akademik yang sedang aktif (Flag is_active = true)
     * Disimpan di Cache selama 60 menit agar hemat query database.
     */
    public static function idTahunAktif()
    {
        return Cache::remember('ta_aktif_id', 3600, function () {
            $ta = TahunAkademik::where('is_active', true)->first();
            return $ta ? $ta->id : null;
        });
    }

    /**
     * Ambil Object Tahun Akademik Aktif secara lengkap
     */
    public static function getTahunAktif()
    {
        return Cache::remember('ta_aktif_obj', 3600, function () {
            return TahunAkademik::where('is_active', true)->first();
        });
    }

    /**
     * Cek apakah Masa KRS sedang dibuka berdasarkan tanggal
     */
    public static function isMasaKrsOpen(): bool
    {
        $ta = self::getTahunAktif();
        if (!$ta || !$ta->tgl_mulai_krs || !$ta->tgl_selesai_krs) return false;

        $now = now()->startOfDay();

        if (!$ta->buka_krs) return false; // langsung tutup

        return $now->between($ta->tgl_mulai_krs, $ta->tgl_selesai_krs);
    }


    public static function semesterMahasiswa($mahasiswa): int
    {
        // validasi mahasiswa
        if (!$mahasiswa || !$mahasiswa->angkatan_id) {
            return 1;
        }

        $ta = self::getTahunAktif();
        if (!$ta || !$ta->kode_tahun) {
            return 1;
        }

        // Ambil tahun awal: 2026/2027 → 2026
        $tahunAktif = (int) substr($ta->kode_tahun, 0, 4);
        $tahunMasuk = (int) $mahasiswa->angkatan_id;

        // Semester pendek TIDAK menaikkan semester
        $semesterTA = $ta->semester == 3 ? 2 : $ta->semester;

        return max(1, (($tahunAktif - $tahunMasuk) * 2) + $semesterTA);
    }



    /**
     * Dapatkan path logo kop surat untuk PDF
     */
    public static function getKopLogoPath()
    {
        // Ganti dengan path logo yang sesuai di sistem Anda
        return public_path('logo.png');
    }
}
