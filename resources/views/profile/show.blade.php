@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.upt')

@php($pageTitle = 'Profil')

@section('content')
<h1 class="text-2xl font-bold text-blue-900 mb-4">Profil</h1>

<div class="bg-white rounded shadow overflow-hidden">
    <div class="px-6 py-4">
        <h2 class="font-bold text-gray-700 mb-4">Data Akun {{ $user->isAdmin() ? 'Admin' : 'UPT' }}</h2>

        <table class="w-full text-sm">
            <tbody>
                <tr class="bg-gray-200/60">
                    <td class="py-3 px-3 font-semibold text-gray-700 w-40">Nama</td>
                    <td class="py-3 px-3 text-gray-600">{{ $user->nama }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-3 font-semibold text-gray-700">Username</td>
                    <td class="py-3 px-3 text-gray-600">{{ $user->username }}</td>
                </tr>
                <tr class="bg-gray-200/60">
                    <td class="py-3 px-3 font-semibold text-gray-700">Alamat</td>
                    <td class="py-3 px-3 text-gray-600">{{ $user->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-3 px-3 font-semibold text-gray-700">Jabatan</td>
                    <td class="py-3 px-3 text-gray-600">{{ $user->jabatan ?? '-' }}</td>
                </tr>
                <tr class="bg-gray-200/60">
                    <td class="py-3 px-3 font-semibold text-gray-700">Role</td>
                    <td class="py-3 px-3 text-gray-600">{{ ucfirst($user->role) }}</td>
                </tr>
                @if($user->gudang)
                <tr>
                    <td class="py-3 px-3 font-semibold text-gray-700">Gudang</td>
                    <td class="py-3 px-3 text-gray-600">{{ $user->gudang->nama_gudang }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="flex justify-end mt-4">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-1.5 bg-[#0f1f3d] hover:bg-[#16305e] text-white text-sm font-semibold px-5 py-2.5 rounded">
                Update Identitas dan Update Password
                <iconify-icon icon="mdi:arrow-right" width="16" height="16"></iconify-icon>
            </a>
        </div>
    </div>
</div>
@endsection
