<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
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

    public $stats = [];
    public $scheduleToday = [];
    public $announcements = [];
    public $todoList = [];

    public function mount()
    {
        $this->user = Auth::user();

        // Eager load person untuk performa SSOT
        if ($this->user->person_id) {
            $this->user->load('person.mahasiswa', 'person.dosen');
        }

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
        $today = Carbon::now()->locale('id')->isoFormat('dddd');

        $this->stats = [
            // Mahasiswa Stats
            'ipk' => 0,
            'sks_total' => 0,
            'status_bayar' => '-',
            'tagihan_nominal' => 0,
            'sisa_tagihan' => 0,

            // Dosen Stats
            'kelas_ajar' => 0,
            'mhs_wali' => 0,
            'krs_perlu_acc' => 0,

            // Admin Stats
            'total_mhs_aktif' => 0,
            'pembayaran_pending' => 0,
            'kelas_aktif' => 0,
            'user_online' => 0
        ];

        // === DASHBOARD MAHASISWA ===
        if ($this->role == 'mahasiswa') {
            // Ambil data via Person, bukan user_id langsung
            $mhs = $this->user->person ? $this->user->person->mahasiswa : null;

            if (!$mhs) {
                $this->announcements[] = [
                    'type' => 'warning',
                    'title' => 'Profil Belum Lengkap',
                    'message' => 'Data akademik mahasiswa belum terhubung dengan akun ini.'
                ];
                return;
            }

            // Stats Akademik
            $riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $mhs->id)
                ->orderBy('tahun_akademik_id', 'desc')->first();

            // Stats Keuangan
            $tagihan = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)
                ->where('tahun_akademik_id', $taId)->first();
            $this->stats['ipk'] = $riwayat->ipk ?? 0.00;
            $this->stats['sks_total'] = $riwayat->sks_total ?? 0;
            $this->stats['status_bayar'] = $tagihan ? $tagihan->status_bayar : 'BELUM';
            $this->stats['tagihan_nominal'] = $tagihan ? $tagihan->total_tagihan : 0;
            $this->stats['sisa_tagihan'] = $tagihan ? $tagihan->sisa_tagihan : 0;

            // Jadwal Hari Ini
            $krs = $mhs->krs()->where('tahun_akademik_id', $taId)->first();
            if ($krs) {
                $this->scheduleToday = $krs->details()
                    ->whereHas('jadwalKuliah', fn($q) => $q->where('hari', $today))
                    ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person']) // Load person dosen
                    ->get()
                    ->sortBy('jadwalKuliah.jam_mulai');
            }
        }

        // === DASHBOARD DOSEN ===
        elseif ($this->role == 'dosen') {
            // [SSOT FIX] Ambil data via Person
            $dosen = $this->user->person ? $this->user->person->dosen : null;

            if (!$dosen) {
                $this->announcements[] = [
                    'type' => 'warning',
                    'title' => 'Profil Belum Lengkap',
                    'message' => 'Data akademik dosen belum terhubung dengan akun ini.'
                ];
                return;
            }

            $this->stats['kelas_ajar'] = JadwalKuliah::where('dosen_id', $dosen->id)
                ->where('tahun_akademik_id', $taId)->count();

            $this->stats['mhs_wali'] = \App\Domains\Mahasiswa\Models\Mahasiswa::where('dosen_wali_id', $dosen->id)
                ->whereHas('riwayatStatus', fn($q) => $q->where('tahun_akademik_id', $taId)->where('status_kuliah', 'A'))
                ->count();

            $this->stats['krs_perlu_acc'] = \App\Domains\Akademik\Models\Krs::whereHas('mahasiswa', function ($q) use ($dosen) {
                $q->where('dosen_wali_id', $dosen->id);
            })
                ->where('status_krs', 'AJUKAN')
                ->where('tahun_akademik_id', $taId)
                ->count();

            $this->scheduleToday = JadwalKuliah::with(['mataKuliah'])
                ->where('dosen_id', $dosen->id)
                ->where('tahun_akademik_id', $taId)
                ->where('hari', $today)
                ->orderBy('jam_mulai')
                ->get();
        }

        // === DASHBOARD ADMIN ===
        else {
            $this->stats['total_mhs_aktif'] = RiwayatStatusMahasiswa::where('tahun_akademik_id', $taId)
                ->where('status_kuliah', 'A')->count();

            $this->stats['pembayaran_pending'] = PembayaranMahasiswa::where('status_verifikasi', 'PENDING')->count();

            $this->stats['kelas_aktif'] = JadwalKuliah::where('tahun_akademik_id', $taId)->count();

            $this->stats['user_online'] = User::where('updated_at', '>=', now()->subMinutes(15))->count();

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
