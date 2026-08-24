@extends('layouts.admin')

@php($pageTitle = 'Stok ' . ($kategori === 'peralatan' ? 'Peralatan' : 'Logistik Non Permakanan'))

@section('content')
<h1 class="text-xl font-bold text-blue-900 mb-4">{{ $pageTitle }}</h1>

<div class="bg-white rounded shadow p-5">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.stok.index', ['kategori' => 'logistik_non_permakanan']) }}"
               class="px-4 py-1.5 rounded text-sm {{ $kategori === 'logistik_non_permakanan' ? 'bg-orange-500 text-white' : 'bg-gray-100' }}">
                Logistik Non Permakanan
            </a>
            <a href="{{ route('admin.stok.index', ['kategori' => 'peralatan']) }}"
               class="px-4 py-1.5 rounded text-sm {{ $kategori === 'peralatan' ? 'bg-orange-500 text-white' : 'bg-gray-100' }}">
                Peralatan
            </a>
        </div>

        <a href="{{ route('admin.stok.create', ['kategori' => $kategori]) }}"
           class="inline-flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded">
            <img src="{{ asset('images/icons/plus.svg') }}" class="w-4 h-4" alt="">
            Tambah Barang Baru
        </a>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="hidden" name="kategori" value="{{ $kategori }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari barang..."
               class="border rounded px-3 py-2 text-sm w-64">
        <button class="inline-flex items-center gap-1.5 bg-gray-700 text-white text-sm px-4 py-2 rounded">
            <img src="{{ asset('images/icons/search.svg') }}" class="w-4 h-4" alt="">
            Cari
        </button>
    </form>

    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-2">Foto</th>
                <th class="p-2">Nama Barang</th>
                <th class="p-2">Stok</th>
                <th class="p-2">Satuan</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $item)
                <tr class="border-b">
                    <td class="p-2">
                        <img src="{{ $item->foto ? asset('storage/'.$item->foto) : asset('images/no-image.png') }}"
                             class="w-12 h-12 object-cover rounded">
                    </td>
                    <td class="p-2">{{ $item->nama_barang }}</td>
                    <td class="p-2">{{ $item->stokBarang->sum('jumlah') }}</td>
                    <td class="p-2">{{ $item->satuan }}</td>
                    <td class="p-2">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.stok.edit', $item->id) }}">
                                <img src="{{ asset('images/icons/pencil.svg') }}" class="w-4 h-4" alt="Edit">
                            </a>
                            <form method="POST" action="{{ route('admin.stok.destroy', $item->id) }}"
                                  onsubmit="return confirm('Yakin hapus barang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit">
                                    <img src="{{ asset('images/icons/trash.svg') }}" class="w-4 h-4" alt="Hapus">
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $barang->links() }}</div>
</div>
@endsection