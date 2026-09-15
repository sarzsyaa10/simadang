<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanBantuan;
use App\Models\PermohonanBantuanDetail;
use Illuminate\Http\Request;

class PermohonanBantuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $permohonan = PermohonanBantuan::with('permohonanBantuanDetail.barang')
            ->withCount('suratDistribusi')
            ->when($search, function ($query, $search) {
                $query->where('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            })
            ->latest('tanggal')
            ->latest('jam')
            ->paginate(10)
            ->withQueryString();

        return view('admin.permohonan-bantuan.index', compact('permohonan', 'search'));
    }

    public function show(PermohonanBantuan $permohonan_bantuan)
    {
        $permohonan_bantuan->load([
            'permohonanBantuanDetail.barang.stokBarang' => function ($query) {
                $query->whereHas('gudang', fn ($g) => $g->gudangUtama())->with('gudang');
            },
            'verifikator',
        ]);

        return view('admin.permohonan-bantuan.show', [
            'permohonan' => $permohonan_bantuan,
        ]);
    }

    /**
     * Setujui satu barang di dalam sebuah permohonan.
     */
    public function setujuItem(PermohonanBantuanDetail $item)
    {
        $item->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);

        $item->permohonanBantuan->refreshStatus();

        return back()->with('success', 'Barang disetujui.');
    }

    /**
     * Tolak satu barang di dalam sebuah permohonan, dengan keterangan opsional.
     */
    public function tolakItem(Request $request, PermohonanBantuanDetail $item)
    {
        $validated = $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $item->update([
            'status' => 'ditolak',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        $item->permohonanBantuan->refreshStatus();

        return back()->with('success', 'Barang ditolak.');
    }

    public function destroy(PermohonanBantuan $permohonan_bantuan)
    {
        $permohonan_bantuan->permohonanBantuanDetail()->delete();
        $permohonan_bantuan->delete();

        return redirect()->route('admin.permohonan')->with('success', 'Permohonan bantuan dihapus.');
    }
}