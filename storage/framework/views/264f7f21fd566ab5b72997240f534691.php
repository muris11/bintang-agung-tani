

<?php $__env->startSection('title', 'Checkout Berhasil'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-0 relative z-10 w-full">
    <div class="card p-8 md:p-12 text-center relative overflow-hidden bg-white shadow-lg border border-gray-100">
        
        <!-- Decoration Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-50 rounded-full -z-10 opacity-70 translate-x-1/3 -translate-y-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-50 rounded-full -z-10 opacity-60 -translate-x-1/4 translate-y-1/4"></div>
        
        <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 text-white rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-lg rotate-3">
            <i class="ph ph-check-circle ph-fill w-14 h-14 -rotate-3"></i>
        </div>
        
        <h1 class="text-[32px] md:text-4xl font-black text-gray-900 mb-3 tracking-tight">Checkout Berhasil!</h1>
        <p class="text-gray-500 mb-10 text-lg">Pesanan Anda telah kami terima dan menunggu pembayaran / pengambilan.</p>

        <div class="bg-gray-50/80 border border-gray-100 rounded-[2rem] p-8 md:p-10 mx-auto shadow-inner relative max-w-sm">
            
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">KODE PENGAMBILAN</p>
            <div class="flex items-center justify-center gap-2 mb-8">
                <h2 class="text-2xl font-black text-gray-900 tracking-wider"><?php echo e($order->order_number ?? '-'); ?></h2>
                <button class="text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none" title="Salin Kode" onclick="navigator.clipboard.writeText('<?php echo e($order->order_number ?? '-'); ?>'); alert('Kode disalin!')">
                    <i class="ph ph-copy ph-bold w-5 h-5"></i>
                </button>
            </div>

            <!-- Enhanced QR Code Box -->
            <div class="mx-auto w-56 h-56 bg-white p-4 rounded-3xl shadow-sm border border-gray-200 flex items-center justify-center mb-8 relative group cursor-pointer hover:border-green-300 transition-colors">
                <!-- Frame Decoration -->
                <div class="absolute top-4 left-4 w-6 h-6 border-t-4 border-l-4 border-green-500 rounded-tl-lg"></div>
                <div class="absolute top-4 right-4 w-6 h-6 border-t-4 border-r-4 border-green-500 rounded-tr-lg"></div>
                <div class="absolute bottom-4 left-4 w-6 h-6 border-b-4 border-l-4 border-green-500 rounded-bl-lg"></div>
                <div class="absolute bottom-4 right-4 w-6 h-6 border-b-4 border-r-4 border-green-500 rounded-br-lg"></div>
                
                <?php if($order && $order->qr_code_path && $order->getQrCodeUrl()): ?>
                    <!-- Actual QR Code Image -->
                    <img 
                        src="<?php echo e($order->getQrCodeUrl()); ?>" 
                        alt="QR Code untuk Order <?php echo e($order->order_number); ?>" 
                        class="w-[85%] h-[85%] object-contain group-hover:scale-105 transition-transform duration-500"
                        id="qr-code-image"
                        onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='flex';"
                    >
                    <!-- Fallback if image fails to load -->
                    <div id="qr-fallback" style="display: none;" class="w-[85%] h-[85%] items-center justify-center">
                        <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100" height="100" fill="white"/>
                            <path d="M10,10 h20 v20 h-20 z M15,15 h10 v10 h-10 z M10,70 h20 v20 h-20 z M15,75 h10 v10 h-10 z M70,10 h20 v20 h-20 z M75,15 h10 v10 h-10 z" fill="#111827"/>
                            <path d="M40,10 h5 v5 h-5 z M50,15 h5 v5 h-5 z M45,25 h15 v5 h-15 z M10,40 h15 v5 h-15 z M30,40 h10 v10 h-10 z M50,45 h5 v15 h-5 z M65,40 h25 v5 h-25 z M75,55 h15 v5 h-15 z M80,70 h10 v5 h-10 z M40,65 h15 v5 h-15 z M45,80 h5 v10 h-5 z M60,70 h10 v20 h-10 z M25,60 h5 v5 h-5 z M85,30 h5 v5 h-5 z M45,55 h5 v5 h-5 z M30,20 h5 v5 h-5 z" fill="#111827"/>
                            <path d="M70,70 h15 v15 h-15 z M75,75 h5 v5 h-5 z" fill="#111827"/>
                        </svg>
                    </div>
                <?php else: ?>
                    <!-- Mock QR Code - No order or QR not available -->
                    <svg width="85%" height="85%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="group-hover:scale-105 transition-transform duration-500">
                        <rect width="100" height="100" fill="white"/>
                        <path d="M10,10 h20 v20 h-20 z M15,15 h10 v10 h-10 z M10,70 h20 v20 h-20 z M15,75 h10 v10 h-10 z M70,10 h20 v20 h-20 z M75,15 h10 v10 h-10 z" fill="#111827"/>
                        <path d="M40,10 h5 v5 h-5 z M50,15 h5 v5 h-5 z M45,25 h15 v5 h-15 z M10,40 h15 v5 h-15 z M30,40 h10 v10 h-10 z M50,45 h5 v15 h-5 z M65,40 h25 v5 h-25 z M75,55 h15 v5 h-15 z M80,70 h10 v5 h-10 z M40,65 h15 v5 h-15 z M45,80 h5 v10 h-5 z M60,70 h10 v20 h-10 z M25,60 h5 v5 h-5 z M85,30 h5 v5 h-5 z M45,55 h5 v5 h-5 z M30,20 h5 v5 h-5 z" fill="#111827"/>
                        <path d="M70,70 h15 v15 h-15 z M75,75 h5 v5 h-5 z" fill="#111827"/>
                    </svg>
                <?php endif; ?>
            </div>
            
            <?php if($order && $order->qr_code_path): ?>
                <a href="<?php echo e(route('user.payments.download-qr', $order)); ?>" class="btn-secondary mb-4 inline-flex items-center text-sm">
                    <i class="ph ph-download-simple w-4 h-4 mr-1.5"></i> Download QR Code
                </a>
            <?php endif; ?>
            
            <p class="text-sm text-gray-600 leading-relaxed font-medium px-4">
                Tunjukkan Barcode QR ini kepada kasir toko saat melakukan pengambilan atau pembayaran.
            </p>
            
            <div class="mt-6 pt-5 border-t border-gray-200/60 border-dashed">
                <div class="flex items-center justify-center gap-2 text-xs text-amber-700 font-bold bg-amber-100/50 py-2.5 px-4 rounded-xl">
                    <i class="ph ph-warning-circle ph-fill w-4 h-4 text-amber-500"></i> Screenshot atau simpan halaman ini!
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo e(route('user.orders.index')); ?>" class="btn-primary w-full sm:w-auto h-12 shadow-md hover:bg-emerald-700">
                <i class="ph ph-file-text ph-bold mr-2 w-5 h-5"></i> Lihat Riwayat Pesanan
            </a>
            <?php if($order): ?>
            <a href="<?php echo e(route('user.orders.show', $order)); ?>" class="btn-secondary w-full sm:w-auto h-12 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200">
                <i class="ph ph-clipboard-text ph-bold mr-2 w-5 h-5"></i> Lihat Rincian Pesanan
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('user.dashboard')); ?>" class="btn-secondary w-full sm:w-auto h-12 bg-white hover:bg-gray-50 text-gray-700">
                Kembali ke Beranda
            </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\user\barcode-pesanan.blade.php ENDPATH**/ ?>