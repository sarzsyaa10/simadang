@extends('layouts.upt')

@php($pageTitle = 'Tambah Barang Baru')

@section('content')
<div class="bg-white rounded shadow p-6 max-w-2xl">
    <h2 class="font-bold text-gray-700 mb-4">Tambah Barang Baru</h2>

    <form method="POST" action="{{ route('upt.stok.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select name="kategori" class="w-full border rounded px-3 py-2 text-sm" required>
                <option value="logistik_non_permakanan" {{ old('kategori', $kategori) === 'logistik_non_permakanan' ? 'selected' : '' }}>Logistik Non Permakanan</option>
                <option value="peralatan" {{ old('kategori', $kategori) === 'peralatan' ? 'selected' : '' }}>Peralatan</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Barang</label>
            <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                   class="w-full border rounded px-3 py-2 text-sm" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Stok Awal</label>
                <input type="number" name="stok" min="0" value="{{ old('stok') }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Satuan</label>
                <input type="text" name="satuan" value="{{ old('satuan') }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Gudang</label>
            <select name="gudang_id" class="w-full border rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Gudang --</option>
                @foreach($gudangList as $g)
                    <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Foto Barang</label>
            <input type="file" name="foto" accept="image/*" class="w-full text-sm">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded px-3 py-2 text-sm">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('upt.stok.index', ['kategori' => $kategori]) }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection