<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Helpers\SistemHelper;

class KrsPage extends Component
{
    public $mahasiswa;
    public $tahunAkademikId;
    public $krsId;
    public $statusKrs = 'DRAFT';
    public $isPaket = false;

    // Statistik Akademik
    public $totalSks = 0;
    public $maxSks = 24; // Default jika SKS Mandiri
    public $semesterBerjalan = 1;

    // Status Pemblokiran & Validasi
    public $blockKrs = false;
    public $pesanBlock = '';
    public $reasonType = ''; // 'NIM', 'FINANCE', 'SYSTEM'
    public $paidPercentage = 0;
    public $minPercentage = 0;

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        if (!$this->tahunAkademikId) {
            $this->blockAccess('Sistem Belum Siap: Tahun Akademik aktif tidak ditemukan.', 'SYSTEM');
            return;
        }

        $user = Auth::user();
        if (!$user->person_id) {
            abort(403, 'Profil SSOT tidak terdeteksi. Hubungi IT Support.');
        }

        $this->loadMahasiswaData();
        
        // 1. Jalankan Validasi Gerbang dengan urutan baru: Sistem -> Keuangan -> PA -> NIM
        if ($this->cekValidasiAwal()) return;

        // 2. Inisialisasi Data KRS
        $this->loadKrsHeader();
        $this->hitungSemesterBerjalan();

