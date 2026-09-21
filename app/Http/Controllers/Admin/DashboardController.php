<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PermohonanBantuan;
use App\Models\SuratDistribusi;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Beranda Admin';

        // Total item logistik = jumlah jenis/jenis barang yang terdaftar
        $totalItemLogistik = Barang::count();

        // Permohonan bantuan yang masih menunggu verifikasi
        $permohonanMenunggu = PermohonanBantuan::where('status', 'pending')->count();

        // Distribusi yang dibuat bulan berjalan
        $distribusiBulanIni = SuratDistribusi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Semua barang, diurutkan dari stok paling sedikit ke terbanyak
        // (ditampilkan di panel scrollable, tidak dibatasi jumlahnya)
        $semuaStokBarang = Barang::withSum('stokBarang as total_stok', 'jumlah')
            ->orderByRaw('COALESCE(total_stok, 0) asc')
            ->orderBy('nama_barang')
            ->get();

        $stokBarangMax = $semuaStokBarang->max('total_stok') ?: 1;

        // Jumlah jenis barang yang stoknya benar-benar 0 di semua gudang
        $stokHabisCount = Barang::has('stokBarang')
            ->withSum('stokBarang as total_stok', 'jumlah')
            ->get()
            ->filter(fn ($barang) => ($barang->total_stok ?? 0) == 0)
            ->count();

        // 4 permohonan bantuan terbaru
        $permohonanTerbaru = PermohonanBantuan::latest('tanggal')
            ->latest('jam')
            ->take(4)
            ->get();

        return view('admin.beranda', compact(
            'pageTitle',
            'totalItemLogistik',
            'permohonanMenunggu',
            'distribusiBulanIni',
            'semuaStokBarang',
            'stokBarangMax',
            'stokHabisCount',
            'permohonanTerbaru'
        ));
    }
}