@extends('layouts.admin')

@php($pageTitle = 'Laporan Keadaan Stok')

@section('content')

    <h1 class="text-2xl md:text-3xl font-extrabold text-[#0f1f3d]">{{ $pageTitle }}</h1>
    <p class="text-sm text-gray-500 mt-1 mb-5">Pantauan logistik dan aktivitas gudang hari ini.</p>

    <div class="bg-white rounded-lg shadow p-6" x-data="{
            bulan: {{ $bulanIni }},
            tahun: {{ $tahunIni }},
            kategori: 'logistik_non_permakanan',
            urlExport(base) {
                return base + '?bulan=' + this.bulan + '&tahun=' + this.tahun + '&kategori=' + this.kategori;
            }
         }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h2 class="text-lg font-bold text-gray-700">Pilih Periode</h2>

            <div class="flex gap-2">
                <a :href="urlExport('{{ route('admin.laporan.export.pdf') }}')" target="_blank"
                   class="inline-flex items-center gap-2 bg-[#0f1f3d] hover:bg-[#16305c] text-white text-sm font-semibold px-5 py-2.5 rounded transition">
                    Export PDF
                </a>
                <a :href="urlExport('{{ route('admin.laporan.export.excel') }}')"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2.5 rounded transition">
                    Export Excel
                </a>
            </div>
        </div>

        <div class="space-y-4 max-w-xl">
            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Bulan:</label>
                <div class="relative">
                    <select x-model.number="bulan" class="w-full appearance-none border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">
                        @foreach ([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ] as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Tahun:</label>
                <input type="number" x-model.number="tahun" min="2000" max="2100"
                       class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1.5">Kategori Barang:</label>
                <div class="relative">
                    <select x-model="kategori" class="w-full appearance-none border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-900">
                        <option value="logistik_non_permakanan">Logistik</option>
                        <option value="peralatan">Peralatan</option>
                    </select>
                    <x-icon name="chevron-down" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
            </div>
        </div>
    </div>
@endsection
