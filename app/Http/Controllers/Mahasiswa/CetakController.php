<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Domains\Akademik\Models\JadwalKuliah;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\PerkuliahanSesi;
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
            ->join('trx_dosen as d', 'd.person_id', '=', 'p.id')
            ->where('j.kode_jabatan', $kodeJabatan)
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')
                    ->orWhere('pj.tanggal_selesai', '>=', $today);
            });

        if ($prodiId) {
            $query->where('pj.prodi_id', $prodiId);
        }

        $person = $query->select('p.nama_lengkap', 'p.nik', 'p.id', 'd.nidn')->first();

        if (!$person) return null;

        // Ambil gelar personil tersebut
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
            'identitas' => $person->nidn
        ];
    }

    /**
     * Cetak Kartu Rencana Studi (KRS)
     */
    public function cetakKrs()
    {
        $user = Auth::user();

        // [FIX SSOT] Cari Mahasiswa via person_id dari User
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'dosenWali.person', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $ta = SistemHelper::getTahunAktif();

        $krs = Krs::with(['details.jadwalKuliah.mataKuliah', 'details.jadwalKuliah.dosen.person'])
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

        // [FIX SSOT]
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $ta = SistemHelper::getTahunAktif();

        $details = DB::table('krs_detail as kd')
            ->join('krs', 'kd.krs_id', '=', 'krs.id')
            ->join('jadwal_kuliah as jk', 'kd.jadwal_kuliah_id', '=', 'jk.id')
            ->join('master_mata_kuliahs as mk', 'jk.mata_kuliah_id', '=', 'mk.id')
            ->where('krs.mahasiswa_id', $mahasiswa->id)
            ->where('krs.tahun_akademik_id', $ta->id)
            ->where('kd.is_published', true)
            ->select('kd.*', 'mk.kode_mk', 'mk.nama_mk', 'mk.sks_default')
            ->get();

        $totalSks = $details->sum('sks_default');
        $totalMutu = $details->sum(fn($d) => $d->sks_default * $d->nilai_indeks);
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
     * Cetak Transkrip Nilai Sementara
     */
    public function cetakTranskrip()
    {
        $user = Auth::user();

        // [FIX SSOT]
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        $transkrip = DB::table('krs_detail as kd')
            ->join('krs', 'kd.krs_id', '=', 'krs.id')
            ->join('jadwal_kuliah as jk', 'kd.jadwal_kuliah_id', '=', 'jk.id')
            ->join('master_mata_kuliahs as mk', 'jk.mata_kuliah_id', '=', 'mk.id')
            ->where('krs.mahasiswa_id', $mahasiswa->id)
            ->where('kd.is_published', true)
            ->select('mk.kode_mk', 'mk.nama_mk', 'mk.sks_default', 'kd.nilai_huruf', 'kd.nilai_indeks')
            ->orderBy('mk.kode_mk', 'asc')
            ->get();

        $totalSks = $transkrip->sum('sks_default');
        $totalMutu = $transkrip->sum(fn($mk) => $mk->sks_default * $mk->nilai_indeks);
        $ipk = $totalSks > 0 ? ($totalMutu / $totalSks) : 0;

        return Pdf::loadView('pdf.cetak-transkrip', [
            'mahasiswa' => $mahasiswa,
            'transkrip' => $transkrip,
            'totalSks' => $totalSks,
            'ipk' => $ipk,
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
            'jadwalKuliah.dosen.person',
            'jadwalKuliah.pesertaKelas.krs.mahasiswa.person', // Load data mahasiswa
            'absensi'
        ])->findOrFail($sesiId);

        // Siapkan data peserta + status kehadiran
        $peserta = $sesi->jadwalKuliah->pesertaKelas->map(function ($p) use ($sesi) {
            $absen = $sesi->absensi->where('krs_detail_id', $p->id)->first();
            return [
                'nim' => $p->krs->mahasiswa->nim,
                'nama' => $p->krs->mahasiswa->person->nama_lengkap,
                'status' => $absen ? $absen->status_kehadiran : 'A', // Default Alpha
                'waktu' => $absen ? $absen->waktu_check_in : null
            ];
        })->sortBy('nim');

        $data = [
            'sesi' => $sesi,
            'peserta' => $peserta,
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ];

        $pdf = Pdf::loadView('pdf.dosen.presensi-sesi', $data);
        return $pdf->stream("Berita_Acara_P{$sesi->pertemuan_ke}.pdf");
    }

    /**
     * Cetak Rekap Absensi Satu Semester (Matriks)
     */
    public function cetakRekapSemester($jadwalId)
    {
        $jadwal = JadwalKuliah::with([
            'mataKuliah',
            'dosen.person',
            'pesertaKelas.krs.mahasiswa.person',
            'sesi.absensi' // Load semua sesi dan absensinya
        ])->findOrFail($jadwalId);

        // 1. List Sesi (Kolom)
        $listSesi = $jadwal->sesi->sortBy('pertemuan_ke');

        // 2. Mapping Data Mahasiswa (Baris)
        $rekap = $jadwal->pesertaKelas->map(function ($p) use ($listSesi) {
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

        $pdf = Pdf::loadView('pdf.dosen.rekap-semester', $data)
            ->setPaper('a4', 'landscape'); // Landscape agar muat 16 pertemuan

        return $pdf->stream("Rekap_Absensi_{$jadwal->mataKuliah->nama_mk}.pdf");
    }


    /**
     * Cetak Rekap Absensi Pribadi per Mata Kuliah
     */
    public function cetakRekapanAbsensi($jadwalId)
    {
        $user = Auth::user();
        $mahasiswa = $user->person->mahasiswa;

        // 1. Ambil Jadwal & Validasi kepemilikan via KRS
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen.person', 'tahunAkademik'])->findOrFail($jadwalId);

        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->firstOrFail();

        // 2. Ambil Semua Sesi yang sudah terlaksana (Status Selesai/Dibuka)
        $sesiList = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwalId)
            ->whereIn('status_sesi', ['selesai', 'dibuka'])
            ->orderBy('pertemuan_ke')
            ->get();

        // 3. Ambil Data Absensi Mahasiswa Ini
        $absensi = PerkuliahanAbsensi::where('krs_detail_id', $krsDetail->id)
            ->get()
            ->keyBy('perkuliahan_sesi_id');

        // 4. Hitung Statistik
        $totalHadir = $absensi->where('status_kehadiran', 'H')->count();
        $totalSesi = $sesiList->count();
        $persentase = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0;

        $data = [
            'mahasiswa' => $mahasiswa,
            'jadwal' => $jadwal,
            'sesiList' => $sesiList,
            'absensi' => $absensi,
            'statistik' => [
                'hadir' => $totalHadir,
                'total' => $totalSesi,
                'persen' => $persentase
            ],
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM Y')
        ];

        $pdf = Pdf::loadView('pdf.mahasiswa.rekap-absensi', $data);
        return $pdf->stream("Rekap_Absensi_{$jadwal->mataKuliah->kode_mk}_{$mahasiswa->nim}.pdf");
    }
}
