@extends('layouts.upt')

@section('content')

    {{-- Banner selamat datang --}}
    <div class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-[#0f1f3d]">
                Selamat Datang di Portal Logistik <span class="text-orange-500">SIMADANG</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola persediaan, permohonan bantuan, dan distribusi logistik dalam satu sistem.
            </p>
        </div>

        <a href="{{ route('upt.permohonan.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-3 rounded whitespace-nowrap transition">
            <x-icon name="plus" class="w-4 h-4" /> Ajukan Permohonan
        </a>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Stok Gudang</p>
            <p class="text-3xl font-extrabold text-[#0f1f3d] mt-3">
                {{ number_format($stokGudang, 0, ',', '.') }}
                <span class="text-base font-normal text-gray-500">Item</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 leading-snug">Menunggu<br>Laporan</p>
            <p class="text-3xl font-extrabold text-[#0f1f3d] mt-3">
                {{ $menungguLaporan }}
                <span class="text-base font-normal text-gray-500">Perlu Diverifikasi</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Laporan Diterima</p>
            <p class="text-3xl font-extrabold text-[#0f1f3d] mt-3">
                {{ $laporanDiterima }}
                <span class="text-base font-normal text-gray-500">Distribusi</span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">Permohonan Pending</p>
            <p class="text-3xl font-extrabold text-[#0f1f3d] mt-3">
                {{ $permohonanPending }}
                <span class="text-base font-normal text-gray-500">Item Menipis</span>
            </p>
        </div>
    </div>

    {{-- Dua kolom bawah --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Distribusi menunggu pelaporan --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="flex items-center gap-2 font-bold text-[#0f1f3d]">
                    <x-icon name="list" class="w-4 h-4 text-orange-500" />
                    Distribusi Menunggu Pelaporan
                </h2>
                <a href="{{ route('upt.distribusi.pelaporan.index') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600 whitespace-nowrap">
                    Lihat Semua →
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($distribusiMenunggu as $surat)
                    <div class="flex items-center justify-between px-5 py-4">
                        <div class="min-w-0">
                            <p class="font-bold text-[#0f1f3d] truncate">{{ $surat->nomor_surat }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Diterima gudang {{ \Carbon\Carbon::parse($surat->tanggal)->format('d/m') }}
                            </p>
                        </div>
                        <a href="{{ route('upt.distribusi.pelaporan.create', ['surat_distribusi_id' => $surat->id]) }}"
                           class="shrink-0 inline-block bg-orange-100 text-orange-600 hover:bg-orange-200 text-xs font-semibold px-3 py-1.5 rounded-full transition">
                            Lapor
                        </a>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">
                        Tidak ada distribusi yang menunggu pelaporan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Permohonan bantuan terakhir --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="flex items-center gap-2 font-bold text-[#0f1f3d]">
                    <x-icon name="list" class="w-4 h-4 text-orange-500" />
                    Permohonan bantuan terakhir
                </h2>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($permohonanTerakhir as $p)
                    @php
                        $namaBarang = $p->permohonanBantuanDetail->pluck('barang.nama_barang')->filter()->implode(', ');

                        $badge = match ($p->status) {
                            'disetujui' => 'bg-green-100 text-green-700',
                            'ditolak'   => 'bg-red-100 text-red-700',
                            'sebagian'  => 'bg-blue-100 text-blue-700',
                            default     => 'bg-orange-100 text-orange-600',
                        };

                        $label = match ($p->status) {
                            'disetujui' => 'Disetujui',
                            'ditolak'   => 'Ditolak',
                            'sebagian'  => 'Sebagian',
                            default     => 'Pending',
                        };
                    @endphp
                    <a href="{{ route('upt.permohonan.show', $p) }}" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="font-bold text-[#0f1f3d] truncate">{{ $namaBarang ?: '-' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Diajukan {{ \Carbon\Carbon::parse($p->tanggal)->locale('id')->translatedFormat('d M Y') }}
                            </p>
                        </div>
                        <span class="shrink-0 inline-block {{ $badge }} text-xs font-semibold px-3 py-1.5 rounded-full">
                            {{ $label }}
                        </span>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">
                        Belum ada permohonan bantuan yang diajukan.
                    </div>
                @endforelse
            </div>

            <a href="{{ route('upt.permohonan.index') }}"
               class="block text-center py-3 border-t border-gray-100 text-sm font-semibold text-orange-500 hover:text-orange-600 hover:bg-gray-50 transition">
                Lihat Semua →
            </a>
        </div>
    </div>
@endsection
