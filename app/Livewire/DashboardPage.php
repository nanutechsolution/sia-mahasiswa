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
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Models\AkademikTranskrip; // Import Model Transkrip Baru
use App\Helpers\SistemHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DashboardPage extends Component
{
    public $user;
    public $role;
    public $taAktif;
    public $greeting;

    public $stats = [];
    public $notifications = [];
    public $scheduleToday = [];
    public $activeSurveys = [];

    public function mount()
    {
        $this->user = Auth::user();
        if ($this->user->person_id) {
            $this->user->load('person.mahasiswa', 'person.dosen');
        }

        $this->role = $this->user->role;
        $this->taAktif = SistemHelper::getTahunAktif();

        $this->setGreeting();
        $this->initStatsStructure();
        $this->loadDataByRole();
        $this->loadActiveSurvey();
    }

    private function initStatsStructure()
    {
        $this->stats = [
            'academic' => ['ipk' => 0.00, 'ips_lalu' => 0.00, 'sks_total' => 0],
            'finance' => ['total_bill' => 0, 'total_paid' => 0, 'debt' => 0, 'deposit' => 0, 'status_smt' => 'N/A'],
            'edom_pending' => 0,
            'teaching' => ['total_kelas' => 0, 'total_mhs_ajar' => 0],
            'mentorship' => ['total_anak_wali' => 0, 'krs_pending' => 0],
            'system' => ['mhs_aktif' => 0, 'pembayaran_pending' => 0, 'krs_diajukan' => 0, 'nilai_unpublished' => 0],
        ];
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

            // Stats Akademik: Sekarang mengambil dari Materialized Transkrip (Lebih Cepat & Akurat)
            $transkripData = AkademikTranskrip::where('mahasiswa_id', $mhs->id)->get();
            $totalSks = $transkripData->sum('sks_diakui');
            $totalBobot = $transkripData->sum(fn($item) => $item->sks_diakui * $item->nilai_indeks_final);
            
            $this->stats['academic'] = [
                'ipk' => $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.00,
                'sks_total' => $totalSks,
                'ips_lalu' => RiwayatStatusMahasiswa::where('mahasiswa_id', $mhs->id)->latest('id')->value('ips') ?? 0.00
            ];

            // Stats Keuangan
            $tagihans = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)->get();
            $this->stats['finance'] = [
                'total_bill' => $tagihans->sum('total_tagihan'),
                'total_paid' => $tagihans->sum('total_bayar'),
                'debt' => max(0, $tagihans->sum('total_tagihan') - $tagihans->sum('total_bayar')),
                'deposit' => KeuanganSaldo::where('mahasiswa_id', $mhs->id)->value('saldo') ?? 0,
                'status_smt' => $tagihans->where('tahun_akademik_id', $taId)->first()->status_bayar ?? 'N/A'
            ];

            // Jadwal Hari Ini: Join ke Team Teaching & Ruangan
            $this->scheduleToday = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosens.person', 'jadwalKuliah.ruang'])
                ->whereHas('krs', fn($q) => $q->where('mahasiswa_id', $mhs->id)->where('tahun_akademik_id', $taId)->where('status_krs', 'DISETUJUI'))
                ->whereHas('jadwalKuliah', fn($q) => $q->where('hari', $today))
                ->get()
                ->sortBy('jadwalKuliah.jam_mulai');
        }

        // --- 2. LOGIKA DASHBOARD DOSEN ---
        elseif ($this->role === 'dosen') {
            $dosen = $this->user->person->dosen ?? null;
            if (!$dosen) return;

            // Stats Mengajar: Sekarang menggunakan relasi Many-to-Many (Team Teaching)
            $this->stats['teaching'] = [
                'total_kelas' => JadwalKuliah::whereHas('dosens', fn($q) => $q->where('dosen_id', $dosen->id))
                    ->where('tahun_akademik_id', $taId)->count(),
                'total_mhs_ajar' => KrsDetail::whereHas('jadwalKuliah.dosens', fn($q) => $q->where('dosen_id', $dosen->id))
                    ->whereHas('krs', fn($q) => $q->where('tahun_akademik_id', $taId))->count(),
            ];

            $this->stats['mentorship'] = [
                'total_anak_wali' => Mahasiswa::where('dosen_wali_id', $dosen->id)->count(),
                'krs_pending' => Krs::whereHas('mahasiswa', fn($q) => $q->where('dosen_wali_id', $dosen->id))
                    ->where('tahun_akademik_id', $taId)->where('status_krs', 'AJUKAN')->count(),
            ];

            // Jadwal Mengajar Hari Ini
            $this->scheduleToday = JadwalKuliah::with(['mataKuliah', 'ruang', 'dosens.person'])
                ->whereHas('dosens', fn($q) => $q->where('dosen_id', $dosen->id))
                ->where('tahun_akademik_id', $taId)
                ->where('hari', $today)
                ->orderBy('jam_mulai')
                ->get();
        }

        // --- 3. LOGIKA DASHBOARD ADMIN ---
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

    private function loadActiveSurvey()
    {
        $identifier = $this->user->username;
        $cacheKey = 'siaset_active_surveys_' . $identifier;

        $this->activeSurveys = Cache::remember($cacheKey, 3600, function () use ($identifier) {
            try {
                $siAsetUrl = env('SIASET_URL', 'http://127.0.0.1:8000'); 
                $response = Http::timeout(3)->get("{$siAsetUrl}/api/surveys/active", ['identifier' => $identifier]);
                return $response->successful() ? ($response->json('data') ?? []) : [];
            } catch (\Exception $e) {
                return [];
            }
        });

        if (count($this->activeSurveys) > 0) {
            $this->notifications[] = [
                'type' => 'info',
                'title' => 'Survei Menunggu',
                'message' => 'Halo! Ada ' . count($this->activeSurveys) . ' survei fasilitas kampus yang menunggu partisipasi Anda.'
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard-page');
    }
}