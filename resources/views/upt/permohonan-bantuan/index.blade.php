@extends('layouts.upt')

@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold text-blue-900">{{ $pageTitle }}</h1>
    <div class="flex gap-2">
        <button onclick="location.reload()" 
           class="inline-flex items-center gap-2 bg-white hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-300">
            <iconify-icon icon="mdi:reload" width="16" height="16"></iconify-icon>
            Reload
        </button>
        <a href="{{ route('upt.permohonan.create') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <iconify-icon icon="mdi:plus" width="16" height="16"></iconify-icon>
            Tambah Data
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
        <form method="GET" class="flex items-center gap-2 text-sm">
            @if($search)
                <input type="hidden" name="search" value="{{ $search }}">
            @endif
            <span>Show</span>
            <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                @foreach ([5, 10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }}</option>
                @endforeach
            </select>
            <span>entries</span>
        </form>

        <form method="GET" id="searchForm" class="relative">
            @if($perPage != 10)
                <input type="hidden" name="per_page" value="{{ $perPage }}">
            @endif
            <iconify-icon icon="mdi:magnify" width="18" height="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search" id="searchInput" autocomplete="off"
                   class="border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm w-64">
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-5">
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-[#0f1f3d] text-white text-left">
                <tr>
                    @php
                        $headers = [
                            ['label' => 'Tanggal', 'key' => 'tanggal'],
                            ['label' => 'Pemohon', 'key' => 'nama_pemohon'],
                            ['label' => 'Jabatan', 'key' => 'jabatan'],
                            ['label' => 'Alamat', 'key' => 'alamat'],
                            ['label' => 'Barang', 'key' => null],
                            ['label' => 'Status', 'key' => 'status'],
                            ['label' => 'Tindakan', 'key' => null],
                        ];
                    @endphp
                    @foreach($headers as $h)
                        <th class="p-3">
                            @if ($h['key'])
                                @php
                                    $newDirection = ($sort === $h['key'] && $direction === 'asc') ? 'desc' : 'asc';
                                    $isActive = $sort === $h['key'];
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $h['key'], 'direction' => $newDirection]) }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide hover:text-orange-300">
                                    {{ $h['label'] }}
                                    <iconify-icon icon="mdi:unfold-more-horizontal" width="14" height="14" class="{{ $isActive ? 'text-orange-400' : 'opacity-70' }}"></iconify-icon>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide">
                                    {{ $h['label'] }}
                                </span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="permohonan-table-body">
                @forelse($permohonan as $item)
                    <tr class="border-b border-gray-100">
                        <td class="p-2">{{ $item->tanggal->format('d/m/Y') }} - {{ substr($item->jam, 0, 5) }}</td>
                        <td class="p-2">{{ $item->nama_pemohon }}</td>
                        <td class="p-2">{{ $item->jabatan }}</td>
                        <td class="p-2">{{ $item->alamat }}</td>
                        <td class="p-2">
                            <a href="{{ route('upt.permohonan.show', $item->id) }}" class="text-blue-600 hover:underline">
                                {{ $item->permohonanBantuanDetail->count() }} jenis barang
                            </a>
                        </td>
                        <td class="p-2">
                            @php
                                $statusLabel = ['pending' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'sebagian' => 'Sebagian Disetujui'];
                                $statusColor = ['pending' => 'bg-orange-100 text-orange-600', 'disetujui' => 'bg-green-100 text-green-600', 'ditolak' => 'bg-red-100 text-red-600', 'sebagian' => 'bg-blue-100 text-blue-600'];
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $statusColor[$item->status] }}">
                                {{ $statusLabel[$item->status] }}
                            </span>
                        </td>
                        <td class="p-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('upt.permohonan.show', $item->id) }}" title="Detail"
                                   class="w-8 h-8 flex items-center justify-center rounded-md border border-green-500 text-green-600 hover:bg-green-50">
                                    <iconify-icon icon="mdi:eye-outline" width="16" height="16"></iconify-icon>
                                </a>
                                @if($item->status === 'pending')
                                    <a href="{{ route('upt.permohonan.edit', $item->id) }}" title="Edit"
                                       class="w-8 h-8 flex items-center justify-center rounded-md border border-blue-400 text-blue-600 hover:bg-blue-50">
                                        <iconify-icon icon="mdi:pencil-outline" width="16" height="16"></iconify-icon>
                                    </a>
                                    <form method="POST" action="{{ route('upt.permohonan.destroy', $item->id) }}"
                                          onsubmit="return confirm('Yakin hapus permohonan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="w-8 h-8 flex items-center justify-center rounded-md border border-red-500 text-red-600 hover:bg-red-50">
                                            <iconify-icon icon="mdi:trash-can-outline" width="16" height="16"></iconify-icon>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-3 mt-4 text-sm text-gray-500">
        <p class="text-left">
            @if ($permohonan->total() > 0)
                Showing {{ $permohonan->firstItem() }} to {{ $permohonan->lastItem() }} of {{ $permohonan->total() }} entries
            @else
                Showing 0 entries
            @endif
        </p>

        <div class="flex justify-center">
            <x-pagination :paginator="$permohonan" />
        </div>

        <div></div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const tableBody = document.getElementById('permohonan-table-body');

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

                const newBody = doc.querySelector('#permohonan-table-body');

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