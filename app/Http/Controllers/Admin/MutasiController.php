<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\MutasiBarang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'logistik_non_permakanan');
        $gudangId = $request->get('gudang_id');
        $search   = $request->get('q');
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $sort      = $request->get('sort', 'tanggal');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['tanggal', 'nama_barang', 'gudang', 'area', 'jumlah'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'tanggal';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = MutasiBarang::with(['barang', 'gudang'])
            ->whereHas('barang', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($sort === 'nama_barang') {
            $query->orderBy(
                Barang::select('nama_barang')
                    ->whereColumn('barang.id', 'mutasi_barang.barang_id')
                    ->limit(1),
                $direction
            );
        } elseif ($sort === 'gudang') {
            $query->orderBy(
                Gudang::select('nama_gudang')
                    ->whereColumn('gudang.id', 'mutasi_barang.gudang_id')
                    ->limit(1),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction)->orderBy('jam', $direction);
        }

        $mutasi     = $query->paginate($perPage)->withQueryString();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.mutasi.index', compact(
            'mutasi',
            'gudangList',
            'kategori',
            'gudangId',
            'search',
            'sort',
            'direction'
        ));
    }

    public function createMasuk(Request $request)
    {
        $kategori   = $request->get('kategori', 'logistik_non_permakanan');
        $gudangId   = $request->get('gudang_id');
        $barangList = Barang::where('kategori', $kategori)->orderBy('nama_barang')->get();
        $gudangList = Gudang::orderBy('nama_gudang')->get();
        $arah       = 'masuk';

        return view('admin.mutasi.create', compact('barangList', 'gudangList', 'gudangId', 'kategori', 'arah'));
    }

    public function createKeluar(Request $request)
    {
        $kategori   = $request->get('kategori', 'logistik_non_permakanan');
        $gudangId   = $request->get('gudang_id');
        $barangList = Barang::where('kategori', $kategori)
            ->whereHas('stokBarang', function ($q) {
                $q->where('jumlah', '>', 0);
            })
            ->with(['stokBarang' => function ($q) {
                $q->where('jumlah', '>', 0);
            }])
            ->orderBy('nama_barang')
            ->get();

        $barangList->each(function ($b) {
            $b->stok_per_gudang = $b->stokBarang->pluck('jumlah', 'gudang_id');
        });

        $gudangList = Gudang::orderBy('nama_gudang')->get();
        $arah       = 'keluar';

        return view('admin.mutasi.create', compact('barangList', 'gudangList', 'gudangId', 'kategori', 'arah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'gudang_id'  => 'required|exists:gudang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'arah'       => 'required|in:masuk,keluar',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kategori = Barang::findOrFail($validated['barang_id'])->kategori;

        $disesuaikan = false;

        DB::transaction(function () use ($validated, &$disesuaikan) {
            $stok = StokBarang::lockForUpdate()->firstOrCreate(
                ['barang_id' => $validated['barang_id'], 'gudang_id' => $validated['gudang_id']],
                ['jumlah' => 0]
            );

            $jumlah = $validated['jumlah'];

            if ($validated['arah'] === 'keluar') {
                if ($stok->jumlah <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok barang ini sudah habis di gudang yang dipilih, tidak bisa dikeluarkan.',
                    ]);
                }

                if ($jumlah > $stok->jumlah) {
                    $jumlah = $stok->jumlah;
                    $disesuaikan = true;
                }

                $stok->decrement('jumlah', $jumlah);
            } else {
                $stok->increment('jumlah', $jumlah);
            }

            MutasiBarang::create([
                'barang_id'  => $validated['barang_id'],
                'gudang_id'  => $validated['gudang_id'],
                'user_id'    => auth()->id(),
                'tanggal'    => $validated['tanggal'],
                'jam'        => $validated['jam'],
                'area'       => $validated['arah'],
                'jumlah'     => $jumlah,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        $redirect = redirect()->route('admin.mutasi.index', ['kategori' => $kategori]);

        return $disesuaikan
            ? $redirect->with('warning', 'Jumlah yang dimasukkan melebihi stok tersedia, jadi otomatis disesuaikan ke jumlah maksimal yang ada di gudang tersebut.')
            : $redirect->with('success', 'Mutasi barang berhasil disimpan.');
    }

    public function edit(MutasiBarang $mutasi)
    {
        $mutasi->load('barang', 'gudang');
        $barangList = Barang::where('kategori', $mutasi->barang->kategori)
            ->with('stokBarang')
            ->orderBy('nama_barang')
            ->get();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        $barangList->each(function ($b) use ($mutasi) {
            $stokPerGudang = $b->stokBarang->pluck('jumlah', 'gudang_id');

            if ($b->id == $mutasi->barang_id && $mutasi->area === 'keluar') {
                $gId = $mutasi->gudang_id;
                $stokPerGudang[$gId] = ($stokPerGudang[$gId] ?? 0) + $mutasi->jumlah;
            }

            $b->stok_per_gudang = $stokPerGudang;
        });

        return view('admin.mutasi.edit', compact('mutasi', 'barangList', 'gudangList'));
    }

    public function update(Request $request, MutasiBarang $mutasi)
    {
        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'gudang_id'  => 'required|exists:gudang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'arah'       => 'required|in:masuk,keluar',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kategori = Barang::findOrFail($validated['barang_id'])->kategori;

        $disesuaikan = false;

        DB::transaction(function () use ($validated, $mutasi, &$disesuaikan) {
            $stokLama = StokBarang::lockForUpdate()->firstOrCreate(
                ['barang_id' => $mutasi->barang_id, 'gudang_id' => $mutasi->gudang_id],
                ['jumlah' => 0]
            );
            if ($mutasi->area === 'keluar') {
                $stokLama->increment('jumlah', $mutasi->jumlah);
            } else {
                $stokLama->decrement('jumlah', $mutasi->jumlah);
            }

            $stokBaru = StokBarang::lockForUpdate()->firstOrCreate(
                ['barang_id' => $validated['barang_id'], 'gudang_id' => $validated['gudang_id']],
                ['jumlah' => 0]
            );

            $jumlah = $validated['jumlah'];

            if ($validated['arah'] === 'keluar') {
                if ($stokBaru->jumlah <= 0) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok barang ini sudah habis di gudang yang dipilih, tidak bisa dikeluarkan.',
                    ]);
                }

                if ($jumlah > $stokBaru->jumlah) {
                    $jumlah = $stokBaru->jumlah;
                    $disesuaikan = true;
                }

                $stokBaru->decrement('jumlah', $jumlah);
            } else {
                $stokBaru->increment('jumlah', $jumlah);
            }

            $mutasi->update([
                'barang_id'  => $validated['barang_id'],
                'gudang_id'  => $validated['gudang_id'],
                'tanggal'    => $validated['tanggal'],
                'jam'        => $validated['jam'],
                'area'       => $validated['arah'],
                'jumlah'     => $jumlah,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        $redirect = redirect()->route('admin.mutasi.index', ['kategori' => $kategori]);

        return $disesuaikan
            ? $redirect->with('warning', 'Jumlah yang dimasukkan melebihi stok tersedia, jadi otomatis disesuaikan ke jumlah maksimal yang ada di gudang tersebut.')
            : $redirect->with('success', 'Mutasi barang berhasil diperbarui.');
    }

    public function destroy(MutasiBarang $mutasi)
    {
        $kategori = $mutasi->barang->kategori;

        DB::transaction(function () use ($mutasi) {
            $stok = StokBarang::firstOrCreate(
                ['barang_id' => $mutasi->barang_id, 'gudang_id' => $mutasi->gudang_id],
                ['jumlah' => 0]
            );

            if ($mutasi->area === 'keluar') {
                $stok->increment('jumlah', $mutasi->jumlah);
            } else {
                $stok->decrement('jumlah', $mutasi->jumlah);
            }

            $mutasi->delete();
        });

        return redirect()
            ->route('admin.mutasi.index', ['kategori' => $kategori])
            ->with('success', 'Data mutasi berhasil dihapus.');
    }
}
