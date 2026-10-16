<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengurusanPenggunaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\TetapanController;
use App\Http\Controllers\PengurusanTanahController;
use App\Http\Controllers\PengurusanBangunanController;
use App\Http\Controllers\PengurusanAduanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
});

//KUMPULAN SYSTEM, BPH
Route::middleware('peranan:SYSTEM,BPH')->group(function () {
    Route::get('/pengguna', [PengurusanPenggunaController::class, 'index'])
        ->name('pengguna.index');

    Route::get('/pengguna/daftar', [PengurusanPenggunaController::class, 'create'])
        ->name('pengguna.create');

    Route::post('/pengguna', [PengurusanPenggunaController::class, 'store'])
        ->name('pengguna.store');

    Route::get('/pengguna/{pengguna}/kemaskini', [PengurusanPenggunaController::class, 'edit'])
        ->name('pengguna.edit');

    Route::put('/pengguna/{pengguna}', [PengurusanPenggunaController::class, 'update'])
        ->name('pengguna.update');

    Route::delete('/pengguna/{pengguna}', [PengurusanPenggunaController::class, 'destroy'])
        ->name('pengguna.destroy');
});

//KUMPULAN AUTH
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('dashboard');

    Route::get('/profil', [ProfilController::class, 'edit'])
        ->name('profil.edit');

    Route::put('/profil', [ProfilController::class, 'update'])
        ->name('profil.update');

    Route::middleware('peranan:SYSTEM')->group(function () {
        Route::get('/tetapan/rujukan', [TetapanController::class, 'rujukan'])
            ->name('tetapan.rujukan');

        Route::put('/tetapan/rujukan', [TetapanController::class, 'kemaskiniRujukan'])
            ->name('tetapan.rujukan.kemaskini');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('peranan:SYSTEM,BPH,BP')->group(function () {
        Route::get('/aset-tanah', [PengurusanTanahController::class, 'index'])
            ->name('tanah.index');

        Route::get('/aset-tanah/daftar', [PengurusanTanahController::class, 'create'])
            ->name('tanah.create');

        Route::post('/aset-tanah', [PengurusanTanahController::class, 'store'])
            ->name('tanah.store');

        Route::get('/aset-tanah/{tanah}/kemaskini', [PengurusanTanahController::class, 'edit'])
            ->name('tanah.edit');

        Route::put('/aset-tanah/{tanah}', [PengurusanTanahController::class, 'update'])
            ->name('tanah.update');
    });

    Route::middleware('peranan:SYSTEM,BPH')->group(function () {
        Route::get('/aset-bangunan', [PengurusanBangunanController::class, 'index'])
            ->name('bangunan.index');

        Route::get('/aset-bangunan/daftar', [PengurusanBangunanController::class, 'create'])
            ->name('bangunan.create');

        Route::post('/aset-bangunan', [PengurusanBangunanController::class, 'store'])
            ->name('bangunan.store');

        Route::get('/aset-bangunan/{bangunan}/kemaskini', [PengurusanBangunanController::class, 'edit'])
            ->name('bangunan.edit');

        Route::put('/aset-bangunan/{bangunan}', [PengurusanBangunanController::class, 'update'])
            ->name('bangunan.update');
    });

    Route::middleware('peranan:SYSTEM,BPH,KJ')->group(function () {
        Route::get('/aduan', [PengurusanAduanController::class, 'index'])
            ->name('aduan.index');

        Route::get('/aduan/{aduan}', [PengurusanAduanController::class, 'show'])
            ->name('aduan.show');
    });

    Route::middleware('peranan:SYSTEM,BPH')->group(function () {
        Route::put('/aduan/{aduan}/terima', [PengurusanAduanController::class, 'terima'])
            ->name('aduan.terima');

        Route::put('/aduan/{aduan}/tolak', [PengurusanAduanController::class, 'tolak'])
            ->name('aduan.tolak');
    });

    Route::put(
        '/aduan/{aduan}/penyelenggaraan',
        [PengurusanAduanController::class, 'kemaskiniPenyelenggaraan']
    )
        ->middleware('peranan:SYSTEM,KJ')
        ->name('aduan.penyelenggaraan');
});