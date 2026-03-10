<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class KhsPage extends Component
{
    public $mahasiswa;
    public $krs;
    public $riwayat;
    public $details = [];
    public $tahunAkademikId;
    public $kaProdi;
    
    // Properti baru untuk Dropdown Semester
    public $listSemester = [];

    // State EDOM Gatekeeper
    public $isEdomComplete = false;
    public $unfilledCourses = [];

    public function mount()
    {
        $user = Auth::user();

        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT).');
        }

        // 1. Load Mahasiswa (Cukup sekali di mount)
        $this->mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        // 2. Ambil Riwayat Semester Mahasiswa
        // Hanya ambil tahun akademik di mana mahasiswa ini sudah memiliki KRS
        $this->listSemester = Krs::join('ref_tahun_akademik as ta', 'krs.tahun_akademik_id', '=', 'ta.id')
            ->where('krs.mahasiswa_id', $this->mahasiswa->id)
            ->select('ta.id', 'ta.kode_tahun', 'ta.nama_tahun')
            ->orderBy('ta.kode_tahun', 'desc')
            ->get();

        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        // Jika mahasiswa belum punya KRS di tahun aktif, otomatis set ke KRS semester terakhirnya
        if ($this->listSemester->isNotEmpty() && !$this->listSemester->contains('id', $this->tahunAkademikId)) {
            $this->tahunAkademikId = $this->listSemester->first()->id;
        }

        $this->loadData();
    }

    // Listener Livewire saat Dropdown Semester diubah
    public function updatedTahunAkademikId()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Reset state sebelumnya
        $this->krs = null;
        $this->details = [];
        $this->unfilledCourses = [];
        $this->riwayat = null;
        $this->isEdomComplete = false;

        if (!$this->tahunAkademikId) return;

        // 1. Ambil KRS Semester yang Dipilih
        $this->krs = Krs::with(['tahunAkademik'])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        if ($this->krs) {
            // 2. Ambil Seluruh Mata Kuliah yang Diambil (Gunakan Query Builder agar Fresh/No Cache)
            $allDetails = DB::table('krs_detail as kd')
                ->leftJoin('jadwal_kuliah as jk', 'kd.jadwal_kuliah_id', '=', 'jk.id')
                ->leftJoin('master_mata_kuliahs as mk', 'jk.mata_kuliah_id', '=', 'mk.id')
                ->where('kd.krs_id', $this->krs->id)
                ->select(
                    'kd.*',
                    DB::raw('COALESCE(mk.nama_mk, kd.nama_mk_snapshot) as nama_mk'),
                    DB::raw('COALESCE(mk.kode_mk, kd.kode_mk_snapshot) as kode_mk'),
                    DB::raw('COALESCE(mk.sks_default, kd.sks_snapshot) as sks_default')
                )
                ->get();
            
            // 3. Filter MK yang belum diisi EDOM
            $this->unfilledCourses = $allDetails->where('is_edom_filled', false);

            // 4. Logika Penentuan Tampilan
            // Mengaktifkan kembali Gatekeeper EDOM: Wajib isi semua EDOM untuk lihat KHS
            if (count($this->unfilledCourses) === 0) {
                $this->isEdomComplete = true;
                // Hanya tampilkan nilai yang sudah dipublikasikan oleh dosen / admin import
                $this->details = $allDetails->where('is_published', true);
            } else {
                $this->isEdomComplete = false;
                $this->details = []; // Sembunyikan semua nilai jika belum lengkap
            }
        }

        // 5. Load Statistik (IPS/IPK)
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        // 6. Ambil Data Pejabat untuk Tanda Tangan
        $this->kaProdi = $this->getPejabat('KAPRODI', $this->mahasiswa->prodi_id);
    }

    private function getPejabat($kodeJabatan, $prodiId)
    {
        $today = now()->format('Y-m-d');
        $person = DB::table('ref_person as p')
            ->join('trx_person_jabatan as pj', 'p.id', '=', 'pj.person_id')
            ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
            ->where('j.kode_jabatan', $kodeJabatan)
            ->where('pj.prodi_id', $prodiId)
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')->orWhere('pj.tanggal_selesai', '>=', $today);
            })
            ->select('p.nama_lengkap', 'p.nik', 'p.id')->first();

        if (!$person) return null;

        // Ambil Gelar (Format Lengkap)
        $gelars = DB::table('trx_person_gelar as tpg')
            ->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')
            ->where('tpg.person_id', $person->id)
            ->select('rg.kode', 'rg.posisi')->orderBy('tpg.urutan', 'asc')->get();

        $gelarDepan = $gelars->where('posisi', 'DEPAN')->pluck('kode')->implode(' ');
        $gelarBelakang = $gelars->where('posisi', 'BELAKANG')->pluck('kode')->implode(', ');

        return (object)[
            'nama' => trim(($gelarDepan ? $gelarDepan . ' ' : '') . $person->nama_lengkap . ($gelarBelakang ? ', ' . $gelarBelakang : '')),
            'identitas' => $person->nik
        ];
    }

    public function render()
    {
        return view('livewire.mahasiswa.khs-page');
    }
}