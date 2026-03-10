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
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\Ekuivalensi;
use App\Models\AkademikTranskrip; // Import model transkrip baru
use App\Models\KurikulumMataKuliah;
use App\Helpers\SistemHelper;

class KrsPage extends Component
{
    public $mahasiswa;
    public $tahunAkademikId;
    public $krsId;
    public $statusKrs = 'DRAFT';
    public $isPaket = false;

    // Stats
    public $totalSks = 0;
    public $maxSks = 24;
    public $semesterBerjalan = 1;

    // Blocking States
    public $blockKrs = false;
    public $pesanBlock = '';
    public $reasonType = ''; // 'NIM', 'FINANCE', 'LEAVE', 'SYSTEM'

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();
        if (!$this->tahunAkademikId) {
            $this->blockAccess('Sistem Belum Siap: Semester aktif tidak ditemukan.', 'SYSTEM');
            return;
        }

        $user = Auth::user();
        if (!$user->person_id) abort(403, 'Profil tidak terhubung.');

        $this->loadMahasiswaData();
        if ($this->cekValidasiAwal()) return;

        $this->loadKrsHeader();
        $this->hitungSemesterBerjalan();

        if ($this->isPaket && $this->statusKrs == 'DRAFT' && $this->totalSks == 0) {
            $this->ambilPaketOtomatis();
        }
    }

    private function loadMahasiswaData()
    {
        $this->mahasiswa = Mahasiswa::with(['prodi', 'person', 'programKelas', 'kurikulum'])
            ->where('person_id', Auth::user()->person_id)
            ->firstOrFail();
    }

    private function cekValidasiAwal()
    {
        if (!SistemHelper::isMasaKrsOpen()) {
            $this->blockAccess('Masa pengisian KRS untuk semester ini belum dibuka atau sudah berakhir.', 'SYSTEM');
            return true;
        }

        $riwayatStatus = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        if ($riwayatStatus && $riwayatStatus->status_kuliah === 'C') {
            $this->blockAccess('Akses Terkunci: Anda sedang dalam status Cuti Akademik pada semester ini.', 'LEAVE');
            return true;
        }

        // Cek Keuangan
        $isDispensasi = (bool) ($this->mahasiswa->data_tambahan['bebas_keuangan'] ?? false);
        if (!$isDispensasi) {
            $tagihan = DB::table('tagihan_mahasiswas')
                ->where('mahasiswa_id', $this->mahasiswa->id)
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->first();

            if (!$tagihan) {
                $this->blockAccess('Akses Terkunci: Data tagihan semester berjalan belum diterbitkan.', 'FINANCE');
                return true;
            }

            $minPercentage = $this->mahasiswa->programKelas->min_pembayaran_persen ?? 50;
            $paidPercentage = ($tagihan->total_tagihan > 0)
                ? round(($tagihan->total_bayar / $tagihan->total_tagihan) * 100) : 100;

            if ($paidPercentage < $minPercentage) {
                $this->blockAccess("Syarat Keuangan: Pembayaran baru {$paidPercentage}%. Minimal {$minPercentage}% untuk mengisi KRS.", 'FINANCE');
                return true;
            }
        }

        if (!$this->mahasiswa->dosen_wali_id) {
            $this->blockAccess('Akses Terkunci: Anda belum memiliki Dosen Wali (Pembimbing Akademik).', 'SYSTEM');
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
     * Logic Pengambilan Mata Kuliah dengan Validasi Prasyarat
     */
    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs || $this->statusKrs !== 'DRAFT') return;

        if (str_starts_with($jadwalId, 'SPECIAL_')) {
            $this->ambilMatkulSpesial(str_replace('SPECIAL_', '', $jadwalId));
            return;
        }

        $jadwal = JadwalKuliah::with(['mataKuliah', 'ruang'])->findOrFail($jadwalId);
        $mk = $jadwal->mataKuliah;

        // 1. Validasi Prasyarat (Many-to-Many)
        $unmetPrerequisites = $this->checkPrerequisites($mk->id);
        if (!empty($unmetPrerequisites)) {
            session()->flash('error', "Gagal: Anda belum memenuhi prasyarat lulus untuk MK: " . implode(', ', $unmetPrerequisites));
            return;
        }

        // 2. Validasi Kuota
        $currentParticipants = KrsDetail::where('jadwal_kuliah_id', $jadwalId)->count();
        if ($currentParticipants >= $jadwal->kuota_kelas) {
            session()->flash('error', "Gagal: Kuota kelas sudah penuh.");
            return;
        }

        // 3. Validasi SKS
        if ($mk->activity_type !== KrsDetail::TYPE_CONTINUATION && !$this->isPaket) {
            if (($this->totalSks + $mk->sks_default) > $this->maxSks) {
                session()->flash('error', "Gagal: SKS melebihi jatah maksimum.");
                return;
            }
        }

        // 4. Cari Jalur Ekuivalensi
        $ekuivalensi = Ekuivalensi::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('mk_tujuan_id', $mk->id)
            ->where('is_active', true)
            ->first();

        KrsDetail::updateOrCreate(
            ['krs_id' => $this->krsId, 'mata_kuliah_id' => $mk->id], // Sekarang unik per MK per semester
            [
                'jadwal_kuliah_id' => $jadwalId,
                'kode_mk_snapshot' => $mk->kode_mk,
                'nama_mk_snapshot' => $mk->nama_mk,
                'sks_snapshot'     => $mk->sks_default,
                'activity_type_snapshot' => $mk->activity_type ?? KrsDetail::TYPE_REGULAR,
                'ekuivalensi_id'   => $ekuivalensi ? $ekuivalensi->id : null,
                'status_ambil'     => 'B'
            ]
        );

        $this->hitungSks();
        session()->flash('success', "Mata kuliah {$mk->nama_mk} berhasil ditambahkan ke rencana studi.");
    }

    /**
     * Memeriksa apakah mahasiswa sudah lulus mata kuliah prasyarat
     */
    private function checkPrerequisites($mkId)
    {
        $kurikulumId = $this->mahasiswa->kurikulum_id ?? DB::table('master_kurikulums')
            ->where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->value('id');

        $kurikulumMk = KurikulumMataKuliah::where('kurikulum_id', $kurikulumId)
            ->where('mata_kuliah_id', $mkId)
            ->first();

        if (!$kurikulumMk) return [];

        $unmet = [];
        // Ambil daftar prasyarat dari tabel pivot baru
        $prasyarats = $kurikulumMk->prasyarats;

        foreach ($prasyarats as $p) {
            // Cek di tabel transkrip (Materialized View) untuk performa
            $passed = AkademikTranskrip::where('mahasiswa_id', $this->mahasiswa->id)
                ->where('mata_kuliah_id', $p->id)
                ->where('nilai_indeks_final', '>', 0) // Asumsi 0 adalah E/Tidak Lulus
                ->first();

            if (!$passed) {
                $unmet[] = $p->nama_mk;
            }
        }

        return $unmet;
    }

    protected function ambilMatkulSpesial($mkId)
    {
        $mk = MataKuliah::findOrFail($mkId);

        KrsDetail::updateOrCreate(
            ['krs_id' => $this->krsId, 'mata_kuliah_id' => $mk->id],
            [
                'jadwal_kuliah_id' => null,
                'kode_mk_snapshot' => $mk->kode_mk,
                'nama_mk_snapshot' => $mk->nama_mk,
                'sks_snapshot'     => $mk->sks_default,
                'activity_type_snapshot' => $mk->activity_type,
                'status_ambil'     => 'B'
            ]
        );

        $this->hitungSks();
        session()->flash('success', "Kegiatan {$mk->nama_mk} ditambahkan.");
    }

    public function hitungSks()
    {
        $this->totalSks = KrsDetail::where('krs_id', $this->krsId)->sum('sks_snapshot');
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

    public function hitungSemesterBerjalan()
    {
        $this->semesterBerjalan = SistemHelper::semesterMahasiswa($this->mahasiswa);
    }

    public function ajukanKrs()
    {
        Krs::find($this->krsId)->update(['status_krs' => 'AJUKAN']);
        $this->statusKrs = 'AJUKAN';
    }

    public function hapusMatkul($id)
    {
        if ($this->statusKrs == 'DRAFT') {
            KrsDetail::destroy($id);
            $this->hitungSks();
        }
    }

    public function ambilPaketOtomatis()
    {
        $curId = $this->mahasiswa->kurikulum_id ?? DB::table('master_kurikulums')
            ->where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->value('id');

        if (!$curId) return;

        $mkPaket = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $curId)
            ->where('semester_paket', $this->semesterBerjalan)->get();

        foreach ($mkPaket as $item) {
            $jadwal = JadwalKuliah::where('tahun_akademik_id', $this->tahunAkademikId)
                ->where('mata_kuliah_id', $item->mata_kuliah_id)
                ->first();

            if ($jadwal) $this->ambilMatkul($jadwal->id);
        }
    }

    public function render()
    {
        $this->semesterBerjalan = SistemHelper::semesterMahasiswa($this->mahasiswa);

        // Load dengan relasi Team Teaching (dosens) dan Ruangan
        $krsDiambil = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosens.person', 'jadwalKuliah.ruang'])
            ->where('krs_id', $this->krsId)->get();

        $takenMkIds = $krsDiambil->pluck('mata_kuliah_id')->filter()->toArray();
        $takenMkCodes = $krsDiambil->pluck('kode_mk_snapshot')->toArray();

        $jadwalTersedia = collect();

        if (!$this->blockKrs) {
            $activeKurikulumId = $this->mahasiswa->kurikulum_id ?? DB::table('master_kurikulums')
                ->where('prodi_id', $this->mahasiswa->prodi_id)
                ->where('is_active', true)
                ->value('id');

            $ta = SistemHelper::getTahunAktif();
            $semesterAktif = $ta->semester; // 1 = ganjil, 2 = genap

            // AMBIL JADWAL REGULER (Filter Ganjil/Genap Kurikulum)
            $jadwalReguler = JadwalKuliah::with(['mataKuliah', 'dosens.person', 'ruang'])
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->whereHas('mataKuliah', function ($q) use ($activeKurikulumId, $semesterAktif) {
                    $q->where('prodi_id', $this->mahasiswa->prodi_id)
                        ->where('activity_type', KrsDetail::TYPE_REGULAR)
                        ->whereIn('id', function ($sub) use ($activeKurikulumId, $semesterAktif) {
                            $sub->select('mata_kuliah_id')
                                ->from('kurikulum_mata_kuliah')
                                ->where('kurikulum_id', $activeKurikulumId)
                                ->whereRaw('MOD(semester_paket, 2) = ?', [$semesterAktif % 2]);
                        });
                })
                ->whereNotIn('mata_kuliah_id', $takenMkIds)
                ->orderBy('hari')->orderBy('jam_mulai')
                ->get();

            $jadwalTersedia = $jadwalTersedia->merge($jadwalReguler);

            // AMBIL MK SPESIAL
            if ($this->semesterBerjalan >= 6) {
                $specialMks = MataKuliah::where('prodi_id', $this->mahasiswa->prodi_id)
                    ->whereIn('activity_type', [KrsDetail::TYPE_THESIS, KrsDetail::TYPE_MBKM, KrsDetail::TYPE_CONTINUATION])
                    ->whereNotIn('kode_mk', $takenMkCodes)
                    ->get();

                foreach ($specialMks as $mk) {
                    $dummyJadwal = new JadwalKuliah();
                    $dummyJadwal->id = 'SPECIAL_' . $mk->id;
                    $dummyJadwal->mata_kuliah_id = $mk->id;
                    $dummyJadwal->mataKuliah = $mk;
                    $dummyJadwal->nama_kelas = $mk->activity_type;
                    $dummyJadwal->hari = 'Fleksibel';
                    $dummyJadwal->jam_mulai = '00:00';
                    $dummyJadwal->jam_selesai = '23:59';
                    $dummyJadwal->ruang_id = null;
                    $jadwalTersedia->push($dummyJadwal);
                }
            }
        }

        return view('livewire.mahasiswa.krs-page', [
            'krsDiambil' => $krsDiambil,
            'jadwalTersedia' => $jadwalTersedia
        ]);
    }
}