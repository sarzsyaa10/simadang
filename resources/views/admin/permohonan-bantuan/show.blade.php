@extends('layouts.admin')

@php($pageTitle = 'Verifikasi Permohonan Bantuan')

@section('content')
<div class="flex items-center gap-3 mb-4">
    <a href="{{ route('admin.permohonan') }}" class="text-blue-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6" />
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Permohonan Bantuan</h1>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-100 text-green-700 text-sm px-4 py-2 rounded">{{ session('success') }}</div>
@endif

<div class="bg-white rounded shadow overflow-hidden max-w-4xl">
    <div class="bg-blue-900 text-white font-semibold px-6 py-3 flex justify-between items-center">
        <span>Verifikasi Permohonan Bantuan</span>

        @php
            $badge = match($permohonan->status) {
                'disetujui' => 'bg-green-100 text-green-700',
                'ditolak' => 'bg-red-100 text-red-700',
                'sebagian' => 'bg-blue-100 text-blue-700',
                default => 'bg-orange-100 text-orange-600',
            };
            $label = match($permohonan->status) {
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'sebagian' => 'Disetujui Sebagian',
                default => 'Diajukan',
            };
        @endphp
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal:</label>
                <input type="text" value="{{ \Carbon\Carbon::parse($permohonan->tanggal)->format('Y-m-d') }} - {{ \Carbon\Carbon::parse($permohonan->jam)->format('H:i') }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-50 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pemohon:</label>
                <input type="text" value="{{ $permohonan->nama_pemohon }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-50 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan:</label>
                <input type="text" value="{{ $permohonan->jabatan }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-50 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat:</label>
                <input type="text" value="{{ $permohonan->alamat }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-50 text-gray-600">
            </div>
        </div>

        <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Barang:</label>
        <table class="w-full text-sm border rounded overflow-hidden">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-2">Nama Barang</th>
                    <th class="p-2">Jumlah</th>
                    <th class="p-2">Satuan</th>
                    <th class="p-2">Status</th>
                    <th class="p-2 w-64">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permohonan->permohonanBantuanDetail as $detail)
                    <tr class="border-t align-top">
                        <td class="p-2">{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="p-2">{{ $detail->jumlah }}</td>
                        <td class="p-2">{{ $detail->barang->satuan ?? '-' }}</td>
                        <td class="p-2">
                            @php
                                $itemBadge = match($detail->status) {
                                    'disetujui' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    default => 'bg-orange-100 text-orange-600',
                                };
                                $itemLabel = match($detail->status) {
                                    'disetujui' => 'Disetujui',
                                    'ditolak' => 'Ditolak',
                                    default => 'Diajukan',
                                };
                            @endphp
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $itemBadge }}">{{ $itemLabel }}</span>
                            @if ($detail->status === 'ditolak' && $detail->keterangan)
                                <p class="text-xs text-gray-500 mt-1">{{ $detail->keterangan }}</p>
                            @endif
                        </td>
                        <td class="p-2">
                            @if ($detail->status === 'pending')
                                <div class="flex flex-col gap-1.5">
                                    <form method="POST" action="{{ route('admin.permohonan.item.setuju', $detail->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full bg-blue-900 hover:bg-blue-950 text-white text-xs font-semibold py-1.5 rounded">
                                            Setuju
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.permohonan.item.tolak', $detail->id) }}" class="flex gap-1.5">
                                        @csrf @method('PATCH')
                                        <input type="text" name="keterangan" placeholder="Alasan (opsional)"
                                               class="border rounded px-2 py-1 text-xs flex-1 min-w-0">
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 rounded shrink-0">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Sudah diputuskan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-2 text-center text-gray-400">Belum ada barang.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            <a href="{{ route('admin.permohonan') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-5 py-2 rounded">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
