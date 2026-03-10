<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Models\AkademikTranskrip;
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
    
    public $listSemester = [];
    public $isEdomComplete = false;
    public $unfilledCourses = [];

    // Stats Kumulatif
    public $ipkKumulatif = 0.00;
    public $totalSksLulus = 0;

    public function mount()
    {
        $user = Auth::user();
        if (!$user->person_id) abort(403, 'Profil Anda belum terhubung dengan data personil.');

        $this->mahasiswa = Mahasiswa::with(['prodi.fakultas', 'person', 'kurikulum'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        // 1. Ambil List Semester yang memiliki record KRS
        $this->listSemester = Krs::with('tahunAkademik')
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->get()
            ->pluck('tahunAkademik')
            ->unique('id')
            ->sortByDesc('kode_tahun');

        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        if ($this->listSemester->isNotEmpty() && !$this->listSemester->contains('id', $this->tahunAkademikId)) {
            $this->tahunAkademikId = $this->listSemester->first()->id;
        }

        $this->loadData();
    }

    public function updatedTahunAkademikId()
    {
        $this->loadData();
    }

    /**
     * Fungsi Inti Pengambilan Data KHS - Refactored for reliability
     */
    public function loadData()
    {
        if (!$this->tahunAkademikId) return;

        // 1. Load KRS Semester Terpilih
        $this->krs = Krs::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        if ($this->krs) {
            // Ambil semua detail KRS (Gunakan Query Builder agar lebih presisi dibanding Collection filter)
            $allDetailsQuery = KrsDetail::where('krs_id', $this->krs->id);
            $allDetails = $allDetailsQuery->get();
            
            /**
             * EVALUASI EDOM (Gunakan filter yang lebih fleksibel terhadap tipe data 0/1 atau true/false)
             */
            $this->unfilledCourses = $allDetails->filter(function($item) {
                return $item->is_edom_filled == false || $item->is_edom_filled == 0;
            });
            
            $this->isEdomComplete = $this->unfilledCourses->isEmpty();

            /**
             * PENGAMBILAN DETAIL NILAI
             * Kita ambil data langsung dari database untuk menghindari isu tipe data pada koleksi PHP.
             * Data diambil hanya jika is_published = 1 (sudah dipublish dosen).
             */
            $this->details = KrsDetail::where('krs_id', $this->krs->id)
                ->where('is_published', 1) 
                ->get();
            
            // Catatan: Jika Anda ingin mengunci nilai berdasarkan EDOM, aktifkan baris di bawah ini:
            // if (!$this->isEdomComplete) { $this->details = collect(); }
            
        } else {
            $this->details = collect();
            $this->isEdomComplete = true;
        }

        // 2. Load Statistik Semester (IPS)
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        // 3. Load Statistik Kumulatif dari Materialized Transkrip
        $transkrip = AkademikTranskrip::where('mahasiswa_id', $this->mahasiswa->id)->get();
        $this->totalSksLulus = $transkrip->sum('sks_diakui');
        $this->ipkKumulatif = $this->totalSksLulus > 0 
            ? round($transkrip->sum(fn($i) => $i->sks_diakui * $i->nilai_indeks_final) / $this->totalSksLulus, 2)
            : 0.00;

        // 4. Ambil Data Pejabat
        $this->kaProdi = $this->getPejabat('KAPRODI', $this->mahasiswa->prodi_id);
    }

    private function getPejabat($kodeJabatan, $prodiId)
    {
        $today = now()->format('Y-m-d');
        $person = DB::table('ref_person as p')
            ->join('trx_dosen as d', 'p.id', '=', 'd.person_id')
            ->join('trx_person_jabatan as pj', 'p.id', '=', 'pj.person_id')
            ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
            ->where('j.kode_jabatan', $kodeJabatan)
            ->where('pj.prodi_id', $prodiId)
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')->orWhere('pj.tanggal_selesai', '>=', $today);
            })
            ->select('p.nama_lengkap', 'd.nidn')->first();

        if (!$person) return null;

        return (object)[
            'nama' => $person->nama_lengkap,
            'identitas' => "NIDN. " . $person->nidn
        ];
    }

    public function render()
    {
        return view('livewire.mahasiswa.khs-page');
    }
}