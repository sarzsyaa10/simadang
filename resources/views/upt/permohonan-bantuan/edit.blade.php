@extends('layouts.upt')

@section('content')
<a href="{{ route('upt.permohonan.index') }}" class="inline-flex items-center gap-1.5 text-[#0f1f3d] font-bold mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
    </svg>
    Permohonan Bantuan
</a>

@php
    $barangListJson = $barangList->map(function ($b) {
        return [
            'id' => $b->id,
            'nama_barang' => $b->nama_barang,
            'stok' => $b->stokBarang->sum('jumlah'),
            'satuan' => $b->satuan,
        ];
    });

    $existingRows = $permohonan->permohonanBantuanDetail->map(function ($d) {
        return [
            'search' => $d->barang->nama_barang ?? '',
            'jumlah' => $d->jumlah,
            'open' => false,
        ];
    })->values();
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden" x-data='permohonanForm(@json($existingRows))'>
    <div class="bg-[#0f1f3d] text-white px-5 py-3 font-semibold">
        Edit Data Permohonan Bantuan
    </div>

    <form method="POST" action="{{ route('upt.permohonan.update', $permohonan->id) }}" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $permohonan->tanggal->format('Y-m-d')) }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jam:</label>
                <input type="time" name="jam" value="{{ old('jam', substr($permohonan->jam, 0, 5)) }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Pemohon:</label>
            <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $permohonan->nama_pemohon) }}"
                   class="w-full border rounded px-3 py-2 text-sm" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Jabatan:</label>
            <input type="text" name="jabatan" value="{{ old('jabatan', $permohonan->jabatan) }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Alamat:</label>
            <textarea name="alamat" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $permohonan->alamat) }}</textarea>
        </div>

        <div class="mb-4">
            <table class="w-full text-sm">
                <thead class="bg-[#0f1f3d] text-white text-left">
                    <tr>
                        <th class="p-2">Nama Barang</th>
                        <th class="p-2">Jumlah Barang</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr class="border-b">
                            <td class="p-2 relative">
                                <div class="relative">
                                    <input type="text"
                                           x-model="row.search"
                                           @input="row.open = true"
                                           @focus="row.open = true"
                                           @click.outside="row.open = false"
                                           placeholder="Pilih Barang"
                                           autocomplete="off"
                                           class="w-full border rounded px-2 py-1.5 pr-8 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                <input type="hidden" :name="'barang[' + index + '][nama_barang]'" x-model="row.search">

                                <div x-show="row.open" x-cloak
                                     class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-48 overflow-y-auto">
                                    <template x-for="opt in filteredBarang(row.search)" :key="opt.id">
                                        <div @click="selectBarang(row, opt)"
                                            class="px-3 py-2 hover:bg-orange-100 cursor-pointer text-sm flex justify-between">
                                            <span x-text="opt.nama_barang"></span>
                                            <span class="text-gray-400 text-xs" x-text="'Stok: ' + opt.stok + ' ' + opt.satuan"></span>
                                        </div>
                                    </template>
                                    <template x-if="filteredBarang(row.search).length === 0">
                                        <div class="px-3 py-2 text-sm text-gray-400">Barang tidak ditemukan</div>
                                    </template>
                                </div>
                            </td>
                            <td class="p-2">
                                <input type="number" :name="'barang[' + index + '][jumlah]'" x-model="row.jumlah"
                                       min="1" placeholder="Jumlah" class="w-full border rounded px-2 py-1.5 text-sm" required>
                            </td>
                            <td class="p-2">
                                <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <button type="button" @click="addRow()"
                class="bg-[#0f1f3d] text-white text-sm px-4 py-2 rounded mb-6">
            + Tambah Barang
        </button>

        <div class="flex gap-3">
            <a href="{{ route('upt.permohonan.index') }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-[#0f1f3d] hover:bg-[#16305e] text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>

<script>
function permohonanForm(existingRows) {
    return {
        barangList: @json($barangListJson),
        rows: existingRows.length > 0 ? existingRows : [
            { search: '', jumlah: '', open: false },
        ],
        addRow() {
            this.rows.push({ search: '', jumlah: '', open: false });
        },
        removeRow(index) {
            if (this.rows.length > 1) this.rows.splice(index, 1);
        },
        filteredBarang(search) {
            if (!search) return this.barangList;
            return this.barangList.filter(b =>
                b.nama_barang.toLowerCase().includes(search.toLowerCase())
            );
        },
        selectBarang(row, opt) {
            row.search = opt.nama_barang;
            row.open = false;
        }
    }
}
</script>
@endsection