<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'logistik_non_permakanan');
        $search   = $request->get('search');

        $query = Barang::with('stokBarang.gudang')->where('kategori', $kategori);

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        $barang = $query->latest()->paginate(10)->withQueryString();
        $gudangList = Gudang::all();

        return view('admin.stok.index', compact('barang', 'gudangList', 'kategori', 'search'));
    }

    public function create(Request $request)
    {
        $kategori = $request->get('kategori', 'logistik_non_permakanan');
        $gudangList = Gudang::all();

        return view('admin.stok.create', compact('gudangList', 'kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori'    => 'required|in:logistik_non_permakanan,peralatan',
            'satuan'      => 'required|string|max:50',
            'gudang_id'   => 'required|exists:gudang,id',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|max:2048',
            'deskripsi'   => 'nullable|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('barang', 'public');
        }

        $barang = Barang::create([
            'nama_barang' => $validated['nama_barang'],
            'kategori'    => $validated['kategori'],
            'satuan'      => $validated['satuan'],
            'foto'        => $fotoPath,
            'deskripsi'   => $validated['deskripsi'] ?? null,
        ]);

        StokBarang::create([
            'barang_id' => $barang->id,
            'gudang_id' => $validated['gudang_id'],
            'jumlah'    => $validated['stok'],
        ]);

        return redirect()
            ->route('admin.stok.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $stok)
    {
        $barang = $stok->load('stokBarang');
        $gudangList = Gudang::all();

        return view('admin.stok.edit', compact('barang', 'gudangList'));
    }

    public function update(Request $request, Barang $stok)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'satuan'      => 'required|string|max:50',
            'gudang_id'   => 'required|exists:gudang,id',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|max:2048',
            'deskripsi'   => 'nullable|string',
        ]);

        $fotoPath = $stok->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('barang', 'public');
        }

        $stok->update([
            'nama_barang' => $validated['nama_barang'],
            'satuan'      => $validated['satuan'],
            'foto'        => $fotoPath,
            'deskripsi'   => $validated['deskripsi'] ?? null,
        ]);

        StokBarang::updateOrCreate(
            ['barang_id' => $stok->id, 'gudang_id' => $validated['gudang_id']],
            ['jumlah' => $validated['stok']]
        );

        return redirect()
            ->route('admin.stok.index', ['kategori' => $stok->kategori])
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $stok)
    {
        if ($stok->foto) {
            Storage::disk('public')->delete($stok->foto);
        }

        $kategori = $stok->kategori;
        $stok->delete();

        return redirect()
            ->route('admin.stok.index', ['kategori' => $kategori])
            ->with('success', 'Barang berhasil dihapus.');
    }
}