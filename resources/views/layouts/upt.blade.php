<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIMADANG - UPT')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
   <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false }">

<div class="flex flex-col min-h-screen">
    <x-navbar :title="$pageTitle ?? 'SIMADANG'" />

    <div class="flex flex-1 relative">
        <x-sidebar-upt />

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <main class="flex-1 p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded px-4 py-2">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="text-center text-xs text-gray-400 py-4 border-t bg-white">
                © 2026 BPBD Kabupaten Cilacap - Bidang Kedaruratan & Logistik
            </footer>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>