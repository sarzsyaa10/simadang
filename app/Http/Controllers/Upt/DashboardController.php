<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use App\Models\PermohonanBantuan;
use App\Models\StokBarang;
use App\Models\SuratDistribusi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $gudangId = auth()->user()->gudang_id;
        $userId   = auth()->id();

        // Kartu 1: total unit barang (bukan jenis) yang ada di gudang UPT ini.
        $stokGudang = (int) StokBarang::where('gudang_id', $gudangId)->sum('jumlah');

        // Kartu 2 & 3: surat distribusi yang ditujukan ke gudang ini,
        // dipisah berdasarkan sudah/belum ada laporan distribusinya.
        $menungguLaporan = SuratDistribusi::where('gudang_tujuan_id', $gudangId)
            ->whereDoesntHave('pelaporanDistribusi')
            ->count();

        $laporanDiterima = SuratDistribusi::where('gudang_tujuan_id', $gudangId)
            ->whereHas('pelaporanDistribusi')
            ->count();

        // Kartu 4: permohonan bantuan milik UPT ini yang masih menunggu keputusan.
        $permohonanPending = PermohonanBantuan::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        // Daftar distribusi yang belum dilaporkan (3 teratas, terbaru duluan).
        $distribusiMenunggu = SuratDistribusi::where('gudang_tujuan_id', $gudangId)
            ->whereDoesntHave('pelaporanDistribusi')
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->take(3)
            ->get();

        // Permohonan bantuan terakhir milik UPT ini (5 teratas, terbaru duluan).
        $permohonanTerakhir = PermohonanBantuan::with('permohonanBantuanDetail.barang')
            ->where('user_id', $userId)
            ->orderByDesc('tanggal')
            ->orderByDesc('jam')
            ->take(5)
            ->get();

        return view('upt.beranda', [
            'pageTitle'          => 'Dashboard',
            'stokGudang'         => $stokGudang,
            'menungguLaporan'    => $menungguLaporan,
            'laporanDiterima'    => $laporanDiterima,
            'permohonanPending'  => $permohonanPending,
            'distribusiMenunggu' => $distribusiMenunggu,
            'permohonanTerakhir' => $permohonanTerakhir,
        ]);
    }
}