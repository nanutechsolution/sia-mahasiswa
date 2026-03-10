<?php

namespace App\Providers;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Core\Models\TahunAkademik;
use App\Listeners\UpdateLastLogin;
use App\Observers\KrsDetailObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event; // Tambahkan ini
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Registrasi Event Listener (Audit Login)
        // Karena AppServiceProvider tidak membaca properti $listen secara otomatis,
        // kita gunakan facade Event::listen di sini.
        Event::listen(
            Login::class,
            UpdateLastLogin::class
        );

        // 2. Registrasi Observer (Performance Engine Transkrip)
        KrsDetail::observe(KrsDetailObserver::class);

        // 3. Global View Share (Tahun Akademik Aktif)
        $taAktif = null;
        if (Schema::hasTable('ref_tahun_akademik')) {
            $taAktif = TahunAkademik::where('is_active', true)->first();
        }
        View::share('globalTa', $taAktif);
    }
}