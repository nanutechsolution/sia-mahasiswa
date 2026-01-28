<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mahasiswa\CetakController;

// Livewire Components
use App\Livewire\Mahasiswa\KrsPage;
use App\Livewire\Mahasiswa\KhsPage;
use App\Livewire\Mahasiswa\KeuanganPage;
use App\Livewire\Mahasiswa\TranskripPage;

use App\Livewire\Admin\Keuangan\VerifikasiPembayaran;
use App\Livewire\Admin\Keuangan\TagihanGenerator;
use App\Livewire\Admin\Keuangan\KomponenBiayaManager;
use App\Livewire\Admin\Keuangan\SkemaTarifManager;
use App\Livewire\Admin\Keuangan\LaporanKeuangan;

use App\Livewire\Admin\Akademik\JadwalKuliahManager;
use App\Livewire\Admin\Akademik\MataKuliahManager;
use App\Livewire\Admin\Akademik\KurikulumManager;
use App\Livewire\Admin\Akademik\MutasiMhsManager;
use App\Livewire\Admin\Keuangan\ManualTagihanManager;
use App\Livewire\Admin\Konfigurasi\TahunAkademikManager;
use App\Livewire\Admin\Master\FakultasManager;
use App\Livewire\Admin\Master\ProdiManager;
use App\Livewire\Admin\Pengguna\CamabaManager;
use App\Livewire\Admin\Pengguna\MahasiswaManager;
use App\Livewire\Admin\Pengguna\DosenManager;

use App\Livewire\Admin\System\AuditLogViewer;
use App\Livewire\Admin\System\UserManager;
use App\Livewire\Admin\System\RoleManager;

use App\Livewire\Dosen\JadwalMengajar;
use App\Livewire\Dosen\InputNilai;
use App\Livewire\Dosen\PerwalianManager;
use App\Livewire\Dosen\PerwalianDetail;

// 1. PUBLIC ROUTES
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. PROTECTED ROUTES
Route::middleware(['auth'])->group(function () {

    // Dashboard Umum (Bisa diakses semua yg login)
    Route::get('/dashboard', \App\Livewire\DashboardPage::class)->name('dashboard');

    // ====================================================
    // AREA MAHASISWA
    // ====================================================
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->group(function () {
        Route::get('/krs', KrsPage::class)->name('mhs.krs');
        Route::get('/khs', KhsPage::class)->name('mhs.khs');
        Route::get('/keuangan', KeuanganPage::class)->name('mhs.keuangan');
        Route::get('/transkrip', TranskripPage::class)->name('mhs.transkrip');
        Route::get('/profile', \App\Livewire\Mahasiswa\ProfilePage::class)->name('mhs.profile');

        // Cetak
        Route::get('/cetak/krs', [CetakController::class, 'cetakKrs'])->name('mhs.cetak.krs');
        Route::get('/cetak/khs', [CetakController::class, 'cetakKhs'])->name('mhs.cetak.khs');
        Route::get('/cetak/transkrip', [CetakController::class, 'cetakTranskrip'])->name('mhs.cetak.transkrip');
    });

    // ====================================================
    // AREA DOSEN
    // ====================================================
    Route::middleware(['role:dosen'])->prefix('dosen')->group(function () {
        Route::get('/jadwal', JadwalMengajar::class)->name('dosen.jadwal');
        Route::get('/input-nilai/{jadwalId}', InputNilai::class)->name('dosen.nilai');
        Route::get('/perwalian', PerwalianManager::class)->name('dosen.perwalian');
        Route::get('/perwalian/{krsId}', PerwalianDetail::class)->name('dosen.perwalian.detail');
    });

    // ====================================================
    // AREA BACK OFFICE (ADMINISTRASI)
    // ====================================================

    // 1. GROUP KEUANGAN (Akses: Superadmin, Admin, Staff Keuangan)
    Route::middleware(['role:superadmin|admin|keuangan'])->prefix('keuangan')->group(function () {
        Route::get('/verifikasi', VerifikasiPembayaran::class)->name('admin.keuangan'); // Main menu
        Route::get('/komponen', KomponenBiayaManager::class)->name('admin.keuangan.komponen');
        Route::get('/skema-tarif', SkemaTarifManager::class)->name('admin.keuangan.skema');
        Route::get('/generator', TagihanGenerator::class)->name('admin.tagihan-generator');
        Route::get('/laporan', LaporanKeuangan::class)->name('admin.keuangan.laporan');
        Route::get('/keuangan/manual', ManualTagihanManager::class)->name('admin.keuangan.manual');
    });

    // 2. GROUP AKADEMIK & BAAK (Akses: Superadmin, Admin, BAAK)
    Route::middleware(['role:superadmin|admin|baak'])->prefix('akademik')->group(function () {
        // Konfigurasi & Master
        Route::get('/semester', TahunAkademikManager::class)->name('admin.semester');
        Route::get('/fakultas', FakultasManager::class)->name('admin.master.fakultas');
        Route::get('/prodi', ProdiManager::class)->name('admin.master.prodi');
        Route::get('/master/program-kelas', \App\Livewire\Admin\Master\ProgramKelasManager::class)->name('admin.master.program-kelas');

        // Operasional Akademik
        Route::get('/matakuliah', MataKuliahManager::class)->name('admin.matakuliah');
        Route::get('/kurikulum', KurikulumManager::class)->name('admin.kurikulum');
        Route::get('/jadwal', JadwalKuliahManager::class)->name('admin.jadwal');

        // Manajemen User Akademik
        Route::get('/mahasiswa', MahasiswaManager::class)->name('admin.mahasiswa');
        Route::get('/dosen', DosenManager::class)->name('admin.dosen');
        Route::get('/pengguna/camaba', CamabaManager::class)->name('admin.camaba');
        Route::get('/akademik/mutasi', MutasiMhsManager::class)->name('admin.akademik.mutasi');
        Route::get('/akademik/ploting-pa', \App\Livewire\Admin\Akademik\PlotingPaManager::class)->name('admin.ploting-pa');
        Route::get('/akademik/cetak-absensi', \App\Livewire\Admin\Akademik\CetakAbsensiManager::class)->name('admin.cetak.absensi.manager');
        Route::get('/akademik/cetak-absensi/{jadwalId}', [\App\Http\Controllers\Admin\AdminCetakController::class, 'cetakAbsensi'])->name('admin.cetak.absensi');
        Route::get('/akademik/skala-nilai', \App\Livewire\Admin\Akademik\SkalaNilaiManager::class)->name('admin.akademik.skala-nilai');
    });

    // 3. GROUP SYSTEM & IT (Akses: Superadmin ONLY)
    // Hanya dewa yang boleh atur role dan lihat audit log sensitive
    Route::middleware(['role:superadmin'])->prefix('system')->group(function () {
        Route::get('/users', UserManager::class)->name('admin.users');
        Route::get('/roles', RoleManager::class)->name('admin.roles');
        Route::get('/audit', AuditLogViewer::class)->name('admin.audit');
    });
});
