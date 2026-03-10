<?php

namespace App\Providers;

use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Core\Models\TahunAkademik;
use App\Observers\KrsDetailObserver;
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


        $taAktif = null;

        if (Schema::hasTable('ref_tahun_akademik')) {
            $taAktif = TahunAkademik::where('is_active', true)->first();
        }

        View::share('globalTa', $taAktif);
        KrsDetail::observe(KrsDetailObserver::class);
    }
}
