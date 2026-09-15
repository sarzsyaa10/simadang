@extends('layouts.admin')

@php($pageTitle = 'Pelaporan Distribusi')

@section('content')
<div class="flex items-center gap-2 mb-1">
    <a href="{{ route('admin.distribusi.pelaporan') }}" class="text-blue-900">
        <iconify-icon icon="mdi:arrow-left" width="26" height="26"></iconify-icon>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Pelaporan Distribusi</h1>
</div>
<p class="text-gray-500 text-sm mb-4">Detail laporan pendistribusian bantuan barang dari petugas UPT.</p>

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
        <h2 class="font-semibold">Detail Pelaporan Distribusi</h2>
    </div>

    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
        <div>
            <span class="text-gray-500">Tanggal Lapor</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->tanggal_lapor->format('d/m/Y') }}</div>
        </div>
        <div>
            <span class="text-gray-500">Nama UPT</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->nama_upt ?: '-' }}</div>
        </div>
        <div>
            <span class="text-gray-500">No. Surat Distribusi</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->suratDistribusi->nomor_surat ?? '-' }}</div>
        </div>
        <div>
            <span class="text-gray-500">Tujuan</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->suratDistribusi->tujuan ?? '-' }}</div>
        </div>
        <div>
            <span class="text-gray-500">Koordinat</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->koordinat ?: '-' }}</div>
        </div>

        <div class="sm:col-span-2">
            <span class="text-gray-500">Keterangan</span>
            <div class="font-semibold text-gray-800">{{ $pelaporan->keterangan ?: '-' }}</div>
        </div>
    </div>

    <div class="px-6 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <span class="text-sm text-gray-500 block mb-1.5 font-semibold">Scan Surat</span>
            @if($pelaporan->scan_surat)
                <a href="{{ asset('storage/'.$pelaporan->scan_surat) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-sm text-blue-900 hover:underline">
                    <iconify-icon icon="mdi:file-document-outline" width="16" height="16"></iconify-icon>
                    Lihat Berkas
                </a>
            @else
                <span class="text-sm text-gray-400">Tidak ada berkas</span>
            @endif
        </div>
        
        <div>
            <span class="text-sm text-gray-500 block mb-1.5 font-semibold">Bukti Foto</span>
            @if($pelaporan->bukti_foto)
                <a href="{{ asset('storage/'.$pelaporan->bukti_foto) }}" target="_blank">
                    <img src="{{ asset('storage/'.$pelaporan->bukti_foto) }}" class="w-32 h-32 object-cover rounded-md border">
                </a>
            @else
                <span class="text-sm text-gray-400">Tidak ada foto</span>
            @endif
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-6 py-3.5">
        <h2 class="font-semibold">Barang yang Didistribusikan</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-3">Nama Barang</th>
                <th class="p-3">Gudang Asal</th>
                <th class="p-3">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelaporan->suratDistribusi->distribusiDetail ?? [] as $detail)
                <tr class="border-t border-gray-100">
                    <td class="p-3 text-gray-800">{{ $detail->barang->nama_barang }}</td>
                    <td class="p-3 text-gray-600">{{ $detail->gudang->nama_gudang }}</td>
                    <td class="p-3 text-gray-600">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="p-6 text-center text-gray-400">Tidak ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection