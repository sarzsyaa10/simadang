<?php

use App\Http\Controllers\Admin\DistribusiController as AdminDistribusiController;
use App\Http\Controllers\Admin\PelaporanDistribusiController as AdminPelaporanDistribusiController;
use App\Http\Controllers\Admin\StokController as AdminStokController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Upt\DashboardController;
use App\Http\Controllers\Upt\StokController as UptStokController;
use Illuminate\Support\Facades\Route;

// ==========================================
// LANDING / BERANDA (General - publik)
// ==========================================
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
    Route::resource('stok', AdminStokController::class)->except(['show']);

    Route::get('/laporan', fn () => 'Laporan (Admin) — akan kita buat nanti')->name('laporan');

    Route::get('/permohonan-bantuan', fn () => 'Permohonan Bantuan (Admin) — akan kita buat nanti')->name('permohonan');

    Route::get('/distribusi', [AdminDistribusiController::class, 'index'])->name('distribusi');
    Route::get('/distribusi/create', [AdminDistribusiController::class, 'create'])->name('distribusi.create');
    Route::post('/distribusi', [AdminDistribusiController::class, 'store'])->name('distribusi.store');
    Route::get('/distribusi/{distribusi}', [AdminDistribusiController::class, 'show'])->name('distribusi.show');
    Route::get('/distribusi/{distribusi}/edit', [AdminDistribusiController::class, 'edit'])->name('distribusi.edit');
    Route::put('/distribusi/{distribusi}', [AdminDistribusiController::class, 'update'])->name('distribusi.update');
    Route::delete('/distribusi/{distribusi}', [AdminDistribusiController::class, 'destroy'])->name('distribusi.destroy');

    Route::get('/distribusi/{distribusi}/item/create', [AdminDistribusiController::class, 'createDetail'])->name('distribusi.detail.create');
    Route::post('/distribusi/{distribusi}/item', [AdminDistribusiController::class, 'storeDetail'])->name('distribusi.detail.store');
    Route::get('/distribusi/{distribusi}/item/{detail}/edit', [AdminDistribusiController::class, 'editDetail'])->name('distribusi.detail.edit');
    Route::put('/distribusi/{distribusi}/item/{detail}', [AdminDistribusiController::class, 'updateDetail'])->name('distribusi.detail.update');
    Route::delete('/distribusi/{distribusi}/item/{detail}', [AdminDistribusiController::class, 'destroyDetail'])->name('distribusi.detail.destroy');

    Route::get('/distribusi-pelaporan', [AdminPelaporanDistribusiController::class, 'index'])->name('distribusi.pelaporan');
    Route::get('/distribusi-pelaporan/{pelaporan}', [AdminPelaporanDistribusiController::class, 'show'])->name('distribusi.pelaporan.show');
    Route::post('/distribusi-pelaporan/{pelaporan}/terima', [AdminPelaporanDistribusiController::class, 'terima'])->name('distribusi.pelaporan.terima');
    Route::post('/distribusi-pelaporan/{pelaporan}/tolak', [AdminPelaporanDistribusiController::class, 'tolak'])->name('distribusi.pelaporan.tolak');

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

    Route::get('/permohonan-bantuan', fn () => 'Permohonan Bantuan (UPT) — akan kita buat nanti')->name('permohonan');

    Route::get('/distribusi/pelaporan', fn () => 'Pelaporan Distribusi (UPT) — akan kita buat nanti')->name('distribusi.pelaporan');
});