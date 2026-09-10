<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index(Request $request, string $kategori)
    {
        $search   = $request->get('q');
        $gudangId = $request->get('gudang_id');

        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $query = Barang::query()
            ->where('kategori', $kategori);

        if ($gudangId) {
            $query->whereHas('stokBarang', function ($q) use ($gudangId) {
                $q->where('gudang_id', $gudangId)
                ->where('jumlah', '>', 0);
            });

            $query->with([
                'stokBarang' => function ($q) use ($gudangId) {
                    $q->where('gudang_id', $gudangId);
                }
            ]);
        } else {
            $query->with('stokBarang.gudang');
        }

        if ($search) {
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        $barang = $query
            ->orderBy('nama_barang', 'asc')
            ->paginate($perPage)
            ->withQueryString();


        $gudangList = Gudang::orderBy('nama_gudang')->get();

        $kategoriLabel = $kategori === 'peralatan'
            ? 'Peralatan'
            : 'Logistik Non Permakanan';

        $pageTitle = 'Ketersediaan Stok ' . $kategoriLabel;

        $gudangTerpilih = $gudangId
            ? $gudangList->firstWhere('id', (int) $gudangId)
            : null;

        $barang->getCollection()->transform(function ($item) use ($gudangTerpilih, $kategoriLabel, $gudangId) {

            $item->stok_tampil = $item->stokBarang->sum('jumlah');

            if ($gudangId) {
                // Jika user memilih gudang tertentu
                $item->gudang_label = $gudangTerpilih
                    ? $gudangTerpilih->nama_gudang
                    : 'Gudang Tidak Ditemukan';
            } else {
                // Jika dropdown = Semua Gudang
                $item->gudang_label = 'Semua Gudang';
            }

            $item->kategori_label = $kategoriLabel;

            return $item;
        });


        return view('general.stok.index', compact(
            'barang',
            'gudangList',
            'kategori',
            'kategoriLabel',
            'pageTitle',
            'gudangId'
        ));
    }
}
