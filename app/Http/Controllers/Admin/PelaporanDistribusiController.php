<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PelaporanDistribusi;
use Illuminate\Http\Request;

class PelaporanDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = PelaporanDistribusi::with(['suratDistribusi.distribusiDetail.barang', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_upt', 'like', "%{$search}%")
                  ->orWhereHas('suratDistribusi', function ($sq) use ($search) {
                      $sq->where('nomor_surat', 'like', "%{$search}%")
                         ->orWhere('tujuan', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $pelaporan = $query->latest('tanggal_lapor')->paginate($perPage)->withQueryString();

        return view('admin.pelaporan-distribusi.index', compact('pelaporan', 'search', 'perPage'));
    }

    public function show(PelaporanDistribusi $pelaporan)
    {
        $pelaporan->load(['suratDistribusi.distribusiDetail.barang', 'suratDistribusi.distribusiDetail.gudang', 'user']);

        return view('admin.pelaporan-distribusi.show', compact('pelaporan'));
    }
}