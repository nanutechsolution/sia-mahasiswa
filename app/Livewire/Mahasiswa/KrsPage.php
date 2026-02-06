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
use App\Domains\Akademik\Models\Ekuivalensi;
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
        $this->mahasiswa = Mahasiswa::with(['prodi', 'person', 'programKelas'])
            ->where('person_id', Auth::user()->person_id)
            ->firstOrFail();
    }

  private function cekValidasiAwal()
    {
        // A. CEK MASA KRS
        if (!SistemHelper::isMasaKrsOpen()) {
            $this->blockAccess('Masa pengisian KRS untuk semester ini belum dibuka atau sudah berakhir.', 'SYSTEM');
            return true;
        }

        // B. [NEW] CEK STATUS CUTI (Administrative Guard)
        $riwayatStatus = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        if ($riwayatStatus && $riwayatStatus->status_kuliah === 'C') {
            $this->blockAccess('Akses Terkunci: Anda sedang dalam status Cuti Akademik pada semester ini.', 'LEAVE');
            return true;
        }

        // C. CEK KEUANGAN (Dispensasi vs Realisasi)
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

        // D. CEK DOSEN WALI
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

    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs || $this->statusKrs !== 'DRAFT') return;

        // Cek Special MK
        if (str_starts_with($jadwalId, 'SPECIAL_')) {
            $this->ambilMatkulSpesial(str_replace('SPECIAL_', '', $jadwalId));
            return;
        }

        $jadwal = JadwalKuliah::with('mataKuliah')->findOrFail($jadwalId);
        $mk = $jadwal->mataKuliah;

        // Validasi SKS
        if ($mk->activity_type !== KrsDetail::TYPE_CONTINUATION && !$this->isPaket) {
            if (($this->totalSks + $mk->sks_default) > $this->maxSks) {
                session()->flash('error', "Gagal: SKS melebihi jatah.");
                return;
            }
        }

        // Cari apakah MK ini diambil melalui jalur ekuivalensi
        $ekuivalensi = Ekuivalensi::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('mk_tujuan_id', $mk->id)
            ->where('is_active', true)
            ->first();

        KrsDetail::updateOrCreate(
            ['krs_id' => $this->krsId, 'jadwal_kuliah_id' => $jadwalId],
            [
                'kode_mk_snapshot' => $mk->kode_mk,
                'nama_mk_snapshot' => $mk->nama_mk,
                'sks_snapshot'     => $mk->sks_default,
                'activity_type_snapshot' => $mk->activity_type ?? KrsDetail::TYPE_REGULAR,
                'ekuivalensi_id'   => $ekuivalensi ? $ekuivalensi->id : null,
                'status_ambil'     => 'B'
            ]
        );

        $this->hitungSks();
        session()->flash('success', "{$mk->nama_mk} berhasil ditambahkan.");
    }

    protected function ambilMatkulSpesial($mkId)
    {
        $mk = MataKuliah::findOrFail($mkId);

        if ($mk->activity_type !== KrsDetail::TYPE_CONTINUATION) {
            if (($this->totalSks + $mk->sks_default) > $this->maxSks) {
                session()->flash('error', "Gagal: SKS melebihi jatah.");
                return;
            }
        }

        KrsDetail::updateOrCreate(
            ['krs_id' => $this->krsId, 'kode_mk_snapshot' => $mk->kode_mk],
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
        session()->flash('success', "{$mk->nama_mk} berhasil ditambahkan.");
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
        $ta = DB::table('ref_tahun_akademik')->find($this->tahunAkademikId);
        $tahunTa = (int) substr($ta->kode_tahun, 0, 4);
        $smtTipe = (int) substr($ta->kode_tahun, 4, 1);
        $angkatan = (int) preg_replace('/[^0-9]/', '', $this->mahasiswa->angkatan_id);
        $this->semesterBerjalan = max(1, (($tahunTa - $angkatan) * 2) + ($smtTipe >= 2 ? 2 : 1));
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


    public function render()
    {
        $krsDiambil = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->where('krs_id', $this->krsId)->get();

        $takenMkIds = $krsDiambil->pluck('jadwalKuliah.mata_kuliah_id')->filter()->toArray();
        $takenMkCodes = $krsDiambil->pluck('kode_mk_snapshot')->toArray();

        $jadwalTersedia = collect();

        if (!$this->blockKrs) {
            $activeKurikulumId = DB::table('master_kurikulums')
                ->where('prodi_id', $this->mahasiswa->prodi_id)
                ->where('is_active', true)
                ->value('id');

            // 1. AMBIL JADWAL REGULER
            // [FIX] Tambahkan use ($takenMkIds) agar variabel bisa diakses dalam closure
            $jadwalReguler = JadwalKuliah::with(['mataKuliah', 'dosen.person'])
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->whereHas('mataKuliah', function ($q) use ($activeKurikulumId) {
                    $q->where('prodi_id', $this->mahasiswa->prodi_id)
                        ->where('activity_type', KrsDetail::TYPE_REGULAR)
                        ->where(function ($sq) use ($activeKurikulumId) {
                            $sq->whereIn('id', function ($sub) use ($activeKurikulumId) {
                                $sub->select('mata_kuliah_id')
                                    ->from('kurikulum_mata_kuliah')
                                    ->where('kurikulum_id', $activeKurikulumId);
                            })
                                ->orWhereIn('id', function ($sub) use ($activeKurikulumId) {
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
                })
                ->whereNotIn('mata_kuliah_id', $takenMkIds)
                ->orderBy('hari')->orderBy('jam_mulai')
                ->get();

            $jadwalTersedia = $jadwalTersedia->merge($jadwalReguler);

            // 2. AMBIL MK SPESIAL (Semester Akhir)
            if ($this->semesterBerjalan >= 7) {
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
                    $dummyJadwal->ruang = '-';

                    $dosen = new \stdClass();
                    $dosen->person = (object)['nama_lengkap' => 'Koordinator Prodi'];
                    $dummyJadwal->setRelation('dosen', $dosen);

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
