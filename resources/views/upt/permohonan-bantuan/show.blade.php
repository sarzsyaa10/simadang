@extends('layouts.upt')

@section('content')
<a href="{{ route('upt.permohonan.index') }}" class="inline-flex items-center gap-1 text-blue-900 font-bold mb-4">
    ← Permohonan Bantuan
</a>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-5 py-3 font-semibold">
        Detail Permohonan Bantuan
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm">
            <div><span class="text-gray-500">Tanggal:</span> <p class="font-medium">{{ $permohonan->tanggal->format('d/m/Y') }} - {{ substr($permohonan->jam, 0, 5) }}</p></div>
            <div><span class="text-gray-500">Nama Pemohon:</span> <p class="font-medium">{{ $permohonan->nama_pemohon }}</p></div>
            <div><span class="text-gray-500">Jabatan:</span> <p class="font-medium">{{ $permohonan->jabatan ?? '-' }}</p></div>
            <div><span class="text-gray-500">Alamat:</span> <p class="font-medium">{{ $permohonan->alamat ?? '-' }}</p></div>
        </div>

        <table class="w-full text-sm rounded-lg overflow-hidden border border-gray-100">
            <thead class="bg-[#0f1f3d] text-white text-left">
                <tr>
                    <th class="p-3">Nama Barang</th>
                    <th class="p-3">Jumlah</th>
                    <th class="p-3">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permohonan->permohonanBantuanDetail as $d)
                    <tr class="border-b">
                        <td class="p-3">{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td class="p-3">{{ $d->jumlah }}</td>
                        <td class="p-3">{{ $d->barang->satuan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('upt.permohonan.index') }}"
           class="inline-block mt-6 bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded">Kembali</a>
    </div>
</div>
@endsection