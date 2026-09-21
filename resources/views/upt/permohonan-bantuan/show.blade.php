@extends('layouts.upt')

@section('content')
<a href="{{ route('upt.permohonan.index') }}" class="inline-flex items-center gap-1.5 text-[#0f1f3d] font-bold mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
    </svg>
    Permohonan Bantuan
</a>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-6 py-4 font-semibold flex justify-between items-center">
        <span>Detail Permohonan Bantuan</span>

        @php
            $badge = match($permohonan->status) {
                'disetujui' => 'bg-green-100 text-green-700',
                'ditolak' => 'bg-red-100 text-red-700',
                'sebagian' => 'bg-blue-100 text-blue-700',
                default => 'bg-orange-100 text-orange-600',
            };
            $label = match($permohonan->status) {
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'sebagian' => 'Disetujui Sebagian',
                default => 'Diajukan',
            };
        @endphp
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <span class="text-gray-500">Tanggal</span>
                <p class="font-medium text-gray-800">{{ $permohonan->tanggal->format('d/m/Y') }} - {{ substr($permohonan->jam, 0, 5) }}</p>
            </div>
            <div>
                <span class="text-gray-500">Nama Pemohon</span>
                <p class="font-medium text-gray-800">{{ $permohonan->nama_pemohon }}</p>
            </div>
            <div>
                <span class="text-gray-500">Jabatan</span>
                <p class="font-medium text-gray-800">{{ $permohonan->jabatan ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Alamat</span>
                <p class="font-medium text-gray-800">{{ $permohonan->alamat ?? '-' }}</p>
            </div>
        </div>

        <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Barang</label>
        <div class="border border-gray-100 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[#0f1f3d] text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-wide text-xs">Nama Barang</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-wide text-xs">Jumlah</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-wide text-xs">Satuan</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-wide text-xs">Status</th>
                        <th class="px-4 py-3 text-left font-bold uppercase tracking-wide text-xs">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($permohonan->permohonanBantuanDetail as $d)
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $d->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $d->jumlah }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $d->barang->satuan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $itemBadge = match($d->status) {
                                        'disetujui' => 'bg-green-100 text-green-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                        default => 'bg-orange-100 text-orange-600',
                                    };
                                    $itemLabel = match($d->status) {
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => 'Diajukan',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $itemBadge }}">{{ $itemLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if ($d->status === 'ditolak' && $d->keterangan)
                                    {{ $d->keterangan }}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('upt.permohonan.index') }}"
           class="inline-flex items-center gap-1.5 mt-6 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection