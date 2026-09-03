@extends('layouts.admin')

<?php $pageTitle = 'Permohonan Bantuan'; ?>

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-blue-900">Permohonan Bantuan</h1>

    <a href="{{ route('admin.permohonan') }}"
       class="inline-flex items-center gap-1.5 bg-white border text-sm px-4 py-2 rounded shadow-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 1 1-3-6.7" />
            <path d="M21 3v6h-6" />
        </svg>
        Reload
    </a>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-100 text-green-700 text-sm px-4 py-2 rounded">{{ session('success') }}</div>
@endif

<div class="bg-white rounded shadow p-5">
    <form method="GET" class="flex justify-end mb-4">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search"
               class="border rounded px-3 py-2 text-sm w-64">
    </form>

    <table class="w-full text-sm">
        <thead class="bg-blue-900 text-white text-left">
            <tr>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Pemohon</th>
                <th class="p-3">Jabatan</th>
                <th class="p-3">Alamat</th>
                <th class="p-3">Barang</th>
                <th class="p-3">Status</th>
                <th class="p-3">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permohonan as $item)
                <tr class="border-b align-top">
                    <td class="p-3 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}
                    </td>
                    <td class="p-3">{{ $item->nama_pemohon }}</td>
                    <td class="p-3">{{ $item->jabatan }}</td>
                    <td class="p-3">{{ $item->alamat }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.permohonan.show', $item->id) }}" class="text-blue-700 hover:underline">
                            {{ $item->permohonanBantuanDetail->count() }} jenis barang
                        </a>
                    </td>
                    <td class="p-3">
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
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.permohonan.show', $item->id) }}">
                                <img src="{{ asset('images/icons/pencil.svg') }}" class="w-4 h-4" alt="Verifikasi">
                            </a>
                            <form method="POST" action="{{ route('admin.permohonan.destroy', $item->id) }}"
                                  onsubmit="return confirm('Yakin hapus permohonan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit">
                                    <img src="{{ asset('images/icons/trash.svg') }}" class="w-4 h-4" alt="Hapus">
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-4 text-center text-gray-400">Belum ada permohonan bantuan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $permohonan->links() }}</div>
</div>
@endsection
