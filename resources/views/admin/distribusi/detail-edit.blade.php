@extends('layouts.admin')

@php($pageTitle = 'Distribusi')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('admin.distribusi.show', $distribusi->id) }}" class="text-blue-900">
        <iconify-icon icon="mdi:arrow-left" width="26" height="26"></iconify-icon>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Distribusi</h1>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-2">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
        <h2 class="font-semibold">Edit Data Distribusi Barang - {{ $detail->barang->nama_barang }}</h2>
        <p class="text-white/60 text-xs mt-0.5">No. Surat {{ $distribusi->nomor_surat }} &bull; Tujuan {{ $distribusi->tujuan ?: '-' }}</p>
    </div>

    <form method="POST" action="{{ route('admin.distribusi.detail.update', [$distribusi->id, $detail->id]) }}">
        @csrf @method('PUT')

        <div class="p-6 space-y-4">
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Nama Barang:</label>
                <select name="barang_id" class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                    @foreach($barangList as $b)
                        <option value="{{ $b->id }}" {{ old('barang_id', $detail->barang_id) == $b->id ? 'selected' : '' }}>{{ $b->nama_barang }} ({{ $b->satuan }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-800 mb-1.5">Tanggal:</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $distribusi->tanggal->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                </div>
                <div>
                    <label class="block font-semibold text-gray-800 mb-1.5">Jam:</label>
                    <input type="time" name="jam" value="{{ old('jam', now()->format('H:i')) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Gudang:</label>
                <select name="gudang_id" class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                    @foreach($gudangList as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id', $detail->gudang_id) == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Jumlah Barang:</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $detail->jumlah) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Keterangan:</label>
                <textarea name="keterangan" rows="3"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">{{ old('keterangan', $detail->keterangan) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 p-4">
            <a href="{{ route('admin.distribusi.show', $distribusi->id) }}"
               class="text-center bg-red-500 hover:bg-red-600 text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Batal</a>
            <button type="submit"
                class="bg-[#0f1f3d] hover:bg-[#16295a] text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Update</button>
        </div>
    </form>
</div>
@endsection