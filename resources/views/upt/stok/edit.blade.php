@extends('layouts.upt')

@php($pageTitle = 'Edit Data Stok Barang')

@section('title', $pageTitle)

@section('content')

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('upt.stok.index', ['kategori' => $barang->kategori]) }}"
        class="text-[#0f1f3d] hover:text-blue-700 transition translate-y-1">
            <iconify-icon icon="mdi:arrow-left" width="28" height="28"></iconify-icon>
        </a>

        <h1 class="text-2xl md:text-3xl font-bold text-[#0f1f3d]">
            {{ $pageTitle }}
        </h1>
    </div>

    <div class="bg-white rounded-lg shadow w-full max-w-none overflow-hidden">
        <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
            <h2 class="font-semibold">Edit Data Stok Barang</h2>
        </div>

        <div class="p-5">
        <form method="POST" action="{{ route('upt.stok.update', $barang->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Foto Saat Ini
            </label>
            @if ($barang->foto_url)
                <img src="{{ $barang->foto_url }}" class="w-32 h-32 object-cover rounded">
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Foto Baru (Opsional)
                </label>

                <div class="flex items-center w-full border border-gray-300 rounded px-3 py-2 bg-white">
                    <label for="foto"
                        class="px-3 py-1 bg-gray-100 border border-gray-300 rounded text-sm text-gray-700 cursor-pointer hover:bg-gray-200 transition">
                        Pilih Foto
                    </label>

                    <span id="file-name" class="ml-3 text-sm text-gray-500 truncate">
                        Belum ada foto dipilih
                    </span>

                    <input type="file"
                        id="foto"
                        name="foto"
                        accept="image/*"
                        class="hidden"
                        onchange="document.getElementById('file-name').textContent = this.files.length ? this.files[0].name : 'Belum ada file dipilih'">
                </div>

                @error('foto')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('nama_barang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" min="0" value="{{ old('stok', $stokBarang->jumlah ?? 0) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('stok') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('satuan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gudang</label>
                <div class="relative">
                    <input type="text" value="{{ $gudang->nama_gudang ?? '-' }}" disabled
                           class="w-full border border-gray-300 rounded pl-3 pr-9 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed">
                    <x-icon name="lock" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('upt.stok.index', ['kategori' => $barang->kategori]) }}"
                   class="px-5 py-2.5 rounded bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">Batal</a>
                <button type="submit"
                        class="px-5 py-2.5 rounded bg-[#0f1f3d] hover:bg-[#16305c] text-white text-sm font-semibold transition">Simpan</button>
            </div>
        </form>
        </div>
    </div>
@endsection
