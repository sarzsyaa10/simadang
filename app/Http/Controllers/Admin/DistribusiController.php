<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DistribusiDetail;
use App\Models\Gudang;
use App\Models\MutasiBarang;
use App\Models\PengaturanSistem;
use App\Models\PermohonanBantuan;
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

    public function create(Request $request)
    {
        // Distribusi cuma boleh keluar dari Gudang Induk & Gudang Radjiman,
        // bukan dari gudang UPT.
        $gudangList = Gudang::gudangUtama()->orderBy('nama_gudang')->get();
        $gudangUptList = Gudang::gudangUpt()->orderBy('nama_gudang')->get();
        $barangList = Barang::orderBy('nama_barang')->get();

        // Peta stok per gudang, dipakai JS (Alpine) buat filter dropdown
        // barang sesuai gudang yang dipilih di tiap baris item, dan biar
        // gak bisa milih barang yang stoknya 0 di gudang itu.
        $stokMap = StokBarang::with('barang')
            ->whereIn('gudang_id', $gudangList->pluck('id'))
            ->where('jumlah', '>', 0)
            ->get()
            ->groupBy('gudang_id')
            ->map(function ($rows) {
                return $rows->map(fn ($s) => [
                    'barang_id'   => $s->barang_id,
                    'nama_barang' => $s->barang->nama_barang,
                    'satuan'      => $s->barang->satuan,
                    'jumlah'      => $s->jumlah,
                ])->values();
            });

        // Kalau datang dari halaman Permohonan Bantuan (tombol "Buat Surat
        // Distribusi"), pre-fill daftar barang dari item yang sudah
        // disetujui, biar Admin tinggal pilih gudang asalnya. Gudang tujuan
        // (gudang UPT si pemohon) juga otomatis kekunci dari situ, biar
        // pelaporan distribusi di sisi UPT bisa nyambung ke surat ini.
        $permohonan = null;
        $prefillItems = collect();
        $gudangTujuanId = null;

        if ($request->filled('permohonan_bantuan_id')) {
            $permohonan = PermohonanBantuan::with('permohonanBantuanDetail.barang', 'user')
                ->whereIn('status', ['disetujui', 'sebagian'])
                ->find($request->get('permohonan_bantuan_id'));

            if ($permohonan) {
                $prefillItems = $permohonan->permohonanBantuanDetail
                    ->where('status', 'disetujui')
                    ->map(fn ($d) => [
                        'barang_id'   => $d->barang_id,
                        'nama_barang' => $d->barang->nama_barang ?? '-',
                        'satuan'      => $d->barang->satuan ?? '',
                        'jumlah'      => $d->jumlah,
                    ])->values();

                $gudangTujuanId = $permohonan->user->gudang_id ?? null;
            }
        }

        return view('admin.distribusi.create', compact(
            'barangList', 'gudangList', 'gudangUptList', 'stokMap', 'permohonan', 'prefillItems', 'gudangTujuanId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat'            => 'required|string|max:100|unique:surat_distribusi,nomor_surat',
            'tanggal'                => 'required|date',
            'jam'                    => 'required',
            'kendaraan'              => 'nullable|string|max:100',
            'tujuan'                 => 'nullable|string|max:150',
            'tingkat_posko'          => 'nullable|string|max:100',
            'petugas'                => 'nullable|string|max:150',
            'permohonan_bantuan_id'  => 'nullable|exists:permohonan_bantuan,id',
            'gudang_tujuan_id'       => 'nullable|exists:gudang,id',
            'items'                  => 'required|array|min:1',
            'items.*.barang_id'      => 'required|exists:barang,id',
            'items.*.gudang_id'      => 'required|exists:gudang,id',
            'items.*.jumlah'         => 'required|integer|min:1',
            'items.*.sumber'         => 'nullable|string|max:100',
            'items.*.keterangan'     => 'nullable|string|max:255',
        ]);

        try {
            $surat = DB::transaction(function () use ($validated) {
                $surat = SuratDistribusi::create([
                    'nomor_surat'           => $validated['nomor_surat'],
                    'tanggal'               => $validated['tanggal'],
                    'jam'                   => $validated['jam'],
                    'kendaraan'             => $validated['kendaraan'] ?? null,
                    'tujuan'                => $validated['tujuan'] ?? null,
                    'tingkat_posko'         => $validated['tingkat_posko'] ?? null,
                    'petugas'               => $validated['petugas'] ?? null,
                    'user_id'               => auth()->id(),
                    'permohonan_bantuan_id' => $validated['permohonan_bantuan_id'] ?? null,
                    'gudang_tujuan_id'      => $validated['gudang_tujuan_id'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    $this->keluarkanStok($surat, $item['barang_id'], $item['gudang_id'], $item['jumlah'], $item['sumber'] ?? null, $item['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
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
        $distribusi->load(['user', 'pelaporanDistribusi', 'permohonanBantuan']);

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
        $gudangUptList = Gudang::gudangUpt()->orderBy('nama_gudang')->get();

        return view('admin.distribusi.edit', compact('distribusi', 'gudangUptList'));
    }

    public function update(Request $request, SuratDistribusi $distribusi)
    {
        $validated = $request->validate([
            'nomor_surat'      => 'required|string|max:100|unique:surat_distribusi,nomor_surat,' . $distribusi->id,
            'tanggal'          => 'required|date',
            'jam'              => 'required',
            'kendaraan'        => 'nullable|string|max:100',
            'tujuan'           => 'nullable|string|max:150',
            'tingkat_posko'    => 'nullable|string|max:100',
            'petugas'          => 'nullable|string|max:150',
            'gudang_tujuan_id' => 'nullable|exists:gudang,id',
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

    public function cetak(SuratDistribusi $distribusi)
    {
        $distribusi->load(['distribusiDetail.barang', 'distribusiDetail.gudang']);
        $pengaturan = PengaturanSistem::current();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.distribusi.cetak', compact('distribusi', 'pengaturan'))
            ->setPaper('a4', 'portrait');

        $namaFile = str_replace(['/', '\\'], '-', $distribusi->nomor_surat);
        $namaFile = preg_replace('/\s+/', ' ', trim($namaFile));

        return $pdf->stream('Surat-Distribusi-' . $namaFile . '.pdf');
    }

    // ---------------------------------------------------------
    // Item / Distribusi Detail
    // ---------------------------------------------------------

    public function createDetail(SuratDistribusi $distribusi)
    {
        $barangList = $this->getBarangListWithStok();
        $gudangList = Gudang::gudangUtama()->orderBy('nama_gudang')->get();

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
            'sumber'     => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($distribusi, $validated) {
                $this->keluarkanStok($distribusi, $validated['barang_id'], $validated['gudang_id'], $validated['jumlah'], $validated['sumber'] ?? null, $validated['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
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
        $barangList = $this->getBarangListWithStok();

        // Kalau barang yang lagi dipilih sekarang stoknya udah 0 (misal habis
        // dipakai di transaksi lain), tetap masukkan ke list biar dropdown
        // gak kehilangan opsi yang sedang aktif.
        if (! $barangList->contains('id', $detail->barang_id)) {
            $barangList = $barangList->push($detail->barang)->sortBy('nama_barang')->values();
        }

        $gudangList = Gudang::gudangUtama()->orderBy('nama_gudang')->get();

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
            'sumber'     => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($distribusi, $detail, $validated) {
                // Kembalikan stok lama dulu, baru keluarkan sesuai data baru
                $this->kembalikanStok($detail);
                $detail->delete();

                $this->keluarkanStok($distribusi, $validated['barang_id'], $validated['gudang_id'], $validated['jumlah'], $validated['sumber'] ?? null, $validated['keterangan'] ?? null, $validated['tanggal'], $validated['jam']);
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

    /**
     * Barang yang stoknya masih tersedia (>0) di Gudang Induk atau Radjiman.
     * Dipakai di form Tambah/Edit item Distribusi biar Admin gak bisa milih
     * barang yang stoknya kosong di gudang admin.
     */
    protected function getBarangListWithStok()
    {
        $adminGudangIds = Gudang::gudangUtama()->pluck('id');

        return Barang::whereHas('stokBarang', function ($q) use ($adminGudangIds) {
            $q->whereIn('gudang_id', $adminGudangIds)->where('jumlah', '>', 0);
        })
        ->with(['stokBarang' => function ($q) use ($adminGudangIds) {
            $q->whereIn('gudang_id', $adminGudangIds);
        }])
        ->orderBy('nama_barang')
        ->get();
    }

    protected function keluarkanStok(SuratDistribusi $surat, int $barangId, int $gudangId, int $jumlah, ?string $sumber, ?string $keterangan, string $tanggal, string $jam): void
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
            'sumber'              => $sumber,
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