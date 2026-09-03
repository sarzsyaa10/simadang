<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'SIMADANG - Admin'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false }">

<div class="flex flex-col min-h-screen">
    <?php if (isset($component)) { $__componentOriginala591787d01fe92c5706972626cdf7231 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala591787d01fe92c5706972626cdf7231 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navbar','data' => ['title' => $pageTitle ?? 'SIMADANG']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle ?? 'SIMADANG')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $attributes = $__attributesOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__attributesOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $component = $__componentOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__componentOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>

    <div class="flex flex-1 relative">
        <?php if (isset($component)) { $__componentOriginal257baafdfbcbd9e6d63f040030148322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal257baafdfbcbd9e6d63f040030148322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar-admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal257baafdfbcbd9e6d63f040030148322)): ?>
<?php $attributes = $__attributesOriginal257baafdfbcbd9e6d63f040030148322; ?>
<?php unset($__attributesOriginal257baafdfbcbd9e6d63f040030148322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal257baafdfbcbd9e6d63f040030148322)): ?>
<?php $component = $__componentOriginal257baafdfbcbd9e6d63f040030148322; ?>
<?php unset($__componentOriginal257baafdfbcbd9e6d63f040030148322); ?>
<?php endif; ?>

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <main class="flex-1 p-6">
                <?php if(session('success')): ?>
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded px-4 py-2">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <footer class="text-center text-xs text-gray-400 py-4 border-t bg-white">
                © 2026 BPBD Kabupaten Cilacap - Bidang Kedaruratan & Logistik
            </footer>
        </div>
    </div>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\ADVAN\simadang\resources\views/layouts/admin.blade.php ENDPATH**/ ?>