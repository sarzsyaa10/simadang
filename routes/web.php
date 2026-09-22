<?php

use App\Http\Controllers\Admin\DistribusiController as AdminDistribusiController;
use App\Http\Controllers\Admin\PelaporanDistribusiController as AdminPelaporanDistribusiController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\StokController as AdminStokController;
use App\Http\Controllers\Admin\DataUserController;
use App\Http\Controllers\Admin\DataGudangController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Upt\DashboardController;
use App\Http\Controllers\Upt\StokController as UptStokController;
use App\Http\Controllers\Upt\MutasiController as UptMutasiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\General\StokController as GeneralStokController;
use App\Http\Controllers\Admin\MutasiController as AdminMutasiController;

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PermohonanBantuanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Upt\PermohonanBantuanController as UptPermohonanController;
use App\Http\Controllers\Upt\PelaporanDistribusiController as UptPelaporanController;

// ==========================================
// MEDIA (serve file dari storage tanpa bergantung symlink public/storage)
// ==========================================
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// ==========================================
// LANDING / BERANDA (General - publik)
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

    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export/pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/export/excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export.excel');

    // Permohonan Bantuan
    Route::get('/permohonan-bantuan', [PermohonanBantuanController::class, 'index'])->name('permohonan');
    Route::get('/permohonan-bantuan/{permohonan_bantuan}', [PermohonanBantuanController::class, 'show'])->name('permohonan.show');
    Route::delete('/permohonan-bantuan/{permohonan_bantuan}', [PermohonanBantuanController::class, 'destroy'])->name('permohonan.destroy');

    Route::patch('/permohonan-bantuan/barang/{item}/setuju', [PermohonanBantuanController::class, 'setujuItem'])->name('permohonan.item.setuju');
    Route::patch('/permohonan-bantuan/barang/{item}/tolak', [PermohonanBantuanController::class, 'tolakItem'])->name('permohonan.item.tolak');

    Route::get('/distribusi', [AdminDistribusiController::class, 'index'])->name('distribusi');
    Route::get('/distribusi/create', [AdminDistribusiController::class, 'create'])->name('distribusi.create');
    Route::post('/distribusi', [AdminDistribusiController::class, 'store'])->name('distribusi.store');
    Route::get('/distribusi/{distribusi}', [AdminDistribusiController::class, 'show'])->name('distribusi.show');
    Route::get('/distribusi/{distribusi}/cetak', [AdminDistribusiController::class, 'cetak'])->name('distribusi.cetak');
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

    Route::resource('data-user', DataUserController::class)
        ->except(['show'])
        ->parameters(['data-user' => 'dataUser']);

    Route::resource('data-gudang', DataGudangController::class)
        ->except(['show'])
        ->parameters(['data-gudang' => 'dataGudang']);

    // Setting
    Route::get('/setting', [SettingController::class, 'edit'])->name('setting');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
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