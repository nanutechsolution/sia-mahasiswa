<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Helpers\SistemHelper;
use Barryvdh\DomPDF\Facade\Pdf;
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
}
