<?php

namespace App\Providers;

use App\Support\MenuSidebar;
use App\Support\Peranan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
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
        Schema::defaultStringLength(191);

        Paginator::useBootstrapFive();

        // Masa relatif dalam Bahasa Melayu, contoh "16 jam yang lalu"
        Carbon::setLocale('ms');

        // Nama peranan tersedia dalam semua view sebagai $namaPeranan
        View::share('namaPeranan', Peranan::NAMA);

        // Data layout utama: pengguna semasa dan menu sidebar
        View::composer('layouts.utama', function ($view): void {
            $view->with([
                'user' => auth()->user(),
                'menu' => MenuSidebar::senarai(),
            ]);
        });
    }
}