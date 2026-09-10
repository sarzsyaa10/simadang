<?php

namespace App\Http\Controllers\Upt;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\MutasiBarang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    private function gudangId(): int
    {
        $gudangId = auth()->user()->gudang_id;

        abort_if(!$gudangId, 403, 'Akun anda belum ditautkan ke gudang manapun.');

        return $gudangId;
    }

    public function index(Request $request)
    {
        $gudangId = $this->gudangId();
        $search   = $request->get('q');
        $perPage  = (int) $request->get('per_page', 10);
        $perPage  = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $sort = $request->get('sort', 'tanggal');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'tanggal',
            'jam',
            'nama_barang',
            'area',
            'jumlah',
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'tanggal';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = MutasiBarang::with('barang')
            ->where('gudang_id', $gudangId);

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
        } else {
            $query->orderBy($sort, $direction);
        }

        $mutasi = $query
            ->paginate($perPage)
            ->withQueryString();

        $gudang = auth()->user()->gudang;

        return view('upt.mutasi.index', compact(
            'mutasi',
            'gudang',
            'search',
            'sort',
            'direction'
        ));
    }

    public function createMasuk()
    {
        $gudangId  = $this->gudangId();
        $barangList = Barang::orderBy('nama_barang')->get();
        $gudang    = auth()->user()->gudang;
        $arah      = 'masuk';

        return view('upt.mutasi.create', compact('barangList', 'gudang', 'arah'));
    }

    public function createKeluar()
    {
        $gudangId  = $this->gudangId();
        $barangList = Barang::whereHas('stokBarang', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId)->where('jumlah', '>', 0);
            })
            ->orderBy('nama_barang')
            ->get();
        $gudang = auth()->user()->gudang;
        $arah   = 'keluar';

        return view('upt.mutasi.create', compact('barangList', 'gudang', 'arah'));
    }

    public function store(Request $request)
    {
        $gudangId = $this->gudangId();

        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'arah'       => 'required|in:masuk,keluar',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $gudangId) {
            $stok = StokBarang::firstOrCreate(
                ['barang_id' => $validated['barang_id'], 'gudang_id' => $gudangId],
                ['jumlah' => 0]
            );

            if ($validated['arah'] === 'keluar') {
                abort_if($stok->jumlah < $validated['jumlah'], 422, 'Stok tidak mencukupi untuk mutasi keluar ini.');
                $stok->decrement('jumlah', $validated['jumlah']);
            } else {
                $stok->increment('jumlah', $validated['jumlah']);
            }

            MutasiBarang::create([
                'barang_id'  => $validated['barang_id'],
                'gudang_id'  => $gudangId,
                'user_id'    => auth()->id(),
                'tanggal'    => $validated['tanggal'],
                'jam'        => $validated['jam'],
                'area'       => $validated['arah'],
                'jumlah'     => $validated['jumlah'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()
            ->route('upt.mutasi.index')
            ->with('success', 'Mutasi barang berhasil disimpan.');
    }

    private function mutasiMilikSendiri(MutasiBarang $mutasi): MutasiBarang
    {
        abort_if($mutasi->gudang_id !== $this->gudangId(), 403, 'Data mutasi ini bukan milik gudang UPT anda.');

        return $mutasi;
    }

    public function edit(MutasiBarang $mutasi)
    {
        $mutasi     = $this->mutasiMilikSendiri($mutasi);
        $barangList = Barang::orderBy('nama_barang')->get();
        $gudang     = auth()->user()->gudang;

        return view('upt.mutasi.edit', compact('mutasi', 'barangList', 'gudang'));
    }

    public function update(Request $request, MutasiBarang $mutasi)
    {
        $mutasi   = $this->mutasiMilikSendiri($mutasi);
        $gudangId = $this->gudangId();

        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'tanggal'    => 'required|date',
            'jam'        => 'required',
            'arah'       => 'required|in:masuk,keluar',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $mutasi, $gudangId) {
            $stokLama = StokBarang::firstOrCreate(
                ['barang_id' => $mutasi->barang_id, 'gudang_id' => $gudangId],
                ['jumlah' => 0]
            );
            if ($mutasi->area === 'keluar') {
                $stokLama->increment('jumlah', $mutasi->jumlah);
            } else {
                $stokLama->decrement('jumlah', $mutasi->jumlah);
            }

            $stokBaru = StokBarang::firstOrCreate(
                ['barang_id' => $validated['barang_id'], 'gudang_id' => $gudangId],
                ['jumlah' => 0]
            );
            if ($validated['arah'] === 'keluar') {
                abort_if($stokBaru->jumlah < $validated['jumlah'], 422, 'Stok tidak mencukupi untuk mutasi keluar ini.');
                $stokBaru->decrement('jumlah', $validated['jumlah']);
            } else {
                $stokBaru->increment('jumlah', $validated['jumlah']);
            }

            $mutasi->update([
                'barang_id'  => $validated['barang_id'],
                'tanggal'    => $validated['tanggal'],
                'jam'        => $validated['jam'],
                'area'       => $validated['arah'],
                'jumlah'     => $validated['jumlah'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        });

        return redirect()
            ->route('upt.mutasi.index')
            ->with('success', 'Mutasi barang berhasil diperbarui.');
    }

    public function destroy(MutasiBarang $mutasi)
    {
        $mutasi   = $this->mutasiMilikSendiri($mutasi);
        $gudangId = $this->gudangId();

        DB::transaction(function () use ($mutasi, $gudangId) {
            $stok = StokBarang::firstOrCreate(
                ['barang_id' => $mutasi->barang_id, 'gudang_id' => $gudangId],
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
            ->route('upt.mutasi.index')
            ->with('success', 'Data mutasi berhasil dihapus.');
    }
}
