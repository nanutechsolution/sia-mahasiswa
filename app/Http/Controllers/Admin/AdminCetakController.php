<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Akademik\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminCetakController extends Controller
{
    public function cetakAbsensi($jadwalId)
    {
        // 1. Ambil Jadwal dengan Relasi Lengkap (SSOT)
        $jadwal = JadwalKuliah::with([
                'mataKuliah.prodi.fakultas', // Eager load hingga fakultas untuk data riil
                'dosen.person', 
                'tahunAkademik',
                'krsDetails' => function($q) {
                    // Filter hanya yang KRS-nya DISETUJUI (Mahasiswa Aktif di Kelas)
                    $q->whereHas('krs', function($k) {
                        $k->where('status_krs', 'DISETUJUI');
                    })
                    ->join('krs', 'krs_detail.krs_id', '=', 'krs.id')
                    ->join('mahasiswas', 'krs.mahasiswa_id', '=', 'mahasiswas.id')
                    ->join('ref_person', 'mahasiswas.person_id', '=', 'ref_person.id') 
                    ->select('krs_detail.*', 'mahasiswas.nim', 'ref_person.nama_lengkap', 'ref_person.jenis_kelamin')
                    ->orderBy('mahasiswas.nim', 'asc');
                }
            ])
            ->findOrFail($jadwalId);

        // 2. Siapkan Data untuk Laporan (Production Ready)
        $data = [
            'jadwal' => $jadwal,
            'mahasiswas' => $jadwal->krsDetails,
            // Data diambil dinamis dari relasi, fallback ke strip (-) jika data master tidak lengkap
            'fakultas' => $jadwal->mataKuliah->prodi->fakultas->nama_fakultas ?? '-', 
            'prodi' => $jadwal->mataKuliah->prodi->nama_prodi ?? '-',
            'semester' => $jadwal->tahunAkademik->nama_tahun ?? '-',
            'dosen' => $jadwal->dosen->person->nama_lengkap ?? '-',
            'jumlah_mhs' => $jadwal->krsDetails->count()
        ];

        // 3. Generate PDF
        $pdf = Pdf::loadView('pdf.cetak-absensi', $data);
        
        // Setup Kertas A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        $namaFile = 'Absensi-' . ($jadwal->mataKuliah->kode_mk ?? 'MK') . '-' . $jadwal->nama_kelas . '.pdf';
        return $pdf->stream($namaFile);
    }
}