<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\KrsDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminCetakController extends Controller
{
    /**
     * Mencetak Daftar Hadir Mahasiswa dan Dosen (DHMD)
     * Mendukung pemetaan data absen 1-16 pertemuan secara otomatis tanpa error relasi.
     */
    public function cetakAbsensi($jadwalId)
    {
        // 1. Ambil Jadwal dengan Relasi Lengkap
        $jadwal = JadwalKuliah::with([
                'mataKuliah.prodi.fakultas',
                'dosens.person',
                'ruang',
                'tahunAkademik',
                'sesi', 
                'krsDetails' => function($q) {
                    $q->whereHas('krs', function($k) {
                        $k->where('status_krs', 'DISETUJUI');
                    })
                    ->with(['krs.mahasiswa.person']); 
                }
            ])
            ->findOrFail($jadwalId);

        // 2. Ambil data absensi secara kolektif
        $listSesi = $jadwal->sesi->sortBy('pertemuan_ke');
        $sesiIds = $listSesi->pluck('id');

        $absensiGlobal = PerkuliahanAbsensi::whereIn('perkuliahan_sesi_id', $sesiIds)
            ->get()
            ->groupBy('krs_detail_id');

        // 3. Olah data Mahasiswa dan pemetaan kehadirannya (1-16 pertemuan)
        $mahasiswas = $jadwal->krsDetails->map(function($kd) use ($listSesi, $absensiGlobal) {
            $kehadiran = [];
            $mhsAbsenList = $absensiGlobal->get($kd->id) ?? collect();
            
            for ($i = 1; $i <= 16; $i++) {
                $sesi = $listSesi->firstWhere('pertemuan_ke', $i);
                $status = ''; 
                
                if ($sesi) {
                    $absen = $mhsAbsenList->firstWhere('perkuliahan_sesi_id', $sesi->id);
                    $status = $absen ? $absen->status_kehadiran : '';
                }
                $kehadiran[$i] = $status;
            }

            return (object)[
                'nim'           => $kd->krs->mahasiswa->nim ?? '-',
                'nama_lengkap'  => $kd->krs->mahasiswa->person->nama_lengkap ?? 'Unknown',
                'jenis_kelamin' => ($kd->krs->mahasiswa->person->jenis_kelamin ?? 'Laki-laki') == 'Laki-laki' ? 'L' : 'P',
                'kehadiran'     => $kehadiran
            ];
        })->sortBy('nim');

        $koordinator = $jadwal->dosens->where('pivot.is_koordinator', true)->first() ?? $jadwal->dosens->first();

        $data = [
            'jadwal'     => $jadwal,
            'mahasiswas' => $mahasiswas, 
            'listSesi'   => $listSesi,
            'fakultas'   => $jadwal->mataKuliah->prodi->fakultas->nama_fakultas ?? '-', 
            'prodi'      => $jadwal->mataKuliah->prodi->nama_prodi ?? '-',
            'semester'   => $jadwal->tahunAkademik->nama_tahun ?? '-',
            'dosen'      => $koordinator->person->nama_lengkap ?? '-',
            'jumlah_mhs' => $mahasiswas->count()
        ];

        $pdf = Pdf::loadView('pdf.cetak-absensi', $data);
        $pdf->setPaper('a4', 'landscape');

        $namaFile = 'DHMD-' . ($jadwal->mataKuliah->kode_mk ?? 'MK') . '-' . str_replace(' ', '_', $jadwal->nama_kelas) . '.pdf';
        
        return $pdf->stream($namaFile);
    }

    /**
     * Mencetak Rekapitulasi Absensi Semester (Matrix 16 Pertemuan)
     * Method ini menangani route admin.cetak.rekap-absensi
     */
    public function cetakRekapAbsensi($jadwalId)
    {
        $jadwal = JadwalKuliah::with([
            'mataKuliah.prodi.fakultas',
            'dosens.person',
            'ruang',
            'tahunAkademik',
            'sesi.absensi'
        ])->findOrFail($jadwalId);

        $listSesi = $jadwal->sesi->sortBy('pertemuan_ke');

        $rekap = KrsDetail::with('krs.mahasiswa.person')
            ->where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get()
            ->map(function ($p) use ($listSesi) {
                $kehadiran = [];
                $totalHadir = 0;

                foreach ($listSesi as $sesi) {
                    $absen = $sesi->absensi->where('krs_detail_id', $p->id)->first();
                    $status = $absen ? $absen->status_kehadiran : '-';
                    $kehadiran[$sesi->pertemuan_ke] = $status;
                    if ($status == 'H') $totalHadir++;
                }

                return [
                    'nim' => $p->krs->mahasiswa->nim,
                    'nama' => $p->krs->mahasiswa->person->nama_lengkap,
                    'kehadiran' => $kehadiran,
                    'persentase' => $listSesi->count() > 0 ? round(($totalHadir / $listSesi->count()) * 100) : 0
                ];
            })->sortBy('nim');

        $data = [
            'jadwal' => $jadwal,
            'listSesi' => $listSesi,
            'rekap' => $rekap,
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ];

        return Pdf::loadView('pdf.dosen.rekap-semester', $data)
            ->setPaper('a4', 'landscape')
            ->stream("Rekap_Absensi_Admin_{$jadwal->mataKuliah->kode_mk}.pdf");
    }
}