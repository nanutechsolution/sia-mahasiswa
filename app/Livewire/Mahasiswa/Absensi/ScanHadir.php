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

class ScanHadir extends Component
{
    public $jadwalAktif;
    public $sesiAktif;
    public $sudahAbsen = false;
    public $waktuAbsen;
    
    // Input Data
    public $inputToken = ''; // Default empty string
    public $latitude;
    public $longitude;
    public $accuracy;

    // Feedback UI
    public $notifMessage = null;
    public $notifType = null; 

    // Dashboard Data
    public $riwayatAbsensi = [];

    // KONFIGURASI LOKASI KAMPUS (Contoh: Monas Jakarta)
    protected $latKampus = -6.175392; 
    protected $longKampus = 106.827153;
    protected $maxRadiusMeter = 100;

    public function mount()
    {
        $this->cekJadwalBerlangsung();
    }

    private function getMahasiswaId()
    {
        return Auth::user()->person?->mahasiswa?->id ?? null;
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 99999;

        $earthRadius = 6371000;
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

        // Ambil SEMUA KRS yang jadwalnya punya sesi status 'dibuka'
        $krsList = KrsDetail::query()
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->whereHas('jadwalKuliah', fn($q) => 
                $q->whereHas('sesi', fn($s) => $s->where('status_sesi', 'dibuka'))
            )
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->get();

        $this->reset(['jadwalAktif', 'sesiAktif']);
        
        foreach ($krsList as $krs) {
            // Query Manual Sesi Aktif
            $sesi = PerkuliahanSesi::where('jadwal_kuliah_id', $krs->jadwal_kuliah_id)
                ->where('status_sesi', 'dibuka')
                ->latest('created_at')
                ->first();

            if ($sesi) {
                // Cek absensi
                $absen = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $sesi->id)
                    ->where('krs_detail_id', $krs->id)
                    ->first();

                $this->jadwalAktif = $krs->jadwalKuliah;
                $this->sesiAktif = $sesi;
                
                if ($absen) {
                    $this->sudahAbsen = true;
                    $this->waktuAbsen = $absen->waktu_check_in ? $absen->waktu_check_in->format('H:i') : 'Manual';
                } else {
                    $this->sudahAbsen = false;
                    $this->waktuAbsen = null;
                    break; 
                }
            }
        }

