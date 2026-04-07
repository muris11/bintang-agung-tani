<?php
use App\Models\Setting;
?>



<?php $__env->startSection('title', 'Detail Riwayat Pesanan'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4 md:mt-0 relative z-10 w-full">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="<?php echo e(route('user.dashboard')); ?>" class="hover:text-primary-600 transition-colors">Beranda</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="<?php echo e(route('user.orders.index')); ?>" class="hover:text-primary-600 transition-colors">Pesanan Saya</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Inv. <?php echo e($order->order_number); ?></span></div></li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Detail Transaksi</h1>
                <?php
                    $statusBadgeClass = $order->getStatusBadgeClass();
                ?>
                <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full <?php echo e($statusBadgeClass); ?> border mt-1.5 shadow-sm">
                    <?php echo e($order->getStatusLabel()); ?>

                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">No. Invoice: <span class="font-bold text-gray-800 tracking-wider"><?php echo e($order->order_number); ?></span></p>
        </div>
        <div class="flex items-center gap-3">
             <a href="<?php echo e(route('user.orders.index')); ?>" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali ke Riwayat
            </a>
            
            <?php if($order->canBeCancelled()): ?>
                <form action="<?php echo e(route('user.orders.cancel', $order)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-danger text-sm h-10 shadow-sm">
                        <i class="ph ph-x-circle ph-bold w-4 h-4"></i> Batalkan Pesanan
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Order Warning / Important Info - Only show when verified -->
            <?php if($order->canViewBarcode()): ?>
            <div class="bg-gradient-to-r from-green-50 to-green-100/50 border border-green-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100/80 text-green-600 flex items-center justify-center shrink-0 border border-green-200/50 shadow-inner">
                        <i class="ph ph-check-circle ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-green-800 text-lg mb-1 tracking-tight">Barang Siap Diambil!</h3>
                        <p class="text-sm text-green-700 font-medium leading-relaxed">Silakan datang ke toko dengan menunjukkan Barcode QR pengambilan kepada admin kami untuk memproses pesanan ini.</p>
                    </div>
                </div>
                <a href="<?php echo e(route('user.barcode-pesanan', $order)); ?>" class="btn-primary shrink-0 bg-primary-600 hover:bg-primary-700 border-primary-600 w-full sm:w-auto text-sm shadow-md">
                    <i class="ph ph-qr-code ph-bold w-5 h-5 mr-1.5 text-white"></i> Lihat Barcode QR
                </a>
            </div>
            <?php elseif($order->isMenungguVerifikasi()): ?>
            <!-- Waiting for admin verification -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border border-orange-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100/80 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/50 shadow-inner">
                        <i class="ph ph-clock ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-orange-800 text-lg mb-1 tracking-tight">Menunggu Verifikasi</h3>
                        <p class="text-sm text-orange-700 font-medium leading-relaxed">Bukti pembayaran Anda sedang diverifikasi oleh admin. Barcode QR akan tersedia setelah verifikasi selesai.</p>
                    </div>
                </div>
                <div class="shrink-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-orange-100 text-orange-800 text-sm font-bold">
                        <i class="ph ph-hourglass ph-duotone w-4 h-4 mr-2"></i>
                        Diproses
                    </span>
                </div>
            </div>
            <?php elseif($order->isPaymentPending()): ?>
            <!-- Waiting for payment -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border border-orange-200 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100/80 text-orange-600 flex items-center justify-center shrink-0 border border-orange-200/50 shadow-inner">
                        <i class="ph ph-credit-card ph-duotone w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-orange-800 text-lg mb-1 tracking-tight">Menunggu Pembayaran</h3>
                        <p class="text-sm text-orange-700 font-medium leading-relaxed">Silakan lakukan pembayaran dan upload bukti pembayaran untuk melanjutkan pesanan Anda.</p>
                    </div>
                </div>
                <a href="<?php echo e(route('user.payments.show-upload', $order)); ?>" class="btn-primary shrink-0 bg-primary-600 hover:bg-primary-700 border-primary-600 w-full sm:w-auto text-sm shadow-md">
                    <i class="ph ph-upload ph-bold w-5 h-5 mr-1.5 text-white"></i> Upload Pembayaran
                </a>
            </div>
            <?php endif; ?>

            <!-- Ordered Items -->
            <div class="card p-0 overflow-hidden border border-gray-100">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-200/50 text-gray-600 flex items-center justify-center">
                        <i class="ph ph-shopping-bag ph-fill w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Produk yang Dipesan</h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-6 flex flex-col sm:flex-row items-start gap-5 hover:bg-gray-50/30 transition-colors">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-gray-100 border border-gray-200 shrink-0 overflow-hidden shadow-sm">
                            <img loading="lazy" src="<?php echo e($item->product->getFirstImage() ?? asset('images/placeholder-product.png')); ?>" alt="<?php echo e($item->product_name); ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow flex flex-col justify-between w-full">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-2">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base sm:text-lg leading-tight mb-1"><?php echo e($item->product_name); ?></h3>
                                    <p class="text-gray-500 text-xs sm:text-sm font-medium"><?php echo e($item->product->category->name ?? '-'); ?></p>
                                </div>
                                <div class="text-left sm:text-right mt-1 sm:mt-0">
                                    <p class="font-black text-gray-900 text-lg"><?php echo e($item->getFormattedSubtotal()); ?></p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-3 w-full border-t border-gray-100 pt-3">
                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider"><?php echo e($item->quantity); ?> Item</span>
                                <span class="text-xs text-gray-500 font-medium">× <?php echo e($item->getFormattedUnitPrice()); ?> / item</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-6 text-center text-gray-500">
                        <p>Tidak ada item dalam pesanan ini</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="bg-gray-50 p-6 border-t border-gray-200 mt-2">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-500">Subtotal Produk</span>
                        <span class="font-bold text-gray-800"><?php echo e($order->getFormattedSubtotal()); ?></span>
                    </div>
                    <div class="flex justify-between items-center mb-5 pb-5 border-b border-gray-200 border-dashed">
                        <span class="text-sm font-medium text-gray-500">Biaya Pengiriman</span>
                        <span class="font-bold text-gray-800"><?php echo e($order->getFormattedShippingCost()); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 uppercase tracking-widest text-sm">Total Pembayaran</span>
                        <span class="text-2xl font-black text-primary-600"><?php echo e($order->getFormattedTotal()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Delivery/Pickup Info -->
            <div class="card p-0 overflow-hidden border border-gray-100">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-200/50 text-gray-600 flex items-center justify-center">
                        <i class="ph ph-map-pin ph-fill w-5 h-5"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Informasi Pengambilan</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6">
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b border-gray-200 pb-2">
                            <i class="ph ph-storefront ph-fill w-4 h-4 text-gray-400"></i>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Titik Lokasi Toko</h3>
                        </div>
                        <p class="font-bold text-gray-900 mb-1"><?php echo e(Setting::get('store_name', 'Bintang Agung Tani Utama')); ?></p>
                        <p class="text-xs text-gray-500 leading-relaxed font-medium"><?php echo e(Setting::get('store_address', 'Jl. Raya Pertanian No.12, Kabupaten Sukabumi, Jawa Barat')); ?></p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b border-gray-200 pb-2">
                            <i class="ph ph-user ph-fill w-4 h-4 text-gray-400"></i>
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Data Pengambil</h3>
                        </div>
                        <p class="font-bold text-gray-900 mb-1"><?php echo e($order->user->name); ?></p>
                        <div class="space-y-1">
                            <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5"><i class="ph ph-phone w-3.5 h-3.5"></i> <?php echo e($order->user->phone ?? '-'); ?></p>
                            <p class="text-xs text-gray-500 font-medium flex items-center gap-1.5"><i class="ph ph-envelope-simple w-3.5 h-3.5"></i> <?php echo e($order->user->email); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Order Summary Sidebar Detail -->
            <div class="card p-6 border-t-4 border-t-primary-500 shadow-sm border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-5 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="ph ph-receipt w-5 h-5 text-gray-400"></i> Info Dokumen
                </h2>
                
                <div class="space-y-4 text-sm">
                    <div class="flex flex-col gap-1 pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">No. Invoice</span>
                        <span class="font-black text-gray-900 font-mono tracking-wider"><?php echo e($order->order_number); ?></span>
                    </div>
                    <div class="flex flex-col gap-1 pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium text-xs uppercase tracking-wider">Waktu Pemesanan</span>
                        <span class="font-bold text-gray-800"><?php echo e($order->created_at->format('d M Y, H:i')); ?> WIB</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                        <span class="text-gray-500 font-medium">Status Pembayaran</span>
                        <span class="inline-flex py-1 px-2.5 text-[10px] font-black uppercase tracking-wider rounded <?php echo e($order->getPaymentStatusClass()); ?>">
                            <?php echo e($order->getPaymentStatusLabel()); ?>

                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Kanal Pembayaran</span>
                        <span class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] uppercase font-black italic border border-blue-100"><?php echo e($order->payment_method ?? '-'); ?></span> <?php echo e($order->paymentMethod->name ?? '-'); ?>

                        </span>
                    </div>
                </div>
                
                <a href="<?php echo e(route('user.payments.show-upload', $order)); ?>" class="btn-secondary w-full mt-6 text-sm font-semibold h-11 border-gray-200 bg-gray-50">
                    <i class="ph ph-image w-4 h-4 mr-1.5"></i> Lihat Bukti Pembayaran Saya
                </a>
            </div>
            
            <!-- Tracking Log Widget -->
            <div class="card p-0 overflow-hidden shadow-sm border border-gray-100">
                <div class="bg-gray-50/80 px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-[15px] flex items-center gap-2">
                        <i class="ph ph-magnifying-glass-plus ph-bold w-4 h-4 text-gray-400"></i> Log Aktivitas
                    </h3>
                </div>
                
                <div class="p-6 bg-white max-h-80 overflow-y-auto">
                    <div class="relative border-l-2 border-primary-200 ml-2 space-y-6">
                        <?php $__empty_1 = true; $__currentLoopData = $order->statusHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="relative pl-5 <?php echo e(!$loop->first ? 'opacity-60' : ''); ?>">
                            <div class="absolute w-3 h-3 bg-white rounded-full border-2 <?php echo e($loop->first ? 'border-primary-500' : 'border-gray-300'); ?> shadow-sm -left-[7px] top-1"></div>
                            <h4 class="font-bold <?php echo e($loop->first ? 'text-gray-900' : 'text-gray-700'); ?> text-sm"><?php echo e($history->getStatusLabel()); ?></h4>
                            <span class="text-[10px] text-gray-500 font-bold tracking-wider uppercase mt-1 block"><?php echo e($history->created_at->format('d M, H:i')); ?> WIB</span>
                            <?php if($history->notes): ?>
                            <p class="text-xs text-gray-400 mt-1"><?php echo e($history->notes); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="relative pl-5">
                            <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/user/detail-pesanan.blade.php ENDPATH**/ ?>