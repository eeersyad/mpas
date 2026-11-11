<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengurusanPenggunaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\TetapanController;
use App\Http\Controllers\TetapanKomponenController;
use App\Http\Controllers\PengurusanTanahController;
use App\Http\Controllers\PengurusanBangunanController;
use App\Http\Controllers\PengurusanAduanController;
use App\Http\Controllers\PengurusanKodQrController;
use App\Http\Controllers\AduanAwamController;
use App\Http\Controllers\PengurusanLaporanController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->middleware('throttle:10,1')
        ->name('authenticate');

    
});

// Aduan awam melalui kod QR (tanpa log masuk)
Route::get('/laporan/{kod}', [AduanAwamController::class, 'create'])
    ->middleware('throttle:20,1')
    ->name('aduan.awam.create');

Route::post('/laporan/{kod}', [AduanAwamController::class, 'store'])
    ->middleware('throttle:5,10,aduan-awam')
    ->name('aduan.awam.store');

// KUMPULAN SYSTEM
Route::middleware('peranan:SYSTEM')->group(function () {
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

// KUMPULAN SYSTEM, BPH
Route::middleware('peranan:SYSTEM,BPH')->group(function () {
    Route::get('/kod-qr', [PengurusanKodQrController::class, 'index'])
        ->name('kodqr.index');

    Route::get('/kod-qr/{bangunan}', [PengurusanKodQrController::class, 'show'])
        ->name('kodqr.show');

    Route::put('/kod-qr/{bangunan}/status', [PengurusanKodQrController::class, 'kemaskiniStatus'])
        ->name('kodqr.status');
});

//KUMPULAN AUTH
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('dashboard');

    Route::get('/laporan-sistem', [PengurusanLaporanController::class, 'index'])
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('laporan.index');

    Route::get(
        '/laporan-sistem/{jenis}/excel',
        [PengurusanLaporanController::class, 'excel']
    )
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('laporan.excel');

    Route::get(
        '/laporan-sistem/{jenis}/pdf',
        [PengurusanLaporanController::class, 'pdf']
    )
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('laporan.pdf');

    Route::get(
        '/laporan-sistem/{jenis}',
        [PengurusanLaporanController::class, 'papar']
    )
        ->middleware('peranan:SYSTEM,BPH,BP,KJ')
        ->name('laporan.papar');

    Route::get('/profil', [ProfilController::class, 'edit'])
        ->name('profil.edit');

    Route::put('/profil', [ProfilController::class, 'update'])
        ->name('profil.update');

    Route::middleware('peranan:SYSTEM')->group(function () {
        Route::get('/tetapan/rujukan', [TetapanController::class, 'rujukan'])
            ->name('tetapan.rujukan');

        Route::put('/tetapan/rujukan', [TetapanController::class, 'kemaskiniRujukan'])
            ->name('tetapan.rujukan.kemaskini');

        Route::get('/tetapan/aduan', [TetapanController::class, 'aduan'])
            ->name('tetapan.aduan');

        Route::put('/tetapan/aduan', [TetapanController::class, 'kemaskiniAduan'])
            ->name('tetapan.aduan.kemaskini');

        Route::get('/tetapan/penyewa', [TetapanController::class, 'penyewa'])
            ->name('tetapan.penyewa');

        Route::put('/tetapan/penyewa', [TetapanController::class, 'kemaskiniPenyewa'])
            ->name('tetapan.penyewa.kemaskini');
        
        Route::get('/tetapan/komponen', [TetapanKomponenController::class, 'index'])
            ->name('tetapan.komponen');

        Route::post('/tetapan/komponen', [TetapanKomponenController::class, 'simpan'])
            ->name('tetapan.komponen.simpan');

        Route::put('/tetapan/komponen/{komponen}', [TetapanKomponenController::class, 'kemaskini'])
            ->name('tetapan.komponen.kemaskini');

        Route::get('/tetapan/penyelenggaraan', [TetapanController::class, 'penyelenggaraan'])
            ->name('tetapan.penyelenggaraan');

        Route::put('/tetapan/penyelenggaraan', [TetapanController::class, 'kemaskiniPenyelenggaraan'])
            ->name('tetapan.penyelenggaraan.kemaskini');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::post('/sesi/kekal', [LoginController::class, 'kekalSesi'])->name('sesi.kekal');

    Route::middleware('peranan:SYSTEM,BPH,BP')->group(function () {
        Route::get('/aset-tanah', [PengurusanTanahController::class, 'index'])
            ->name('tanah.index');

        Route::get('/aset-tanah/{tanah}/pdf', [PengurusanTanahController::class, 'muatTurunPdf'])
            ->name('tanah.pdf');

        Route::get('/aset-tanah/daftar', [PengurusanTanahController::class, 'create'])
            ->name('tanah.create');

        Route::post('/aset-tanah', [PengurusanTanahController::class, 'store'])
            ->name('tanah.store');

        Route::get('/aset-tanah/{tanah}/butiran', [PengurusanTanahController::class, 'butiran'])
            ->name('tanah.butiran');

        Route::get('/aset-tanah/{tanah}/kemaskini', [PengurusanTanahController::class, 'edit'])
            ->name('tanah.edit');

        Route::put('/aset-tanah/{tanah}', [PengurusanTanahController::class, 'update'])
            ->name('tanah.update');

        Route::delete('/aset-tanah/{tanah}/dokumen/{dokumen}', [PengurusanTanahController::class, 'hapusDokumen'])
            ->name('tanah.dokumen.hapus');
            
        Route::get('/aset-tanah/{tanah}/dokumen/{dokumen}', [PengurusanTanahController::class, 'lihatDokumen'])
            ->name('tanah.dokumen.lihat');
    });

    Route::middleware('peranan:SYSTEM,BPH')->group(function () {
        Route::get('/aset-bangunan', [PengurusanBangunanController::class, 'index'])
            ->name('bangunan.index');

        Route::get('/aset-bangunan/{bangunan}/pdf', [PengurusanBangunanController::class, 'muatTurunPdf'])
            ->name('bangunan.pdf');

        Route::get('/aset-bangunan/daftar', [PengurusanBangunanController::class, 'create'])
            ->name('bangunan.create');

        Route::post('/aset-bangunan', [PengurusanBangunanController::class, 'store'])
            ->name('bangunan.store');

        Route::get('/aset-bangunan/{bangunan}/kemaskini', [PengurusanBangunanController::class, 'edit'])
            ->name('bangunan.edit');

        Route::put('/aset-bangunan/{bangunan}', [PengurusanBangunanController::class, 'update'])
            ->name('bangunan.update');

        Route::delete(
            '/aset-bangunan/{bangunan}/dokumen/{dokumen}',
            [PengurusanBangunanController::class, 'hapusDokumen']
        )->name('bangunan.dokumen.hapus');
        
        Route::get(
            '/aset-bangunan/{bangunan}/dokumen/{dokumen}',
            [PengurusanBangunanController::class, 'lihatDokumen']
        )->name('bangunan.dokumen.lihat');
    });

    // Butiran bangunan: akses disemak dalam controller
    // (SYSTEM/BPH, atau peranan yang boleh lihat rekod penyelenggaraan)
    Route::get('/aset-bangunan/{bangunan}/butiran', [PengurusanBangunanController::class, 'butiran'])
        ->name('bangunan.butiran');
        
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])
        ->name('notifikasi.index');

    Route::put('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])
        ->name('notifikasi.baca-semua');

    Route::put('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])
        ->name('notifikasi.baca');

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
