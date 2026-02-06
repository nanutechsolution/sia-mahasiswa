<?php
namespace App\Domains\Akademik\Actions;

use App\Domains\Akademik\Models\Krs;

class DetermineStudentActivityMode
{
    public function execute(Krs $krs): string
    {
        // 1. Ambil semua detail KRS
        $details = $krs->details; // Load relasi

        // 2. Cek Prioritas Mode (Hierarchy of Needs)

        // Priority 1: Apakah ada MK MBKM?
        if ($details->contains('activity_type_snapshot', 'MBKM')) {
            return 'MBKM_ACTIVE';
        }

        // Priority 2: Apakah ada Skripsi?
        if ($details->contains('activity_type_snapshot', 'THESIS')) {
            return 'FINAL_YEAR_PROJECT';
        }

        // Priority 3: Apakah hanya MK Continuation?
        if ($details->contains('activity_type_snapshot', 'CONTINUATION')) {
            return 'WAITING_GRADUATION';
        }

        // Default: Kuliah Biasa
        return 'REGULAR_STUDY';
    }
}
