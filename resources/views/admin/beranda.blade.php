@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-bold text-blue-900 mb-4">{{ $pageTitle }}</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded shadow p-5">
        <p class="text-sm text-gray-500">Total Jenis Barang</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalJenisBarang }}</p>
    </div>
    <div class="bg-white rounded shadow p-5">
        <p class="text-sm text-gray-500">Total Distribusi</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalDistribusi }}</p>
    </div>
    <div class="bg-white rounded shadow p-5">
        <p class="text-sm text-gray-500">Permohonan Menunggu</p>
        <p class="text-3xl font-bold text-orange-500 mt-1">{{ $permohonanMenunggu }}</p>
    </div>
</div>
@endsection