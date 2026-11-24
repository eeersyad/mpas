<?php

namespace App\Providers;

use App\Models\StatusAset;
use App\Models\TetapanPenyelenggaraan;
use App\Support\MenuSidebar;
use App\Support\Peranan;
use Illuminate\Support\Facades\Queue;
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

        // Senarai status premis & status tanah (dari Tetapan > Status Aset), dikunci ikut kod
        View::composer(['bangunan.*', 'tanah.*', 'laporan.*', 'pdf.*'], function ($view): void {
            $view->with([
                'senaraiStatusPremis' => StatusAset::semua('BANGUNAN'),
                'senaraiStatusTanah' => StatusAset::semua('TANAH'),
            ]);
        });

        // Pilihan muat turun: PDF pemeriksaan hanya untuk pengguna yang ada akses penyelenggaraan
        View::composer(['pdf._pilihan-muat-turun', 'pdf._item-muat-turun-apl'], function ($view): void {
            $view->with('bolehMuatTurunPemeriksaan', TetapanPenyelenggaraan::bolehLihat(auth()->user()));
        });

        // Proses queue berjalan lama: kosongkan cache status sebelum setiap job supaya tetapan terkini digunakan
        Queue::before(fn () => StatusAset::lupakanCache());

        // Data layout utama: pengguna semasa dan menu sidebar
        View::composer('layouts.utama', function ($view): void {
            $view->with([
                'user' => auth()->user(),
                'menu' => MenuSidebar::senarai(),
            ]);
        });
    }
}