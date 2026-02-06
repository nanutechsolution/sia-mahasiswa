<?php

namespace App\Livewire\Mahasiswa\Absensi;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\PerkuliahanSesi; // Import Model Sesi
use App\Domains\Akademik\Models\KrsDetail;
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
    
    // Data Lokasi
    public $latitude;
    public $longitude;
    public $accuracy;

    // Feedback UI
    public $notifMessage = null;
    public $notifType = null; 

    // KONFIGURASI LOKASI KAMPUS (Monas Jakarta)
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

        // 1. Ambil SEMUA KRS yang jadwalnya punya sesi status 'dibuka' (bukan cuma first)
        $krsList = KrsDetail::query()
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->whereHas('jadwalKuliah', fn($q) => 
                $q->whereHas('sesi', fn($s) => $s->where('status_sesi', 'dibuka'))
            )
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->get();

        $candidateKrs = null;
        $candidateSesi = null;

        // 2. Loop untuk mencari prioritas: Kelas yang BELUM absen
        foreach ($krsList as $krs) {
            // Cari sesi aktif secara manual (lebih akurat daripada relasi cached)
            $sesi = PerkuliahanSesi::where('jadwal_kuliah_id', $krs->jadwal_kuliah_id)
                ->where('status_sesi', 'dibuka')
                ->latest('updated_at') // Ambil yang paling baru diupdate
                ->first();
            
            if ($sesi) {
                // Cek apakah mahasiswa sudah absen di sesi ini
                $isAbsen = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $sesi->id)
                    ->where('krs_detail_id', $krs->id)
                    ->exists();

                // PRIORITAS UTAMA: Kelas yang belum diabsen
                if (!$isAbsen) {
                    $candidateKrs = $krs;
                    $candidateSesi = $sesi;
                    break; // Stop loop, kita temukan target utama
                }

                // PRIORITAS KEDUA: Simpan kelas yang sudah diabsen (sebagai backup tampilan)
                if (!$candidateKrs) {
                    $candidateKrs = $krs;
                    $candidateSesi = $sesi;
                }
            }
        }

        // 3. Set State
        if ($candidateKrs && $candidateSesi) {
            $this->jadwalAktif = $candidateKrs->jadwalKuliah;
            $this->sesiAktif = $candidateSesi;
            $this->cekStatusAbsensi();
        } else {
            $this->reset(['jadwalAktif', 'sesiAktif']);
        }
    }

    public function cekStatusAbsensi()
    {
        if (!$this->sesiAktif) return;
        $mahasiswaId = $this->getMahasiswaId();
        
        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $this->jadwalAktif->id)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->first();

        if ($krsDetail) {
            $absen = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $this->sesiAktif->id)
                ->where('krs_detail_id', $krsDetail->id)
                ->first();
            
            if ($absen) {
                $this->sudahAbsen = true;
                $this->waktuAbsen = $absen->waktu_check_in ? $absen->waktu_check_in->format('H:i') : 'Manual';
            } else {
                $this->sudahAbsen = false; // Reset jika ganti sesi
            }
        }
    }

    // Helper untuk kirim notifikasi ke UI
    private function notify($type, $msg)
    {
        $this->notifType = $type;
        $this->notifMessage = $msg;
    }

    public function checkIn()
    {
        // Reset notifikasi sebelumnya
        $this->reset(['notifMessage', 'notifType']);

        Log::info('ABSENSI_DEBUG: Start CheckIn', [
            'lat' => $this->latitude, 
            'long' => $this->longitude,
            'acc' => $this->accuracy
        ]);

        // 0. Refresh Data
        $this->cekJadwalBerlangsung();

        // 1. Validasi Jadwal
        if (!$this->jadwalAktif || !$this->sesiAktif) {
            $this->notify('error', 'Gagal: Tidak ada kelas yang sedang berlangsung saat ini.');
            return;
        }

        // 2. Validasi Sudah Absen
        if ($this->sudahAbsen) {
            $this->notify('info', 'Status kehadiran Anda sudah tercatat (Hadir/Ijin/Sakit).');
            return;
        }

        // 3. Validasi Geolocation & Anti-Fake GPS
        if ($this->latitude && $this->longitude) {
            
            // [BARU] Cek Akurasi Sinyal
            // Jika akurasi > radius kampus (misal sinyal meleset 500m), tolak karena tidak presisi.
            if ($this->accuracy && $this->accuracy > $this->maxRadiusMeter) {
                $this->notify('warning', 'Sinyal GPS Lemah: Akurasi data lokasi kurang presisi (±' . round($this->accuracy) . 'm). Silakan cari area terbuka.');
                return;
            }

            $jarak = $this->hitungJarak($this->latitude, $this->longitude, $this->latKampus, $this->longKampus);
            Log::info("ABSENSI_DEBUG: Jarak user $jarak meter");

            if ($jarak > $this->maxRadiusMeter) {
                // Konversi ke KM jika > 1000m agar lebih enak dibaca
                $jarakTampil = $jarak > 1000 
                    ? number_format($jarak / 1000, 2) . ' KM' 
                    : number_format($jarak, 0) . ' Meter';

                $this->notify('error', "Gagal: Lokasi Anda terlalu jauh ($jarakTampil). Harap mendekat ke area kampus.");
                return;
            }
        } else {
            $this->notify('warning', 'GPS Mati: Gagal mendeteksi lokasi. Pastikan GPS browser aktif.');
            return;
        }

        // 4. Validasi Data Mahasiswa
        $mahasiswaId = $this->getMahasiswaId();
        if (!$mahasiswaId) {
            $this->notify('error', 'Error Data: Data mahasiswa tidak ditemukan.');
            return;
        }

        $krsDetail = KrsDetail::where('jadwal_kuliah_id', $this->jadwalAktif->id)
            ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mahasiswaId))
            ->first();

        if (!$krsDetail) {
            $this->notify('error', 'Akses Ditolak: Anda tidak terdaftar di kelas ini.');
            return;
        }

        // 5. Simpan Data
        try {
            PerkuliahanAbsensi::create([
                'perkuliahan_sesi_id' => $this->sesiAktif->id,
                'krs_detail_id' => $krsDetail->id,
                'status_kehadiran' => 'H',
                'waktu_check_in' => Carbon::now(),
                'bukti_validasi' => [
                    'ip' => Request::ip(),
                    'user_agent' => Request::header('User-Agent'),
                    'lat' => $this->latitude,
                    'long' => $this->longitude,
                    'accuracy' => $this->accuracy, // Simpan akurasi untuk audit
                    'distance' => $jarak ?? 0,
                    'method' => 'GPS_CHECK',
                    'device_timestamp' => Carbon::now()->timestamp
                ],
                'is_manual_update' => false
            ]);

            $this->sudahAbsen = true;
            $this->waktuAbsen = Carbon::now()->format('H:i');
            
            $this->notify('success', 'Berhasil! Check-in kehadiran Anda telah tercatat.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->notify('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function cetakRekapan()
    {
        // Logic redirect
    }

    public function render()
    {
        return view('livewire.mahasiswa.absensi.scan-hadir');
    }
}