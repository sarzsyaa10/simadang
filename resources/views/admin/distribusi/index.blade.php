@extends('layouts.admin')

@php($pageTitle = 'Distribusi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-3xl font-bold text-blue-900">Distribusi</h1>
        <p class="text-gray-500 mt-1">Informasi pendistribusian bantuan barang logistik dan peralatan kebencanaan.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('admin.distribusi') }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-300">
            <iconify-icon icon="mdi:reload" width="16" height="16"></iconify-icon>
            Reload
        </a>
        <a href="{{ route('admin.distribusi.create') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
            Tambah Data
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-5 mb-4">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            Show
            <select name="per_page" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
            entries
        </div>

        <div class="relative w-full sm:w-80">
            <iconify-icon icon="mdi:magnify" width="18" height="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search"
                   class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900"
                   onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#0f1f3d] text-white text-left text-xs font-semibold uppercase tracking-wide">
            <tr>
                <th class="p-4">Tanggal</th>
                <th class="p-4">No. Surat</th>
                <th class="p-4">Tujuan</th>
                <th class="p-4">Kendaraan</th>
                <th class="p-4">Petugas</th>
                <th class="p-4">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distribusi as $item)
                <tr class="border-t border-gray-100">
                    <td class="p-4 text-gray-700">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="p-4 text-gray-700">{{ $item->nomor_surat }}</td>
                    <td class="p-4 text-gray-700">{{ $item->tujuan ?: '-' }}</td>
                    <td class="p-4 text-gray-700">{{ $item->kendaraan ?: '-' }}</td>
                    <td class="p-4 text-gray-700">{{ $item->petugas ?: '-' }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.distribusi.show', $item->id) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-md border border-green-500 text-green-600 hover:bg-green-50">
                                <iconify-icon icon="mdi:eye-outline" width="16" height="16"></iconify-icon>
                            </a>
                            <button type="button" title="Unduh Surat (segera hadir)"
                                    onclick="alert('Fitur unduh surat distribusi belum tersedia.')"
                                    class="w-8 h-8 flex items-center justify-center rounded-md border border-orange-400 text-orange-500 hover:bg-orange-50">
                                <iconify-icon icon="mdi:download-outline" width="16" height="16"></iconify-icon>
                            </button>
                            <a href="{{ route('admin.distribusi.show', $item->id) }}"
                               class="w-8 h-8 flex items-center justify-center rounded-md border border-blue-400 text-blue-600 hover:bg-blue-50">
                                <iconify-icon icon="mdi:pencil-outline" width="16" height="16"></iconify-icon>
                            </a>
                            <form method="POST" action="{{ route('admin.distribusi.destroy', $item->id) }}"
                                  onsubmit="return confirm('Yakin hapus surat distribusi ini? Stok barang akan dikembalikan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-md border border-red-500 text-red-600 hover:bg-red-50">
                                    <iconify-icon icon="mdi:trash-can-outline" width="16" height="16"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada data distribusi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-3 px-5 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500 text-center sm:text-left">
            Showing {{ $distribusi->firstItem() ?? 0 }} to {{ $distribusi->lastItem() ?? 0 }} of {{ $distribusi->total() }} entries
        </p>
        <div class="flex items-center justify-center gap-1">
            <a href="{{ $distribusi->previousPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ $distribusi->onFirstPage() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Previous</a>
            @foreach($distribusi->getUrlRange(1, $distribusi->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="px-3 py-1.5 text-sm rounded-md {{ $page == $distribusi->currentPage() ? 'bg-[#0f1f3d] text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            <a href="{{ $distribusi->nextPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ !$distribusi->hasMorePages() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Next</a>
        </div>
    </div>
</div>
@endsection