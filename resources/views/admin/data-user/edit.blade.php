@extends('layouts.admin')

@php($pageTitle = 'Data User')

@section('content')
<a href="{{ route('admin.data-user.index') }}" class="inline-flex items-center gap-1.5 text-blue-900 font-bold mb-4">
    <iconify-icon icon="mdi:arrow-left" width="18" height="18"></iconify-icon>
    Edit Data User
</a>

<form method="POST" action="{{ route('admin.data-user.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="bg-white rounded shadow overflow-hidden mb-5">
        <div class="bg-[#0f1f3d] text-white text-sm font-semibold px-5 py-3">Ubah Data User</div>

        <div class="p-6">
            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                           class="w-full border rounded px-3 py-2 text-sm @error('nama') border-red-400 @enderror" required>
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                           class="w-full border rounded px-3 py-2 text-sm @error('username') border-red-400 @enderror" required>
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $user->alamat) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2 text-sm" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="upt" {{ old('role', $user->role) === 'upt' ? 'selected' : '' }}>UPT</option>
                </select>
                @if($user->gudang)
                    <p class="text-xs text-gray-400 mt-1">Saat ini terhubung ke gudang: <strong>{{ $user->gudang->nama_gudang }}</strong> (ubah lewat menu Data Gudang).</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden mb-5">
        <div class="bg-[#0f1f3d] text-white text-sm font-semibold px-5 py-3">Ubah Password User</div>

        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2 text-sm @error('password') border-red-400 @enderror"
                       placeholder="Kosongkan jika tidak ingin mengubah password">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.data-user.index') }}"
           class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white py-2 rounded text-sm">Batal</a>
        <button type="submit" class="flex-1 bg-[#0f1f3d] hover:bg-[#16305e] text-white py-2 rounded text-sm">Update</button>
    </div>
</form>
@endsection