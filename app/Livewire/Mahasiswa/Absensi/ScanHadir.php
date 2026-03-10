<?php

namespace App\Livewire\Mahasiswa\Absensi;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon;
use Illuminate\Support\Str;

class ScanHadir extends Component
{
    public $jadwalAktif;
    public $sesiAktif;
    public $krsDetailAktif;
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

        // Cari KrsDetail yang jadwalnya memiliki sesi status 'dibuka'
        // Join ke relasi untuk memastikan SSOT
        $krsDetail = KrsDetail::query()
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId)->where('status_krs', 'DISETUJUI'))
            ->whereHas('jadwalKuliah', function($q) {
                $q->whereHas('sesi', fn($s) => $s->where('status_sesi', 'dibuka'));
            })
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosens.person', 'jadwalKuliah.ruang'])
            ->first();

        $this->reset(['jadwalAktif', 'sesiAktif', 'krsDetailAktif', 'sudahAbsen', 'waktuAbsen']);

        if ($krsDetail) {
            $this->krsDetailAktif = $krsDetail;
            $this->jadwalAktif = $krsDetail->jadwalKuliah;
            
            // Ambil sesi terbaru yang dibuka untuk jadwal ini
            $this->sesiAktif = PerkuliahanSesi::where('jadwal_kuliah_id', $this->jadwalAktif->id)
                ->where('status_sesi', 'dibuka')
                ->latest('created_at')
                ->first();

            if ($this->sesiAktif) {
                $absen = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $this->sesiAktif->id)
                    ->where('krs_detail_id', $this->krsDetailAktif->id)
                    ->first();

                if ($absen) {
                    $this->sudahAbsen = true;
                    $this->waktuAbsen = $absen->waktu_check_in ? $absen->waktu_check_in->timezone('Asia/Makassar')->format('H:i') : 'Manual';
                }
                
                $this->loadRiwayatAbsensi();
            }
        }
    }

    public function loadRiwayatAbsensi()
    {
        if (!$this->krsDetailAktif) return;

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
        // Reset message setelah 5 detik di sisi frontend (dispatch browser event jika perlu)
    }

    public function checkIn()
    {
        $this->reset(['notifMessage', 'notifType']);
        $this->cekJadwalBerlangsung(); // Re-validation

        if (!$this->sesiAktif) {
            $this->notify('error', 'Sesi perkuliahan sudah ditutup oleh dosen.');
            return;
        }

        if ($this->sudahAbsen) {
            $this->notify('info', 'Anda sudah tercatat hadir.');
            return;
        }

        $metode = $this->sesiAktif->metode_validasi;
        $jarak = 0;

        // 1. Validasi Metode: QR/TOKEN
        if ($metode === 'QR') {
            if (empty($this->inputToken)) {
                $this->notify('error', 'Token wajib diisi. Lihat kode di proyektor kelas.');
                return;
            }
            if (strtoupper(trim($this->inputToken)) !== strtoupper(trim($this->sesiAktif->token_sesi))) {
                $this->notify('error', 'Token salah atau sudah kadaluarsa.');
                return;
            }
        }

        // 2. Validasi Metode: GPS
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

        // 3. Eksekusi Simpan
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
            $this->notify('success', 'Presensi berhasil tercatat. Selamat belajar!');
            $this->loadRiwayatAbsensi();

        } catch (\Exception $e) {
            Log::error("Absensi Gagal: " . $e->getMessage());
            $this->notify('error', 'Terjadi kesalahan sistem saat memproses presensi.');
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