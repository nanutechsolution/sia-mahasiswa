<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use App\Models\AkademikTranskrip;
use App\Helpers\SistemHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CetakController extends Controller
{
    /**
     * Helper untuk mengambil data Pejabat secara dinamis dari HR Module (SSOT)
     */
    private function getPejabat($kodeJabatan, $prodiId = null)
    {
        $today = now()->format('Y-m-d');

        $query = DB::table('ref_person as p')
            ->join('trx_person_jabatan as pj', 'p.id', '=', 'pj.person_id')
            ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
            ->leftJoin('trx_dosen as d', 'd.person_id', '=', 'p.id')
            ->where('j.kode_jabatan', $kodeJabatan)
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')
                    ->orWhere('pj.tanggal_selesai', '>=', $today);
            });

        if ($prodiId) {
            $query->where('pj.prodi_id', $prodiId);
        }

        $person = $query->select('p.nama_lengkap', 'p.id', 'd.nidn', 'p.nik')->first();

        if (!$person) return null;

        $gelars = DB::table('trx_person_gelar as tpg')
            ->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')
            ->where('tpg.person_id', $person->id)
            ->select('rg.kode', 'rg.posisi')
            ->orderBy('tpg.urutan', 'asc')
            ->get();

        $gelarDepan = $gelars->where('posisi', 'DEPAN')->pluck('kode')->implode(' ');
        $gelarBelakang = $gelars->where('posisi', 'BELAKANG')->pluck('kode')->implode(', ');

        return (object)[
            'nama' => trim(($gelarDepan ? $gelarDepan . ' ' : '') . $person->nama_lengkap . ($gelarBelakang ? ', ' . $gelarBelakang : '')),
            'identitas' => $person->nidn ? "NIDN. " . $person->nidn : "NIK. " . $person->nik
        ];
    }

    /**
     * Cetak Kartu Ujian (UTS / UAS) Mahasiswa
     */
    public function cetakKartuUjian($jenisUjian)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $ta = SistemHelper::getTahunAktif();

        // Cari daftar ujian berdasarkan KRS mahasiswa yang telah disetujui
        $jadwalUjians = \App\Models\JadwalUjianPeserta::with(['jadwalUjian.jadwalKuliah.mataKuliah', 'jadwalUjian.ruang'])
            ->whereHas('krsDetail.krs', function ($q) use ($mahasiswa, $ta) {
                $q->where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_akademik_id', $ta->id)
                    ->where('status_krs', 'DISETUJUI');
            })
            ->whereHas('jadwalUjian', function ($q) use ($jenisUjian) {
                $q->where('jenis_ujian', strtoupper($jenisUjian));
            })
            ->get()
            ->sortBy(function ($peserta) {
                return $peserta->jadwalUjian->tanggal_ujian . ' ' . $peserta->jadwalUjian->jam_mulai;
            });

        return Pdf::loadView('pdf.cetak-kartu-ujian', [
            'mahasiswa' => $mahasiswa,
            'ta' => $ta,
            'jenis_ujian' => strtoupper($jenisUjian),
            'pesertaUjians' => $jadwalUjians,
            'kaProdi' => $this->getPejabat('KAPRODI', $mahasiswa->prodi_id)
        ])->setPaper('a4', 'portrait')->stream('Kartu_Ujian_' . strtoupper($jenisUjian) . '_' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Cetak Kartu Rencana Studi (KRS)
     */
    public function cetakKrs()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'dosenWali.person', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $ta = SistemHelper::getTahunAktif();

        $krs = Krs::with([
            'details.jadwalKuliah.mataKuliah',
            'details.jadwalKuliah.dosens.person',
            'details.jadwalKuliah.ruang'
        ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $ta->id)
            ->firstOrFail();

        return Pdf::loadView('pdf.cetak-krs', [
            'mahasiswa' => $mahasiswa,
            'ta' => $ta,
            'details' => $krs->details,
            'kaBaak' => $this->getPejabat('KABAAK'),
            'kaProdi' => $this->getPejabat('KAPRODI', $mahasiswa->prodi_id)
        ])->setPaper('a4', 'portrait')->stream('KRS-' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Cetak Kartu Hasil Studi (KHS)
     */
    public function cetakKhs()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $ta = SistemHelper::getTahunAktif();

        $details = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.ruang'])
            ->whereHas('krs', function ($q) use ($mahasiswa, $ta) {
                $q->where('mahasiswa_id', $mahasiswa->id)->where('tahun_akademik_id', $ta->id);
            })
            ->where('is_published', true)
            ->get();

        $totalSks = $details->sum('sks_snapshot');
        $totalMutu = $details->sum(fn($d) => $d->sks_snapshot * $d->nilai_indeks);
        $ips = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        return Pdf::loadView('pdf.cetak-khs', [
            'mahasiswa' => $mahasiswa,
            'ta' => $ta,
            'details' => $details,
            'totalSks' => $totalSks,
            'totalMutu' => $totalMutu,
            'ips' => $ips,
            'kaProdi' => $this->getPejabat('KAPRODI', $mahasiswa->prodi_id)
        ])->setPaper('a4', 'portrait')->stream('KHS-' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Cetak Transkrip Nilai
     */
    public function cetakTranskrip()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $transkrip = AkademikTranskrip::with('mataKuliah')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get()
            ->sortBy('mataKuliah.kode_mk');

        $totalSks = $transkrip->sum('sks_diakui');
        $totalMutu = $transkrip->sum(fn($i) => $i->sks_diakui * $i->nilai_indeks_final);
        $ipk = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        return Pdf::loadView('pdf.cetak-transkrip', [
            'mahasiswa' => $mahasiswa,
            'transkrip' => $transkrip,
            'totalSks' => $totalSks,
            'ipk' => round($ipk, 2),
            'kaProdi' => $this->getPejabat('KAPRODI', $mahasiswa->prodi_id)
        ])->setPaper('a4', 'portrait')->stream('Transkrip-' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Cetak Berita Acara & Daftar Hadir per Pertemuan
     */
    public function cetakPresensiSesi($sesiId)
    {
        $sesi = PerkuliahanSesi::with([
            'jadwalKuliah.mataKuliah',
            'jadwalKuliah.dosens.person',
            'jadwalKuliah.ruang',
            'absensi'
        ])->findOrFail($sesiId);

        $koordinator = $sesi->jadwalKuliah->dosens->where('pivot.is_koordinator', true)->first() ?? $sesi->jadwalKuliah->dosens->first();

        $ttdDosen = (object)[
            'nama' => $koordinator->person->nama_lengkap ?? 'Dosen Pengampu',
            'identitas' => "NIDN. " . ($koordinator->nidn ?? '-')
        ];

        $peserta = KrsDetail::with('krs.mahasiswa.person')
            ->where('jadwal_kuliah_id', $sesi->jadwal_kul_id ?? $sesi->jadwal_kuliah_id)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get()
            ->map(function ($p) use ($sesi) {
                $absen = $sesi->absensi->where('krs_detail_id', $p->id)->first();
                return [
                    'nim' => $p->krs->mahasiswa->nim,
                    'nama' => $p->krs->mahasiswa->person->nama_lengkap,
                    'status' => $absen->status_kehadiran ?? 'A',
                    'waktu' => $absen ? Carbon::parse($absen->waktu_check_in)->format('H:i') : null
                ];
            })->sortBy('nim');

        return Pdf::loadView('pdf.dosen.presensi-sesi', [
            'sesi' => $sesi,
            'peserta' => $peserta,
            'ttdDosen' => $ttdDosen,
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ])->stream("Berita_Acara_P{$sesi->pertemuan_ke}.pdf");
    }

    /**
     * Cetak Rekap Absensi Semester (Dosen) - PERBAIKAN MATRIX
     */
    public function cetakRekapSemester($jadwalId)
    {
        $jadwal = JadwalKuliah::with([
            'mataKuliah.prodi',
            'dosens.person',
            'ruang',
            'tahunAkademik',
            'sesi.absensi'
        ])->findOrFail($jadwalId);

        $listSesi = $jadwal->sesi->sortBy('pertemuan_ke');

        // Cari Koordinator untuk Tanda Tangan
        $koordinator = $jadwal->dosens->where('pivot.is_koordinator', true)->first() ?? $jadwal->dosens->first();
        $ttdDosen = (object)[
            'nama' => $koordinator->person->nama_lengkap ?? 'Dosen Pengampu',
            'identitas' => $koordinator->nidn ? "NIDN. " . $koordinator->nidn : "-"
        ];

        $rekap = KrsDetail::with('krs.mahasiswa.person')
            ->where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get()
            ->map(function ($p) use ($listSesi) {
                $kehadiran = [];
                $totalHadir = 0;

                foreach ($listSesi as $sesi) {
                    $absen = $sesi->absensi->where('krs_detail_id', $p->id)->first();
                    $status = $absen->status_kehadiran ?? '-';
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

        return Pdf::loadView('pdf.dosen.rekap-semester', [
            'jadwal' => $jadwal,
            'listSesi' => $listSesi,
            'rekap' => $rekap,
            'ttdDosen' => $ttdDosen,
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ])->setPaper('a4', 'landscape')->stream("Rekap_Absensi_{$jadwal->mataKuliah->kode_mk}.pdf");
    }

    /**
     * Cetak Rekap Absensi Pribadi (Mahasiswa)
     */
    public function cetakRekapanAbsensi($jadwalId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('person_id', $user->person_id)->firstOrFail();

        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosens.person', 'tahunAkademik', 'ruang'])->findOrFail($jadwalId);

        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->firstOrFail();

        $sesiList = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwalId)
            ->whereIn('status_sesi', ['selesai', 'dibuka'])
            ->orderBy('pertemuan_ke')
            ->get();

        $absensi = PerkuliahanAbsensi::where('krs_detail_id', $krsDetail->id)
            ->get()
            ->keyBy('perkuliahan_sesi_id');

        $totalHadir = $absensi->where('status_kehadiran', 'H')->count();
        $totalSesi = $sesiList->count();

        return Pdf::loadView('pdf.mahasiswa.rekap-absensi', [
            'mahasiswa' => $mahasiswa,
            'jadwal' => $jadwal,
            'sesiList' => $sesiList,
            'absensi' => $absensi,
            'statistik' => [
                'hadir' => $totalHadir,
                'total' => $totalSesi,
                'persen' => $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0
            ],
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ])->stream("Absensi_{$jadwal->mataKuliah->kode_mk}.pdf");
    }
}
