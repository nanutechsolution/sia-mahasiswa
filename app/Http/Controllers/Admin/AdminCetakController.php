<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;

class AdminCetakController extends Controller
{
    public function cetakAbsensi($jadwalId)
    {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen', 'tahunAkademik'])->findOrFail($jadwalId);

        // Ambil mahasiswa yang KRS-nya DISETUJUI
        $peserta = KrsDetail::where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', function($q) {
                $q->where('status_krs', 'DISETUJUI');
            })
            ->with(['krs.mahasiswa'])
            ->get()
            ->sortBy('krs.mahasiswa.nim'); // Urutkan by NIM

        $pdf = Pdf::loadView('pdf.cetak-absensi', [
            'jadwal' => $jadwal,
            'peserta' => $peserta
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Absensi-' . $jadwal->mataKuliah->kode_mk . '-' . $jadwal->nama_kelas . '.pdf');
    }
}