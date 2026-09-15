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

        $query->selectRaw(
            'barang.*, (
                select coalesce(sum(jumlah), 0)
                from stok_barang
                where stok_barang.barang_id = barang.id'
                . ($gudangId ? ' and stok_barang.gudang_id = ?' : '') .
            ') as stok_urut',
            $gudangId ? [$gudangId] : []
        );

        if ($gudangId) {
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
            ->orderByRaw('stok_urut = 0 asc')
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
                $item->gudang_label = $gudangTerpilih
                    ? $gudangTerpilih->nama_gudang
                    : 'Gudang Tidak Ditemukan';
            } else {
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