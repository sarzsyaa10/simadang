@extends('layouts.admin')

@php($pageTitle = 'Data User')

@section('content')
<a href="{{ route('admin.data-user.index') }}" class="inline-flex items-center gap-1.5 text-blue-900 font-bold mb-4">
    <iconify-icon icon="mdi:arrow-left" width="18" height="18"></iconify-icon>
    Data User
</a>

<div class="bg-white rounded shadow overflow-hidden">
    <div class="bg-[#0f1f3d] text-white text-sm font-semibold px-5 py-3">Tambah Data User</div>

    <form method="POST" action="{{ route('admin.data-user.store') }}" class="p-6">
        @csrf

        <div class="grid sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                       class="w-full border rounded px-3 py-2 text-sm @error('nama') border-red-400 @enderror" required>
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       class="w-full border rounded px-3 py-2 text-sm @error('username') border-red-400 @enderror" required>
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Jabatan</label>
            <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 text-sm @error('password') border-red-400 @enderror" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2 text-sm" required>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="upt" {{ old('role') === 'upt' ? 'selected' : '' }}>UPT</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">Penautan ke gudang tertentu diatur lewat menu Data Gudang.</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.data-user.index') }}"
               class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
            <button type="submit" class="flex-1 bg-[#0f1f3d] hover:bg-[#16305e] text-white py-2 rounded text-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection