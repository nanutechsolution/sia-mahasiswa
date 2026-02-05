<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\TahunAkademik;
use App\Helpers\SistemHelper;

class KrsPage extends Component
{
    public $mahasiswa;
    public $tahunAkademikId;
    public $krsId;
    public $statusKrs = 'DRAFT';
    public $isPaket = false;

    // Stats Akademik
    public $totalSks = 0;
    public $maxSks = 20;
    public $ipsLalu = 0;
    public $semesterBerjalan = 1;

    // State & Blocking
    public $blockKrs = false;
    public $pesanBlock = '';
    public $logs = [];

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        if (!$this->tahunAkademikId) {
            $this->blockAccess('Tidak ada Tahun Akademik yang aktif saat ini.');
            return;
        }

        $user = Auth::user();
        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT).');
        }

        // load mahasiswa
        $this->mahasiswas_load();

        // cek pembayaran minimal bayar spp minimal cicl sesuai aturan program kelas atau dispensasi baru bisa krs
        // cek juga sudah diterbitkan nim apa belum
        // syarat terbit nim minimal cicil spp dan sudah diverifikasi keuangan untuk pembaran spp dnn

        // 1. Inisialisasi Header
        $this->loadKrsHeader();
        $this->hitungSemesterDanJatahSks();

        // 2. Validasi Berjenjang
        if ($this->cekValidasiAwal()) return;

        // 3. LOGIKA PAKET: Jika draf masih kosong, jalankan auto-sync
        if ($this->isPaket && $this->statusKrs == 'DRAFT' && $this->totalSks == 0) {
            $this->ambilPaketOtomatis();
        }
    }


    public function mahasiswas_load()
    {
        $this->mahasiswa = Mahasiswa::with(['programKelas', 'prodi', 'person', 'dosenWali.person'])
            ->where('person_id', Auth::user()->person_id)
            ->firstOrFail();
    }

    /**
     * Logic Pengambilan Matakuliah dengan SNAPSHOT & PENYETARAAN (Ekuivalensi)
     */
    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs || $this->statusKrs !== 'DRAFT') return;

        $jadwal = JadwalKuliah::with(['mataKuliah'])->findOrFail($jadwalId);
        $mk = $jadwal->mataKuliah;

        // 1. Cari Kebijakan Penyetaraan (Ekuivalensi)
        // Cek apakah mahasiswa angkatan lama mengambil MK baru sebagai pengganti
        $ekuivalensi = DB::table('akademik_ekuivalensi')
            ->where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('mk_tujuan_id', $mk->id)
            ->where('is_active', true)
            ->first();

        // 2. Validasi SKS & Bentrok
        if (($this->totalSks + $mk->sks_default) > $this->maxSks) {
            session()->flash('error', "Gagal: SKS melebihi jatah maksimal.");
            return;
        }
        if ($this->isBentrok($jadwal)) {
            session()->flash('error', "Gagal: Jadwal bentrok.");
            return;
        }

        // 3. EKSEKUSI SNAPSHOT (Audit-Proof Transaction)
        KrsDetail::updateOrCreate([
            'krs_id' => $this->krsId,
            'jadwal_kuliah_id' => $jadwalId
        ], [
            // Simpan identitas asli MK saat transaksi (Snapshot)
            'kode_mk_snapshot' => $mk->kode_mk,
            'nama_mk_snapshot' => $mk->nama_mk,
            'sks_snapshot' => $mk->sks_default,

            // Simpan referensi penyetaraan jika ini pengambilan lintas kurikulum
            'ekuivalensi_id' => $ekuivalensi->id ?? null,
            'status_ambil' => 'B'
        ]);

        $this->hitungSks();
        session()->flash('success', "Mata kuliah " . ($ekuivalensi ? "pengganti " : "") . "{$mk->nama_mk} berhasil ditambahkan.");
    }

    /**
     * Logic Pengambilan Paket Otomatis
     */
    public function ambilPaketOtomatis()
    {
        $this->logs = [];

        $activeKurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->orderBy('tahun_mulai', 'desc')
            ->first();

        if (!$activeKurikulum) {
            $this->addLog('danger', 'Struktur Kurikulum Prodi belum aktif.');
            return;
        }

        $mkPaket = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $activeKurikulum->id)
            ->where('semester_paket', $this->semesterBerjalan)->get();

        if ($mkPaket->isEmpty()) {
            $this->addLog('warning', "Belum ada pemetaan MK untuk Semester {$this->semesterBerjalan}.");
            return;
        }

        foreach ($mkPaket as $item) {
            $jadwal = JadwalKuliah::where('tahun_akademik_id', $this->tahunAkademikId)
                ->where('mata_kuliah_id', $item->mata_kul_id ?? $item->mata_kuliah_id)
                ->where(fn($q) => $q->whereNull('id_program_kelas_allow')->orWhere('id_program_kelas_allow', $this->mahasiswa->program_kelas_id))
                ->first();

            if ($jadwal) {
                $this->ambilMatkul($jadwal->id);
            }
        }
    }

    private function isBentrok($jT)
    {
        $diambil = KrsDetail::join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->where('krs_id', $this->krsId)
            ->select('jadwal_kuliah.*')->get();

        foreach ($diambil as $ex) {
            if ($ex->id == $jT->id) continue;
            if ($ex->hari == $jT->hari) {
                if ($jT->jam_mulai < $ex->jam_selesai && $jT->jam_selesai > $ex->jam_mulai) return true;
            }
        }
        return false;
    }

    public function loadKrsHeader()
    {
        $krs = Krs::firstOrCreate(
            ['mahasiswa_id' => $this->mahasiswa->id, 'tahun_akademik_id' => $this->tahunAkademikId],
            ['status_krs' => 'DRAFT', 'tgl_krs' => now(), 'dosen_wali_id' => $this->mahasiswa->dosen_wali_id]
        );
        $this->isPaket = (bool) ($this->mahasiswa->prodi->is_paket ?? true);
        $this->krsId = $krs->id;
        $this->statusKrs = $krs->status_krs;
        $this->hitungSks();
    }

    public function hitungSks()
    {
        $this->totalSks = KrsDetail::where('krs_id', $this->krsId)->sum('sks_snapshot');
    }

    public function hitungSemesterDanJatahSks()
    {
        $ta = TahunAkademik::find($this->tahunAkademikId);
        $tahunTa = (int) substr($ta->kode_tahun, 0, 4);
        $smtTipe = (int) substr($ta->kode_tahun, 4, 1);
        $angkatanMhs = (int) preg_replace('/[^0-9]/', '', $this->mahasiswa->angkatan_id);
        $this->semesterBerjalan = max(1, (($tahunTa - $angkatanMhs) * 2) + ($smtTipe >= 2 ? 2 : 1));
        $this->maxSks = 24;
    }

    private function cekValidasiAwal()
    {
        if (!$this->mahasiswa->dosen_wali_id) {
            $this->blockAccess('PA belum ditentukan.');
            return true;
        }
        if (!SistemHelper::isMasaKrsOpen()) {
            $this->blockAccess('Masa KRS ditutup.');
            return true;
        }
        return false;
    }

    private function blockAccess($msg)
    {
        $this->blockKrs = true;
        $this->pesanBlock = $msg;
    }
    private function addLog($type, $msg)
    {
        $this->logs[] = ['type' => $type, 'msg' => $msg];
    }

    public function ajukanKrs()
    {
        Krs::find($this->krsId)->update(['status_krs' => 'AJUKAN']);
        $this->statusKrs = 'AJUKAN';
    }
    public function hapusMatkul($id)
    {
        if ($this->statusKrs == 'DRAFT') KrsDetail::destroy($id);
        $this->hitungSks();
    }

    public function render()
    {
        $diambil = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->where('krs_id', $this->krsId)->get();

        $takenMkIds = $diambil->pluck('jadwalKuliah.mata_kuliah_id')->toArray();

        // Query Utama Jadwal (Event Layer)
        $query = JadwalKuliah::with(['mataKuliah', 'dosen.person'])
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->whereNotIn('mata_kuliah_id', $takenMkIds);

        // Jika Sistem Paket: Saring hanya yang ada di Kurikulum Semester ini
        // ATAU yang merupakan ekuivalensi sah untuk kurikulum mahasiswa tersebut
        if ($this->isPaket) {
            $activeKurikulumId = DB::table('master_kurikulums')
                ->where('prodi_id', $this->mahasiswa->prodi_id)
                ->where('is_active', true)
                ->value('id');

            $query->where(function ($q) use ($activeKurikulumId) {
                // Skenario A: MK memang ada di kurikulum sekarang
                $q->whereIn('mata_kuliah_id', function ($sub) use ($activeKurikulumId) {
                    $sub->select('mata_kuliah_id')
                        ->from('kurikulum_mata_kuliah')
                        ->where('kurikulum_id', $activeKurikulumId)
                        ->where('semester_paket', $this->semesterBerjalan);
                })
                    // Skenario B: MK di jadwal adalah tujuan dari ekuivalensi MK di kurikulum mhs
                    ->orWhereIn('mata_kuliah_id', function ($sub) use ($activeKurikulumId) {
                        $sub->select('mk_tujuan_id')
                            ->from('akademik_ekuivalensi')
                            ->where('is_active', true)
                            ->whereIn('mk_asal_id', function ($origin) use ($activeKurikulumId) {
                                $origin->select('mata_kuliah_id')
                                    ->from('kurikulum_mata_kuliah')
                                    ->where('kurikulum_id', $activeKurikulumId);
                            });
                    });
            });
        }

        return view('livewire.mahasiswa.krs-page', [
            'krsDiambil' => $diambil,
            'jadwalTersedia' => $query->get()
        ]);
    }
}
