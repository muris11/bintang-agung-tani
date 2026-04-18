<?php $__env->startSection('title', 'Detail Stok: ' . $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/admin/stok" class="hover:text-primary-600 transition-colors">Kelola Stok</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Detail Stok</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Detail Stok: <?php echo e($product->name); ?></h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/stok" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 shadow-sm">
                <img loading="lazy" src="<?php echo e($product->image_url ?? asset('images/no-product.jpg')); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover">
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-900"><?php echo e($product->name); ?></h2>
                <p class="text-sm text-gray-500 mt-1">SKU: <span class="font-mono"><?php echo e($product->sku); ?></span></p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-primary-50 rounded-xl p-4 border border-primary-100">
                <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-1">Stok Saat Ini</p>
                <p class="text-3xl font-black text-primary-700"><?php echo e($product->stock); ?></p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Masuk</p>
                <p class="text-2xl font-black text-green-600"><?php echo e($stockLogs->where('type', 'increase')->sum('quantity')); ?></p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Keluar</p>
                <p class="text-2xl font-black text-red-600"><?php echo e($stockLogs->where('type', 'decrease')->sum('quantity')); ?></p>
            </div>
        </div>
    </div>

    <!-- Stock History -->
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="ph ph-clock-counter-clockwise w-5 h-5 text-primary-600"></i>
                Riwayat Perubahan Stok
            </h2>
            <span class="text-sm text-gray-500"><?php echo e($stockLogs->count()); ?> entri</span>
        </div>
        
        <?php if($stockLogs->isEmpty()): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="ph ph-clock w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p>Belum ada riwayat perubahan stok.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-bold tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Tipe</th>
                            <th class="px-6 py-3">Jumlah</th>
                            <th class="px-6 py-3">Stok Sebelum</th>
                            <th class="px-6 py-3">Stok Setelah</th>
                            <th class="px-6 py-3">Alasan</th>
                            <th class="px-6 py-3">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $stockLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-600"><?php echo e($log->created_at->format('d/m/Y H:i')); ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full <?php echo e($log->type === 'increase' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                                        <?php echo e($log->type === 'increase' ? 'Tambah' : 'Kurang'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono <?php echo e($log->type === 'increase' ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e($log->type === 'increase' ? '+' : '-'); ?><?php echo e($log->quantity); ?>

                                </td>
                                <td class="px-6 py-4 text-gray-600"><?php echo e($log->before_stock); ?></td>
                                <td class="px-6 py-4 font-bold text-gray-900"><?php echo e($log->after_stock); ?></td>
                                <td class="px-6 py-4 text-gray-600"><?php echo e($log->reason ?? '-'); ?></td>
                                <td class="px-6 py-4 text-gray-600"><?php echo e($log->createdBy?->name ?? 'System'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\admin\stok-detail.blade.php ENDPATH**/ ?>