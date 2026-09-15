@extends('layouts.admin')

@php($pageTitle = 'Distribusi')

@section('content')
<div class="flex items-center gap-2 mb-5">
    <a href="{{ route('admin.distribusi') }}" class="text-blue-900">
        <iconify-icon icon="mdi:arrow-left" width="26" height="26"></iconify-icon>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Distribusi</h1>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden mb-4">
    <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
        <h2 class="font-semibold">Detail Distribusi Barang</h2>
    </div>

    <div class="text-sm">
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] bg-gray-100">
            <div class="px-6 py-3 font-semibold text-gray-700">Tanggal</div>
            <div class="px-6 py-3 text-gray-700">{{ $distribusi->tanggal->translatedFormat('l, d F Y') }} {{ \Illuminate\Support\Str::limit($distribusi->jam, 5, '') }}</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] bg-white">
            <div class="px-6 py-3 font-semibold text-gray-700">Nomor Surat</div>
            <div class="px-6 py-3 text-gray-700">{{ $distribusi->nomor_surat }}</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] bg-gray-100">
            <div class="px-6 py-3 font-semibold text-gray-700">Tujuan</div>
            <div class="px-6 py-3 text-gray-700">{{ $distribusi->tujuan ?: '-' }}</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] bg-white">
            <div class="px-6 py-3 font-semibold text-gray-700">Kendaraan</div>
            <div class="px-6 py-3 text-gray-700">{{ $distribusi->kendaraan ?: '-' }}</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] bg-gray-100">
            <div class="px-6 py-3 font-semibold text-gray-700">Petugas</div>
            <div class="px-6 py-3 text-gray-700">{{ $distribusi->petugas ?: '-' }}</div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
        <a href="{{ route('admin.distribusi.edit', $distribusi->id) }}"
           class="w-9 h-9 flex items-center justify-center rounded-md border border-blue-400 text-blue-600 hover:bg-blue-50">
            <iconify-icon icon="mdi:pencil-outline" width="18" height="18"></iconify-icon>
        </a>
        <form method="POST" action="{{ route('admin.distribusi.destroy', $distribusi->id) }}"
              onsubmit="return confirm('Yakin hapus surat distribusi ini beserta seluruh itemnya? Stok akan dikembalikan.')">
            @csrf @method('DELETE')
            <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-md border border-red-500 text-red-600 hover:bg-red-50">
                <iconify-icon icon="mdi:trash-can-outline" width="18" height="18"></iconify-icon>
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="flex items-center gap-3 p-5 border-b border-gray-100">
        <a href="{{ route('admin.distribusi.show', $distribusi->id) }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-300">
            <iconify-icon icon="mdi:reload" width="16" height="16"></iconify-icon>
            Reload
        </a>
        <a href="{{ route('admin.distribusi.detail.create', $distribusi->id) }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
            Tambah Data
        </a>
    </div>

    <form method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-gray-100">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            Show
            <select name="item_per_page" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900">
                <option value="10" {{ $itemPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $itemPerPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $itemPerPage == 50 ? 'selected' : '' }}>50</option>
            </select>
            entries
        </div>

        <div class="relative w-full sm:w-80">
            <iconify-icon icon="mdi:magnify" width="18" height="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
            <input type="text" name="item_search" value="{{ $itemSearch }}" placeholder="Search"
                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900"
                   onchange="this.form.submit()">
        </div>
    </form>

    <table class="w-full text-sm">
        <thead class="text-left text-gray-600 text-xs font-semibold uppercase tracking-wide bg-gray-50">
            <tr>
                <th class="p-4">Barang</th>
                <th class="p-4">Qty</th>
                <th class="p-4">Satuan</th>
                <th class="p-4">Gudang</th>
                <th class="p-4">Keterangan</th>
                <th class="p-4">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $detail)
                <tr class="border-t border-gray-100">
                    <td class="p-4 text-gray-800">{{ $detail->barang->nama_barang }}</td>
                    <td class="p-4 text-gray-700">{{ $detail->jumlah }}</td>
                    <td class="p-4 text-gray-700">{{ $detail->barang->satuan }}</td>
                    <td class="p-4 text-gray-700">{{ $detail->gudang->nama_gudang }}</td>
                    <td class="p-4 text-gray-700">{{ $detail->keterangan ?: '-' }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.distribusi.detail.edit', [$distribusi->id, $detail->id]) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-md border border-blue-400 text-blue-600 hover:bg-blue-50">
                                <iconify-icon icon="mdi:pencil-outline" width="16" height="16"></iconify-icon>
                            </a>
                            <form method="POST" action="{{ route('admin.distribusi.detail.destroy', [$distribusi->id, $detail->id]) }}"
                                  onsubmit="return confirm('Yakin hapus item ini? Stok akan dikembalikan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-md border border-red-500 text-red-600 hover:bg-red-50">
                                    <iconify-icon icon="mdi:trash-can-outline" width="16" height="16"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada item barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">
            Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} entries
        </p>
        <div class="flex items-center gap-1">
            <a href="{{ $items->previousPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ $items->onFirstPage() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Previous</a>
            @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="px-3 py-1.5 text-sm rounded-md {{ $page == $items->currentPage() ? 'bg-[#0f1f3d] text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            <a href="{{ $items->nextPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ !$items->hasMorePages() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Next</a>
        </div>
    </div>
</div>
@endsection