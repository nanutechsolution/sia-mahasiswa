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
    public $kaProdi; // Property untuk menyimpan data pejabat (Kaprodi)

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        
        // [SSOT FIX] Validasi koneksi User ke Person
        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT). Silakan hubungi Admin.');
        }

        // [SSOT FIX] Ambil Mahasiswa berdasarkan person_id
        $this->mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        if (!$this->tahunAkademikId) {
            return;
        }

        // Cari KRS Semester Aktif
        $this->krs = Krs::with(['tahunAkademik'])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId) 
            ->first();

        if ($this->krs) {
            // Ambil Detail Nilai (Hanya yang sudah dipublish Dosen)
            $this->details = $this->krs->details()
                ->with(['jadwalKuliah.mataKuliah'])
                ->where('is_published', true) 
                ->get();
        }

        // Ambil Data IPS/IPK dari Riwayat Status
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        // Ambil Data Kaprodi untuk Tanda Tangan (Dinamis dari HR Module)
        $this->kaProdi = $this->getPejabat('KAPRODI', $this->mahasiswa->prodi_id);
    }

    /**
     * Helper untuk mengambil data Pejabat berdasarkan Kode Jabatan & Prodi
     * Mengambil dari tabel HR (ref_person, trx_person_jabatan)
     */
    private function getPejabat($kodeJabatan, $prodiId)
    {
        $today = now()->format('Y-m-d');

        // Cari siapa yang menjabat KAPRODI di prodi ini dan masih aktif tanggalnya
        $person = DB::table('ref_person as p')
            ->join('trx_person_jabatan as pj', 'p.id', '=', 'pj.person_id')
            ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
            ->where('j.kode_jabatan', $kodeJabatan)
            ->where('pj.prodi_id', $prodiId)
            ->where('pj.tanggal_mulai', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('pj.tanggal_selesai')
                  ->orWhere('pj.tanggal_selesai', '>=', $today);
            })
            ->select('p.nama_lengkap', 'p.nik', 'p.id')
            ->first();
        
        if (!$person) return null;

        // Ambil gelar dan gabungkan
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
            'identitas' => $person->nik
        ];
    }

    public function render()
    {
        return view('livewire.mahasiswa.khs-page');
    }
}