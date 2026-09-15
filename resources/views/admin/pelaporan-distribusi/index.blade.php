@extends('layouts.admin')

@php($pageTitle = 'Pelaporan Distribusi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-3xl font-bold text-blue-900">Riwayat Pelaporan Distribusi</h1>
        <p class="text-gray-500 mt-1">Daftar riwayat pelaporan distribusi bantuan logistik dan peralatan</p>
    </div>

    <div class="shrink-0">
        <a href="{{ route('admin.distribusi.pelaporan') }}"
           class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-300">
            <iconify-icon icon="mdi:reload" width="16" height="16"></iconify-icon>
            Reload
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
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Tanggal <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Nama UPT <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Scan Surat <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Bukti Foto <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Koordinat <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">
                    <span class="inline-flex items-center gap-1">Keterangan <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" class="inline-block"><path d="M5 0L9 5H1L5 0Z"/><path d="M5 14L1 9H9L5 14Z"/></svg></span>
                </th>
                <th class="p-4">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelaporan as $item)
                <tr class="border-t border-gray-100">
                    <td class="p-4 text-gray-700">{{ $item->tanggal_lapor->format('d/m/Y') }}</td>
                    <td class="p-4 text-gray-700">{{ $item->nama_upt ?: '-' }}</td>
                    <td class="p-4">
                        @if($item->scan_surat)
                            <a href="{{ asset('storage/'.$item->scan_surat) }}" target="_blank"
                               class="text-blue-700 underline hover:text-blue-900">{{ $item->scan_surat_nama_asli ?: basename($item->scan_surat) }}</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($item->bukti_foto)
                            <a href="{{ asset('storage/'.$item->bukti_foto) }}" target="_blank"
                               class="text-blue-700 underline hover:text-blue-900">{{ $item->bukti_foto_nama_asli ?: basename($item->bukti_foto) }}</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-gray-700">{{ \Illuminate\Support\Str::limit($item->koordinat, 18) ?: '-' }}</td>
                    <td class="p-4 text-gray-700">{{ \Illuminate\Support\Str::limit($item->keterangan, 30) ?: '-' }}</td>
                    <td class="p-4">
                        <a href="{{ route('admin.distribusi.pelaporan.show', $item->id) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-md border border-green-500 text-green-600 hover:bg-green-50">
                            <iconify-icon icon="mdi:eye-outline" width="16" height="16"></iconify-icon>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-6 text-center text-gray-400">Belum ada laporan distribusi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-3 px-5 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500 text-center sm:text-left">
            Showing {{ $pelaporan->firstItem() ?? 0 }} to {{ $pelaporan->lastItem() ?? 0 }} of {{ $pelaporan->total() }} entries
        </p>
        <div class="flex items-center justify-center gap-1">
            <a href="{{ $pelaporan->previousPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ $pelaporan->onFirstPage() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Previous</a>
            @foreach($pelaporan->getUrlRange(1, $pelaporan->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="px-3 py-1.5 text-sm rounded-md {{ $page == $pelaporan->currentPage() ? 'bg-[#0f1f3d] text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            <a href="{{ $pelaporan->nextPageUrl() ?? '#' }}"
               class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600 {{ !$pelaporan->hasMorePages() ? 'pointer-events-none opacity-50' : 'hover:bg-gray-50' }}">Next</a>
        </div>
    </div>
</div>
@endsection