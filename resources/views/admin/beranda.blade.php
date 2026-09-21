@extends('layouts.admin')

@php($pageTitle = 'Beranda')

@section('content')
<div class="bg-white rounded-xl shadow p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-blue-900">
            Selamat Datang di Portal Admin Logistik <span class="text-orange-500">SIAP MADANG</span>
        </h1>
        <p class="text-gray-500 text-sm mt-1 max-w-xl">
            Pantau persediaan material tanggap darurat dan percepat verifikasi permohonan logistik bencana wilayah Kabupaten Cilacap.
        </p>
    </div>
    <a href="{{ route('admin.mutasi.create.masuk') }}"
       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-3 rounded-lg whitespace-nowrap">
        <iconify-icon icon="mdi:plus" width="18" height="18"></iconify-icon>
        Input Stok
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm font-semibold text-gray-700 mb-2">Total Item Logistik</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalItemLogistik, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">Item</span></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm font-semibold text-gray-700 mb-2">Permohonan Menunggu</p>
        <p class="text-2xl font-bold text-gray-900">{{ $permohonanMenunggu }} <span class="text-sm font-normal text-gray-500">Perlu Diverifikasi</span></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm font-semibold text-gray-700 mb-2">Distribusi Bulan Ini</p>
        <p class="text-2xl font-bold text-gray-900">{{ $distribusiBulanIni }} <span class="text-sm font-normal text-gray-500">Distribusi</span></p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm font-semibold text-gray-700 mb-2">Stok Habis</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stokHabisCount }} <span class="text-sm font-normal text-gray-500">Jenis Barang</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-5">
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <p class="font-bold text-blue-900 flex items-center gap-2">
                <iconify-icon icon="mdi:file-document-outline" width="20" height="20" class="text-orange-500"></iconify-icon>
                Permohonan Bantuan Terbaru
            </p>
            <a href="{{ route('admin.permohonan') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">Lihat Semua &rarr;</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-700 border-gray-100 ">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Pemohon &amp; Jabatan</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permohonanTerbaru as $p)
                    <tr class="border-t border-gray-100">
                        <td class="px-5 py-3 text-gray-500 text-xs align-top">
                            {{ $p->tanggal->format('d M Y') }}<br>{{ \Illuminate\Support\Str::limit($p->jam, 5, '') }} WIB
                        </td>
                        <td class="px-5 py-3 align-top">
                            <p class="font-semibold text-gray-800">{{ $p->nama_pemohon }}</p>
                            <p class="text-gray-500 text-xs">{{ $p->jabatan }}</p>
                        </td>
                        <td class="px-5 py-3 align-top">
                            @if($p->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-600">Diajukan</span>
                            @elseif($p->status === 'disetujui')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-600">Disetujui</span>
                            @elseif($p->status === 'ditolak')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">Ditolak</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">Sebagian</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400">Belum ada permohonan bantuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-bold text-blue-900 flex items-center gap-2">
                <iconify-icon icon="mdi:warehouse" width="20" height="20" class="text-orange-500"></iconify-icon>
                Stok Terendah
            </p>
        </div>
        <div class="max-h-[260px] overflow-y-auto">
            @forelse($semuaStokBarang as $barang)
                <div class="px-5 py-2.5 border-b border-gray-50">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-800">{{ $barang->nama_barang }}</span>
                        <span class="font-bold {{ ($barang->total_stok ?? 0) == 0 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $barang->total_stok ?? 0 }} <span class="font-normal text-gray-400">{{ $barang->satuan }}</span>
                        </span>
                    </div>
                    @php($persen = ($barang->total_stok ?? 0) > 0 ? max(2, round(($barang->total_stok / $stokBarangMax) * 100)) : 0)
                    <div class="h-1.5 bg-gray-100 rounded-full">
                        <div class="h-1.5 bg-red-500 rounded-full" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 text-sm py-6">Belum ada data barang.</p>
            @endforelse
        </div>
        <div class="px-5 py-4 border-t border-gray-100 text-center">
            <a href="{{ route('admin.stok.index') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">Lihat Stok Selengkapnya &rarr;</a>
        </div>
    </div>
</div>
@endsection