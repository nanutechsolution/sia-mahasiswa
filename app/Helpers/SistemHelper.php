<?php

namespace App\Helpers;

use App\Domains\Core\Models\TahunAkademik;
use Illuminate\Support\Facades\Cache;

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
    public static function isMasaKrsOpen()
    {
        $ta = self::getTahunAktif();

        // Jika tidak ada semester aktif, atau tanggal belum diset -> Tutup
        if (!$ta || !$ta->tgl_mulai_krs || !$ta->tgl_selesai_krs) {
            return false;
        }

        return now()->between($ta->tgl_mulai_krs, $ta->tgl_selesai_krs);
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
