<header class="bg-white shadow-sm px-4 md:px-6 py-3 flex justify-between items-center relative z-20">
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>

        <img src="<?php echo e(asset('images/asset/bpbd.png')); ?>" alt="Logo BPBD" class="w-8 h-8 rounded-full">
        <span class="font-bold text-blue-900 text-lg">SIMADANG</span>
    </div>

    <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
        <div class="text-right text-sm leading-tight">
            <div class="font-medium text-gray-700"><?php echo e(auth()->user()->nama); ?></div>
            <div class="text-xs text-gray-400"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
        </div>
        <iconify-icon icon="gg:profile" width="32" height="32"></iconify-icon>
    </a>
</header><?php /**PATH C:\Users\ADVAN\simadang\resources\views/components/navbar.blade.php ENDPATH**/ ?>