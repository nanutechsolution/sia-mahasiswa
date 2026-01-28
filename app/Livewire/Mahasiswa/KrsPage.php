<?php

namespace App\Livewire\Mahasiswa;

use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\AturanSks; 
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah; 
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SistemHelper;
use App\Domains\Core\Models\TahunAkademik;
use Illuminate\Support\Facades\DB;

class KrsPage extends Component
{
    public $mahasiswa;
    public $tahunAkademikId;
    public $krsId;
    public $statusKrs; 
    public $totalSks = 0; 
    public $maxSks = 20; 
    public $ipsLalu = 0; 
    
    public $keuanganLunas = false;
    public $blockKrs = false;
    public $pesanBlock = '';

    public function mount()
    {
        $this->tahunAkademikId = SistemHelper::idTahunAktif();

        if (!$this->tahunAkademikId) {
            $this->blockKrs = true;
            $this->pesanBlock = 'Tidak ada Tahun Akademik yang aktif.';
            return;
        }

        $this->mahasiswa = Mahasiswa::with(['programKelas', 'prodi'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // CEK NIM SEMENTARA (CAMABA)
        if (str_contains(strtoupper($this->mahasiswa->nim), 'PMB') || strlen($this->mahasiswa->nim) > 15) {
            $this->blockKrs = true;
            $tagihan = TagihanMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
                ->where('tahun_akademik_id', $this->tahunAkademikId)
                ->first();
            
            if ($tagihan && $tagihan->status_bayar == 'LUNAS') {
                $this->pesanBlock = 'Pembayaran Daftar Ulang Anda sudah LUNAS. Mohon tunggu Bagian Akademik (BAAK) menerbitkan NIM Resmi Anda.';
            } else {
                $this->pesanBlock = 'Status Anda masih CALON MAHASISWA. Silakan selesaikan pembayaran Daftar Ulang agar NIM Resmi diterbitkan.';
            }
            return; 
        }

        if (!$this->blockKrs && !$this->mahasiswa->dosen_wali_id) {
            $this->blockKrs = true;
            $this->pesanBlock = 'Anda belum memiliki Dosen Wali (PA). Silakan hubungi Admin Prodi untuk plotting PA sebelum mengisi KRS.';
        }

        if (!$this->blockKrs && !SistemHelper::isMasaKrsOpen()) {
            $this->blockKrs = true;
            $this->pesanBlock = 'Masa pengisian KRS telah berakhir.';
        }

        if (!$this->blockKrs) {
            $this->cekStatusKeuangan();
        }

        $this->loadKrsHeader();
        $this->hitungMaxSks();
    }

    public function render()
    {
        unset($this->krsDiambil); 
        
        $this->totalSks = 0;
        if ($this->krsDiambil) {
            $this->totalSks = $this->krsDiambil->sum(function($detail) {
                return $detail->jadwalKuliah->mataKuliah->sks_default ?? 0;
            });
        }

        $semesterMap = [];
        $activeKurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->orderBy('tahun_mulai', 'desc') 
            ->first();

        if ($activeKurikulum) {
            $semesterMap = DB::table('kurikulum_mata_kuliah')
                ->where('kurikulum_id', $activeKurikulum->id)
                ->pluck('semester_paket', 'mata_kuliah_id')
                ->toArray();
        }

        return view('livewire.mahasiswa.krs-page', [
            'semesterMap' => $semesterMap
        ]);
    }
    
    public function hitungMaxSks()
    {
        $taAktif = TahunAkademik::find($this->tahunAkademikId);
        if (!$taAktif) return;

        $riwayatLalu = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->whereHas('tahunAkademik', function($q) use ($taAktif) {
                $q->where('kode_tahun', '<', $taAktif->kode_tahun);
            })
            ->join('ref_tahun_akademik', 'riwayat_status_mahasiswas.tahun_akademik_id', '=', 'ref_tahun_akademik.id')
            ->orderBy('ref_tahun_akademik.kode_tahun', 'desc')
            ->select('riwayat_status_mahasiswas.*')
            ->first();

        if (!$riwayatLalu) {
            $this->maxSks = 20; 
            $this->ipsLalu = 0;
            return;
        }

        $this->ipsLalu = $riwayatLalu->ips; 
        $ips = $riwayatLalu->ips;

        $aturan = AturanSks::where('min_ips', '<=', $ips)
            ->where('max_ips', '>=', $ips)
            ->first();

        if ($aturan) {
            $this->maxSks = $aturan->max_sks;
        } else {
            $this->maxSks = 18; 
        }
    }

    public function cekStatusKeuangan()
    {
        $tagihan = TagihanMahasiswa::where('mahasiswa_id', $this->mahasiswa->id)
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->first();

        if (!$tagihan) {
            $this->blockKrs = true;
            $this->pesanBlock = 'Tagihan semester ini belum diterbitkan.';
            return;
        }

        $persenBayar = $tagihan->total_tagihan > 0 
            ? ($tagihan->total_bayar / $tagihan->total_tagihan) * 100 
            : 100;
        
        $minBayar = $this->mahasiswa->programKelas->min_pembayaran_persen ?? 50;
        
        // Cek Dispensasi Khusus
        $dispensasi = $this->mahasiswa->data_tambahan['bebas_keuangan'] ?? false;

        if ($persenBayar < $minBayar && !$dispensasi) {
            $this->blockKrs = true;
            $this->keuanganLunas = false;
            $this->pesanBlock = "Syarat KRS: Wajib bayar minimal {$minBayar}% dari total tagihan. (Terbayar: " . number_format($persenBayar, 1) . "%)";
        } else {
            $this->keuanganLunas = true;
        }
    }

    public function loadKrsHeader()
    {
        if (!$this->tahunAkademikId) return;
        if ($this->blockKrs && (str_contains($this->pesanBlock, 'CALON MAHASISWA') || str_contains($this->pesanBlock, 'Pembayaran Daftar Ulang') || str_contains($this->pesanBlock, 'DISPENSASI'))) return;

        $krs = Krs::firstOrCreate(
            [
                'mahasiswa_id' => $this->mahasiswa->id,
                'tahun_akademik_id' => $this->tahunAkademikId
            ],
            [
                'status_krs' => 'DRAFT',
                'tgl_krs' => now(),
                'dosen_wali_id' => $this->mahasiswa->dosen_wali_id 
            ]
        );
        
        $this->krsId = $krs->id;
        $this->statusKrs = $krs->status_krs; 
    }

    public function getJadwalTersediaProperty()
    {
        if (!$this->tahunAkademikId) return [];

        $takenJadwalIds = [];
        if ($this->krsDiambil) {
            $takenJadwalIds = $this->krsDiambil->pluck('jadwal_kuliah_id')->toArray();
        }

        return JadwalKuliah::with(['mataKuliah', 'dosen'])
            ->where('tahun_akademik_id', $this->tahunAkademikId)
            ->where(function($q) {
                $q->whereNull('id_program_kelas_allow')
                  ->orWhere('id_program_kelas_allow', $this->mahasiswa->program_kelas_id);
            })
            ->whereNotIn('id', $takenJadwalIds) 
            ->orderBy('mata_kuliah_id')
            ->get();
    }

    public function getKrsDiambilProperty()
    {
        if (!$this->krsId) return collect();

        return KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->where('krs_id', $this->krsId)
            ->get();
    }

    public function ambilMatkul($jadwalId)
    {
        if ($this->blockKrs) return;
        
        if ($this->statusKrs !== 'DRAFT') {
            session()->flash('error', 'KRS sudah diajukan/disetujui. Tidak bisa ubah KRS.');
            return;
        }

        $jadwal = JadwalKuliah::with('mataKuliah')->find($jadwalId);
        if (!$jadwal) return;

        // 1. Validasi Batas SKS
        $sksBaru = $jadwal->mataKuliah->sks_default;
        
        if (($this->totalSks + $sksBaru) > $this->maxSks) {
            session()->flash('error', "Gagal ambil: Total SKS akan melebihi batas maksimal ({$this->maxSks} SKS).");
            return;
        }

        // 2. VALIDASI PRASYARAT (BARU)
        $kurikulum = Kurikulum::where('prodi_id', $this->mahasiswa->prodi_id)
            ->where('is_active', true)
            ->orderBy('tahun_mulai', 'desc')
            ->first();

        if ($kurikulum) {
            $syarat = DB::table('kurikulum_mata_kuliah')
                ->where('kurikulum_id', $kurikulum->id)
                ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
                ->first();

            if ($syarat && $syarat->prasyarat_mk_id) {
                // Cek apakah sudah lulus MK Prasyarat (Nilai bukan E)
                $sudahLulus = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
                    ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
                    ->where('krs.mahasiswa_id', $this->mahasiswa->id)
                    ->where('jadwal_kuliah.mata_kuliah_id', $syarat->prasyarat_mk_id)
                    ->where('krs_detail.is_published', true)
                    ->where('krs_detail.nilai_huruf', '!=', 'E') // Minimal lulus
                    ->exists();

                if (!$sudahLulus) {
                    $namaPrasyarat = MataKuliah::find($syarat->prasyarat_mk_id)->nama_mk ?? 'Unknown';
                    session()->flash('error', "Gagal ambil: Anda belum lulus mata kuliah prasyarat: {$namaPrasyarat}");
                    return;
                }
            }
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

    public function hapusMatkul($detailId)
    {
        if ($this->statusKrs !== 'DRAFT') {
            session()->flash('error', 'KRS terkunci.');
            return;
        }

        KrsDetail::destroy($detailId);
    }

    public function ajukanKrs()
    {
        unset($this->krsDiambil);
        $this->hitungSks();

        if ($this->totalSks < 2) {
            session()->flash('error', 'Minimal ambil 2 SKS untuk mengajukan.');
            return;
        }

        $krs = Krs::find($this->krsId);
        
        if ($krs) {
            $krs->update([
                'status_krs' => 'AJUKAN',
                'tgl_krs' => now()
            ]);

            $this->statusKrs = 'AJUKAN';
            session()->flash('success', 'KRS Berhasil diajukan ke Dosen Wali. Menunggu Persetujuan.');
        } else {
            session()->flash('error', 'Data KRS tidak ditemukan.');
        }
    }

    public function hitungSks()
    {
        if (!$this->krsDiambil) {
            $this->totalSks = 0;
            return;
        }
        
        $this->totalSks = $this->krsDiambil->sum(function($detail) {
            return $detail->jadwalKuliah->mataKuliah->sks_default ?? 0;
        });
    }
}