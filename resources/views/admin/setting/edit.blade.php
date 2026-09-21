@extends('layouts.admin')

@php($pageTitle = 'Pengaturan Sistem')

@section('content')
<h1 class="text-2xl font-bold text-blue-900 mb-4">Pengaturan Sistem</h1>

<div class="bg-white rounded shadow p-6 max-w-3xl">
    <h2 class="font-bold text-gray-700 mb-4">Ubah Pengaturan Sistem</h2>

    <form method="POST" action="{{ route('admin.setting.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pengurus Barang:</label>
            <input type="text" name="nama_pengurus_barang"
                   value="{{ old('nama_pengurus_barang', $pengaturan->nama_pengurus_barang) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nama_pengurus_barang')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">NIP Pengurus Barang:</label>
            <input type="text" name="nip_pengurus_barang"
                   value="{{ old('nip_pengurus_barang', $pengaturan->nip_pengurus_barang) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nip_pengurus_barang')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kabid Kedaruratan dan Logistik:</label>
            <input type="text" name="nama_kabid_kedaruratan_logistik"
                   value="{{ old('nama_kabid_kedaruratan_logistik', $pengaturan->nama_kabid_kedaruratan_logistik) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nama_kabid_kedaruratan_logistik')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">NIP Kabid Kedaruratan dan Logistik:</label>
            <input type="text" name="nip_kabid_kedaruratan_logistik"
                   value="{{ old('nip_kabid_kedaruratan_logistik', $pengaturan->nip_kabid_kedaruratan_logistik) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nip_kabid_kedaruratan_logistik')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kepala Pelaksana BPBD:</label>
            <input type="text" name="nama_kepala_pelaksana_bpbd"
                   value="{{ old('nama_kepala_pelaksana_bpbd', $pengaturan->nama_kepala_pelaksana_bpbd) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nama_kepala_pelaksana_bpbd')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">NIP Kepala Pelaksana BPBD:</label>
            <input type="text" name="nip_kepala_pelaksana_bpbd"
                   value="{{ old('nip_kepala_pelaksana_bpbd', $pengaturan->nip_kepala_pelaksana_bpbd) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            @error('nip_kepala_pelaksana_bpbd')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-blue-950 hover:bg-blue-900 text-white font-semibold py-3 rounded text-sm">
            SIMPAN →
        </button>
    </form>
</div>
@endsection
