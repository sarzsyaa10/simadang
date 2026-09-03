@extends('layouts.upt')

@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-blue-900">{{ $pageTitle }}</h1>
    <div class="flex gap-2">
        <button onclick="location.reload()" class="inline-flex items-center gap-1.5 bg-white border text-sm px-4 py-2 rounded-lg shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Reload
        </button>
        <a href="{{ route('upt.distribusi.pelaporan.create') }}"
           class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Data
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
        <form method="GET" class="flex items-center gap-2 text-sm">
            @if($search)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif
            <span>Show</span>
            <select name="per_page" onchange="this.form.submit()" class="border rounded-lg px-2 py-1.5 text-sm">
                @foreach ([5, 10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>entries</span>
        </form>

        <form method="GET" class="relative">
            @if($perPage != 5)
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search"
                   class="border rounded-lg pl-9 pr-3 py-2 text-sm w-64">
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-5">
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-[#0f1f3d] text-white text-left">
                <tr>
                    @foreach (['Tanggal', 'Nama UPT', 'Scan Surat', 'Bukti Foto', 'Koordinat', 'Keterangan', 'Tindakan'] as $h)
                        <th class="p-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide">
                                {{ $h }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 5l4 5H8l4-5zm0 14l-4-5h8l-4 5z"/>
                                </svg>
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($pelaporan as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->tanggal_lapor->format('d/m/Y') }}</td>
                        <td class="p-2">{{ $item->nama_upt }}</td>
                        <td class="p-2">
                            @if($item->scan_surat)
                                <a href="{{ asset('storage/' . $item->scan_surat) }}" target="_blank" class="text-blue-600 underline">Surat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-2">
                            @if($item->bukti_foto)
                                <a href="{{ asset('storage/' . $item->bukti_foto) }}" target="_blank" class="text-blue-600 underline">Foto</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-2 max-w-[160px] truncate" title="{{ $item->koordinat }}">{{ $item->koordinat ?? '-' }}</td>
                        <td class="p-2 max-w-[200px] truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                        <td class="p-2">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('upt.distribusi.pelaporan.edit', $item->id) }}" title="Edit" class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('upt.distribusi.pelaporan.destroy', $item->id) }}"
                                      onsubmit="return confirm('Yakin hapus laporan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-3 mt-4 text-sm text-gray-500">
        <p class="text-left">
            @if ($pelaporan->total() > 0)
                Showing {{ $pelaporan->firstItem() }} to {{ $pelaporan->lastItem() }} of {{ $pelaporan->total() }} entries
            @else
                Showing 0 entries
            @endif
        </p>
        <div class="flex justify-center">
            <x-pagination :paginator="$pelaporan" />
        </div>
        <div></div>
    </div>
</div>
@endsection