@extends('layouts.upt')

@section('content')
<a href="{{ route('upt.distribusi.pelaporan.index') }}" class="inline-flex items-center gap-1.5 text-[#0f1f3d] font-bold mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
    </svg>
    Pelaporan Distribusi
</a>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-5 py-3 font-semibold">
        Edit Data Pelaporan Distribusi
    </div>

    <form method="POST" action="{{ route('upt.distribusi.pelaporan.update', $pelaporan->id) }}" enctype="multipart/form-data" class="p-6" x-data="{ scanName: '', fotoName: '' }">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Pilih Surat Distribusi:</label>
            <select name="surat_distribusi_id" class="w-full border rounded px-3 py-2 text-sm" required>
                @foreach($suratList as $s)
                    <option value="{{ $s->id }}" @selected(old('surat_distribusi_id', $pelaporan->surat_distribusi_id) == $s->id)>
                        {{ $s->nomor_surat }} - {{ $s->tanggal->format('d/m/Y') }}
                        @if($s->permohonanBantuan)
                            (Permohonan: {{ $s->permohonanBantuan->nama_pemohon }})
                        @else
                            ({{ $s->tujuan }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nama UPT</label>
                <input type="text" value="{{ $pelaporan->nama_upt }}"
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-600" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal</label>
                <input type="date" name="tanggal_lapor" value="{{ old('tanggal_lapor', $pelaporan->tanggal_lapor->format('Y-m-d')) }}"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Upload Scan Surat</label>
            @if($pelaporan->scan_surat)
                <p class="text-xs text-gray-500 mb-2">
                    File saat ini: <a href="{{ asset('storage/' . $pelaporan->scan_surat) }}" target="_blank" class="text-blue-600 underline">Lihat file</a>
                </p>
            @endif
            <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg py-8 cursor-pointer hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span class="text-sm font-medium text-gray-700" x-text="scanName || 'Klik untuk mengganti file (opsional)'"></span>
                <span class="text-xs text-gray-400 mt-1">Format yang didukung: PDF, JPG, PNG (Maksimal 5MB per file)</span>
                <input type="file" name="scan_surat" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                       @change="scanName = $event.target.files[0]?.name">
            </label>
            @error('scan_surat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Upload Bukti Foto Barang</label>
            @if($pelaporan->bukti_foto)
                <p class="text-xs text-gray-500 mb-2">
                    File saat ini: <a href="{{ asset('storage/' . $pelaporan->bukti_foto) }}" target="_blank" class="text-blue-600 underline">Lihat file</a>
                </p>
            @endif
            <label class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg py-8 cursor-pointer hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span class="text-sm font-medium text-gray-700" x-text="fotoName || 'Klik untuk mengganti file (opsional)'"></span>
                <span class="text-xs text-gray-400 mt-1">Format yang didukung: JPG, PNG (Maksimal 5MB per file)</span>
                <input type="file" name="bukti_foto" accept=".jpg,.jpeg,.png" class="hidden"
                       @change="fotoName = $event.target.files[0]?.name">
            </label>
            @error('bukti_foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Koordinat</label>
            <input type="text" name="koordinat" value="{{ old('koordinat', $pelaporan->koordinat) }}"
                   placeholder="Contoh: -7.734729891206802, 109.00620751719286"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Keterangan"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('keterangan', $pelaporan->keterangan) }}</textarea>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('upt.distribusi.pelaporan.index') }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-[#0f1f3d] hover:bg-[#16305e] text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection