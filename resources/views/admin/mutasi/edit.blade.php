@extends('layouts.admin')

@php($pageTitle = 'Edit Mutasi ' . ($mutasi->barang->kategori === 'peralatan' ? 'Peralatan' : 'Logistik Non Permakanan'))

@section('title', $pageTitle)

@section('content')

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.mutasi.index', ['kategori' => $mutasi->barang->kategori]) }}"
        class="text-[#0f1f3d] hover:text-blue-700 transition translate-y-1">
            <iconify-icon icon="mdi:arrow-left" width="28" height="28"></iconify-icon>
        </a>

        <h1 class="text-2xl md:text-3xl font-bold text-[#0f1f3d]">
            Mutasi {{ $mutasi->barang->kategori === 'peralatan' ? 'Peralatan' : 'Logistik Non Permakanan' }}
        </h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl overflow-hidden">
        <div class="bg-[#0f1f3d] text-white px-6 py-3.5 -mx-6 -mt-6 mb-5 rounded-t-lg">
            <h2 class="font-semibold">{{ $pageTitle }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.mutasi.update', $mutasi) }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', \Illuminate\Support\Carbon::parse($mutasi->tanggal)->format('Y-m-d')) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jam</label>
                    <input type="time" name="jam" value="{{ old('jam', \Illuminate\Support\Carbon::parse($mutasi->jam)->format('H:i')) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('jam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label>
                <select name="barang_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @foreach ($barangList as $b)
                        <option value="{{ $b->id }}" @selected(old('barang_id', $mutasi->barang_id) == $b->id)>{{ $b->nama_barang }} ({{ $b->satuan }})</option>
                    @endforeach
                </select>
                @error('barang_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Arus</label>
                <select name="arah" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="masuk" @selected(old('arah', $mutasi->area) === 'masuk')>Masuk</option>
                    <option value="keluar" @selected(old('arah', $mutasi->area) === 'keluar')>Keluar</option>
                </select>
                @error('arah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Barang</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $mutasi->jumlah) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('jumlah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ old('keterangan', $mutasi->keterangan) }}</textarea>
                @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gudang</label>
                <select name="gudang_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @foreach ($gudangList as $g)
                        <option value="{{ $g->id }}" @selected(old('gudang_id', $mutasi->gudang_id) == $g->id)>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('gudang_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.mutasi.index', ['kategori' => $mutasi->barang->kategori]) }}"
                   class="px-5 py-2.5 rounded bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">Batal</a>
                <button type="submit"
                        class="px-5 py-2.5 rounded bg-[#0f1f3d] hover:bg-[#16305c] text-white text-sm font-semibold transition">Simpan</button>
            </div>
        </form>
    </div>
@endsection
