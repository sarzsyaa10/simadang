<?php

use App\Http\Controllers\Admin\StokController as AdminStokController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Upt\DashboardController;
use App\Http\Controllers\Upt\StokController as UptStokController;
use App\Http\Controllers\Upt\MutasiController as UptMutasiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\General\StokController as GeneralStokController;
use App\Http\Controllers\Admin\MutasiController as AdminMutasiController;

use Illuminate\Support\Facades\Route;

// ==========================================
// MEDIA (serve file dari storage tanpa bergantung symlink public/storage)
// ==========================================
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// ==========================================
// LANDING / BERANDA (General - publik)
// ==========================================
Route::get('/', function () {
    return view('general.beranda');
})->name('landing');

Route::prefix('general')->name('general.')->group(function () {
    Route::get('/stok/logistik-non-permakanan', [GeneralStokController::class, 'index'])
        ->name('stok.logistik')
        ->defaults('kategori', 'logistik_non_permakanan');

    Route::get('/stok/peralatan', [GeneralStokController::class, 'index'])
        ->name('stok.peralatan')
        ->defaults('kategori', 'peralatan');

    Route::get('/laporan', function () {
        return 'Laporan — akan kita buat nanti';
    })->name('laporan')->middleware('auth');

    Route::get('/permohonan-bantuan', function () {
        return 'Halaman Permohonan Bantuan — akan kita buat nanti';
    })->name('permohonan');

    Route::get('/distribusi', function () {
        return 'Halaman Distribusi — akan kita buat nanti';
    })->name('distribusi');

    Route::get('/distribusi/pelaporan', function () {
        return 'Pelaporan Distribusi — akan kita buat nanti';
    })->name('distribusi.pelaporan')->middleware('auth');
});

// ==========================================
// AUTH
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// PROFILE (dipakai bareng Admin & UPT)
// ==========================================
Route::get('/profile', function () {
    return 'Halaman Profil — akan kita buat nanti';
})->middleware('auth')->name('profile');

// ==========================================
// ADMIN
// ==========================================
Route::middleware('role.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('beranda');

    Route::resource('stok', AdminStokController::class)->except(['show']);

    Route::prefix('mutasi')->name('mutasi.')->group(function () {
    Route::get('/', [AdminMutasiController::class, 'index'])->name('index');
    Route::get('/masuk/tambah', [AdminMutasiController::class, 'createMasuk'])->name('create.masuk');
    Route::get('/keluar/tambah', [AdminMutasiController::class, 'createKeluar'])->name('create.keluar');
    Route::post('/', [AdminMutasiController::class, 'store'])->name('store');
    Route::get('/{mutasi}/edit', [AdminMutasiController::class, 'edit'])->name('edit');
    Route::put('/{mutasi}', [AdminMutasiController::class, 'update'])->name('update');
    Route::delete('/{mutasi}', [AdminMutasiController::class, 'destroy'])->name('destroy');
});

    Route::get('/laporan', fn () => 'Laporan (Admin) — akan kita buat nanti')->name('laporan');

    Route::get('/permohonan-bantuan', fn () => 'Permohonan Bantuan (Admin) — akan kita buat nanti')->name('permohonan');

    Route::get('/distribusi', fn () => 'Distribusi (Admin) — akan kita buat nanti')->name('distribusi');
    Route::get('/distribusi/pelaporan', fn () => 'Pelaporan Distribusi (Admin) — akan kita buat nanti')->name('distribusi.pelaporan');

    Route::get('/data-user', fn () => 'Data User — akan kita buat nanti')->name('data-user.index');
    Route::get('/data-gudang', fn () => 'Data Gudang — akan kita buat nanti')->name('data-gudang.index');
    Route::get('/setting', fn () => 'Setting — akan kita buat nanti')->name('setting');
});

// ==========================================
// UPT
// ==========================================
Route::middleware('role.upt')->prefix('upt')->name('upt.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('beranda');

    Route::resource('stok', UptStokController::class)->except(['show']);

    Route::prefix('mutasi')->name('mutasi.')->group(function () {
        Route::get('/', [UptMutasiController::class, 'index'])->name('index');
        Route::get('/masuk/tambah', [UptMutasiController::class, 'createMasuk'])->name('create.masuk');
        Route::get('/keluar/tambah', [UptMutasiController::class, 'createKeluar'])->name('create.keluar');
        Route::post('/', [UptMutasiController::class, 'store'])->name('store');
        Route::get('/{mutasi}/edit', [UptMutasiController::class, 'edit'])->name('edit');
        Route::put('/{mutasi}', [UptMutasiController::class, 'update'])->name('update');
        Route::delete('/{mutasi}', [UptMutasiController::class, 'destroy'])->name('destroy');
    });

    Route::get('/permohonan-bantuan', fn () => 'Permohonan Bantuan (UPT) — akan kita buat nanti')->name('permohonan');

    Route::get('/distribusi/pelaporan', fn () => 'Pelaporan Distribusi (UPT) — akan kita buat nanti')->name('distribusi.pelaporan');
});