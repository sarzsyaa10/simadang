@extends('layouts.admin')

@php($pageTitle = 'Edit Data Stok Barang')

@section('content')
<div class="bg-white rounded shadow p-6 max-w-2xl">
    <h2 class="font-bold text-gray-700 mb-4">Edit Data Stok Barang</h2>

    @php $stok = $barang->stokBarang->first(); @endphp

    <form method="POST" action="{{ route('admin.stok.update', $barang->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        @if($barang->foto)
            <img src="{{ asset('storage/'.$barang->foto) }}" class="w-32 h-32 object-cover rounded mb-3">
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Foto Baru (Opsional)</label>
            <input type="file" name="foto" accept="image/*" class="w-full text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Barang</label>
            <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                   class="w-full border rounded px-3 py-2 text-sm" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Stok</label>
                <input type="number" name="stok" min="0" value="{{ old('stok', $stok->jumlah ?? 0) }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Gudang</label>
            <select name="gudang_id" class="w-full border rounded px-3 py-2 text-sm" required>
                @foreach($gudangList as $g)
                    <option value="{{ $g->id }}" {{ old('gudang_id', $stok->gudang_id ?? '') == $g->id ? 'selected' : '' }}>
                        {{ $g->nama_gudang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded px-3 py-2 text-sm">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.stok.index', ['kategori' => $barang->kategori]) }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection