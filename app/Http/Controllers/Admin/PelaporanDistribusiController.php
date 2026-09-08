<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PelaporanDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelaporanDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

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

        if ($status) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->get('per_page', 10);
        $pelaporan = $query->latest('tanggal_lapor')->paginate($perPage)->withQueryString();

        return view('admin.pelaporan-distribusi.index', compact('pelaporan', 'search', 'status', 'perPage'));
    }

    public function show(PelaporanDistribusi $pelaporan)
    {
        $pelaporan->load(['suratDistribusi.distribusiDetail.barang', 'suratDistribusi.distribusiDetail.gudang', 'user']);

        return view('admin.pelaporan-distribusi.show', compact('pelaporan'));
    }

    public function terima(PelaporanDistribusi $pelaporan)
    {
        $pelaporan->update(['status' => 'diterima']);

        return redirect()
            ->route('admin.distribusi.pelaporan')
            ->with('success', 'Laporan distribusi diterima.');
    }

    public function tolak(PelaporanDistribusi $pelaporan)
    {
        // Skema saat ini hanya mengenal status pending/diterima.
        // Laporan yang ditolak dihapus agar pihak UPT dapat mengirim laporan ulang.
        if ($pelaporan->scan_surat) {
            Storage::disk('public')->delete($pelaporan->scan_surat);
        }
        if ($pelaporan->bukti_foto) {
            Storage::disk('public')->delete($pelaporan->bukti_foto);
        }

        $pelaporan->delete();

        return redirect()
            ->route('admin.distribusi.pelaporan')
            ->with('success', 'Laporan distribusi ditolak dan dihapus. UPT perlu melapor ulang.');
    }
}
