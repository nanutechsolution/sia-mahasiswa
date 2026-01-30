<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\AturanSks;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
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
    public $maxSks = 24;
    public $ipsLalu = 0;
    public $semesterBerjalan = 1;

    // State & Blocking
    public $blockKrs = false;
    public $pesanBlock = '';
    public $keuanganLunas = false;
    public $logs = []; // Laporan proses paket

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        if (!$this->tahunAkademikId) {
            $this->blockAccess('Tidak ada Tahun Akademik yang aktif saat ini.');
            return;
        }

        $user = Auth::user();

        // [SSOT VALIDATION]
        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT). Hubungi Admin.');
        }

        $this->mahasiswa = Mahasiswa::with(['programKelas', 'prodi', 'person', 'dosenWali.person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();

        // 1. Validasi Berjenjang (Camaba -> PA -> Keuangan)
        if ($this->cekValidasiAwal()) return;

        // 2. Inisialisasi Header KRS & Hitung Jatah
        $this->loadKrsHeader();
        $this->hitungSemesterDanJatahSks();

        // 3. Auto-Take Paket (Hanya jika DRAFT, Paket Aktif, dan Belum ada MK)
        $jumlahAmbil = KrsDetail::where('krs_id', $this->krsId)->count();
        if ($this->isPaket && $this->statusKrs == 'DRAFT' && $jumlahAmbil == 0) {
            $this->ambilPaketOtomatis();
        }
    }

    /**
     * LOGIKA UTAMA: Pengambilan Paket Otomatis (Cerdas)
     */
    public function ambilPaketOtomatis()
    {
        $this->logs = [];

        // A. Cari Kurikulum Aktif Prodi
        $activeKurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->orderBy('tahun_mulai', 'desc')
            ->first();

        if (!$activeKurikulum) {
            $this->addLog('danger', 'Gagal: Kurikulum aktif tidak ditemukan.');
            return;
        }

        // B. Ambil MK Paket Semester Ini
        $mkPaket = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $activeKurikulum->id)
            ->where('semester_paket', $this->semesterBerjalan)
            ->get(); // Collection pivot

        if ($mkPaket->isEmpty()) {
            $this->addLog('warning', "Belum ada mata kuliah yang diset untuk Semester {$this->semesterBerjalan}.");
            return;
        }

        foreach ($mkPaket as $item) {
            $mkMaster = MataKuliah::find($item->mata_kuliah_id);
            if (!$mkMaster) continue;

            // C. Cek Prasyarat (Lulus Minimal D misalnya)
            if ($item->prasyarat_mk_id) {
                if (!$this->cekKelulusanPrasyarat($item->prasyarat_mk_id, $item->min_nilai_prasyarat)) {
                    $mkSyarat = MataKuliah::find($item->prasyarat_mk_id);
                    $nmSyarat = $mkSyarat->nama_mk ?? 'Unknown';
                    $this->addLog('danger', "Gagal ambil <strong>{$mkMaster->nama_mk}</strong>: Belum lulus prasyarat <em>{$nmSyarat}</em> (Min: {$item->min_nilai_prasyarat}).");
                    continue;
                }
            }

            // D. Cari Jadwal (First Fit Algorithm)
            // Prioritaskan jadwal yang:
            // 1. Sesuai Prodi & MK
            // 2. Kuota belum penuh
            // 3. Tidak bentrok waktu
            // 4. Sesuai program kelas mahasiswa (Reguler/Ekstensi)

            $jadwalCandidates = JadwalKuliah::withCount(['krsDetails' => function ($q) {
                $q->whereHas('krs', fn($k) => $k->where('status_krs', 'DISETUJUI'));
            }])
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->where('mata_kuliah_id', $item->mata_kuliah_id)
                ->where(function ($q) {
                    $q->whereNull('id_program_kelas_allow')
                        ->orWhere('id_program_kelas_allow', $this->mahasiswa->program_kelas_id);
                })
                ->get();

            $jadwalFinal = null;

            foreach ($jadwalCandidates as $jadwal) {
                // Cek Kuota
                if ($jadwal->krs_details_count >= $jadwal->kuota_kelas) continue;

                // Cek Bentrok
                if ($this->isBentrok($jadwal)) continue;

                $jadwalFinal = $jadwal;
                break; // Found one!
            }

            if ($jadwalFinal) {
                // Simpan
                KrsDetail::firstOrCreate([
                    'krs_id' => $this->krsId,
                    'jadwal_kuliah_id' => $jadwalFinal->id
                ], [
                    'status_ambil' => 'B',
                    'is_published' => false
                ]);
                $this->addLog('success', "Berhasil: {$mkMaster->nama_mk} (Kelas {$jadwalFinal->nama_kelas})");
            } else {
                $this->addLog('danger', "Gagal ambil <strong>{$mkMaster->nama_mk}</strong>: Kelas penuh atau jadwal bentrok/tidak tersedia.");
            }
        }

        $this->hitungSks();
    }

    /**
     * Pengambilan Manual (SKS Murni)
     */
    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs || $this->statusKrs !== 'DRAFT') return;

        $jadwal = JadwalKuliah::with('mataKuliah')->find($jadwalId);
        if (!$jadwal) return;

        // 1. Cek SKS Limit
        $sksBaru = $jadwal->mataKuliah->sks_default;
        if (($this->totalSks + $sksBaru) > $this->maxSks) {
            session()->flash('error', "Gagal: Melebihi batas {$this->maxSks} SKS.");
            return;
        }

        // 2. Cek Bentrok
        if ($this->isBentrok($jadwal)) {
            session()->flash('error', "Gagal: Jadwal bentrok dengan mata kuliah lain yang sudah diambil.");
            return;
        }

        // 3. Cek Prasyarat Manual
        if (!$this->validasiPrasyaratManual($jadwal->mata_kuliah_id)) {
            return; // Error message diset di dalam fungsi
        }

        try {
            KrsDetail::create([
                'krs_id' => $this->krsId,
                'jadwal_kuliah_id' => $jadwalId,
                'status_ambil' => 'B'
            ]);
            session()->flash('success', 'Mata kuliah berhasil diambil.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: MK sudah diambil.');
        }
    }

    // --- Helpers Validasi ---

    private function cekKelulusanPrasyarat($mkId, $minNilai = 'D')
    {
        $bobotMin = $this->getBobot($minNilai);
        $nilaiMhs = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
            ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->where('krs.mahasiswa_id', $this->mahasiswa->id)
            ->where('jadwal_kuliah.mata_kuliah_id', $mkId)
            ->where('krs_detail.is_published', true)
            ->max('krs_detail.nilai_indeks');
        return ($nilaiMhs !== null && $nilaiMhs >= $bobotMin);
    }

    private function validasiPrasyaratManual($mkId)
    {
        $kurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)->where('is_active', true)->orderBy('tahun_mulai', 'desc')->first();
        if (!$kurikulum) return true;

        $syarat = DB::table('kurikulum_mata_kuliah')
            ->where('kurikulum_id', $kurikulum->id)
            ->where('mata_kuliah_id', $mkId)
            ->first();

        if ($syarat && $syarat->prasyarat_mk_id) {
            if (!$this->cekKelulusanPrasyarat($syarat->prasyarat_mk_id, $syarat->min_nilai_prasyarat)) {
                $nm = MataKuliah::find($syarat->prasyarat_mk_id)->nama_mk ?? 'Unknown';
                session()->flash('error', "Prasyarat belum terpenuhi: Lulus {$nm} (Min: {$syarat->min_nilai_prasyarat})");
                return false;
            }
        }
        return true;
    }

    private function isBentrok($jadwalTarget)
    {
        $existingJadwals = KrsDetail::with('jadwalKuliah')
            ->where('krs_id', $this->krsId)
            ->get()->pluck('jadwalKuliah');

        foreach ($existingJadwals as $existing) {
            if ($existing->id == $jadwalTarget->id) continue;
            if ($existing->hari == $jadwalTarget->hari) {
                // Overlap: (StartA < EndB) && (EndA > StartB)
                if ($jadwalTarget->jam_mulai < $existing->jam_selesai && $jadwalTarget->jam_selesai > $existing->jam_mulai) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getBobot($huruf)
    {
        $map = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];
        if (str_contains($huruf, 'A')) return 4.0;
        if (str_contains($huruf, 'B')) return 3.0;
        if (str_contains($huruf, 'C')) return 2.0;
        return $map[$huruf] ?? 0;
    }


    private function cekValidasiAwal()
    {
        if (str_contains(strtoupper($this->mahasiswa->nim), 'PMB') || strlen($this->mahasiswa->nim) > 15) {
            $this->cekStatusKeuanganCamaba();
            return true;
        }
        if (!$this->mahasiswa->dosen_wali_id) {
            $this->blockAccess('Anda belum memiliki Dosen Wali (PA). Hubungi Admin Prodi.');
            return true;
        }
        if (!SistemHelper::isMasaKrsOpen()) {
            $this->blockAccess('Masa pengisian KRS belum dibuka/berakhir.');
            return true;
        }
        $this->cekStatusKeuanganMhs();
        if ($this->blockKrs) return true;
        return false;
    }

    private function blockAccess($msg)
    {
        $this->blockKrs = true;
        $this->pesanBlock = $msg;
    }

    private function cekStatusKeuanganMhs()
    {
        if ($this->mahasiswa->data_tambahan['bebas_keuangan'] ?? false) {
            $this->keuanganLunas = true;
            return;
        }
        $tagihan = TagihanMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)->where('tahun_akademik_id', $this->tahunAkademikId)->first();
        if (!$tagihan) {
            $this->blockAccess('Tagihan semester ini belum diterbitkan.');
            return;
        }
        $min = $this->mahasiswa->programKelas->min_pembayaran_persen ?? 50;
        $persen = ($tagihan->total_tagihan > 0) ? ($tagihan->total_bayar / $tagihan->total_tagihan * 100) : 100;
        if ($persen < $min) {
            $this->blockAccess("Wajib bayar minimal {$min}% dari total tagihan.");
        } else {
            $this->keuanganLunas = true;
        }
    }

    private function cekStatusKeuanganCamaba()
    {
        $this->blockKrs = true;
        $tagihan = TagihanMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)->where('tahun_akademik_id', $this->tahunAkademikId)->first();
        if ($this->mahasiswa->data_tambahan['bebas_keuangan'] ?? false) {
            $this->pesanBlock = 'Anda mendapatkan DISPENSASI. Hubungi BAAK untuk penerbitan NIM.';
        } elseif ($tagihan && $tagihan->status_bayar == 'LUNAS') {
            $this->pesanBlock = 'Pembayaran LUNAS. Tunggu BAAK menerbitkan NIM Resmi.';
        } else {
            $this->pesanBlock = 'Status CALON MAHASISWA. Lunasi tagihan Daftar Ulang untuk mendapatkan NIM.';
        }
    }

    public function loadKrsHeader()
    {
        $krs = Krs::firstOrCreate(
            ['mahasiswa_id' => $this->mahasiswa->id, 'tahun_akademik_id' => $this->tahunAkademikId],
            ['status_krs' => 'DRAFT', 'tgl_krs' => now(), 'dosen_wali_id' => $this->mahasiswa->dosen_wali_id]
        );

        // [PERBAIKAN LOGIKA]
        // Selama status masih DRAFT, selalu sinkronkan dengan settingan PRODI terbaru.
        // Ini mengatasi kasus Admin telat setting "Is Paket" di Master Prodi.
        if ($krs->status_krs == 'DRAFT') {
            $currentProdiSetting = (bool) ($this->mahasiswa->prodi->is_paket ?? true);

            // Update jika berbeda
            if ($krs->is_paket_snapshot !== $currentProdiSetting) {
                $krs->update(['is_paket_snapshot' => $currentProdiSetting]);
            }
            $this->isPaket = $currentProdiSetting;
        } else {
            // Jika sudah diajukan/disetujui, gunakan snapshot yang tersimpan (history aman)
            $this->isPaket = (bool) $krs->is_paket_snapshot;
        }

        $this->krsId = $krs->id;
        $this->statusKrs = $krs->status_krs;
    }

    public function hitungSemesterDanJatahSks()
    {
        $ta = TahunAkademik::find($this->tahunAkademikId);
        $tahunTa = (int) substr($ta->kode_tahun, 0, 4);
        $smtTipe = (int) substr($ta->kode_tahun, 4, 1);
        $angkatan = (int) $this->mahasiswa->angkatan_id;
        $this->semesterBerjalan = max(1, (($tahunTa - $angkatan) * 2) + ($smtTipe >= 2 ? 2 : 1));

        $riwayatLalu = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)->orderBy('tahun_akademik_id', 'desc')->first();
        if ($riwayatLalu) {
            $this->ipsLalu = $riwayatLalu->ips;
            $aturan = AturanSks::where('min_ips', '<=', $this->ipsLalu)->where('max_ips', '>=', $this->ipsLalu)->first();
            $this->maxSks = $aturan ? $aturan->max_sks : 24;
        } else {
            $this->maxSks = 20; // Default Maba
        }
    }

    // --- Helpers Render ---
    public function render()
    {
        $this->hitungSks();
        $semesterMap = [];
        $kurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)->where('is_active', true)->orderBy('tahun_mulai', 'desc')->first();
        if ($kurikulum) {
            $semesterMap = DB::table('kurikulum_mata_kuliah')->where('kurikulum_id', $kurikulum->id)->pluck('semester_paket', 'mata_kuliah_id')->toArray();
        }
        return view('livewire.mahasiswa.krs-page', ['semesterMap' => $semesterMap]);
    }
    public function getKrsDiambilProperty()
    {
        return $this->krsId ? KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])->where('krs_id', $this->krsId)->get() : collect();
    }
    public function getJadwalTersediaProperty()
    {
        if ($this->isPaket) return collect();
        $takenIds = $this->krsDiambil->pluck('jadwal_kuliah_id')->toArray();
        return JadwalKuliah::with(['mataKuliah', 'dosen.person'])->where('tahun_akademik_id', $this->tahunAkademikId)->whereNotIn('id', $takenIds)->get();
    }
    public function hitungSks()
    {
        $this->totalSks = $this->krsDiambil->sum(fn($d) => $d->jadwalKuliah->mataKuliah->sks_default ?? 0);
    }
    public function ajukanKrs()
    {
        Krs::find($this->krsId)->update(['status_krs' => 'AJUKAN', 'tgl_krs' => now()]);
        $this->statusKrs = 'AJUKAN';
        session()->flash('success', 'KRS Berhasil diajukan.');
    }
    public function hapusMatkul($detailId)
    {
        if ($this->statusKrs !== 'DRAFT') return;
        KrsDetail::destroy($detailId);
    }
    private function addLog($type, $msg)
    {
        $this->logs[] = ['type' => $type, 'msg' => $msg];
    }
}
