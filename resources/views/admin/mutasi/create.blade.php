@extends('layouts.admin')

@php($pageTitle = $arah === 'keluar' ? 'Tambah Stok Barang Keluar' : 'Tambah Stok Barang Masuk')

@section('title', $pageTitle)

@section('content')

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.mutasi.index', ['kategori' => $kategori, 'gudang_id' => $gudangId]) }}"
        class="text-[#0f1f3d] hover:text-blue-700 transition translate-y-1">
            <iconify-icon icon="mdi:arrow-left" width="28" height="28"></iconify-icon>
        </a>

        <h1 class="text-2xl md:text-3xl font-bold text-[#0f1f3d]">
            {{ $pageTitle }}
        </h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl overflow-hidden">
        <div class="bg-[#0f1f3d] text-white px-6 py-3.5 -mx-6 -mt-6 mb-5 rounded-t-lg">
            <h2 class="font-semibold">{{ $pageTitle }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.mutasi.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="arah" value="{{ $arah }}">
            <input type="hidden" name="kategori" value="{{ $kategori }}">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gudang</label>
                <select name="gudang_id" id="gudang_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">Pilih Gudang</option>
                    @foreach ($gudangList as $g)
                        <option value="{{ $g->id }}" @selected(old('gudang_id', $gudangId) == $g->id)>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('gudang_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label>
                <select name="barang_id" id="barang_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                        @if ($arah === 'keluar') disabled @endif>
                    <option value="">{{ $arah === 'keluar' ? 'Pilih gudang dulu' : 'Pilih Barang' }}</option>
                    @foreach ($barangList as $b)
                        <option value="{{ $b->id }}"
                                data-satuan="{{ $b->satuan }}"
                                @if ($arah === 'keluar')
                                    data-gudang-stok='{{ json_encode($b->stok_per_gudang ?? []) }}'
                                    hidden
                                @endif
                                @selected(old('barang_id') == $b->id)>
                            {{ $b->nama_barang }} ({{ $b->satuan }})
                        </option>
                    @endforeach
                </select>
                @if ($arah === 'keluar')
                    <p class="text-xs text-gray-400 mt-1">Cuma nampilin barang yang beneran ada stoknya di gudang yang dipilih.</p>
                    <p id="stok-tersedia-hint" class="text-xs font-semibold text-blue-700 mt-1 hidden"></p>
                @endif
                @error('barang_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jam</label>
                    <input type="time" name="jam" value="{{ old('jam', now()->format('H:i')) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('jam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Barang</label>
                <input type="number" name="jumlah" id="jumlah" min="1" value="{{ old('jumlah') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('jumlah') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            @if ($arah === 'keluar')
                <script>
                    (function () {
                        var selectGudang = document.getElementById('gudang_id');
                        var selectBarang = document.getElementById('barang_id');
                        var inputJumlah  = document.getElementById('jumlah');
                        var hint         = document.getElementById('stok-tersedia-hint');

                        function stokDiGudang(opt, gudangId) {
                            if (!opt || !gudangId) return null;
                            var map = {};
                            try { map = JSON.parse(opt.getAttribute('data-gudang-stok') || '{}'); } catch (e) {}
                            return Object.prototype.hasOwnProperty.call(map, gudangId) ? parseInt(map[gudangId], 10) : null;
                        }

                        function refreshDaftarBarang() {
                            var gudangId   = selectGudang.value;
                            var opsiBarang = Array.prototype.slice.call(selectBarang.querySelectorAll('option[data-gudang-stok]'));
                            var nilaiLama  = selectBarang.value;
                            var masihValid = false;

                            selectBarang.disabled = !gudangId;

                            opsiBarang.forEach(function (opt) {
                                var stok     = stokDiGudang(opt, gudangId);
                                var tersedia = !!gudangId && stok !== null && stok > 0;
                                opt.hidden   = !tersedia;
                                if (tersedia && opt.value === nilaiLama) {
                                    masihValid = true;
                                }
                            });

                            if (!masihValid) {
                                selectBarang.value = '';
                            }

                            var placeholder = selectBarang.querySelector('option[value=""]');
                            if (placeholder) {
                                placeholder.textContent = gudangId ? 'Pilih Barang' : 'Pilih gudang dulu';
                            }

                            terapkanBatas();
                        }

                        function terapkanBatas() {
                            var gudangId = selectGudang.value;
                            var opt      = selectBarang.options[selectBarang.selectedIndex];
                            var stok     = stokDiGudang(opt, gudangId);

                            if (gudangId && opt && opt.value && stok !== null) {
                                inputJumlah.setAttribute('max', stok);
                                hint.textContent = 'Stok tersedia di gudang ini: ' + stok + ' ' + (opt.getAttribute('data-satuan') || '');
                                hint.classList.remove('hidden');
                            } else {
                                inputJumlah.removeAttribute('max');
                                hint.classList.add('hidden');
                            }
                        }

                        function clampJumlah() {
                            var max = parseInt(inputJumlah.getAttribute('max'), 10);
                            if (!isNaN(max) && inputJumlah.value !== '' && parseInt(inputJumlah.value, 10) > max) {
                                inputJumlah.value = max;
                            }
                        }

                        selectGudang.addEventListener('change', function () { refreshDaftarBarang(); clampJumlah(); });
                        selectBarang.addEventListener('change', function () { terapkanBatas(); clampJumlah(); });
                        inputJumlah.addEventListener('input', clampJumlah);

                        refreshDaftarBarang();
                    })();
                </script>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.mutasi.index', ['kategori' => $kategori, 'gudang_id' => $gudangId]) }}"
                   class="px-5 py-2.5 rounded bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">Batal</a>
                <button type="submit"
                        class="px-5 py-2.5 rounded bg-[#0f1f3d] hover:bg-[#16305c] text-white text-sm font-semibold transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
