@extends('layouts.upt')

@php($pageTitle = $kategori === 'peralatan' ? 'Stok Peralatan' : 'Stok Logistik Non Permakanan')

@section('title', $pageTitle)

@section('content')

    <h1 class="text-2xl md:text-3xl font-bold text-[#0f1f3d] mb-5">{{ $pageTitle }}</h1>

    <div class="bg-white rounded-lg shadow p-5">

        {{-- Gudang UPT ini terkunci: operator tidak bisa berpindah / melihat gudang UPT lain --}}
        <div class="flex items-center gap-3 mb-4">
            <label class="text-sm font-semibold text-gray-700 shrink-0">Pilih Gudang:</label>
            <div class="relative w-full sm:w-72">
                <select disabled
                        class="w-full appearance-none border border-gray-300 rounded px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed">
                    <option>{{ auth()->user()->gudang->nama_gudang ?? '-' }}</option>
                </select>
                <x-icon name="lock" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2" />
            </div>
            <span class="text-xs text-gray-400 hidden md:inline">Stok gudang UPT lain tidak ditampilkan</span>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('upt.stok.create', $kategori) }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded transition">
                <x-icon name="plus" class="w-4 h-4" /> Tambah Barang Baru
            </a>

            @if (Route::has('upt.mutasi.index'))
                <a href="{{ route('upt.mutasi.index') }}"
                   class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded transition">
                    <x-icon name="list" class="w-4 h-4" /> Mutasi Barang
                </a>
            @endif

            <a href="{{ route('upt.stok.index', ['kategori' => $kategori]) }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded transition">
                <x-icon name="refresh" class="w-4 h-4" /> Reload
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-2 text-sm text-gray-600">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
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

            <form method="GET" id="searchForm" class="relative w-full sm:w-72">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <x-icon name="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search" id="searchInput" autocomplete="off" class="w-full border border-gray-300 rounded pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            </form>
        </div>

        <div class="overflow-x-auto rounded border border-gray-100">
            <table class="w-full text-sm text-left">
                <thead class="bg-[#0f1f3d] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Foto</th>

                        <th class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'nama_barang',
                                'direction' => ($sort === 'nama_barang' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-1.5 hover:text-blue-200">
                                Nama Barang
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'stok',
                                'direction' => ($sort === 'stok' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Stok
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort' => 'satuan',
                                'direction' => ($sort === 'satuan' && $direction === 'asc') ? 'desc' : 'asc',
                                'page' => 1,
                            ]) }}"
                            class="inline-flex items-center gap-2 hover:text-blue-200">
                                Satuan
                                <x-icon name="bxs:sort-alt" class="w-4 h-4" />
                            </a>
                        </th>

                        <th class="px-4 py-3 pr-5 text-right whitespace-nowrap">Tindakan</th>
                    </tr>
                </thead>
                
                <tbody id="barang-table-body" class="divide-y divide-gray-100">
                    @forelse ($barang as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5">
                                <div class="w-14 h-14 rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                                    @if ($item->foto_url)
                                        <img src="{{ $item->foto_url }}" class="w-full h-full object-cover">
                                    @else
                                        <x-icon name="image" class="w-5 h-5 text-gray-300" />
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-4 py-2.5 font-medium text-gray-800">{{ $item->nama_barang }}</td>
                            {{-- stokBarang sudah difilter di controller: hanya baris stok milik gudang UPT ini --}}
                            <td class="px-4 py-2.5 text-gray-700">{{ $item->stokBarang->first()->jumlah ?? 0 }}</td>
                            <td class="px-4 py-2.5 text-gray-700">{{ $item->satuan }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('upt.stok.edit', $item) }}"
                                       title="Edit"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('upt.stok.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
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
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada data barang di gudang anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="relative flex items-center mt-4 text-sm text-gray-500">
            <p>
                @if ($barang->total() > 0)
                    Showing {{ $barang->firstItem() }} to {{ $barang->lastItem() }} of {{ $barang->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </p>

            <div class="absolute left-1/2 -translate-x-1/2">
                <x-pagination :paginator="$barang" />
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const tableBody = document.getElementById('barang-table-body');

        let timeout;
        let controller;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);

            timeout = setTimeout(async () => {
                if (controller) {
                    controller.abort();
                }

                controller = new AbortController();

                const params = new URLSearchParams(new FormData(searchForm));

                params.delete('page');

                try {
                    const response = await fetch(
                        window.location.pathname + '?' + params.toString(),
                        {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            signal: controller.signal
                        }
                    );

                    const html = await response.text();

                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    const newBody = doc.querySelector('#barang-table-body');

                    if (newBody) {
                        tableBody.innerHTML = newBody.innerHTML;
                    }

                    window.history.replaceState(
                        {},
                        '',
                        window.location.pathname + '?' + params.toString()
                    );

                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Search error:', error);
                    }
                }
            }, 400);
        });
    </script>

    @endsection
