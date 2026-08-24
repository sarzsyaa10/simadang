<aside
    class="w-64 bg-[#0f1f3d] text-white flex flex-col fixed md:static inset-y-0 left-0 z-40 transform transition-transform duration-200 md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto"
         x-data="{ openStok: {{ request()->routeIs('upt.stok.*') ? 'true' : 'false' }} }">

        <a href="{{ route('upt.beranda') }}"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded {{ request()->routeIs('upt.beranda') ? 'bg-orange-500' : 'hover:bg-white/10' }}">
            <img src="{{ asset('images/icons/beranda.svg') }}" class="w-4 h-4" alt="">
            Beranda
        </a>

        <div>
            <button @click="openStok = !openStok"
                    class="w-full flex items-center justify-between px-3 py-2 rounded {{ request()->routeIs('upt.stok.*') ? 'bg-orange-500' : 'hover:bg-white/10' }}">
                <span class="flex items-center gap-2.5">
                    <img src="{{ asset('images/icons/stok.svg') }}" class="w-4 h-4" alt="">
                    Stok
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="openStok ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openStok" x-collapse x-cloak class="space-y-1 mt-1">
                <a href="{{ route('upt.stok.index', ['kategori' => 'logistik_non_permakanan']) }}"
                   @click="sidebarOpen = false"
                   class="block pl-9 pr-3 py-2 rounded {{ request()->routeIs('upt.stok.index') && request('kategori', 'logistik_non_permakanan') === 'logistik_non_permakanan' ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10' }}">
                    Logistik Non Permakanan
                </a>
                <a href="{{ route('upt.stok.index', ['kategori' => 'peralatan']) }}"
                   @click="sidebarOpen = false"
                   class="block pl-9 pr-3 py-2 rounded {{ request('kategori') === 'peralatan' ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10' }}">
                    Peralatan
                </a>
            </div>
        </div>

        <a href="{{ route('upt.permohonan') }}"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded {{ request()->routeIs('upt.permohonan') ? 'bg-orange-500' : 'hover:bg-white/10' }}">
            <img src="{{ asset('images/icons/permohonan_bantuan.svg') }}" class="w-4 h-4" alt="">
            Permohonan Bantuan
        </a>

        <a href="{{ route('upt.distribusi.pelaporan') }}"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded {{ request()->routeIs('upt.distribusi.pelaporan') ? 'bg-orange-500' : 'hover:bg-white/10' }}">
            <img src="{{ asset('images/icons/distribusi.svg') }}" class="w-4 h-4" alt="">
            Pelaporan Distribusi
        </a>
    </nav>

    <div class="px-3 py-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2.5 text-red-400 hover:text-red-300 text-sm">
                <img src="{{ asset('images/icons/keluar.svg') }}" class="w-4 h-4" alt="">
                Keluar
            </button>
        </form>
    </div>
</aside>