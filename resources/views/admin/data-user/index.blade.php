@extends('layouts.admin')

@php($pageTitle = 'Data User')

@section('content')
<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
    <div>
        <h1 class="text-xl font-bold text-blue-900">Data User</h1>
        <p class="text-sm text-gray-500">Kelola akun admin dan operator gudang (UPT).</p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.data-user.index') }}"
           class="inline-flex items-center gap-1.5 bg-white border text-gray-600 text-sm px-4 py-2 rounded hover:bg-gray-50">
            <iconify-icon icon="mdi:refresh" width="16" height="16"></iconify-icon>
            Reset
        </a>
        <a href="{{ route('admin.data-user.create') }}"
           class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">
            <iconify-icon icon="mdi:account-plus-outline" width="16" height="16"></iconify-icon>
            Tambah User
        </a>
    </div>
</div>

<div class="bg-white rounded shadow p-5">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-2">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
        <select name="role" class="border rounded px-3 py-2 text-sm sm:w-48" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="upt" {{ $role === 'upt' ? 'selected' : '' }}>UPT</option>
        </select>
        <div class="flex gap-2 flex-1">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau username..."
                   class="border rounded px-3 py-2 text-sm flex-1">
            <button class="inline-flex items-center gap-1.5 bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded">
                <iconify-icon icon="mdi:magnify" width="16" height="16"></iconify-icon>
                Cari
            </button>
        </div>
    </form>

    <div class="rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#0f1f3d] text-white text-left">
                <tr>
                    <th class="p-2.5">Nama</th>
                    <th class="p-2.5">Username</th>
                    <th class="p-2.5">Jabatan</th>
                    <th class="p-2.5">Gudang</th>
                    <th class="p-2.5">Role</th>
                    <th class="p-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userList as $item)
                    <tr class="border-b">
                        <td class="p-2.5 font-medium text-gray-700">{{ $item->nama }}</td>
                        <td class="p-2.5">{{ $item->username }}</td>
                        <td class="p-2.5">{{ $item->jabatan ?? '-' }}</td>
                        <td class="p-2.5">{{ $item->gudang->nama_gudang ?? '-' }}</td>
                        <td class="p-2.5">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $item->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ strtoupper($item->role) }}
                            </span>
                        </td>
                        <td class="p-2.5">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.data-user.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <iconify-icon icon="mdi:pencil-outline" width="18" height="18"></iconify-icon>
                                </a>
                                <form method="POST" action="{{ route('admin.data-user.destroy', $item->id) }}"
                                      onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <iconify-icon icon="mdi:trash-can-outline" width="18" height="18"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $userList->links() }}</div>
</div>
@endsection