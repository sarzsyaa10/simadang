<?php $pageTitle = 'Permohonan Bantuan'; ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-blue-900">Permohonan Bantuan</h1>

    <a href="<?php echo e(route('admin.permohonan')); ?>"
       class="inline-flex items-center gap-1.5 bg-white border text-sm px-4 py-2 rounded shadow-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 1 1-3-6.7" />
            <path d="M21 3v6h-6" />
        </svg>
        Reload
    </a>
</div>

<?php if(session('success')): ?>
    <div class="mb-4 bg-green-100 text-green-700 text-sm px-4 py-2 rounded"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="bg-white rounded shadow p-5">
    <form method="GET" class="flex justify-end mb-4">
        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search"
               class="border rounded px-3 py-2 text-sm w-64">
    </form>

    <table class="w-full text-sm">
        <thead class="bg-blue-900 text-white text-left">
            <tr>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Pemohon</th>
                <th class="p-3">Jabatan</th>
                <th class="p-3">Alamat</th>
                <th class="p-3">Barang</th>
                <th class="p-3">Status</th>
                <th class="p-3">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $permohonan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b align-top">
                    <td class="p-3 whitespace-nowrap">
                        <?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($item->jam)->format('H:i')); ?>

                    </td>
                    <td class="p-3"><?php echo e($item->nama_pemohon); ?></td>
                    <td class="p-3"><?php echo e($item->jabatan); ?></td>
                    <td class="p-3"><?php echo e($item->alamat); ?></td>
                    <td class="p-3">
                        <a href="<?php echo e(route('admin.permohonan.show', $item->id)); ?>" class="text-blue-700 hover:underline">
                            <?php echo e($item->permohonanBantuanDetail->count()); ?> jenis barang
                        </a>
                    </td>
                    <td class="p-3">
                        <?php
                            $badge = match($item->status) {
                                'disetujui' => 'bg-green-100 text-green-700',
                                'ditolak' => 'bg-red-100 text-red-700',
                                'sebagian' => 'bg-blue-100 text-blue-700',
                                default => 'bg-orange-100 text-orange-600',
                            };
                            $label = match($item->status) {
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'sebagian' => 'Disetujui Sebagian',
                                default => 'Diajukan',
                            };
                        ?>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo e($badge); ?>"><?php echo e($label); ?></span>
                    </td>
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <a href="<?php echo e(route('admin.permohonan.show', $item->id)); ?>">
                                <img src="<?php echo e(asset('images/icons/pencil.svg')); ?>" class="w-4 h-4" alt="Verifikasi">
                            </a>
                            <form method="POST" action="<?php echo e(route('admin.permohonan.destroy', $item->id)); ?>"
                                  onsubmit="return confirm('Yakin hapus permohonan ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit">
                                    <img src="<?php echo e(asset('images/icons/trash.svg')); ?>" class="w-4 h-4" alt="Hapus">
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="p-4 text-center text-gray-400">Belum ada permohonan bantuan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-4"><?php echo e($permohonan->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\simadang\resources\views/admin/permohonan-bantuan/index.blade.php ENDPATH**/ ?>