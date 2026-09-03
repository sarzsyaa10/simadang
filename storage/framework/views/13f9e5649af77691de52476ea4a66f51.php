<?php $__env->startSection('title', 'Beranda'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-[320px] md:h-[420px] py-16 md:py-0 bg-cover bg-center flex items-center justify-center text-center"
     style="background-image: url('<?php echo e(asset('images/asset/beranda-bpbd.png')); ?>')">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative z-10 px-4 text-white max-w-3xl">
        <h1 class="text-3xl md:text-4xl font-bold leading-snug">
            Sistem Manajemen Gudang dan Logistik<br>BPBD Cilacap (SIMADANG)
        </h1>
        <p class="mt-3 text-gray-200">
            Platform terpadu untuk pemantauan logistik, pengajuan bantuan, dan manajemen distribusi bencana.
        </p>
        <a href="<?php echo e(route('login')); ?>"
           class="inline-flex items-center gap-2 mt-6 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" />
            </svg>
            Masuk
        </a>
    </div>
</div>

<div class="relative z-10 -mt-8 md:-mt-16 grid grid-cols-1 md:grid-cols-3 gap-4 px-6">
    <a href="<?php echo e(route('general.stok.logistik')); ?>"
       class="bg-red-600 hover:bg-red-700 text-white rounded-md py-8 shadow-lg flex flex-col items-center gap-2 font-bold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        STOK
    </a>

    <a href="<?php echo e(route('general.permohonan')); ?>"
       class="bg-orange-500 hover:bg-orange-600 text-white rounded-md py-8 shadow-lg flex flex-col items-center gap-2 font-bold text-lg text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
        </svg>
        PERMOHONAN<br>BANTUAN
    </a>

    <a href="<?php echo e(route('general.distribusi')); ?>"
       class="bg-blue-600 hover:bg-blue-700 text-white rounded-md py-8 shadow-lg flex flex-col items-center gap-2 font-bold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5V13.5" />
        </svg>
        DISTRIBUSI
    </a>
</div>

<div class="h-16"></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\simadang\resources\views/general/beranda.blade.php ENDPATH**/ ?>