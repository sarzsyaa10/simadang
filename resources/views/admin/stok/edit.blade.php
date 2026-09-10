@extends('layouts.admin')

@php($pageTitle = 'Edit Data Stok Barang')

@section('title', $pageTitle)

@section('content')

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.stok.index', ['kategori' => $barang->kategori]) }}"
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
        <form method="POST" action="{{ route('admin.stok.update', $barang) }}" enctype="multipart/form-data" class="space-y-4"
              x-data="{ preview: null }">
            @csrf @method('PUT')
            <input type="hidden" name="stok_id" value="{{ $stokBarang->id }}">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Barang</label>

                @if ($barang->foto_url)
                    <img src="{{ $barang->foto_url }}" class="w-32 h-32 object-cover rounded border border-gray-200 mb-3">
                @endif

                <label class="block text-xs text-gray-500 mb-1">Foto Baru (Opsional)</label>
                <div class="border border-gray-300 rounded px-3 py-2 bg-white">
                    <div x-show="!preview" class="flex items-center">
                        <label for="foto"
                            class="px-3 py-1 bg-gray-100 border border-gray-300 rounded text-sm text-gray-700 cursor-pointer hover:bg-gray-200 transition">
                            Pilih Foto
                        </label>
                        <span id="file-name" class="ml-3 text-sm text-gray-500 truncate">
                            Belum ada foto dipilih
                        </span>
                    </div>

                    <div x-show="preview" class="flex items-center gap-3">
                        <img :src="preview" class="w-20 h-20 object-cover rounded border border-gray-200">
                        <div class="flex flex-col gap-1">
                            <span id="file-name-preview" class="text-sm text-gray-600 truncate max-w-xs"></span>
                            <label for="foto" class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer">Ganti Foto</label>
                        </div>
                    </div>

                    <input id="foto" type="file" name="foto" accept="image/*" class="hidden"
                        @change="preview = $event.target.files.length? URL.createObjectURL($event.target.files[0]): null;
                            document.getElementById('file-name').textContent = $event.target.files.length? $event.target.files[0].name: 'Belum ada file dipilih';
                            document.getElementById('file-name-preview').textContent =$event.target.files.length? $event.target.files[0].name: '';">
                </div>
                @error('foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('nama_barang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok</label>
                <input type="number" name="stok" min="0" value="{{ old('stok', $stokBarang->jumlah) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('stok') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ satuan: '{{ old('satuan', $barang->satuan) }}' }">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan</label>
                <select x-model="satuan"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="buah">Buah</option>
                    <option value="lembar">Lembar</option>
                    <option value="batang">Batang</option>
                    <option value="rol">Dus</option>
                    <option value="box">Paket</option>
                    <option value="set">Set</option>
                    <option value="unit">Unit</option>
                    <option value="lainnya">Lainnya</option>
                </select>

                <input x-show="satuan === 'lainnya'" type="text" name="satuan_custom" value="{{ old('satuan_custom') }}"
                    placeholder="Masukkan satuan..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm mt-2 focus:outline-none focus:ring-2 focus:ring-blue-200">

                <input type="hidden" name="satuan" :value="satuan === 'lainnya' ? '' : satuan">

                @error('satuan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                @error('satuan_custom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gudang</label>
                <select name="gudang_id" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @foreach ($gudangList as $g)
                        <option value="{{ $g->id }}" @selected(old('gudang_id', $stokBarang->gudang_id) == $g->id)>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('gudang_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.stok.index', ['kategori' => $barang->kategori]) }}"
                   class="px-5 py-2.5 rounded bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">Batal</a>
                <button type="submit"
                        class="px-5 py-2.5 rounded bg-[#0f1f3d] hover:bg-[#16305c] text-white text-sm font-semibold transition">Simpan</button>
            </div>
        </form>
        </div>
    </div>
@endsection
