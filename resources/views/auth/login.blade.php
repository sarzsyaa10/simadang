<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMADANG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center gap-3">
        <img src="{{ asset('images/asset/bpbd.png') }}" alt="Logo BPBD" class="w-9 h-9 object-contain">
        <span class="text-lg font-bold text-blue-900">SIMADANG</span>
    </header>

    <div class="min-h-[calc(100vh-57px)] flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow p-8">
                <div class="flex flex-col items-center mb-6">
                    <img src="{{ asset('images/asset/bpbd.png') }}" alt="Logo BPBD" class="w-24 h-24 object-contain mb-4">
                    <h1 class="text-xl font-bold text-gray-800">Masuk ke Sistem</h1>
                    <p class="text-sm text-gray-500 text-center mt-1">
                        Sistem Manajemen Gudang dan Logistik BPBD Kab. Cilacap
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <iconify-icon icon="mdi:account-outline" width="18" height="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
                            <input type="text" name="username" value="{{ old('username') }}"
                                   class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                   placeholder="Masukan username anda" required autofocus>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <iconify-icon icon="mdi:lock-outline" width="18" height="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></iconify-icon>
                            <input type="password" name="password"
                                   class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                   placeholder="Masukan password anda" required>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-400 hover:bg-orange-500 text-gray-900 font-semibold py-2.5 rounded-md transition inline-flex items-center justify-center gap-2">
                        Masuk
                        <iconify-icon icon="mdi:arrow-right" width="18" height="18"></iconify-icon>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                        <iconify-icon icon="mdi:arrow-left" width="14" height="14"></iconify-icon>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</body>
</html>