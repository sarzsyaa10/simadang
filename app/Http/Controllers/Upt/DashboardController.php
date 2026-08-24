<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $gudangId = auth()->user()->gudang_id;

        $totalJenisBarang = DB::table('stok_barang')
            ->where('gudang_id', $gudangId)
            ->distinct('barang_id')
            ->count('barang_id');

                $totalDistribusi = DB::table('distribusi_detail')
            ->where('gudang_id', $gudangId)
            ->distinct('surat_distribusi_id')
            ->count('surat_distribusi_id');

        $permohonanMenunggu = DB::table('permohonan_bantuan')
            ->where('status', 'pending')
            ->count();

        return view('upt.beranda', [
            'pageTitle' => 'Dashboard',
            'totalJenisBarang' => $totalJenisBarang,
            'totalDistribusi' => $totalDistribusi,
            'permohonanMenunggu' => $permohonanMenunggu,
        ]);
    }
    
}