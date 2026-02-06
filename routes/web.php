<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mahasiswa\CetakController;
use App\Http\Controllers\Admin\AdminCetakController;
// Livewire Components
use App\Livewire\Mahasiswa\KhsPage;
use App\Livewire\Mahasiswa\KeuanganPage;
use App\Livewire\Mahasiswa\TranskripPage;
use App\Livewire\Mahasiswa\ProfilePage as MhsProfile;

use App\Livewire\Admin\Keuangan\VerifikasiPembayaran;
use App\Livewire\Admin\Keuangan\TagihanGenerator;
use App\Livewire\Admin\Keuangan\KomponenBiayaManager;
use App\Livewire\Admin\Keuangan\SkemaTarifManager;
use App\Livewire\Admin\Keuangan\LaporanKeuangan;
use App\Livewire\Admin\Keuangan\ManualTagihanManager;
use App\Livewire\Admin\Keuangan\AdjustmentManager;

use App\Livewire\Admin\Akademik\JadwalKuliahManager;
use App\Livewire\Admin\Akademik\MataKuliahManager;
use App\Livewire\Admin\Akademik\KurikulumManager;
use App\Livewire\Admin\Akademik\MutasiMhsManager;
use App\Livewire\Admin\Akademik\PlotingPaManager;
use App\Livewire\Admin\Akademik\CetakAbsensiManager;
use App\Livewire\Admin\Akademik\EkuivalensiManager;
use App\Livewire\Admin\Akademik\PerbaikanNilaiManager;
use App\Livewire\Admin\Akademik\SkalaNilaiManager;

use App\Livewire\Admin\Konfigurasi\TahunAkademikManager;
use App\Livewire\Admin\Konfigurasi\AturanSksManager;

use App\Livewire\Admin\Master\FakultasManager;
use App\Livewire\Admin\Master\ProdiManager;
use App\Livewire\Admin\Master\ProgramKelasManager;

use App\Livewire\Admin\Pengguna\MahasiswaManager;
use App\Livewire\Admin\Pengguna\DosenManager;
use App\Livewire\Admin\Pengguna\CamabaManager;

use App\Livewire\Admin\HR\HRModuleManager;
use App\Livewire\Admin\Lpm\AmiManager;
use App\Livewire\Admin\Lpm\DokumenManager;
use App\Livewire\Admin\Lpm\EdomManager;
use App\Livewire\Admin\Lpm\IkuManager;
use App\Livewire\Admin\Lpm\IndikatorManager;
use App\Livewire\Admin\Lpm\KuisionerManager;
use App\Livewire\Admin\Lpm\LpmDashboard;
use App\Livewire\Admin\Lpm\StandarManager;
use App\Livewire\Admin\System\AuditLogViewer;
use App\Livewire\Admin\System\UserManager;
use App\Livewire\Admin\System\RoleManager;

