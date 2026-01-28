<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Models\User;
use App\Helpers\SistemHelper;
use Carbon\Carbon;

class DashboardPage extends Component
{
    public $user;
    public $role;
    public $greeting;
    public $taAktif;

    // Data Containers
    public $stats = [];
    public $scheduleToday = [];
    public $announcements = [];
    public $todoList = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->role = $this->user->role;
        $this->taAktif = SistemHelper::getTahunAktif();
        
        $this->setGreeting();
        $this->loadDashboardData();
    }

    private function setGreeting()
    {
        $hour = date('H');
        if ($hour < 12) $this->greeting = 'Selamat Pagi';
        elseif ($hour < 15) $this->greeting = 'Selamat Siang';
        elseif ($hour < 18) $this->greeting = 'Selamat Sore';
        else $this->greeting = 'Selamat Malam';
    }

    public function loadDashboardData()
    {
        $taId = SistemHelper::idTahunAktif();
        $today = Carbon::now()->locale('id')->isoFormat('dddd'); // Senin, Selasa...

        // === DASHBOARD MAHASISWA ===
        if ($this->role == 'mahasiswa') {
            $mhs = Mahasiswa::where('user_id', $this->user->id)->first();
            
            if ($mhs) {
                // 1. Stats Utama
                $riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $mhs->id)
                    ->orderBy('tahun_akademik_id', 'desc')->first();
                
                $tagihan = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $taId)->first();

                $this->stats = [
                    'ipk' => $riwayat->ipk ?? 0.00,
                    'sks_total' => $riwayat->sks_total ?? 0,
                    'status_bayar' => $tagihan ? $tagihan->status_bayar : 'BELUM',
                    'tagihan_nominal' => $tagihan ? $tagihan->total_tagihan : 0,
                    'sisa_tagihan' => $tagihan ? $tagihan->sisa_tagihan : 0,
                ];

                // 2. Jadwal Hari Ini
                // Cari KRS yg diambil, lalu filter jadwalnya berdasarkan hari ini
                $krs = $mhs->krs()->where('tahun_akademik_id', $taId)->first();
                if ($krs) {
                    $this->scheduleToday = $krs->details()
                        ->whereHas('jadwalKuliah', function($q) use ($today) {
                            $q->where('hari', $today);
                        })
                        ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
                        ->get()
                        ->sortBy('jadwalKuliah.jam_mulai');
                }
            }
        } 
        
        // === DASHBOARD DOSEN ===
        elseif ($this->role == 'dosen') {
            $dosen = Dosen::where('user_id', $this->user->id)->first();
            
            if ($dosen) {
                // 1. Stats
                $kelasAjar = JadwalKuliah::where('dosen_id', $dosen->id)
                    ->where('tahun_akademik_id', $taId)->count();
                
                $mhsWali = Mahasiswa::where('dosen_wali_id', $dosen->id)
                    ->whereHas('riwayatStatus', function($q) use ($taId) {
                        $q->where('tahun_akademik_id', $taId)->where('status_kuliah', 'A');
                    })->count();

                $this->stats = [
                    'kelas_ajar' => $kelasAjar,
                    'mhs_wali' => $mhsWali,
                    'krs_perlu_acc' => \App\Domains\Akademik\Models\Krs::whereIn('mahasiswa_id', function($q) use ($dosen) {
                        $q->select('id')->from('mahasiswas')->where('dosen_wali_id', $dosen->id);
                    })->where('status_krs', 'AJUKAN')->where('tahun_akademik_id', $taId)->count()
                ];

                // 2. Jadwal Mengajar Hari Ini
                $this->scheduleToday = JadwalKuliah::with(['mataKuliah', 'programKelasAllow'])
                    ->where('dosen_id', $dosen->id)
                    ->where('tahun_akademik_id', $taId)
                    ->where('hari', $today)
                    ->orderBy('jam_mulai')
                    ->get();
            }
        }

        // === DASHBOARD ADMIN / STAFF ===
        else {
            // Stats Global
            $this->stats = [
                'total_mhs_aktif' => RiwayatStatusMahasiswa::where('tahun_akademik_id', $taId)->where('status_kuliah', 'A')->count(),
                'pembayaran_pending' => PembayaranMahasiswa::where('status_verifikasi', 'PENDING')->count(),
                'kelas_aktif' => JadwalKuliah::where('tahun_akademik_id', $taId)->count(),
                'user_online' => User::where('updated_at', '>=', now()->subMinutes(5))->count() // Estimasi kasar
            ];

            // Todo List (Pekerjaan Tertunda)
            if ($this->stats['pembayaran_pending'] > 0) {
                $this->todoList[] = [
                    'title' => 'Verifikasi Pembayaran',
                    'count' => $this->stats['pembayaran_pending'],
                    'route' => 'admin.keuangan',
                    'color' => 'bg-orange-100 text-orange-700'
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard-page');
    }
}