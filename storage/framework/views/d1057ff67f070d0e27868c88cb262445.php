<aside
    class="w-64 bg-[#0f1f3d] text-white flex flex-col fixed md:static inset-y-0 left-0 z-40 transform transition-transform duration-200 md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto"
         x-data="{
            openStok: <?php echo e(request()->routeIs('general.stok.*', 'general.laporan') ? 'true' : 'false'); ?>,
            openDistribusi: <?php echo e(request()->routeIs('general.distribusi.*') ? 'true' : 'false'); ?>

         }">

        
        <a href="<?php echo e(route('landing')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('landing') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <img src="<?php echo e(asset('images/icons/beranda.svg')); ?>" class="w-4 h-4" alt="">
            Beranda
        </a>

        
        <div>
            <button @click="openStok = !openStok"
                    class="w-full flex items-center justify-between px-3 py-2 rounded <?php echo e(request()->routeIs('general.stok.*', 'general.laporan') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
                <span class="flex items-center gap-2.5">
                    <img src="<?php echo e(asset('images/icons/stok.svg')); ?>" class="w-4 h-4" alt="">
                    Stok
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="openStok ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openStok" x-collapse x-cloak class="pl-4 space-y-1 mt-1">
                <a href="<?php echo e(route('general.stok.logistik')); ?>"
                   @click="sidebarOpen = false"
                   class="block px-3 py-1.5 rounded <?php echo e(request()->routeIs('general.stok.logistik') ? 'bg-orange-400 font-semibold' : 'hover:bg-white/10'); ?>">
                    Logistik Non Permakanan
                </a>
                <a href="<?php echo e(route('general.stok.peralatan')); ?>"
                   @click="sidebarOpen = false"
                   class="block px-3 py-1.5 rounded <?php echo e(request()->routeIs('general.stok.peralatan') ? 'bg-orange-400 font-semibold' : 'hover:bg-white/10'); ?>">
                    Peralatan
                </a>
                <a href="<?php echo e(route('general.laporan')); ?>"
                   @click="sidebarOpen = false"
                   class="block px-3 py-1.5 rounded <?php echo e(request()->routeIs('general.laporan') ? 'bg-orange-400 font-semibold' : 'hover:bg-white/10'); ?>">
                    Laporan
                </a>
            </div>
        </div>

        
        <a href="<?php echo e(route('general.permohonan')); ?>"
           @click="sidebarOpen = false"
           class="flex items-center gap-2.5 px-3 py-2 rounded <?php echo e(request()->routeIs('general.permohonan') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
            <img src="<?php echo e(asset('images/icons/permohonan_bantuan.svg')); ?>" class="w-4 h-4" alt="">
            Permohonan Bantuan
        </a>

        
        <div>
            <button @click="openDistribusi = !openDistribusi"
                    class="w-full flex items-center justify-between px-3 py-2 rounded <?php echo e(request()->routeIs('general.distribusi.*') ? 'bg-orange-500' : 'hover:bg-white/10'); ?>">
                <span class="flex items-center gap-2.5">
                    <img src="<?php echo e(asset('images/icons/distribusi.svg')); ?>" class="w-4 h-4" alt="">
                    Distribusi
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="openDistribusi ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openDistribusi" x-collapse x-cloak class="pl-4 space-y-1 mt-1">
                <a href="<?php echo e(route('general.distribusi.pelaporan')); ?>"
                   @click="sidebarOpen = false"
                   class="block px-3 py-1.5 rounded <?php echo e(request()->routeIs('general.distribusi.pelaporan') ? 'bg-orange-400 font-semibold' : 'hover:bg-white/10'); ?>">
                    Pelaporan Distribusi
                </a>
            </div>
        </div>
    </nav>

    
    <div class="px-3 py-4 border-t border-white/10">
        <?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" class="flex items-center gap-2.5 text-white hover:text-gray-200 text-sm">
                <img src="<?php echo e(asset('images/icons/masuk.svg')); ?>" class="w-4 h-4" alt="">
                Masuk
            </a>
        <?php else: ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="flex items-center gap-2.5 text-red-400 hover:text-red-300 text-sm">
                    <img src="<?php echo e(asset('images/icons/keluar.svg')); ?>" class="w-4 h-4" alt="">
                    Keluar
                </button>
            </form>
        <?php endif; ?>
    </div>
</aside><?php /**PATH C:\Users\ADVAN\simadang\resources\views/components/sidebar-general.blade.php ENDPATH**/ ?>