use App\Livewire\Dosen\JadwalMengajar;
use App\Livewire\Dosen\InputNilai;
use App\Livewire\Dosen\Perkuliahan\ManagerKelas;
use App\Livewire\Dosen\PerwalianManager;
use App\Livewire\Dosen\PerwalianDetail;
use App\Livewire\Dosen\ProfilePage as DosenProfile;
use App\Livewire\Mahasiswa\Absensi\ScanHadir;
use App\Livewire\Mahasiswa\KrsPage;
use App\Livewire\Mahasiswa\SurveiEdomPage;

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
    // AREA MAHASISWA (Role: Mahasiswa)
    // ====================================================
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->group(function () {
        Route::get('/krs', KrsPage::class)->name('mhs.krs');
        Route::get('/khs', KhsPage::class)->name('mhs.khs');
        Route::get('/keuangan', KeuanganPage::class)->name('mhs.keuangan');
        Route::get('/transkrip', TranskripPage::class)->name('mhs.transkrip');
        Route::get('/profile', MhsProfile::class)->name('mhs.profile');
        Route::get('/survei-edom/{krsDetailId}', SurveiEdomPage::class)->name('mhs.survei-edom');
        // --- ROUTE BARU: SCAN ABSENSI MAHASISWA ---
        Route::get('/absensi/scan', ScanHadir::class)->name('mhs.absensi.scan');



        // Cetak PDF
        Route::get('/cetak/krs', [CetakController::class, 'cetakKrs'])->name('mhs.cetak.krs');
        Route::get('/cetak/khs', [CetakController::class, 'cetakKhs'])->name('mhs.cetak.khs');
        Route::get('/cetak/transkrip', [CetakController::class, 'cetakTranskrip'])->name('mhs.cetak.transkrip');
    });

    // ====================================================
    // AREA DOSEN (Role: Dosen)
    // ====================================================
    Route::middleware(['role:dosen'])->prefix('dosen')->group(function () {
        Route::get('/jadwal', JadwalMengajar::class)->name('dosen.jadwal');
        Route::get('/input-nilai/{jadwalId}', InputNilai::class)->name('dosen.nilai');
        Route::get('/manager-kelas', ManagerKelas::class)->name('dosen.manager-kelas');
        Route::get('/perwalian', PerwalianManager::class)->name('dosen.perwalian');
        Route::get('/perwalian/{krsId}', PerwalianDetail::class)->name('dosen.perwalian.detail');
        Route::get('/profile', DosenProfile::class)->name('dosen.profile');
    });

    // ====================================================
    // AREA BACK OFFICE (ADMINISTRASI)
    // Menggunakan Permission-Based Access Control (PBAC)
    // ====================================================

    Route::prefix('backoffice')->group(function () {

        // 1. GRUP AKADEMIK (Permission: akses_modul_akademik)
        // Diakses oleh: Superadmin, Admin, BARA
        Route::middleware(['permission:akses_modul_akademik'])->group(function () {
            // Konfigurasi Akademik
            Route::get('/semester', TahunAkademikManager::class)->name('admin.semester');
            Route::get('/skala-nilai', SkalaNilaiManager::class)->name('admin.skala-nilai');
            Route::get('/aturan-sks', AturanSksManager::class)->name('admin.aturan-sks');

            // Master Data Akademik
            Route::get('/fakultas', FakultasManager::class)->name('admin.master.fakultas');
            Route::get('/prodi', ProdiManager::class)->name('admin.master.prodi');
            Route::get('/program-kelas', ProgramKelasManager::class)->name('admin.master.program-kelas');
            Route::get('/matakuliah', MataKuliahManager::class)->name('admin.matakuliah');
            Route::get('/kurikulum', KurikulumManager::class)->name('admin.kurikulum');
            // Route::get('/kurikulum/{id}', KurikulumDetail::class)->name('admin.kurikulum.detail'); // Jika ada detail
            Route::get('/komponen-nilai', \App\Livewire\Admin\Akademik\KomponenNilaiManager::class)->name('admin.komponen-nilai');

            // Operasional Perkuliahan
            Route::get('/jadwal', JadwalKuliahManager::class)->name('admin.jadwal');
            Route::get('/cetak-absensi', CetakAbsensiManager::class)->name('admin.cetak.absensi.manager');
            Route::get('/cetak-absensi/{jadwalId}', [AdminCetakController::class, 'cetakAbsensi'])->name('admin.cetak.absensi');
            Route::get('/ploting-pa', PlotingPaManager::class)->name('admin.ploting-pa');
            Route::get('/mutasi', MutasiMhsManager::class)->name('admin.akademik.mutasi');

            // Manajemen Pengguna Akademik
            Route::get('/mahasiswa', MahasiswaManager::class)->name('admin.mahasiswa');
            Route::get('/camaba', CamabaManager::class)->name('admin.camaba');
            Route::get('/dosen', DosenManager::class)->name('admin.dosen');
            // SDM & Pejabat
            Route::get('/hr-manager', HRModuleManager::class)->name('admin.hr.manager');
            Route::get('/akademik/ekuivalensi-mata-kuliah', EkuivalensiManager::class)
                ->name('admin.akademik.ekuivalensi-mk');
            Route::get('/perbaikan-nilai', PerbaikanNilaiManager::class)->name('admin.perbaikan-nilai');
            // absen

        });

        // 2. GRUP KEUANGAN (Permission: akses_modul_keuangan)
        // Diakses oleh: Superadmin, Admin, BAUK
        Route::middleware(['permission:akses_modul_keuangan'])->group(function () {
            Route::get('/verifikasi', VerifikasiPembayaran::class)->name('admin.keuangan');
            Route::get('/komponen', KomponenBiayaManager::class)->name('admin.keuangan.komponen');
            Route::get('/skema-tarif', SkemaTarifManager::class)->name('admin.keuangan.skema');
            Route::get('/generator', TagihanGenerator::class)->name('admin.tagihan-generator');
            Route::get('/tagihan-manual', ManualTagihanManager::class)->name('admin.keuangan.manual');
            Route::get('/laporan-keu', LaporanKeuangan::class)->name('admin.keuangan.laporan');
            Route::get('/koreksi-saldo', AdjustmentManager::class)->name('admin.keuangan.adjustment');
        });

        // 4. GRUP LPM (Permission: akses_modul_lpm)
        // Diakses oleh: Superadmin, Admin, Staf LPM
        Route::middleware(['permission:akses_modul_lpm'])->prefix('lpm')->name('admin.lpm.')->group(function () {
            Route::get('/dashboard', LpmDashboard::class)->name('dashboard');
            // Rute Operasional LPM
            Route::get('/standar', StandarManager::class)->name('standar');
            Route::get('/ami', AmiManager::class)->name('ami');
            Route::get('/dokumen', DokumenManager::class)->name('dokumen');
            Route::get('/edom', EdomManager::class)->name('edom.index');
            Route::get('/iku', IkuManager::class)->name('iku');
            Route::get('/edom/setup', KuisionerManager::class)->name('edom.setup');
            Route::get('/indikator', IndikatorManager::class)->name('indikator');
        });

        // 3. GRUP SYSTEM (Permission: akses_modul_system)
        // Diakses oleh: Superadmin (Admin biasa tidak boleh akses ini)
        Route::middleware(['permission:akses_modul_system'])->group(function () {
            Route::get('/users', UserManager::class)->name('admin.users');
            Route::get('/roles', RoleManager::class)->name('admin.roles');
            Route::get('/audit', AuditLogViewer::class)->name('admin.audit');
        });
    });
});
