@extends('layouts.admin')

<?php $pageTitle = 'Permohonan Bantuan'; ?>

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#0f1f3d]">Permohonan Bantuan</h1>

    <a href="{{ route('admin.permohonan') }}"
       class="inline-flex items-center gap-1.5 bg-white border text-sm px-4 py-2.5 rounded-lg shadow-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 1 1-3-6.7" />
            <path d="M21 3v6h-6" />
        </svg>
        Reload
    </a>
</div>

{{-- Toolbar card: Show entries + Search --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <form method="GET" class="flex items-center gap-2 text-sm text-gray-700">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <span>Show</span>
        <select name="per_page" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0f1f3d]/20">
            @foreach ([5, 10, 25, 50, 100] as $size)
                <option value="{{ $size }}" @selected(request('per_page', 5) == $size)>{{ $size }}</option>
            @endforeach
        </select>
        <span>entries</span>
    </form>

    <form method="GET" class="relative w-full sm:w-80">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search"
               class="w-full border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0f1f3d]/20">
    </form>
</div>

{{-- Table card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#0f1f3d] text-white">
                <tr>
                    @php
                        $columns = [
                            ['key' => 'tanggal', 'label' => 'Tanggal', 'sortable' => true],
                            ['key' => 'nama_pemohon', 'label' => 'Pemohon', 'sortable' => true],
                            ['key' => 'jabatan', 'label' => 'Jabatan', 'sortable' => true],
                            ['key' => 'alamat', 'label' => 'Alamat', 'sortable' => true],
                            ['key' => 'barang', 'label' => 'Barang', 'sortable' => false],
                            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
                            ['key' => 'tindakan', 'label' => 'Tindakan', 'sortable' => false],
                        ];
                        $currentSort = request('sort');
                        $currentDir = request('direction', 'asc');
                    @endphp
                    @foreach ($columns as $column)
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wide text-xs whitespace-nowrap">
                            @if ($column['sortable'])
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $column['key'], 'direction' => ($currentSort === $column['key'] && $currentDir === 'asc') ? 'desc' : 'asc']) }}"
                                   class="inline-flex items-center gap-1.5 hover:text-orange-300">
                                    {{ $column['label'] }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7 15 5 5 5-5" /><path d="m7 9 5-5 5 5" />
                                    </svg>
                                </a>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($permohonan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $item->nama_pemohon }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $item->jabatan }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $item->alamat }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.permohonan.show', $item->id) }}" class="text-blue-600 font-medium hover:underline">
                                {{ $item->permohonanBantuanDetail->count() }} jenis barang
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badge = match($item->status) {
                                    'disetujui' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    'sebagian' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-orange-100 text-orange-600',
                                };
                                $label = match($item->status) {
                                    'disetujui' => 'Disetujui',
                                    'ditolak' => 'Ditolak',
                                    'sebagian' => 'Disetujui Sebagian',
                                    default => 'Diajukan',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.permohonan.show', $item->id) }}" title="Lihat / Verifikasi" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.permohonan.destroy', $item->id) }}"
                                      onsubmit="return confirm('Yakin hapus permohonan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                            <path d="M10 11v6" /><path d="M14 11v6" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada permohonan bantuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-3 px-6 py-4 border-t border-gray-100 text-sm text-gray-500">
        <p class="text-left">
            @if ($permohonan->total() > 0)
                Showing {{ $permohonan->firstItem() }} to {{ $permohonan->lastItem() }} of {{ $permohonan->total() }} entries
            @else
                Showing 0 entries
            @endif
        </p>

        <div class="flex justify-center">
            <x-pagination :paginator="$permohonan" />
        </div>

        <div></div>
    </div>
</div>
@endsection