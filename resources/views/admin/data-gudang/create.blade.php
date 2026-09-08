@extends('layouts.admin')

@php($pageTitle = 'Data Gudang')

@section('content')
<a href="{{ route('admin.data-gudang.index') }}" class="inline-flex items-center gap-1.5 text-blue-900 font-bold mb-4">
    <iconify-icon icon="mdi:arrow-left" width="18" height="18"></iconify-icon>
    Data Gudang
</a>

<div class="bg-white rounded shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white text-sm font-semibold px-5 py-3">Tambah Gudang</div>

    <form method="POST" action="{{ route('admin.data-gudang.store') }}" class="p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Gudang</label>
            <input type="text" name="nama_gudang" value="{{ old('nama_gudang') }}"
                   class="w-full border rounded px-3 py-2 text-sm @error('nama_gudang') border-red-400 @enderror" required>
            @error('nama_gudang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Jl. Merdeka No. 10, Cilacap"
                   class="w-full border rounded px-3 py-2 text-sm @error('lokasi') border-red-400 @enderror">
            @error('lokasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Operator Gudang</label>
            <select name="operator_gudang_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">-- Belum ada operator --</option>
                @foreach($operatorList as $op)
                    <option value="{{ $op->id }}" {{ old('operator_gudang_id') == $op->id ? 'selected' : '' }}>
                        {{ $op->nama }} ({{ $op->username }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Hanya user ber-role UPT yang belum ditugaskan ke gudang lain yang muncul di sini.</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.data-gudang.index') }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-[#0f1f3d] hover:bg-[#16305e] text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection