<?php $__env->startSection('title', 'Pesanan Masuk'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Manajemen Pesanan</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pesanan Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pesanan masuk, verifikasi pembayaran, dan perbarui status pengiriman.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-download-simple ph-bold w-4 h-4"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-blue-500 bg-blue-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Perlu Diproses</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo e($stats['pending']); ?></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-clock-countdown ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-orange-500 bg-orange-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-1">Menunggu Verifikasi</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo e($stats['verification']); ?></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="ph ph-receipt ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-blue-500 bg-blue-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Dalam Pengiriman</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo e($stats['shipped']); ?></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="ph ph-truck ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card p-4 hover:-translate-y-1 transition-transform cursor-pointer border-b-4 border-b-green-500 bg-green-50/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Selesai (Bulan Ini)</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo e($stats['completed_this_month']); ?></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="ph ph-check-circle ph-duotone w-5 h-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="card p-0 overflow-hidden w-full border-primary-100">

        <!-- Toolbar -->
        <div class="p-5 border-b border-primary-100 flex flex-col md:flex-row gap-4 justify-between md:items-center bg-white">
            <div class="flex items-center gap-3">
                <div class="relative min-w-[280px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Cari ID Pesanan / Nama Pelanggan..." 
                           class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="w-full text-left text-sm whitespace-nowrap" style="min-width: 1000px;">
                <thead class="bg-gradient-to-r from-primary-50/60 to-primary-50/30 text-primary-700 text-xs uppercase font-bold tracking-wider border-b-2 border-primary-100">
                    <tr>
                        <th class="px-4 py-4 w-12 text-center">No</th>
                        <th class="px-4 py-4 min-w-[120px]">ID Pesanan</th>
                        <th class="px-4 py-4 min-w-[160px]">Pelanggan</th>
                        <th class="px-4 py-4 min-w-[100px]">Tanggal</th>
                        <th class="px-4 py-4 text-right min-w-[120px]">Total</th>
                        <th class="px-4 py-4 text-center min-w-[100px]">Status</th>
                        <th class="px-4 py-4 text-right min-w-[200px]">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/50 transition-colors <?php echo e($order->status === 'payment_pending' ? 'bg-gray-50/30 text-gray-500' : ''); ?>">
                        <td class="px-4 py-4 text-gray-500 text-center text-sm font-medium"><?php echo e($orders->firstItem() + $index); ?></td>
                        <td class="px-4 py-4">
                            <span class="font-bold <?php echo e($order->status === 'payment_pending' ? 'text-gray-600 border-gray-600/20' : 'text-gray-900 border-gray-900/20 hover:border-primary-500 hover:text-primary-600'); ?> border-b pb-0.5 cursor-pointer transition-colors">
                                <?php echo e($order->order_number); ?>

                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full <?php echo e($order->status === 'payment_pending' ? 'bg-gray-200 text-gray-500 opacity-70' : 'bg-gray-100 text-gray-600'); ?> flex items-center justify-center font-bold text-xs shrink-0">
                                    <?php echo e(substr($order->user->name, 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-bold <?php echo e($order->status === 'payment_pending' ? 'text-gray-600' : 'text-gray-900'); ?> text-sm leading-tight"><?php echo e($order->user->name); ?></p>
                                    <p class="text-[11px] <?php echo e($order->status === 'payment_pending' ? 'text-gray-400' : 'text-gray-500'); ?> mt-0.5"><?php echo e($order->user->email); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-medium <?php echo e($order->status === 'payment_pending' ? 'text-gray-500' : 'text-gray-900'); ?>"><?php echo e($order->created_at->format('d M Y')); ?></p>
                            <p class="text-[11px] <?php echo e($order->status === 'payment_pending' ? 'text-gray-400' : 'text-gray-500'); ?> mt-0.5"><?php echo e($order->created_at->format('H:i')); ?> WIB</p>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="font-bold <?php echo e($order->status === 'payment_pending' ? 'text-gray-600' : 'text-gray-900'); ?>">Rp<?php echo e(number_format($order->total_amount, 0, ',', '.')); ?></span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 text-[11px] font-bold rounded-full <?php echo e($order->getStatusBadgeClass()); ?> border">
                                <?php echo e($order->getStatusLabel()); ?>

                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ showActions: false }">
                                <!-- View Detail -->
                                <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn-secondary h-8 w-8 p-0 rounded-lg flex items-center justify-center text-gray-500 hover:text-primary-600 hover:bg-primary-50 border-gray-200 shrink-0" title="Lihat Detail">
                                    <i class="ph ph-eye ph-bold w-4 h-4"></i>
                                </a>
                                
                                <!-- Quick Actions Based on Status -->
                                <?php if($order->status === 'payment_pending' || $order->status === 'menunggu_verifikasi'): ?>
                                    <a href="<?php echo e(route('admin.verifikasi.index')); ?>?order=<?php echo e($order->id); ?>" class="btn-primary h-8 px-2 text-xs border-orange-500 bg-orange-500 hover:bg-orange-600 hover:border-orange-600 shadow-sm focus:ring-orange-500/30 flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-receipt w-3.5 h-3.5"></i> Verifikasi
                                    </a>
                                <?php elseif($order->status === 'processing'): ?>
                                    <button onclick="openTrackingModal(<?php echo e($order->id); ?>)" class="btn-primary h-8 px-2 text-xs border-blue-600 bg-blue-600 hover:bg-blue-700 hover:border-blue-700 shadow-sm focus:ring-blue-500/30 flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-truck w-3.5 h-3.5"></i> Kirim
                                    </button>
                                <?php elseif($order->status === 'shipped'): ?>
                                    <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST" class="inline" onsubmit="return confirm('Tandai pesanan sebagai selesai?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn-primary h-8 px-2 text-xs border-green-600 bg-green-600 hover:bg-green-700 hover:border-green-700 shadow-sm focus:ring-green-500/30 flex items-center gap-1 whitespace-nowrap">
                                            <i class="ph ph-check-circle w-3.5 h-3.5"></i> Selesai
                                        </button>
                                    </form>
                                <?php elseif($order->status === 'completed'): ?>
                                    <button class="btn-secondary h-8 px-2 text-xs bg-gray-50 text-gray-400 border-transparent cursor-default pointer-events-none flex items-center gap-1 whitespace-nowrap">
                                        <i class="ph ph-check-circle ph-fill w-3.5 h-3.5 text-green-500"></i> Berhasil
                                    </button>
                                <?php endif; ?>
                                
                                <!-- More Actions Dropdown -->
                                <div class="relative">
                                    <button @click="showActions = !showActions" @click.away="showActions = false" class="btn-secondary h-8 w-8 p-0 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 border-gray-200" title="Aksi Lainnya">
                                        <i class="ph ph-dots-three-vertical ph-bold w-4 h-4"></i>
                                    </button>
                                    
                                    <div x-show="showActions" x-cloak x-transition class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 py-2">
                                        <!-- QR Code -->
                                        <a href="<?php echo e(route('user.payments.qr-code', $order)); ?>" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ph ph-qr-code w-4 h-4 text-primary-600"></i>
                                            <span>Lihat QR Code</span>
                                        </a>
                                        
                                        <!-- Print Invoice -->
                                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>?print=true" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ph ph-printer w-4 h-4 text-gray-500"></i>
                                            <span>Cetak Invoice</span>
                                        </a>
                                        
                                        <!-- Update Status -->
                                        <?php if(in_array($order->status, ['pending', 'payment_pending', 'menunggu_verifikasi', 'processing', 'shipped'])): ?>
                                            <button onclick="openStatusModal(<?php echo e($order->id); ?>, '<?php echo e($order->status); ?>')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors text-left">
                                                <i class="ph ph-arrows-clockwise w-4 h-4 text-blue-600"></i>
                                                <span>Update Status</span>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <hr class="my-2 border-gray-100">
                                        
                                        <!-- Cancel Order -->
                                        <?php if($order->canBeCancelled()): ?>
                                            <form action="<?php echo e(route('admin.orders.cancel', $order)); ?>" method="POST" class="block" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                                    <i class="ph ph-x-circle w-4 h-4"></i>
                                                    <span>Batalkan Pesanan</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="ph ph-package w-12 h-12 text-gray-300"></i>
                                <p class="text-sm font-medium">Tidak ada pesanan ditemukan</p>
                                <p class="text-xs text-gray-400">Coba ubah filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 flex flex-col sm:flex-row items-center justify-between text-sm bg-gradient-to-r from-primary-50/30 to-primary-50/10 border-t border-primary-100 gap-4">
            <span class="text-gray-500 font-medium text-center sm:text-left">
                Menampilkan <span class="text-gray-900 font-bold"><?php echo e($orders->firstItem() ?? 0); ?>-<?php echo e($orders->lastItem() ?? 0); ?></span> 
                dari <span class="text-gray-900 font-bold"><?php echo e($orders->total()); ?></span> total pesanan
            </span>
            <div class="flex gap-2">
                <?php if($orders->onFirstPage()): ?>
                    <button disabled class="btn-secondary h-8 px-3 text-xs opacity-50 cursor-not-allowed border-gray-200">Sebelumnya</button>
                <?php else: ?>
                    <a href="<?php echo e($orders->previousPageUrl()); ?>" class="btn-secondary h-8 px-3 text-xs border-gray-200 hover:bg-gray-50 hover:text-gray-900">Sebelumnya</a>
                <?php endif; ?>
                
                <div class="flex items-center gap-1">
                    <?php $__currentLoopData = $orders->getUrlRange(1, $orders->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $orders->currentPage()): ?>
                            <button class="icon-button rounded-lg bg-primary-600 text-white font-bold shadow-sm min-w-[44px]"><?php echo e($page); ?></button>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>" class="icon-button rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors font-medium min-w-[44px]"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <?php if($orders->hasMorePages()): ?>
                    <a href="<?php echo e($orders->nextPageUrl()); ?>" class="btn-secondary h-8 px-3 text-xs border-gray-200 hover:bg-gray-50 hover:text-gray-900">Selanjutnya</a>
                <?php else: ?>
                    <button disabled class="btn-secondary h-8 px-3 text-xs opacity-50 cursor-not-allowed border-gray-200">Selanjutnya</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Modal for Tracking Number -->
<div id="trackingModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeTrackingModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                <h3 class="text-lg font-bold text-white">Tambah Nomor Resi</h3>
                <p class="text-blue-100 text-sm">Masukkan informasi pengiriman untuk pesanan ini</p>
            </div>
            <form id="trackingForm" method="POST" class="p-6 space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="order_id" id="trackingOrderId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kurir</label>
                    <input type="text" name="courier" required class="form-input w-full h-11" placeholder="Contoh: JNE, J&T, SiCepat">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Resi</label>
                    <input type="text" name="tracking_number" required class="form-input w-full h-11" placeholder="Nomor resi pengiriman">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeTrackingModal()" class="flex-1 btn-secondary h-11">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 btn-primary h-11 bg-blue-600 hover:bg-blue-700">
                        Simpan & Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Status Update -->
<div id="statusModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatusModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                <h3 class="text-lg font-bold text-white">Update Status Pesanan</h3>
                <p class="text-primary-100 text-sm">Pilih status baru untuk pesanan ini</p>
            </div>
            <form id="statusForm" method="POST" class="p-6 space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <input type="hidden" name="order_id" id="statusOrderId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
                    <select name="status" id="statusSelect" required class="form-input w-full h-11">
                        <option value="">Pilih Status</option>
                        <option value="payment_pending">Menunggu Pembayaran</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" class="form-input w-full" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeStatusModal()" class="flex-1 btn-secondary h-11">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 btn-primary h-11">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTrackingModal(orderId) {
    document.getElementById('trackingOrderId').value = orderId;
    document.getElementById('trackingForm').action = `/admin/orders/${orderId}/tracking`;
    document.getElementById('trackingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTrackingModal() {
    document.getElementById('trackingModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('trackingForm').reset();
}

function openStatusModal(orderId, currentStatus) {
    document.getElementById('statusOrderId').value = orderId;
    document.getElementById('statusForm').action = `/admin/orders/${orderId}/status`;
    
    // Set current status in dropdown
    const statusSelect = document.getElementById('statusSelect');
    statusSelect.value = '';
    
    // Disable current status option
    Array.from(statusSelect.options).forEach(option => {
        option.disabled = option.value === currentStatus;
    });
    
    document.getElementById('statusModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('statusForm').reset();
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrackingModal();
        closeStatusModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/admin/pesanan.blade.php ENDPATH**/ ?>