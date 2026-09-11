<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\MutasiBarang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $kategori  = $request->get('kategori', 'logistik_non_permakanan');
        $gudangId  = $request->get('gudang_id');
        $search    = $request->get('search', $request->get('q'));
        $perPage   = (int) $request->get('per_page', 10);
        $perPage   = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $sort      = $request->get('sort', 'nama_barang');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['nama_barang', 'stok', 'satuan', 'created_at'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'nama_barang';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = Barang::with(['stokBarang' => function ($q) use ($gudangId) {
                $q->with('gudang');
                if ($gudangId) {
                    $q->where('gudang_id', $gudangId);
                }
            }])
            ->withSum(['stokBarang as stok_total' => function ($q) use ($gudangId) {
                if ($gudangId) {
                    $q->where('gudang_id', $gudangId);
                }
            }], 'jumlah')
            ->where('kategori', $kategori);

        if ($gudangId) {
            $query->whereHas('stokBarang', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId);
            });
        }

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        if ($sort === 'stok') {
            $query->orderBy('stok_total', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $barang     = $query->paginate($perPage)->withQueryString();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.stok.index', compact(
            'barang',
            'gudangList',
            'kategori',
            'gudangId',
            'search',
            'sort',
            'direction'
        ));
    }

    public function create(Request $request)
    {
        $kategori   = $request->get('kategori', 'logistik_non_permakanan');
        $gudangId   = $request->get('gudang_id');
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.stok.create', compact('kategori', 'gudangList', 'gudangId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori'    => 'required|in:logistik_non_permakanan,peralatan',
            'satuan'      => 'required|string|max:50',
            'gudang_id'   => 'required|exists:gudang,id',
            'stok'        => 'required|integer|min:0',
            'tanggal'     => 'required|date',
            'jam'         => 'required',
            'foto'        => 'nullable|image|max:2048',
            'deskripsi'   => 'nullable|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('barang', 'public');
        }

        DB::transaction(function () use ($validated, $fotoPath) {
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

            if ($validated['stok'] > 0) {
                MutasiBarang::create([
                    'barang_id'  => $barang->id,
                    'gudang_id'  => $validated['gudang_id'],
                    'user_id'    => auth()->id(),
                    'tanggal'    => $validated['tanggal'],
                    'jam'        => $validated['jam'],
                    'area'       => 'masuk',
                    'jumlah'     => $validated['stok'],
                    'keterangan' => 'Stok awal barang baru',
                ]);
            }
        });

        return redirect()
            ->route('admin.stok.index', ['kategori' => $validated['kategori'], 'gudang_id' => $validated['gudang_id']])
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    private function resolveStokBarang(Barang $barang, ?int $stokId): StokBarang
    {
        $query = StokBarang::where('barang_id', $barang->id);

        $stokBarang = $stokId
            ? $query->where('id', $stokId)->first()
            : $query->first();

        abort_if(!$stokBarang, 404, 'Data stok untuk barang ini tidak ditemukan.');

        return $stokBarang;
    }

    public function edit(Request $request, Barang $stok)
    {
        $barang     = $stok;
        $stokBarang = $this->resolveStokBarang($barang, $request->integer('stok_id') ?: null);
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.stok.edit', compact('barang', 'stokBarang', 'gudangList'));
    }

    public function update(Request $request, Barang $stok)
    {
        $validated = $request->validate([
            'stok_id'     => 'required|exists:stok_barang,id',
            'nama_barang' => 'required|string|max:150',
            'satuan'      => 'required|string|max:50',
            'gudang_id'   => 'required|exists:gudang,id',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|max:2048',
            'deskripsi'   => 'nullable|string',
        ]);

        $stokBarang = $this->resolveStokBarang($stok, (int) $validated['stok_id']);

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

        $stokBarang->update([
            'gudang_id' => $validated['gudang_id'],
            'jumlah'    => $validated['stok'],
        ]);

        return redirect()
            ->route('admin.stok.index', ['kategori' => $stok->kategori])
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Request $request, Barang $stok)
    {
        $stokBarang = $this->resolveStokBarang($stok, $request->integer('stok_id') ?: null);
        $kategori   = $stok->kategori;

        $stokBarang->delete();

        if ($stok->stokBarang()->doesntExist()) {
            if ($stok->foto) {
                Storage::disk('public')->delete($stok->foto);
            }
            $stok->delete();
        }

        return redirect()
            ->route('admin.stok.index', ['kategori' => $kategori])
            ->with('success', 'Barang berhasil dihapus.');
    }
}
