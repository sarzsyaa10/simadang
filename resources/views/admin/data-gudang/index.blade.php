@extends('layouts.admin')

@php($pageTitle = 'Data Gudang')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
    <div>
        <h1 class="text-xl font-bold text-blue-900">Data Gudang</h1>
        <p class="text-sm text-gray-500">Kelola daftar gudang dan operator (UPT) yang bertanggung jawab.</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.data-gudang.index') }}"
           class="inline-flex items-center gap-1.5 bg-white border text-gray-600 text-sm px-4 py-2 rounded hover:bg-gray-50">
            <iconify-icon icon="mdi:refresh" width="16" height="16"></iconify-icon>
            Reset
        </a>
        <a href="{{ route('admin.data-gudang.create') }}"
           class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">
            <iconify-icon icon="mdi:warehouse" width="16" height="16"></iconify-icon>
            Tambah Gudang
        </a>
    </div>
</div>

<div class="bg-white rounded shadow p-5">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-2">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama gudang..."
               class="border border-gray-300 rounded px-3 py-2 text-sm flex-1 sm:flex-none sm:w-72">
        <button class="inline-flex items-center gap-1.5 bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded">
            <iconify-icon icon="mdi:magnify" width="16" height="16"></iconify-icon>
            Cari
        </button>
    </form>

    <div class="rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#0f1f3d] text-white text-left">
                <tr>
                    <th class="p-2.5">Nama Gudang</th>
                    <th class="p-2.5">Lokasi</th>
                    <th class="p-2.5">Operator (UPT)</th>
                    <th class="p-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gudangList as $item)
                    <tr class="border-b border-gray-100">
                        <td class="p-2.5 font-medium text-gray-700">{{ $item->nama_gudang }}</td>
                        <td class="p-2.5">{{ $item->lokasi ?? '-' }}</td>
                        <td class="p-2.5">{{ $item->operator->nama ?? '-' }}</td>
                        <td class="p-2.5">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.data-gudang.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <iconify-icon icon="mdi:pencil-outline" width="18" height="18"></iconify-icon>
                                </a>
                                <form method="POST" action="{{ route('admin.data-gudang.destroy', $item->id) }}"
                                      onsubmit="return confirm('Yakin hapus gudang ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <iconify-icon icon="mdi:trash-can-outline" width="18" height="18"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $gudangList->links() }}</div>
</div>
@endsection