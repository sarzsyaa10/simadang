@extends('layouts.general')

@section('title', $pageTitle)

@section('content')

    <div class="p-4 md:p-6" x-data="{ open: false, selected: null,
        search(value) {
            const params = new URLSearchParams(window.location.search);

            if (value.trim()) {
                params.set('q', value);
            } else {
                params.delete('q');
            }

            params.delete('page');

            fetch(window.location.pathname + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');

                document.querySelector('#barang-grid').innerHTML =
                    doc.querySelector('#barang-grid').innerHTML;

                window.history.replaceState(
                    {},
                    '',
                    window.location.pathname + '?' + params.toString()
                );
            });
        }
    }">
        <h1 class="text-2xl md:text-3xl font-extrabold text-[#0f1f3d]">{{ $pageTitle }}</h1>
        <p class="text-sm text-gray-500 mt-1 mb-5">
            Informasi persediaan bantuan barang logistik dan peralatan kebencanaan di gudang BPBD Kabupaten Cilacap.
        </p>

        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-5">
            <div class="relative flex-1">
                <x-icon name="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang..."
                autocomplete="off"
                x-on:input.debounce.500ms="search($event.target.value)"
                    class="w-full border border-gray-300 rounded pl-9 pr-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="relative w-full sm:w-56">
                <select name="gudang_id" onchange="this.form.submit()"
                        class="w-full appearance-none border border-gray-300 rounded pl-3 pr-9 py-2.5 text-sm bg-white">
                    <option value="">Semua Gudang</option>
                    @foreach ($gudangList as $g)
                        <option value="{{ $g->id }}" @selected($gudangId == $g->id)>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded transition whitespace-nowrap">
                Refresh <x-icon name="refresh" class="w-4 h-4" />
            </button>
        </form>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <form method="GET" class="flex items-center gap-2 text-sm text-gray-600">
                <input type="hidden" name="kategori" value="{{ $kategori }}">

                @if (request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif

                @if (request('gudang_id'))
                    <input type="hidden" name="gudang_id" value="{{ request('gudang_id') }}">
                @endif

                <span>Show</span>

                <div class="relative">
                    <select name="per_page" onchange="this.form.submit()"
                            class="appearance-none border border-gray-300 rounded pl-3 pr-8 py-1.5 text-sm bg-white">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', 10) == $n)>
                                {{ $n }}
                            </option>
                        @endforeach
                    </select>

                    <x-icon name="chevron-down"
                            class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>

                <span>entries</span>
            </form>
        </div>

        <div id="barang-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($barang as $item)
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="h-36 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if ($item->foto_url)
                            <img src="{{ $item->foto_url }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="image" class="w-8 h-8 text-gray-300" />
                        @endif
                    </div>

                    <div class="p-3 flex flex-col flex-1">
                        <p class="text-xs text-gray-400">{{ $item->kategori_label }}</p>
                        <p class="font-bold text-[#0f1f3d] text-sm leading-snug mt-0.5">{{ $item->nama_barang }}</p>

                        <div class="flex items-end justify-between mt-3 pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-400">Stok Saat Ini :</p>
                                <p class="font-bold text-[#0f1f3d] text-sm">
                                    {{ $item->stok_tampil }} <span class="font-normal text-gray-500">{{ $item->satuan }}</span>
                                </p>
                            </div>

                            <button type="button"
                                    @click="selected = {
                                        nama: @js($item->nama_barang),
                                        stok: @js($item->stok_tampil),
                                        satuan: @js($item->satuan),
                                        kategori: @js($item->kategori_label),
                                        gudang: @js($item->gudang_label),
                                        deskripsi: @js($item->deskripsi ?: 'Belum ada deskripsi untuk barang ini.'),
                                        foto: @js($item->foto_url)
                                    }; open = true"
                                    class="shrink-0 bg-[#0f1f3d] hover:bg-[#16305c] text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-400 py-16">Belum ada data barang.</div>
            @endforelse
        </div>

        <div class="relative flex items-center mt-6 text-sm text-gray-500 min-h-[36px]">
            <p>
                @if ($barang->total() > 0)
                    Showing {{ $barang->firstItem() }} to {{ $barang->lastItem() }} of {{ $barang->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </p>

            @if ($barang->hasPages())
                <div class="absolute left-1/2 -translate-x-1/2">
                    <x-pagination :paginator="$barang" />
                </div>
            @endif
        </div>

        <div x-show="open" x-cloak
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="open = false">
            <div x-show="open" x-transition
                class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-[#0f1f3d]">Informasi Detail Barang</h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <template x-if="selected">
                    <div class="p-5">
                        <div class="flex gap-4">
                            <div class="w-24 h-24 rounded bg-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                <template x-if="selected.foto">
                                    <img :src="selected.foto" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!selected.foto">
                                    <x-icon name="image" class="w-7 h-7 text-gray-300" />
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#0f1f3d]" x-text="selected.nama"></p>

                                <p class="text-xs text-gray-400 mt-2">Jumlah Stok Saat Ini :</p>
                                <p class="font-bold text-[#0f1f3d]" x-text="selected.stok + ' ' + selected.satuan"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div class="bg-gray-50 rounded px-3 py-2">
                                <p class="text-xs text-gray-400">Kategori</p>
                                <p class="text-sm font-semibold text-[#0f1f3d]" x-text="selected.kategori"></p>
                            </div>
                            <div class="bg-gray-50 rounded px-3 py-2">
                                <p class="text-xs text-gray-400">Gudang</p>
                                <p class="text-sm font-semibold text-[#0f1f3d]" x-text="selected.gudang"></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Deskripsi Lengkap</p>
                            <p class="text-sm text-gray-500 leading-relaxed" x-text="selected.deskripsi"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

@endsection
