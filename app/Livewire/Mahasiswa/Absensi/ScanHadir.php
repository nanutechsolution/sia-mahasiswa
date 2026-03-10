<?php

namespace App\Livewire\Mahasiswa\Absensi;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use App\Models\JadwalUjianPeserta; // Import Model Ujian
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon;
use Illuminate\Support\Str;

class ScanHadir extends Component
{
    // Mode State: 'KULIAH' atau 'UJIAN'
    public $scanMode = null; 

    // Active Instances
    public $jadwalAktif;
    public $sesiAktif; // Untuk Kuliah
    public $ujianAktif; // Untuk Ujian
    public $krsDetailAktif;
    public $pesertaUjianAktif; // Untuk Ujian

    public $sudahAbsen = false;
    public $waktuAbsen;
    
    // Input Data
    public $inputToken = '';
    public $latitude;
    public $longitude;
    public $accuracy;

    // Feedback UI
    public $notifMessage = null;
    public $notifType = null; 

    public $riwayatAbsensi = [];

    // KONFIGURASI LOKASI KAMPUS (Sync dengan Titik Koordinat Kampus)
    protected $latKampus = -6.175392; 
    protected $longKampus = 106.827153;
    protected $maxRadiusMeter = 150; // Radius toleransi (meter)

    public function mount()
    {
        $this->cekJadwalBerlangsung();
    }

    private function getMahasiswaId()
    {
        return Auth::user()->person->mahasiswa->id ?? null;
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 99999;

        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function cekJadwalBerlangsung()
    {
        $mahasiswaId = $this->getMahasiswaId();
        if (!$mahasiswaId) return;

        // Reset semua state sebelum mengecek ulang
        $this->reset(['scanMode', 'jadwalAktif', 'sesiAktif', 'ujianAktif', 'pesertaUjianAktif', 'krsDetailAktif', 'sudahAbsen', 'waktuAbsen']);

        // ========================================================
        // 1. CEK PERKULIAHAN BIASA (Dosen membuka sesi)
        // ========================================================
        $krsDetailKuliah = KrsDetail::query()
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId)->where('status_krs', 'DISETUJUI'))
            ->whereHas('jadwalKuliah', function($q) {
                $q->whereHas('sesi', fn($s) => $s->where('status_sesi', 'dibuka'));
            })
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosens.person', 'jadwalKuliah.ruang'])
            ->first();

