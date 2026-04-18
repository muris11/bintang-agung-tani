<?php $__env->startSection('title', 'Edit Stok Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/admin/stok" class="hover:text-primary-600 transition-colors">Kelola Stok</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Edit Stok</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Update Stok Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola stok untuk produk yang dipilih.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/stok" class="btn-secondary text-sm h-10">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Product Info Card -->
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center">
                <i class="ph ph-package w-8 h-8 text-gray-400"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900"><?php echo e($product->name ?? 'Nama Produk'); ?></h2>
                <p class="text-sm text-gray-500">SKU: <?php echo e($product->sku ?? 'SKU-001'); ?></p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.stock.update', $product ?? 1)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <!-- Current Stock Display -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stok Saat Ini</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($product->stock ?? 0); ?></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stok Minimum</p>
                    <p class="text-2xl font-bold text-orange-600"><?php echo e($product->min_stock ?? 10); ?></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e(($product->stock ?? 0) > ($product->min_stock ?? 10) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e(($product->stock ?? 0) > ($product->min_stock ?? 10) ? 'Stok Aman' : 'Stok Menipis'); ?>

                    </span>
                </div>
            </div>

            <!-- Stock Update Form -->
            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Stok</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Adjustment Type -->
                    <div>
                        <label for="adjustment_type" class="form-label block mb-2">Jenis Penyesuaian</label>
                        <select name="adjustment_type" id="adjustment_type" class="form-input w-full" required>
                            <option value="add">Tambah Stok (Restock)</option>
                            <option value="subtract">Kurangi Stok (Rusak/Hilang)</option>
                            <option value="set">Set Ulang Stok (Reset)</option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="form-label block mb-2">Jumlah</label>
                        <input type="number" name="quantity" id="quantity" min="0" class="form-input w-full" placeholder="Masukkan jumlah..." required>
                    </div>
                </div>

                <!-- Reason -->
                <div class="mt-4">
                    <label for="reason" class="form-label block mb-2">Alasan Perubahan</label>
                    <textarea name="reason" id="reason" rows="3" class="form-input w-full resize-none" placeholder="Contoh: Restock dari supplier, barang rusak, dll..." required></textarea>
                </div>

                <!-- New Stock Preview -->
                <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ph ph-info w-5 h-5 text-blue-600"></i>
                        <p class="text-sm font-semibold text-blue-900">Preview Stok Baru</p>
                    </div>
                    <p class="text-sm text-blue-700">Stok akan diupdate dari <strong><?php echo e($product->stock ?? 0); ?></strong> menjadi <strong id="new-stock-preview">-</strong></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="/admin/stok" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="ph ph-floppy-disk ph-bold w-5 h-5 mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Stock History -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Perubahan Stok</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Stok Akhir</th>
                        <th class="px-4 py-3 rounded-r-lg">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $stockLogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($log->type == 'add' ? 'bg-green-100 text-green-800' : ($log->type == 'subtract' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')); ?>">
                                <?php echo e($log->type == 'add' ? 'Tambah' : ($log->type == 'subtract' ? 'Kurangi' : 'Reset')); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 <?php echo e($log->type == 'add' ? 'text-green-600' : ($log->type == 'subtract' ? 'text-red-600' : 'text-blue-600')); ?>">
                            <?php echo e($log->type == 'add' ? '+' : ($log->type == 'subtract' ? '-' : '')); ?><?php echo e($log->quantity); ?>

                        </td>
                        <td class="px-4 py-3 font-medium"><?php echo e($log->stock_after); ?></td>
                        <td class="px-4 py-3 text-gray-500"><?php echo e($log->reason); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="ph ph-clock-counter-clockwise w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p>Belum ada riwayat perubahan stok</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Preview new stock calculation
    const currentStock = <?php echo e($product->stock ?? 0); ?>;
    const adjustmentType = document.getElementById('adjustment_type');
    const quantity = document.getElementById('quantity');
    const newStockPreview = document.getElementById('new-stock-preview');

    function updatePreview() {
        const qty = parseInt(quantity.value) || 0;
        let newStock = currentStock;

        if (adjustmentType.value === 'add') {
            newStock = currentStock + qty;
        } else if (adjustmentType.value === 'subtract') {
            newStock = Math.max(0, currentStock - qty);
        } else if (adjustmentType.value === 'set') {
            newStock = qty;
        }

        newStockPreview.textContent = newStock;
    }

    adjustmentType.addEventListener('change', updatePreview);
    quantity.addEventListener('input', updatePreview);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\admin\edit-stok.blade.php ENDPATH**/ ?>