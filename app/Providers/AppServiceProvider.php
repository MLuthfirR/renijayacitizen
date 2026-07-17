<?php

namespace App\Providers;

use App\Support\PeriodeContext;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');

        // Bagikan periode aktif & daftar periode ke layout aplikasi.
        View::composer('components.layouts.app', function ($view) {
            try {
                $view->with([
                    'periodeAktif' => PeriodeContext::current(),
                    'daftarPeriode' => PeriodeContext::all(),
                ]);
            } catch (\Throwable $e) {
                $view->with(['periodeAktif' => null, 'daftarPeriode' => collect()]);
            }
        });
    }
}
