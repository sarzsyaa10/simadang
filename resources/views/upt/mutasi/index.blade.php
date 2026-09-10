@extends('layouts.upt')

@php($pageTitle = 'Mutasi Barang')

@section('title', $pageTitle)

@section('content')

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('upt.stok.index') }}"
        class="text-[#0f1f3d] hover:text-blue-700 transition translate-y-1">
            <iconify-icon icon="mdi:arrow-left" width="28" height="28"></iconify-icon>
        </a>

        <h1 class="text-2xl md:text-3xl font-bold text-[#0f1f3d]">
            {{ $pageTitle }}
        </h1>
    </div>

    <div class="bg-white rounded-lg shadow p-5">

        <div class="flex items-center gap-3 mb-4">
            <label class="text-sm font-semibold text-gray-700 shrink-0">Gudang:</label>
            <div class="w-full sm:w-72 border border-gray-300 rounded px-3 py-2 text-sm bg-gray-100 text-gray-600">
                {{ $gudang->nama_gudang ?? '-' }}
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('upt.mutasi.create.masuk') }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded transition">
                <x-icon name="plus" class="w-4 h-4" /> Tambah Stok Barang Masuk
            </a>
            <a href="{{ route('upt.mutasi.create.keluar') }}"
               class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2.5 rounded transition">
                <x-icon name="plus" class="w-4 h-4" /> Tambah Stok Barang Keluar
            </a>
            <a href="{{ route('upt.mutasi.index') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded transition">
                <x-icon name="refresh" class="w-4 h-4" /> Reload
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-2 text-sm text-gray-600">
                @if (request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                <span>Show</span>
                <div class="relative">
                    <select name="per_page" onchange="this.form.submit()"
                            class="appearance-none border border-gray-300 rounded pl-3 pr-8 py-1.5 text-sm bg-white">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', 10) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
                <span>entries</span>
            </form>

            <form method="GET" class="relative w-full sm:w-72">
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <x-icon name="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search"
                       class="w-full border border-gray-300 rounded pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </form>
        </div>

        <div class="overflow-x-auto rounded border border-gray-100">
            <table class="w-full text-sm text-left">
                <thead class="bg-[#0f1f3d] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'tanggal',
                                'direction' => ($sort === 'tanggal' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Tanggal
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'jam',
                                'direction' => ($sort === 'jam' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Jam
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'nama_barang',
                                'direction' => ($sort === 'nama_barang' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Nama Barang
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'area',
                                'direction' => ($sort === 'area' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Arah
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'jumlah',
                                'direction' => ($sort === 'jumlah' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Qty
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($mutasi as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 text-gray-700">{{ \Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ \Illuminate\Support\Carbon::parse($item->jam)->format('H:i') }}</td>
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                @if ($item->area === 'masuk')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Masuk</span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Keluar</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $item->jumlah }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('upt.mutasi.edit', $item) }}" title="Edit"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('upt.mutasi.destroy', $item) }}" onsubmit="return confirm('Hapus data mutasi ini? Stok akan disesuaikan kembali.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded bg-red-50 text-red-600 hover:bg-red-100 transition">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">Belum ada data mutasi di gudang anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 text-sm text-gray-500">
            <p>
                @if ($mutasi->total() > 0)
                    Showing {{ $mutasi->firstItem() }} to {{ $mutasi->lastItem() }} of {{ $mutasi->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </p>
            <x-pagination :paginator="$mutasi" />
        </div>
    </div>
@endsection