        if ($krsDetailKuliah) {
            $this->krsDetailAktif = $krsDetailKuliah;
            $this->jadwalAktif = $krsDetailKuliah->jadwalKuliah;
            
            $this->sesiAktif = PerkuliahanSesi::where('jadwal_kuliah_id', $this->jadwalAktif->id)
                ->where('status_sesi', 'dibuka')
                ->latest('created_at')
                ->first();

            if ($this->sesiAktif) {
                $this->scanMode = 'KULIAH';
                $absen = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $this->sesiAktif->id)
                    ->where('krs_detail_id', $this->krsDetailAktif->id)
                    ->first();

                if ($absen) {
                    $this->sudahAbsen = true;
                    $this->waktuAbsen = $absen->waktu_check_in ? $absen->waktu_check_in->timezone('Asia/Makassar')->format('H:i') : 'Manual';
                }
                
                $this->loadRiwayatAbsensi();
                return; // Stop, dahulukan kelas reguler jika ada
            }
        }

        // ========================================================
        // 2. CEK JADWAL UJIAN (UTS / UAS)
        // ========================================================
        $now = Carbon::now('Asia/Makassar');
        $today = $now->toDateString();

        $pesertaUjian = JadwalUjianPeserta::with([
                'jadwalUjian.jadwalKuliah.mataKuliah', 
                'jadwalUjian.ruang', 
                'jadwalUjian.jadwalKuliah.dosens.person', 
                'krsDetail'
            ])
            ->whereHas('krsDetail.krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId)->where('status_krs', 'DISETUJUI'))
            ->whereHas('jadwalUjian', function($q) use ($today, $now) {
                // Toleransi absen 30 menit sebelum ujian dimulai hingga ujian selesai
                $q->where('tanggal_ujian', $today)
                  ->whereTime('jam_mulai', '<=', $now->copy()->addMinutes(30)->toTimeString())
                  ->whereTime('jam_selesai', '>=', $now->toTimeString());
            })
            ->first();

        if ($pesertaUjian) {
            $this->scanMode = 'UJIAN';
            $this->pesertaUjianAktif = $pesertaUjian;
            $this->ujianAktif = $pesertaUjian->jadwalUjian;
            $this->jadwalAktif = $pesertaUjian->jadwalUjian->jadwalKuliah;
            $this->krsDetailAktif = $pesertaUjian->krsDetail;

            if ($pesertaUjian->status_kehadiran === 'H') {
                $this->sudahAbsen = true;
                $this->waktuAbsen = $pesertaUjian->waktu_check_in ? Carbon::parse($pesertaUjian->waktu_check_in)->timezone('Asia/Makassar')->format('H:i') : 'Manual';
            }
            $this->loadRiwayatAbsensi();
        }
    }

    public function loadRiwayatAbsensi()
    {
        if (!$this->krsDetailAktif) return;

        // Tampilkan 5 riwayat absensi kelas terakhir sebagai referensi
        $this->riwayatAbsensi = PerkuliahanAbsensi::with('sesi')
            ->where('krs_detail_id', $this->krsDetailAktif->id)
            ->orderByDesc('created_at')
            ->take(5) 
            ->get();
    }

    private function notify($type, $msg)
    {
        $this->notifType = $type;
        $this->notifMessage = $msg;
    }

    public function checkIn()
    {
        $this->reset(['notifMessage', 'notifType']);
        $this->cekJadwalBerlangsung(); // Re-validation

        if (!$this->scanMode) {
            $this->notify('error', 'Sesi perkuliahan atau ujian sudah ditutup / belum dimulai.');
            return;
        }

        if ($this->sudahAbsen) {
            $this->notify('info', 'Anda sudah tercatat hadir.');
            return;
        }

        // ========================================
        // CHECK IN MODE KULIAH REGULER
        // ========================================
        if ($this->scanMode === 'KULIAH') {
            $metode = $this->sesiAktif->metode_validasi;
            $jarak = 0;

            if ($metode === 'QR') {
                if (empty($this->inputToken) || strtoupper(trim($this->inputToken)) !== strtoupper(trim($this->sesiAktif->token_sesi))) {
                    $this->notify('error', 'Token salah atau sudah kadaluarsa.');
                    return;
                }
            }

            if ($metode === 'GPS') {
                if (!$this->latitude || !$this->longitude) {
                    $this->notify('warning', 'Data lokasi tidak tersedia. Pastikan GPS aktif.');
                    return;
                }
                $jarak = $this->hitungJarak($this->latitude, $this->longitude, $this->latKampus, $this->longKampus);
                if ($jarak > $this->maxRadiusMeter) {
                    $this->notify('error', "Terdeteksi di luar radius kampus (" . number_format($jarak, 0) . "m). Silakan mendekat ke area kelas.");
                    return;
                }
            }

            try {
                PerkuliahanAbsensi::create([
                    'id' => (string) Str::uuid(),
                    'perkuliahan_sesi_id' => $this->sesiAktif->id,
                    'krs_detail_id' => $this->krsDetailAktif->id,
                    'status_kehadiran' => 'H',
                    'waktu_check_in' => Carbon::now('Asia/Makassar'),
                    'bukti_validasi' => [
                        'ip' => Request::ip(),
                        'method' => $metode,
                        'dist' => round($jarak, 2),
                        'lat' => $this->latitude,
                        'long' => $this->longitude,
                        'acc' => $this->accuracy,
                        'ua' => Request::header('User-Agent')
                    ],
                    'is_manual_update' => false
                ]);

                $this->sudahAbsen = true;
                $this->waktuAbsen = Carbon::now('Asia/Makassar')->format('H:i');
                $this->notify('success', 'Presensi kuliah berhasil tercatat. Selamat belajar!');
                $this->loadRiwayatAbsensi();

            } catch (\Exception $e) {
                Log::error("Absensi Kuliah Gagal: " . $e->getMessage());
                $this->notify('error', 'Terjadi kesalahan sistem saat memproses presensi.');
            }

        // ========================================
        // CHECK IN MODE UJIAN (UTS / UAS)
        // ========================================
        } elseif ($this->scanMode === 'UJIAN') {
            
            // Wajib GPS untuk Ujian (Keamanan ketat)
            if (!$this->latitude || !$this->longitude) {
                $this->notify('warning', 'Data lokasi tidak tersedia. Absen ujian memerlukan Validasi GPS.');
                return;
            }

            $jarak = $this->hitungJarak($this->latitude, $this->longitude, $this->latKampus, $this->longKampus);
            
            if ($jarak > $this->maxRadiusMeter) {
                $this->notify('error', "Terdeteksi di luar kampus (" . number_format($jarak, 0) . "m). Dilarang melakukan absen ujian dari luar area kampus!");
                return;
            }

            try {
                $this->pesertaUjianAktif->update([
                    'status_kehadiran' => 'H',
                    'waktu_check_in' => Carbon::now('Asia/Makassar')
                ]);

                $this->sudahAbsen = true;
                $this->waktuAbsen = Carbon::now('Asia/Makassar')->format('H:i');
                $this->notify('success', 'Kehadiran ujian berhasil disahkan. Semoga sukses!');

            } catch (\Exception $e) {
                Log::error("Absensi Ujian Gagal: " . $e->getMessage());
                $this->notify('error', 'Terjadi kesalahan sistem saat menyimpan data kehadiran ujian.');
            }
        }
    }

    public function cetakRekapan() 
    {
        if (!$this->jadwalAktif) return;
        return redirect()->route('mhs.cetak.rekapan', ['jadwalId' => $this->jadwalAktif->id]);
    }

    public function render()
    {
        return view('livewire.mahasiswa.absensi.scan-hadir');
    }
}