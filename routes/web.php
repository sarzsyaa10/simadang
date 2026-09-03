<?php

use App\Http\Controllers\Admin\StokController as AdminStokController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Upt\DashboardController;
use App\Http\Controllers\Upt\StokController as UptStokController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Upt\PermohonanBantuanController as UptPermohonanController;
use App\Http\Controllers\Upt\PelaporanDistribusiController as UptPelaporanController;

// LANDING / BERANDA (General - publik)
Route::get('/', function () {
    return view('general.beranda');
})->name('landing');

Route::prefix('general')->name('general.')->group(function () {
    Route::get('/stok/logistik-non-permakanan', function () {
        return 'Stok Logistik Non Permakanan — akan kita buat nanti';
    })->name('stok.logistik');

    Route::get('/stok/peralatan', function () {
        return 'Stok Peralatan — akan kita buat nanti';
    })->name('stok.peralatan');

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

// AUTH
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
    Route::resource('stok', AdminStokController::class)->except(['show']);

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

    Route::prefix('permohonan-bantuan')->name('permohonan.')->group(function () {
        Route::get('/', [UptPermohonanController::class, 'index'])->name('index');
        Route::get('/create', [UptPermohonanController::class, 'create'])->name('create');
        Route::post('/', [UptPermohonanController::class, 'store'])->name('store');
        Route::get('/{permohonan}', [UptPermohonanController::class, 'show'])->name('show');
        Route::get('/{permohonan}/edit', [UptPermohonanController::class, 'edit'])->name('edit');
        Route::put('/{permohonan}', [UptPermohonanController::class, 'update'])->name('update');
        Route::delete('/{permohonan}', [UptPermohonanController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('distribusi/pelaporan')->name('distribusi.pelaporan.')->group(function () {
        Route::get('/', [UptPelaporanController::class, 'index'])->name('index');
        Route::get('/create', [UptPelaporanController::class, 'create'])->name('create');
        Route::post('/', [UptPelaporanController::class, 'store'])->name('store');
        Route::get('/{pelaporan}/edit', [UptPelaporanController::class, 'edit'])->name('edit');
        Route::put('/{pelaporan}', [UptPelaporanController::class, 'update'])->name('update');
        Route::delete('/{pelaporan}', [UptPelaporanController::class, 'destroy'])->name('destroy');
    });
});