        if ($this->jadwalAktif) {
            $this->loadRiwayatAbsensi();
        } else {
            $this->riwayatAbsensi = [];
        }
    }

    public function loadRiwayatAbsensi()
    {
        $mahasiswaId = $this->getMahasiswaId();
        
        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $this->jadwalAktif->id)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->first();

        if ($krsDetail) {
            $this->riwayatAbsensi = PerkuliahanAbsensi::with('sesi')
                ->where('krs_detail_id', $krsDetail->id)
                ->orderByDesc('created_at')
                ->take(5) 
                ->get();
        } else {
            $this->riwayatAbsensi = [];
        }
    }

    private function notify($type, $msg)
    {
        $this->notifType = $type;
        $this->notifMessage = $msg;
    }

    public function checkIn()
    {
        $this->reset(['notifMessage', 'notifType']);
        
        // 0. Refresh Data (Cegah Race Condition: Sesi ditutup saat tombol ditekan)
        $this->cekJadwalBerlangsung();

        // 1. Validasi Keberadaan Sesi
        if (!$this->jadwalAktif || !$this->sesiAktif) {
            $this->notify('error', 'Gagal: Sesi perkuliahan telah berakhir atau ditutup.');
            return;
        }

        // 2. Validasi Sudah Absen
        if ($this->sudahAbsen) {
            $this->notify('info', 'Anda sudah tercatat hadir sebelumnya.');
            return;
        }

        $metode = $this->sesiAktif->metode_validasi; // GPS, QR, DARING, MANUAL
        $jarak = 0;

        // --- VALIDASI METODE: MANUAL ---
        if ($metode === 'MANUAL') {
            $this->notify('warning', 'Kelas ini menggunakan presensi manual. Silakan lapor ke Dosen.');
            return;
        }

        // --- VALIDASI METODE: QR TOKEN ---
        if ($metode === 'QR') {
            // Cek Input Token
            if (empty($this->inputToken)) {
                $this->notify('error', 'Token Wajib Diisi! Masukkan kode dari layar proyektor.');
                return;
            }

            // Cocokkan Token (Case Insensitive & Trim Spasi)
            $tokenValid = strtoupper(trim($this->sesiAktif->token_sesi));
            $tokenInput = strtoupper(trim($this->inputToken));

            if ($tokenInput !== $tokenValid) {
                $this->notify('error', 'Token Salah! Kode yang Anda masukkan tidak sesuai.');
                return;
            }
        }

        // --- VALIDASI METODE: GPS ---
        if ($metode === 'GPS') {
            if ($this->latitude && $this->longitude) {
                // Cek Akurasi (Anti Fake GPS Kasar)
                if ($this->accuracy && $this->accuracy > 500) {
                     // Jika akurasi radius > 500m, tolak karena terlalu tidak presisi
                     $this->notify('warning', 'Sinyal GPS Lemah. Akurasi data lokasi rendah. Silakan cari area terbuka.');
                     return;
                }

                $jarak = $this->hitungJarak($this->latitude, $this->longitude, $this->latKampus, $this->longKampus);
                
                if ($jarak > $this->maxRadiusMeter) {
                    $jarakFmt = number_format($jarak, 0);
                    $this->notify('error', "Di Luar Jangkauan: Anda berjarak $jarakFmt m dari kampus. Wajib < {$this->maxRadiusMeter}m.");
                    return;
                }
            } else {
                $this->notify('warning', 'GPS Mati: Browser tidak mengirim data lokasi. Aktifkan GPS.');
                return;
            }
        }

        // --- PROSES SIMPAN DATA ---
        $mahasiswaId = $this->getMahasiswaId();
        if (!$mahasiswaId) {
            $this->notify('error', 'Error Sistem: Data mahasiswa tidak ditemukan.');
            return;
        }

        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $this->jadwalAktif->id)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->first();

        if (!$krsDetail) {
            $this->notify('error', 'Akses Ditolak: Anda tidak terdaftar di kelas ini.');
            return;
        }

        try {
            PerkuliahanAbsensi::create([
                'perkuliahan_sesi_id' => $this->sesiAktif->id,
                'krs_detail_id' => $krsDetail->id,
                'status_kehadiran' => 'H',
                'waktu_check_in' => Carbon::now(),
                'bukti_validasi' => [
                    'ip' => Request::ip(),
                    'ua' => Request::header('User-Agent'),
                    'lat' => $this->latitude,
                    'long' => $this->longitude,
                    'acc' => $this->accuracy,
                    'token_input' => $this->inputToken, // Simpan token yang diinput user
                    'distance' => $jarak,
                    'method' => $metode . '_CHECK',
                    'device_timestamp' => Carbon::now()->timestamp
                ],
                'is_manual_update' => false
            ]);

            $this->sudahAbsen = true;
            $this->waktuAbsen = Carbon::now()->format('H:i');
            $this->loadRiwayatAbsensi();
            
            $this->notify('success', 'Berhasil! Presensi Anda telah tercatat.');

        } catch (\Exception $e) {
            Log::error("Absensi Error: " . $e->getMessage());
            $this->notify('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function cetakRekapan() 
    {
        if (!$this->jadwalAktif) {
            $this->notify('error', 'Tidak ada mata kuliah aktif untuk dicetak.');
            return;
        }
        return redirect()->route('mhs.cetak.rekapan', ['jadwalId' => $this->jadwalAktif->id]);
    }

    public function render()
    {
        return view('livewire.mahasiswa.absensi.scan-hadir');
    }
}