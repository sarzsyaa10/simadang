<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DistribusiDetail;
use App\Models\Gudang;
use App\Models\MutasiBarang;
use App\Models\StokBarang;
use App\Models\SuratDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = SuratDistribusi::withCount('distribusiDetail')
            ->with('pelaporanDistribusi');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhere('petugas', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $distribusi = $query->latest('tanggal')->paginate($perPage)->withQueryString();

        return view('admin.distribusi.index', compact('distribusi', 'search', 'perPage'));
    }

    public function create()
    {
        $barangList = Barang::orderBy('nama_barang')->get();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.distribusi.create', compact('barangList', 'gudangList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat'         => 'required|string|max:100|unique:surat_distribusi,nomor_surat',
            'tanggal'             => 'required|date',
            'jam'                 => 'required',
            'kendaraan'           => 'nullable|string|max:100',
            'tujuan'              => 'nullable|string|max:150',
            'petugas'             => 'nullable|string|max:150',
            'items'               => 'required|array|min:1',
            'items.*.barang_id'   => 'required|exists:barang,id',
            'items.*.gudang_id'   => 'required|exists:gudang,id',
            'items.*.jumlah'      => 'required|integer|min:1',
            'items.*.keterangan'  => 'nullable|string|max:255',
        ]);

        try {
            $surat = DB::transaction(function () use ($validated) {
                $surat = SuratDistribusi::create([
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal'     => $validated['tanggal'],
                    'jam'         => $validated['jam'],
                    'kendaraan'   => $validated['kendaraan'] ?? null,
                    'tujuan'      => $validated['tujuan'] ?? null,
                    'petugas'     => $validated['petugas'] ?? null,
                    'user_id'     => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    $this->keluarkanStok($surat, $item['barang_id'], $item['gudang_id'], $item['jumlah'], $item['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
                }

                return $surat;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.distribusi.show', $surat->id)
            ->with('success', 'Surat distribusi berhasil dibuat.');
    }

    public function show(Request $request, SuratDistribusi $distribusi)
    {
        $distribusi->load(['user', 'pelaporanDistribusi']);

        $itemSearch = $request->get('item_search');
        $itemPerPage = (int) $request->get('item_per_page', 10);

        $itemsQuery = DistribusiDetail::where('surat_distribusi_id', $distribusi->id)
            ->with(['barang', 'gudang']);

        if ($itemSearch) {
            $itemsQuery->where(function ($q) use ($itemSearch) {
                $q->whereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%{$itemSearch}%"))
                  ->orWhere('keterangan', 'like', "%{$itemSearch}%");
            });
        }

        $items = $itemsQuery->latest('id')->paginate($itemPerPage, ['*'], 'item_page')->withQueryString();

        return view('admin.distribusi.show', compact('distribusi', 'items', 'itemSearch', 'itemPerPage'));
    }

    public function edit(SuratDistribusi $distribusi)
    {
        return view('admin.distribusi.edit', compact('distribusi'));
    }

    public function update(Request $request, SuratDistribusi $distribusi)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surat_distribusi,nomor_surat,' . $distribusi->id,
            'tanggal'     => 'required|date',
            'jam'         => 'required',
            'kendaraan'   => 'nullable|string|max:100',
            'tujuan'      => 'nullable|string|max:150',
            'petugas'     => 'nullable|string|max:150',
        ]);

        $distribusi->update($validated);

        return redirect()
            ->route('admin.distribusi.show', $distribusi->id)
            ->with('success', 'Data surat distribusi berhasil diperbarui.');
    }

    public function destroy(SuratDistribusi $distribusi)
    {
        DB::transaction(function () use ($distribusi) {
            foreach ($distribusi->distribusiDetail as $detail) {
                $this->kembalikanStok($detail);
            }
            $distribusi->distribusiDetail()->delete();
            $distribusi->delete();
        });

        return redirect()
            ->route('admin.distribusi')
            ->with('success', 'Surat distribusi berhasil dihapus dan stok dikembalikan.');
    }

    // ---------------------------------------------------------
    // Item / Distribusi Detail
    // ---------------------------------------------------------

    public function createDetail(SuratDistribusi $distribusi)
    {
        $barangList = Barang::orderBy('nama_barang')->get();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.distribusi.detail-create', compact('distribusi', 'barangList', 'gudangList'));
    }

    public function storeDetail(Request $request, SuratDistribusi $distribusi)
    {
        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'gudang_id'  => 'required|exists:gudang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($distribusi, $validated) {
                $this->keluarkanStok($distribusi, $validated['barang_id'], $validated['gudang_id'], $validated['jumlah'], $validated['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['jumlah' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.distribusi.show', $distribusi->id)
            ->with('success', 'Barang distribusi berhasil ditambahkan.');
    }

    public function editDetail(SuratDistribusi $distribusi, DistribusiDetail $detail)
    {
        $barangList = Barang::orderBy('nama_barang')->get();
        $gudangList = Gudang::orderBy('nama_gudang')->get();

        return view('admin.distribusi.detail-edit', compact('distribusi', 'detail', 'barangList', 'gudangList'));
    }

    public function updateDetail(Request $request, SuratDistribusi $distribusi, DistribusiDetail $detail)
    {
        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'gudang_id'  => 'required|exists:gudang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($distribusi, $detail, $validated) {
                // Kembalikan stok lama dulu, baru keluarkan sesuai data baru
                $this->kembalikanStok($detail);
                $detail->delete();

                $this->keluarkanStok($distribusi, $validated['barang_id'], $validated['gudang_id'], $validated['jumlah'], $validated['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['jumlah' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.distribusi.show', $distribusi->id)
            ->with('success', 'Barang distribusi berhasil diperbarui.');
    }

    public function destroyDetail(SuratDistribusi $distribusi, DistribusiDetail $detail)
    {
        DB::transaction(function () use ($detail) {
            $this->kembalikanStok($detail);
            $detail->delete();
        });

        return redirect()
            ->route('admin.distribusi.show', $distribusi->id)
            ->with('success', 'Barang distribusi berhasil dihapus dan stok dikembalikan.');
    }

    // ---------------------------------------------------------
    // Helper stok & mutasi
    // ---------------------------------------------------------

    protected function keluarkanStok(SuratDistribusi $surat, int $barangId, int $gudangId, int $jumlah, ?string $keterangan, string $tanggal, string $jam): void
    {
        $stok = StokBarang::where('barang_id', $barangId)->where('gudang_id', $gudangId)->first();

        if (!$stok || $stok->jumlah < $jumlah) {
            $barang = Barang::find($barangId);
            $tersedia = $stok->jumlah ?? 0;
            throw new \RuntimeException("Stok {$barang?->nama_barang} di gudang tidak cukup (tersedia: {$tersedia}).");
        }

        $stok->decrement('jumlah', $jumlah);

        DistribusiDetail::create([
            'surat_distribusi_id' => $surat->id,
            'barang_id'           => $barangId,
            'gudang_id'           => $gudangId,
            'jumlah'              => $jumlah,
            'keterangan'          => $keterangan,
        ]);

        MutasiBarang::create([
            'barang_id'  => $barangId,
            'gudang_id'  => $gudangId,
            'user_id'    => auth()->id(),
            'tanggal'    => $tanggal,
            'jam'        => $jam,
            'area'       => 'keluar',
            'jumlah'     => $jumlah,
            'keterangan' => 'Distribusi ' . $surat->nomor_surat . ($keterangan ? " - {$keterangan}" : ''),
        ]);
    }

    protected function kembalikanStok(DistribusiDetail $detail): void
    {
        $stok = StokBarang::firstOrCreate(
            ['barang_id' => $detail->barang_id, 'gudang_id' => $detail->gudang_id],
            ['jumlah' => 0]
        );
        $stok->increment('jumlah', $detail->jumlah);

        MutasiBarang::create([
            'barang_id'  => $detail->barang_id,
            'gudang_id'  => $detail->gudang_id,
            'user_id'    => auth()->id(),
            'tanggal'    => now()->toDateString(),
            'jam'        => now()->toTimeString(),
            'area'       => 'masuk',
            'jumlah'     => $detail->jumlah,
            'keterangan' => 'Pembatalan/koreksi distribusi ' . ($detail->suratDistribusi?->nomor_surat ?? ''),
        ]);
    }
}