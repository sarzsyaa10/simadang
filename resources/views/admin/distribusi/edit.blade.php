@extends('layouts.admin')

@php($pageTitle = 'Distribusi')

@section('content')
<div class="flex items-center gap-2 mb-1">
    <a href="{{ route('admin.distribusi.show', $distribusi->id) }}" class="text-blue-900">
        <iconify-icon icon="mdi:arrow-left" width="26" height="26"></iconify-icon>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Distribusi</h1>
</div>
<p class="text-gray-500 text-sm mb-4">Ubah data surat distribusi barang.</p>

@if ($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-2">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
        <h2 class="font-semibold">Edit Data Surat Distribusi Barang</h2>
    </div>

    <form method="POST" action="{{ route('admin.distribusi.update', $distribusi->id) }}">
        @csrf @method('PUT')

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $distribusi->tanggal->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Jam:</label>
                <input type="time" name="jam" value="{{ old('jam', \Illuminate\Support\Str::limit($distribusi->jam, 5, '')) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Nomor Surat:</label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $distribusi->nomor_surat) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Kendaraan:</label>
                <input type="text" name="kendaraan" value="{{ old('kendaraan', $distribusi->kendaraan) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Petugas:</label>
                <input type="text" name="petugas" value="{{ old('petugas', $distribusi->petugas) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Tujuan:</label>
                <textarea name="tujuan" rows="3"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">{{ old('tujuan', $distribusi->tujuan) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 p-4 border-t border-gray-100">
            <a href="{{ route('admin.distribusi.show', $distribusi->id) }}"
               class="text-center bg-red-500 hover:bg-red-600 text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Batal</a>
            <button type="submit"
                class="bg-[#0f1f3d] hover:bg-[#16295a] text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Update</button>
        </div>
    </form>
</div>
@endsection