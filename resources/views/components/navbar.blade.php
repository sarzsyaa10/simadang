<header class="bg-white shadow-sm px-4 md:px-6 py-3 flex justify-between items-center relative z-20">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>

        <img src="{{ asset('images/asset/bpbd.png') }}" alt="Logo BPBD" class="w-8 h-8 rounded-full">
        <span class="font-bold text-blue-900 text-lg">SIMADANG</span>
    </div>

    <div class="relative" x-data="{ openProfile: false }" @click.outside="openProfile = false">
        <button @click="openProfile = !openProfile" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
            <div class="text-right text-sm leading-tight">
                <div class="font-medium text-gray-700">{{ auth()->user()->nama }}</div>
                <div class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <iconify-icon icon="gg:profile" width="32" height="32"></iconify-icon>
        </button>

        <div x-show="openProfile" x-cloak x-transition
             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-30">
            <a href="{{ route('profile') }}"
               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <iconify-icon icon="gg:profile" width="18" height="18"></iconify-icon>
                Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
                    <img src="{{ asset('images/icons/keluar.svg') }}" class="w-[18px] h-[18px]" alt="" style="filter: invert(45%) sepia(80%) saturate(2000%) hue-rotate(330deg);">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>