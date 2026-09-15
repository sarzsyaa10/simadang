@extends('layouts.admin')

@php($pageTitle = 'Distribusi')

@section('content')
<div class="flex items-center gap-2 mb-1">
    <a href="{{ route('admin.distribusi') }}" class="text-blue-900">
        <iconify-icon icon="mdi:arrow-left" width="26" height="26"></iconify-icon>
    </a>
    <h1 class="text-2xl font-bold text-blue-900">Distribusi</h1>
</div>
<p class="text-gray-500 text-sm mb-4">Informasi pendistribusian bantuan barang logistik dan peralatan kebencanaan.</p>

@if ($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-2">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if ($permohonan)
    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded px-4 py-3">
        Barang di bawah ini otomatis diisi dari <strong>Permohonan Bantuan #{{ $permohonan->id }}</strong>
        ({{ $permohonan->nama_pemohon }} - {{ $permohonan->alamat }}) yang sudah disetujui.
        Kamu tinggal pilih gudang asal tiap barang.
    </div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden"
     x-data="{
        stok: {{ Illuminate\Support\Js::from($stokMap) }},
        items: {{ Illuminate\Support\Js::from(
            $prefillItems->isNotEmpty()
                ? $prefillItems->map(fn ($i) => [
                    'barang_id' => $i['barang_id'],
                    'nama_barang' => $i['nama_barang'],
                    'satuan' => $i['satuan'],
                    'gudang_id' => '',
                    'jumlah' => $i['jumlah'],
                    'max' => $i['jumlah'],
                    'sumber' => '',
                    'keterangan' => '',
                    'locked' => true,
                ])->values()
                : [['barang_id' => '', 'nama_barang' => '', 'satuan' => '', 'gudang_id' => '', 'jumlah' => '', 'max' => null, 'sumber' => '', 'keterangan' => '', 'locked' => false]]
        ) }},
        addItem() {
            this.items.push({ barang_id: '', nama_barang: '', satuan: '', gudang_id: '', jumlah: '', max: null, sumber: '', keterangan: '', locked: false });
        },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1) },
        barangOptions(gudangId) { return this.stok[gudangId] || []; },
        stokTersedia(item) {
            const list = this.stok[item.gudang_id] || [];
            const found = list.find(b => String(b.barang_id) === String(item.barang_id));
            return found ? found.jumlah : null;
        },
        maxJumlah(item) {
            const tersedia = this.stokTersedia(item);
            if (item.locked) {
                return tersedia !== null ? Math.min(item.max, tersedia) : item.max;
            }
            return tersedia !== null ? tersedia : null;
        }
     }">

    <div class="bg-[#0f1f3d] text-white px-6 py-3.5 flex items-center justify-between">
        <h2 class="font-semibold">Tambah Data Surat Distribusi Barang</h2>
    </div>

    <form method="POST" action="{{ route('admin.distribusi.store') }}">
        @csrf
        @if ($permohonan)
            <input type="hidden" name="permohonan_bantuan_id" value="{{ $permohonan->id }}">
        @endif

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Jam:</label>
                <input type="time" name="jam" value="{{ old('jam', now()->format('H:i')) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Nomor Surat:</label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" placeholder="Nomor Surat"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Kendaraan:</label>
                <input type="text" name="kendaraan" value="{{ old('kendaraan') }}" placeholder="Kendaraan"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>

            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Petugas:</label>
                <input type="text" name="petugas" value="{{ old('petugas') }}" placeholder="Petugas"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Tingkat Posko:</label>
                <input type="text" name="tingkat_posko" value="{{ old('tingkat_posko') }}" placeholder="Contoh: Kecamatan, Kelurahan, - "
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">
            </div>
            <div>
                <label class="block font-semibold text-gray-800 mb-1.5">Gudang Tujuan (UPT):</label>
                @if ($gudangTujuanId)
                    @php($gudangTujuanNama = $gudangUptList->firstWhere('id', $gudangTujuanId)?->nama_gudang)
                    <input type="hidden" name="gudang_tujuan_id" value="{{ $gudangTujuanId }}">
                    <div class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2.5 text-sm text-gray-700">
                        {{ $gudangTujuanNama ?? '-' }}
                    </div>
                @else
                    <select name="gudang_tujuan_id" class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900">
                        <option value="">Pilih Gudang UPT Tujuan</option>
                        @foreach($gudangUptList as $g)
                            <option value="{{ $g->id }}" {{ old('gudang_tujuan_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Wajib dipilih biar UPT bisa lapor distribusi ini nanti.</p>
                @endif
            </div>
            <div class="sm:col-span-2">
                <label class="block font-semibold text-gray-800 mb-1.5">Tujuan:</label>
                <textarea name="tujuan" rows="3" placeholder="Tujuan"
                    class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">{{ old('tujuan', $permohonan->alamat ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-[#0f1f3d] text-white px-6 py-3 text-xs font-semibold uppercase tracking-wide">
            <div class="grid grid-cols-5 gap-6">
                <span>Nama Barang</span>
                <span>Gudang</span>
                <span>Jumlah Barang</span>
                <span>Sumber</span>
                <span>Keterangan</span>
            </div>
        </div>

        <div class="px-6 pb-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_1fr_1fr_1fr_auto] gap-4 items-start py-4 border-b border-gray-100">

                    {{-- Barang: kalau locked (dari permohonan), tampil sebagai teks tetap; kalau manual, dropdown filter sesuai gudang --}}
                    <div>
                        <template x-if="item.locked">
                            <div>
                                <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.barang_id">
                                <div class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2 text-sm text-gray-700" x-text="item.nama_barang"></div>
                            </div>
                        </template>
                        <template x-if="!item.locked">
                            <select :name="`items[${index}][barang_id]`" x-model="item.barang_id"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                                <option value="">Pilih Barang</option>
                                <template x-for="b in barangOptions(item.gudang_id)" :key="b.barang_id">
                                    <option :value="b.barang_id" x-text="`${b.nama_barang} (stok: ${b.jumlah} ${b.satuan})`"></option>
                                </template>
                            </select>
                            <p x-show="item.gudang_id && barangOptions(item.gudang_id).length === 0" class="text-xs text-red-500 mt-1">
                                Tidak ada stok tersedia di gudang ini.
                            </p>
                        </template>
                    </div>

                    <select :name="`items[${index}][gudang_id]`" x-model="item.gudang_id"
                            @change="if (!item.locked) item.barang_id = ''"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                        <option value="">Pilih Gudang</option>
                        @foreach($gudangList as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>

                    <div>
                        <input type="number" min="1" placeholder="Jumlah" :name="`items[${index}][jumlah]`" x-model="item.jumlah"
                            :max="maxJumlah(item)"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900" required>
                        <p x-show="item.gudang_id && item.barang_id" class="text-xs text-gray-400 mt-1">
                            Stok tersedia: <span x-text="stokTersedia(item) ?? 0"></span>
                            <template x-if="item.locked"><span>/ diminta: <span x-text="item.max"></span></span></template>
                        </p>
                    </div>

                    <input type="text" placeholder="Sumber (BNPB, APBD, dst)" :name="`items[${index}][sumber]`" x-model="item.sumber"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">

                    <input type="text" placeholder="Keterangan" :name="`items[${index}][keterangan]`" x-model="item.keterangan"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-900">

                    <button type="button" @click="removeItem(index)" class="text-red-500 justify-self-center mt-2">
                        <iconify-icon icon="mdi:trash-can-outline" width="18" height="18"></iconify-icon>
                    </button>
                </div>
            </template>

            <button type="button" @click="addItem()" class="mt-4 mb-8 inline-flex items-center gap-2 bg-[#0f1f3d] hover:bg-[#16295a] text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
                Tambah Barang
            </button>
        </div>

        <div class="grid grid-cols-2 gap-3 p-4 border-t border-gray-100">
            <a href="{{ route('admin.distribusi') }}"
               class="text-center bg-red-500 hover:bg-red-600 text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Batal</a>
            <button type="submit" class="bg-[#0f1f3d] hover:bg-[#16295a] text-white font-semibold uppercase text-sm py-3.5 tracking-wide rounded-lg">Submit</button>
        </div>
    </form>
</div>
@endsection