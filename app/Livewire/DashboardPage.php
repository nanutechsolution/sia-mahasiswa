<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Helpers\SistemHelper;
use Carbon\Carbon;

class DashboardPage extends Component
{
    public $user;
    public $role;
    public $taAktif;
    public $greeting;

    // Shared States
    public $stats = [];
    public $notifications = [];
    public $scheduleToday = [];

    public function mount()
    {
        $this->user = Auth::user();
        if ($this->user->person_id) {
            $this->user->load('person.mahasiswa', 'person.dosen');
        }

        $this->role = $this->user->role;
        $this->taAktif = SistemHelper::getTahunAktif();
        
        $this->setGreeting();
        $this->loadDataByRole();
    }

    private function setGreeting()
    {
        $hour = date('H');
        if ($hour < 11) $this->greeting = 'Selamat Pagi';
        elseif ($hour < 15) $this->greeting = 'Selamat Siang';
        elseif ($hour < 19) $this->greeting = 'Selamat Sore';
        else $this->greeting = 'Selamat Malam';
    }

    public function loadDataByRole()
    {
        $taId = SistemHelper::idTahunAktif();
        $today = Carbon::now()->locale('id')->isoFormat('dddd');

        // --- 1. LOGIKA DASHBOARD MAHASISWA ---
        if ($this->role === 'mahasiswa') {
            $mhs = $this->user->person->mahasiswa ?? null;
            if (!$mhs) return;

            // Akademik Stats
            $riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $mhs->id)
                ->orderBy('id', 'desc')->first();
            
            $this->stats['academic'] = [
                'ipk' => $riwayat->ipk ?? 0.00,
                'ips_lalu' => $riwayat->ips ?? 0.00,
                'sks_total' => $riwayat->sks_total ?? 0,
            ];

            // Keuangan Stats (Kumulatif)
            $allTagihans = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)->get();
            $totalBill = $allTagihans->sum('total_tagihan');
            $totalPaid = $allTagihans->sum('total_bayar');
            
            $this->stats['finance'] = [
                'total_bill' => $totalBill,
                'total_paid' => $totalPaid,
                'debt' => max(0, $totalBill - $totalPaid),
                'deposit' => KeuanganSaldo::where('mahasiswa_id', $mhs->id)->value('saldo') ?? 0,
                'status_smt' => $allTagihans->where('tahun_akademik_id', $taId)->first()->status_bayar ?? 'N/A'
            ];

            // Gatekeepers (EDOM & Validasi)
            $this->stats['edom_pending'] = KrsDetail::whereHas('krs', function($q) use ($mhs, $taId) {
                    $q->where('mahasiswa_id', $mhs->id)->where('tahun_akademik_id', $taId);
                })
                ->where('is_published', true)
                ->where('is_edom_filled', false)
                ->count();

            // Jadwal Hari Ini
            $this->scheduleToday = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
                ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mhs->id)->where('tahun_akademik_id', $taId)->where('status_krs', 'DISETUJUI'))
                ->whereHas('jadwalKuliah', fn($q) => $q->where('hari', $today))
                ->get()
                ->sortBy('jadwalKuliah.jam_mulai');
        }

        // --- 2. LOGIKA DASHBOARD DOSEN ---
        elseif ($this->role === 'dosen') {
            $dosen = $this->user->person->dosen ?? null;
            if (!$dosen) return;

            $this->stats['teaching'] = [
                'total_kelas' => JadwalKuliah::where('dosen_id', $dosen->id)->where('tahun_akademik_id', $taId)->count(),
                'total_mhs_ajar' => KrsDetail::whereHas('jadwalKuliah', fn($q) => $q->where('dosen_id', $dosen->id)->where('tahun_akademik_id', $taId))->count(),
            ];

            $this->stats['mentorship'] = [
                'total_anak_wali' => Mahasiswa::where('dosen_wali_id', $dosen->id)->count(),
                'krs_pending' => Krs::whereHas('mahasiswa', fn($q) => $q->where('dosen_wali_id', $dosen->id))
                    ->where('tahun_akademik_id', $taId)
                    ->where('status_krs', 'AJUKAN')
                    ->count(),
            ];

            // Jadwal Mengajar Hari Ini
            $this->scheduleToday = JadwalKuliah::with(['mataKuliah'])
                ->where('dosen_id', $dosen->id)
                ->where('tahun_akademik_id', $taId)
                ->where('hari', $today)
                ->orderBy('jam_mulai')
                ->get();
        }

        // --- 3. LOGIKA DASHBOARD ADMIN (BARA/BAUK/SUPER) ---
        else {
            $this->stats['system'] = [
                'mhs_aktif' => RiwayatStatusMahasiswa::where('tahun_akademik_id', $taId)->where('status_kuliah', 'A')->count(),
                'pembayaran_pending' => PembayaranMahasiswa::where('status_verifikasi', 'PENDING')->count(),
                'krs_diajukan' => Krs::where('tahun_akademik_id', $taId)->where('status_krs', 'AJUKAN')->count(),
                'nilai_unpublished' => KrsDetail::whereHas('krs', fn($q) => $q->where('tahun_akademik_id', $taId))
                    ->where('is_published', false)->count(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard-page');
    }
}