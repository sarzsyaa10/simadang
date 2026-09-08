@extends('layouts.admin')

<?php $pageTitle = 'Verifikasi Permohonan Bantuan'; ?>

@section('content')
<!-- Tombol Kembali Atas -->
<a href="{{ route('admin.permohonan') }}" class="inline-flex items-center gap-1.5 text-[#0f1f3d] font-bold mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6" />
    </svg>
    Permohonan Bantuan
</a>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white px-5 py-3 font-semibold flex justify-between items-center">
        <span>Verifikasi Permohonan Bantuan</span>
        <?php
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
        ?>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
    </div>

    <div class="p-6">
        <!-- Grid Informasi Permohonan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal & Jam:</label>
                <input type="text" value="{{ \Carbon\Carbon::parse($permohonan->tanggal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($permohonan->jam)->format('H:i') }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nama Pemohon:</label>
                <input type="text" value="{{ $permohonan->nama_pemohon }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jabatan:</label>
                <input type="text" value="{{ $permohonan->jabatan }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Alamat:</label>
                <input type="text" value="{{ $permohonan->alamat }}" readonly
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100 text-gray-600">
            </div>
        </div>

        <!-- Tabel Daftar Barang -->
        <div class="mb-6 overflow-x-auto">
            <label class="block text-sm font-medium mb-2">Daftar Barang:</label>
            <table class="w-full text-sm">
                <thead class="bg-[#0f1f3d] text-white text-left">
                    <tr>
                        <th class="p-2">Nama Barang</th>
                        <th class="p-2">Jumlah</th>
                        <th class="p-2">Satuan</th>
                        <th class="p-2">Status</th>
                        <th class="p-2 w-64">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonan->permohonanBantuanDetail as $detail)
                        <tr class="border-b align-top">
                            <td class="p-2 text-gray-700">{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="p-2 text-gray-700">{{ $detail->jumlah }}</td>
                            <td class="p-2 text-gray-700">{{ $detail->barang->satuan ?? '-' }}</td>
                            <td class="p-2">
                                @php
                                    $itemBadge = match($detail->status) {
                                        'disetujui' => 'bg-green-100 text-green-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                        default => 'bg-orange-100 text-orange-600',
                                    };
                                    $itemLabel = match($detail->status) {
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => 'Diajukan',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $itemBadge }}">{{ $itemLabel }}</span>
                                @if ($detail->status === 'ditolak' && $detail->keterangan)
                                    <p class="text-xs text-gray-500 mt-1">{{ $detail->keterangan }}</p>
                                @endif
                            </td>
                            <td class="p-2">
                                @if ($detail->status === 'pending')
                                    <div class="flex flex-col gap-2">
                                        <!-- Form khusus Penolakan (menyimpan alasan) -->
                                        <form method="POST" action="{{ route('admin.permohonan.item.tolak', $detail->id) }}" id="form-tolak-{{ $detail->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="keterangan" placeholder="Alasan (opsional)"
                                                   class="w-full border rounded px-2 py-1.5 text-xs">
                                        </form>

                                        <!-- Baris Tombol Setuju & Tolak Bersampingan -->
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('admin.permohonan.item.setuju', $detail->id) }}" class="flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 rounded transition text-center">
                                                    Setuju
                                                </button>
                                            </form>

                                            <button type="submit" 
                                                    form="form-tolak-{{ $detail->id }}" 
                                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-1.5 rounded transition text-center">
                                                Tolak
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Sudah diputuskan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-4 text-center text-gray-400">Belum ada barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tombol Batal / Kembali Bawah -->
        <a href="{{ route('admin.permohonan') }}"
           class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded transition">
            Kembali
        </a>
    </div>
</div>
@endsection