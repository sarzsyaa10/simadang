<header class="bg-white shadow-sm px-4 md:px-6 py-3 flex justify-between items-center relative z-20">
    <div class="flex items-center gap-3">
        
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>

        <div class="flex items-center gap-2">
            <img src="<?php echo e(asset('images/asset/bpbd.png')); ?>" alt="Logo BPBD" class="w-9 h-9 rounded-full">
            <span class="font-bold text-blue-900 text-lg">SIMADANG</span>
        </div>
    </div>

    <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('login')); ?>" class="text-gray-700 flex items-center justify-center">
            <iconify-icon icon="gg:profile" width="28" height="28"></iconify-icon>
        </a>
    <?php else: ?>
        <div class="text-sm text-gray-500 flex items-center gap-2">
            <?php echo e(auth()->user()->nama); ?>

            <span class="text-xs bg-gray-100 rounded px-2 py-0.5"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
        </div>
    <?php endif; ?>
</header><?php /**PATH C:\Users\ADVAN\simadang\resources\views/components/navbar-general.blade.php ENDPATH**/ ?>