        // 3. Sinkronisasi Otomatis jika sistem paket dan draf masih kosong
        if ($this->isPaket && $this->statusKrs == 'DRAFT' && $this->totalSks == 0) {
            $this->ambilPaketOtomatis();
        }
    }

    private function loadMahasiswaData()
    {
        $this->mahasiswa = Mahasiswa::with(['prodi', 'person', 'programKelas'])
            ->where('person_id', Auth::user()->person_id)
            ->firstOrFail();
    }

    /**
     * Logika Gatekeeper: Validasi Masa KRS, Keuangan, Dosen Wali, dan NIM Resmi
     */
    private function cekValidasiAwal()
    {
        // 1. CEK MASA KRS (Sistem)
        if (!SistemHelper::isMasaKrsOpen()) {
            $this->blockAccess('Masa pengisian KRS untuk semester ini belum dibuka atau sudah berakhir.', 'SYSTEM');
            return true;
        }

        // 2. CEK KEUANGAN (SPP & Tagihan)
        $isDispensasi = (bool) ($this->mahasiswa->data_tambahan['bebas_keuangan'] ?? false);
        
        if (!$isDispensasi) {
            $tagihan = TagihanMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->first();

            if (!$tagihan) {
                $this->blockAccess('Akses Terkunci: Data tagihan semester berjalan belum diterbitkan oleh bagian Keuangan. Silakan lapor ke bagian Administrasi Keuangan.', 'FINANCE');
                return true;
            }

            $this->minPercentage = $this->mahasiswa->programKelas->min_pembayaran_persen ?? 50;
            $this->paidPercentage = ($tagihan->total_tagihan > 0) 
                ? round(($tagihan->total_bayar / $tagihan->total_tagihan) * 100) 
                : 100;

            if ($this->paidPercentage < $this->minPercentage) {
                $this->blockAccess("Syarat Keuangan: Pembayaran Anda baru tercatat {$this->paidPercentage}%. Minimal pembayaran untuk mengisi KRS adalah {$this->minPercentage}% sesuai aturan Program Kelas Anda.", 'FINANCE');
                return true;
            }
        }

        // 3. CEK DOSEN WALI (Pembimbing Akademik)
        // Jika dosen_wali_id kosong, mahasiswa tidak bisa melakukan perwalian
        if (!$this->mahasiswa->dosen_wali_id) {
            $this->blockAccess('Akses Terkunci: Anda belum memiliki Dosen Wali (Pembimbing Akademik). Silakan hubungi Admin Program Studi untuk proses penugasan PA.', 'SYSTEM');
            return true;
        }

        // 4. CEK NIM RESMI (Administrasi)
        $isNimPmb = str_contains(strtoupper($this->mahasiswa->nim), 'PMB') || strlen($this->mahasiswa->nim) > 15;
        if ($isNimPmb) {
            $this->blockAccess('Akses Ditolak: Pembayaran Anda sudah tervalidasi, namun Anda masih menggunakan NIM Sementara. Silakan selesaikan proses Daftar Ulang di BAAK untuk mendapatkan NIM Resmi.', 'NIM');
            return true;
        }

        return false;
    }

    private function blockAccess($msg, $type)
    {
        $this->blockKrs = true;
        $this->pesanBlock = $msg;
        $this->reasonType = $type;
    }

    /**
     * Mengambil Mata Kuliah dengan Validasi Kurikulum & Ekuivalensi
     */
    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs || $this->statusKrs !== 'DRAFT') return;

        $jadwal = JadwalKuliah::with('mataKuliah')->findOrFail($jadwalId);
        $mk = $jadwal->mataKuliah;

        // 1. Validasi Batas SKS (Hanya jika SKS Mandiri)
        if (!$this->isPaket && ($this->totalSks + $mk->sks_default) > $this->maxSks) {
            session()->flash('error', "Gagal: SKS melebihi jatah maksimal.");
            return;
        }

        // 2. VALIDASI KURIKULUM & EKUIVALENSI (Recognition Logic)
        $activeKurikulumId = DB::table('master_kurikulums')
            ->where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->value('id');

        $isInCurriculum = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $activeKurikulumId)
            ->where('mata_kuliah_id', $mk->id)
            ->exists();

        $ekuivalensiId = null;
        if (!$isInCurriculum) {
            $ekuivalensi = DB::table('akademik_ekuivalensi')
                ->where('prodi_id', $this->mahasiswa->prodi_id)
                ->where('mk_tujuan_id', $mk->id)
                ->where('is_active', true)
                ->whereIn('mk_asal_id', function($query) use ($activeKurikulumId) {
                    $query->select('mata_kuliah_id')
                          ->from('kurikulum_mata_kuliah')
                          ->where('kurikulum_id', $activeKurikulumId);
                })
                ->first();

            if (!$ekuivalensi) {
                session()->flash('error', "Gagal: Mata kuliah ini tidak terdaftar dalam kurikulum Anda maupun tabel penyetaraan.");
                return;
            }
            $ekuivalensiId = $ekuivalensi->id;
        }

        // 3. Simpan Detail KRS dengan Snapshot (Audit-Proof)
        KrsDetail::updateOrCreate(
            ['krs_id' => $this->krsId, 'jadwal_kuliah_id' => $jadwalId],
            [
                'kode_mk_snapshot' => $mk->kode_mk,
                'nama_mk_snapshot' => $mk->nama_mk,
                'sks_snapshot'     => $mk->sks_default,
                'ekuivalensi_id'   => $ekuivalensiId,
                'status_ambil'     => 'B'
            ]
        );

        $this->hitungSks();
    }

    public function ambilPaketOtomatis()
    {
        $curId = DB::table('master_kurikulums')
            ->where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->value('id');
            
        if (!$curId) return;

        $mkPaket = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $curId)
            ->where('semester_paket', $this->semesterBerjalan)->get();

        foreach ($mkPaket as $item) {
            $jadwal = JadwalKuliah::where('tahun_akademik_id', $this->tahunAkademikId)
                ->where('mata_kuliah_id', $item->mata_kul_id ?? $item->mata_kuliah_id)
                ->first();
            
            if ($jadwal) $this->ambilMatkul($jadwal->id);
        }
    }

    public function hitungSemesterBerjalan()
    {
        $ta = DB::table('ref_tahun_akademik')->find($this->tahunAkademikId);
        $tahunTa = (int) substr($ta->kode_tahun, 0, 4);
        $smtTipe = (int) substr($ta->kode_tahun, 4, 1);
        $angkatan = (int) preg_replace('/[^0-9]/', '', $this->mahasiswa->angkatan_id);
        $this->semesterBerjalan = max(1, (($tahunTa - $angkatan) * 2) + ($smtTipe >= 2 ? 2 : 1));
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

    public function render()
    {
        $krsDiambil = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->where('krs_id', $this->krsId)->get();

        $takenMkIds = $krsDiambil->pluck('jadwalKuliah.mata_kuliah_id')->toArray();
        
        $jadwalTersedia = [];
        if (!$this->blockKrs) {
            $activeKurikulumId = DB::table('master_kurikulums')
                ->where('prodi_id', $this->mahasiswa->prodi_id)
                ->where('is_active', true)
                ->value('id');

            $jadwalTersedia = JadwalKuliah::with(['mataKuliah', 'dosen.person'])
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->whereHas('mataKuliah', function($q) use ($activeKurikulumId) {
                    $q->where('prodi_id', $this->prodi_id ?? $this->mahasiswa->prodi_id)
                      ->where(function($sq) use ($activeKurikulumId) {
                          $sq->whereIn('id', function($sub) use ($activeKurikulumId) {
                              $sub->select('mata_kuliah_id')
                                  ->from('kurikulum_mata_kuliah')
                                  ->where('kurikulum_id', $activeKurikulumId);
                          })
                          ->orWhereIn('id', function($sub) use ($activeKurikulumId) {
                              $sub->select('mk_tujuan_id')
                                  ->from('akademik_ekuivalensi')
                                  ->where('is_active', true)
                                  ->whereIn('mk_asal_id', function($origin) use ($activeKurikulumId) {
                                      $origin->select('mata_kuliah_id')
                                             ->from('kurikulum_mata_kuliah')
                                             ->where('kurikulum_id', $activeKurikulumId);
                                  });
                          });
                      });
                })
                ->whereNotIn('mata_kuliah_id', $takenMkIds)
                ->get();
        }

        return view('livewire.mahasiswa.krs-page', [
            'krsDiambil' => $krsDiambil,
            'jadwalTersedia' => $jadwalTersedia
        ]);
    }

    public function confirmHapus($id) { if($this->statusKrs == 'DRAFT') KrsDetail::destroy($id); $this->hitungSks(); }
    public function ajukanKrs() { Krs::find($this->krsId)->update(['status_krs' => 'AJUKAN']); $this->statusKrs = 'AJUKAN'; }
}