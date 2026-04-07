<?php $__env->startSection('title', 'Riwayat Perubahan Stok'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Riwayat Stok</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Riwayat Perubahan Stok</h1>
            <p class="text-sm text-gray-500 mt-1">Lihat semua perubahan stok produk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/stok" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali ke Stok
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6 border-primary-100">
        <form method="GET" action="/admin/stok-logs" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" name="product" placeholder="Cari produk..." value="<?php echo e(request('product')); ?>" 
                               class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" class="form-input w-full">
                        <option value="">Semua Tipe</option>
                        <option value="increase" <?php echo e(request('type') == 'increase' ? 'selected' : ''); ?>>Tambah Stok</option>
                        <option value="decrease" <?php echo e(request('type') == 'decrease' ? 'selected' : ''); ?>>Kurang Stok</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary h-10 px-4 whitespace-nowrap">
                    <i class="ph ph-funnel ph-bold w-4 h-4"></i> Filter
                </button>
                <a href="/admin/stok-logs" class="btn-secondary h-10 px-4 whitespace-nowrap">
                    <i class="ph ph-arrow-counter-clockwise ph-bold w-4 h-4"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-hidden w-full border-primary-100">
        <div class="table-responsive">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-center">Tipe</th>
                        <th class="px-6 py-4 text-right">Jumlah</th>
                        <th class="px-6 py-4 text-right">Stok Sebelum</th>
                        <th class="px-6 py-4 text-right">Stok Setelah</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $stockLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-primary-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                <?php echo e($log->created_at->format('d M Y')); ?>

                                <span class="text-gray-500 text-xs block"><?php echo e($log->created_at->format('H:i')); ?> WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                        <i class="ph ph-package text-gray-500 text-lg"></i>
                                    </div>
                                    <span class="font-medium text-gray-900"><?php echo e($log->product->name ?? '-'); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($log->type === 'increase'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border bg-green-100 text-green-700 border-green-200">
                                        <i class="ph ph-arrow-up"></i> Tambah
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border bg-red-100 text-red-700 border-red-200">
                                        <i class="ph ph-arrow-down"></i> Kurang
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900"><?php echo e($log->quantity); ?></td>
                            <td class="px-6 py-4 text-right text-gray-600"><?php echo e($log->before_stock); ?></td>
                            <td class="px-6 py-4 text-right font-bold <?php echo e($log->after_stock > $log->before_stock ? 'text-green-600' : 'text-red-600'); ?>"><?php echo e($log->after_stock); ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="<?php echo e($log->reason); ?>"><?php echo e($log->reason); ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-bold">
                                        <?php echo e(substr($log->createdBy?->name ?? 'S', 0, 1)); ?>

                                    </div>
                                    <span class="text-sm text-gray-700"><?php echo e($log->createdBy?->name ?? 'System'); ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ph ph-clipboard-text text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada riwayat</h3>
                                    <p class="text-gray-500 mb-4">Belum ada perubahan stok yang tercatat.</p>
                                    <a href="/admin/stok" class="btn-primary">
                                        Kelola Stok
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($stockLogs->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100">
            <?php echo e($stockLogs->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/admin/stock-logs.blade.php ENDPATH**/ ?>