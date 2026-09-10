<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StokController extends Controller
{
    private function gudangId(): int
    {
        $gudangId = auth()->user()->gudang_id;

        abort_if(!$gudangId, 403, 'Akun anda belum ditautkan ke gudang manapun.');

        return $gudangId;
    }

    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'logistik_non_permakanan');
        $search   = $request->get('search', $request->get('q'));
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $gudangId = $this->gudangId();

        $sort = $request->get('sort', 'nama_barang');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['nama_barang', 'stok', 'satuan', 'created_at'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'nama_barang';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = Barang::with([
            'stokBarang' => function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId);
            }
        ])
            ->whereHas('stokBarang', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId);
            })
            ->where('kategori', $kategori);

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        if ($sort === 'stok') {
            $query->orderBy(
                StokBarang::select('jumlah')
                    ->whereColumn('stok_barang.barang_id', 'barang.id')
                    ->where('gudang_id', $gudangId)
                    ->limit(1),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $barang = $query->paginate($perPage)->withQueryString();

        return view('upt.stok.index', compact(
            'barang',
            'kategori',
            'search',
            'sort',
            'direction'
        ));
    }

    public function create(Request $request)
    {
        $kategori = $request->get('kategori', 'logistik_non_permakanan');
        $gudang   = auth()->user()->gudang;

        return view('upt.stok.create', compact('kategori', 'gudang'));
    }

    public function store(Request $request)
    {
        $gudangId = $this->gudangId();

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori'    => 'required|in:logistik_non_permakanan,peralatan',
            'satuan'      => 'required|string|max:50',
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
            'gudang_id' => $gudangId,
            'jumlah'    => $validated['stok'],
        ]);

        return redirect()
            ->route('upt.stok.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    private function stokMilikSendiri(Barang $barang): StokBarang
    {
        $gudangId = $this->gudangId();

        $stok = $barang->stokBarang()->where('gudang_id', $gudangId)->first();

        abort_if(!$stok, 403, 'Barang ini bukan milik gudang UPT anda.');

        return $stok;
    }

    public function edit(Barang $stok)
    {
        $stokBarang = $this->stokMilikSendiri($stok);
        $barang     = $stok;
        $gudang     = auth()->user()->gudang;

        return view('upt.stok.edit', compact('barang', 'stokBarang', 'gudang'));
    }

    public function update(Request $request, Barang $stok)
    {
        $stokBarang = $this->stokMilikSendiri($stok);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'satuan'      => 'required|string|max:50',
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

        $stokBarang->update(['jumlah' => $validated['stok']]);

        return redirect()
            ->route('upt.stok.index', ['kategori' => $stok->kategori])
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $stok)
    {
        $stokBarang = $this->stokMilikSendiri($stok);
        $kategori   = $stok->kategori;

        $stokBarang->delete();

        if ($stok->stokBarang()->doesntExist()) {
            if ($stok->foto) {
                Storage::disk('public')->delete($stok->foto);
            }
            $stok->delete();
        }

        return redirect()
            ->route('upt.stok.index', ['kategori' => $kategori])
            ->with('success', 'Barang berhasil dihapus.');
    }
}
