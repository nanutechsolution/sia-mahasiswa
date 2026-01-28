<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;

class CetakController extends Controller
{
    public function cetakKrs()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi', 'programKelas'])->where('user_id', $user->id)->firstOrFail();
        
        // Ambil KRS Semester Ini (Hardcode ID 1 untuk MVP)
        $krs = Krs::with(['details.jadwalKuliah.mataKuliah', 'details.jadwalKuliah.dosen', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', 1)
            ->firstOrFail();

        // Load View khusus PDF
        $pdf = Pdf::loadView('pdf.cetak-krs', [
            'mahasiswa' => $mahasiswa,
            'krs' => $krs,
            'details' => $krs->details
        ]);

        // Stream (Tampilkan di browser) atau Download
        return $pdf->stream('KRS-' . $mahasiswa->nim . '.pdf');
    }

    public function cetakKhs()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi'])->where('user_id', $user->id)->firstOrFail();
        
        $krs = Krs::with(['details.jadwalKuliah.mataKuliah', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', 1)
            ->firstOrFail();

        // Filter hanya yang sudah dipublish
        $details = $krs->details->where('is_published', true);

        $pdf = Pdf::loadView('pdf.cetak-khs', [
            'mahasiswa' => $mahasiswa,
            'krs' => $krs,
            'details' => $details
        ]);

        return $pdf->stream('KHS-' . $mahasiswa->nim . '.pdf');
    }


     public function cetakTranskrip()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi', 'programKelas'])->where('user_id', $user->id)->firstOrFail();

        // Logic Query sama dengan Livewire TranskripPage
        $riwayatBelajar = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
            ->join('ref_tahun_akademik', 'krs.tahun_akademik_id', '=', 'ref_tahun_akademik.id')
            ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
            ->where('krs.mahasiswa_id', $mahasiswa->id)
            ->where('krs_detail.is_published', true)
            ->select(
                'krs_detail.*',
                'ref_tahun_akademik.nama_tahun as nama_semester',
                'ref_tahun_akademik.kode_tahun',
                'master_mata_kuliahs.kode_mk',
                'master_mata_kuliahs.nama_mk',
                'master_mata_kuliahs.sks_default'
            )
            ->orderBy('ref_tahun_akademik.kode_tahun', 'asc')
            ->get();

        // Hitung IPK
        $totalSks = 0;
        $totalMutu = 0;
        foreach ($riwayatBelajar as $mk) {
            $totalSks += $mk->sks_default;
            $totalMutu += ($mk->sks_default * $mk->nilai_indeks);
        }
        $ipk = ($totalSks > 0) ? $totalMutu / $totalSks : 0;

        $pdf = Pdf::loadView('pdf.cetak-transkrip', [
            'mahasiswa' => $mahasiswa,
            'transkrip' => $riwayatBelajar,
            'ipk' => $ipk,
            'totalSks' => $totalSks
        ]);

        return $pdf->stream('Transkrip-' . $mahasiswa->nim . '.pdf');
    }
}