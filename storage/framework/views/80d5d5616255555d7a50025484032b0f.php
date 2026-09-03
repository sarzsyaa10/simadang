<aside
    class="w-64 bg-[#0f1f3d] text-white flex flex-col fixed md:static inset-y-0 left-0 z-40 transform transition-transform duration-200 md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto"
        x-data="{
            openStok: <?php echo e(request()->routeIs('admin.stok.*') ? 'true' : 'false'); ?>,
            openDistribusi: <?php echo e(request()->routeIs('admin.distribusi*') ? 'true' : 'false'); ?>

        }">

        <a href="<?php echo e(route('landing')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded hover:bg-white/10">
            <img src="<?php echo e(asset('images/icons/beranda.svg')); ?>" class="w-4 h-4" alt="">
            Beranda
        </a>

        <div>
            <button @click="openStok = !openStok"
                    class="w-full flex items-center justify-between px-3 py-2 rounded <?php echo e(request()->routeIs('admin.stok.*') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
                <span class="flex items-center gap-2.5">
                    <img src="<?php echo e(asset('images/icons/stok.svg')); ?>" class="w-4 h-4" alt="">
                    Stok
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="openStok ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openStok" x-collapse x-cloak class="space-y-1 mt-1">
                <a href="<?php echo e(route('admin.stok.index', ['kategori' => 'logistik_non_permakanan'])); ?>"
                   @click="sidebarOpen = false"
                   class="block pl-9 pr-3 py-2 rounded <?php echo e(request()->routeIs('admin.stok.index') && (request('kategori', 'logistik_non_permakanan') === 'logistik_non_permakanan') ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10'); ?>">
                    Logistik Non Permakanan
                </a>
                <a href="<?php echo e(route('admin.stok.index', ['kategori' => 'peralatan'])); ?>"
                   @click="sidebarOpen = false"
                   class="block pl-9 pr-3 py-2 rounded <?php echo e(request('kategori') === 'peralatan' ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10'); ?>">
                    Peralatan
                </a>
                <a href="<?php echo e(route('admin.laporan')); ?>"
                   @click="sidebarOpen = false"
                   class="block pl-9 pr-3 py-2 rounded <?php echo e(request()->routeIs('admin.laporan') ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10'); ?>">
                    Laporan
                </a>
            </div>
        </div>

        <a href="<?php echo e(route('admin.permohonan')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('admin.permohonan') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <img src="<?php echo e(asset('images/icons/permohonan_bantuan.svg')); ?>" class="w-4 h-4" alt="">
            Permohonan Bantuan
        </a>
        <div>
            <button @click="openDistribusi = !openDistribusi"
                    class="w-full flex items-center justify-between px-3 py-2 rounded <?php echo e(request()->routeIs('admin.distribusi*') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
                <span class="flex items-center gap-2.5">
                    <img src="<?php echo e(asset('images/icons/distribusi.svg')); ?>" class="w-4 h-4" alt="">
                    Distribusi
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="openDistribusi ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openDistribusi" x-collapse x-cloak class="space-y-1 mt-1">
                <a href="<?php echo e(route('admin.distribusi')); ?>"
                @click="sidebarOpen = false"
                class="block pl-9 pr-3 py-2 rounded <?php echo e(request()->routeIs('admin.distribusi') ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10'); ?>">
                    Distribusi
                </a>
                <a href="<?php echo e(route('admin.distribusi.pelaporan')); ?>"
                @click="sidebarOpen = false"
                class="block pl-9 pr-3 py-2 rounded <?php echo e(request()->routeIs('admin.distribusi.pelaporan') ? 'bg-orange-500 font-semibold' : 'hover:bg-white/10'); ?>">
                    Pelaporan Distribusi
                </a>
            </div>
        </div>
        <div class="pt-3 mt-3 border-t border-white/10 text-xs uppercase text-white/40 px-3">Master Data</div>
        <a href="<?php echo e(route('admin.data-user.index')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('admin.data-user.*') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <iconify-icon icon="mdi:account-group-outline" width="16" height="16"></iconify-icon>
            Data User
        </a>

        <a href="<?php echo e(route('admin.data-gudang.index')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('admin.data-gudang.*') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <iconify-icon icon="mdi:warehouse" width="16" height="16"></iconify-icon>
            Data Gudang
        </a>

        <a href="<?php echo e(route('admin.setting')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('admin.setting') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <iconify-icon icon="mdi:cog-outline" width="16" height="16"></iconify-icon>
            Setting
        </a>
    </nav>

    <div class="px-3 py-4 border-t border-white/10">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="flex items-center gap-2.5 text-red-400 hover:text-red-300 text-sm">
                <img src="<?php echo e(asset('images/icons/keluar.svg')); ?>" class="w-4 h-4" alt="">
                Keluar
            </button>
        </form>
    </div>
</aside><?php /**PATH C:\Users\ADVAN\simadang\resources\views/components/sidebar-admin.blade.php ENDPATH**/ ?>