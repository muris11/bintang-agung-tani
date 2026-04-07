<?php
use App\Models\Setting;
?>



<?php $__env->startSection('title', 'QR Code Pembayaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6 pb-12">
    <!-- Header -->
    <div class="animate-fade-in-up">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li><a href="/user/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/user/riwayat" class="hover:text-primary-600 transition-colors">Pesanan</a></div></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">QR Code</span></div></li>
            </ol>
        </nav>
        <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">QR Code Pembayaran</h1>
        <p class="text-gray-500 mt-1 text-sm">Scan kode ini di aplikasi mobile atau tunjukkan ke admin</p>
    </div>

    <!-- QR Code Card -->
    <div class="card p-8 animate-fade-in-up delay-100">
        <div class="text-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Order #<?php echo e($order->order_number); ?></h2>
            <p class="text-3xl font-bold text-primary-600"><?php echo e($order->getFormattedTotal()); ?></p>
        </div>

        <?php if($order->qr_code_path && $order->getQrCodeUrl()): ?>
            <!-- QR Code Display -->
            <div class="flex flex-col items-center">
                <div class="relative bg-white p-6 rounded-2xl shadow-lg border-2 border-gray-100">
                    <img 
                        src="<?php echo e($order->getQrCodeUrl()); ?>" 
                        alt="QR Code untuk Order <?php echo e($order->order_number); ?>" 
                        class="w-64 h-64 object-contain"
                        id="qr-code-image"
                        onerror="handleQrError()"
                    >
                    <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2">
                        <div class="bg-primary-600 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-md">
                            SCAN ME
                        </div>
                    </div>
                </div>

                <!-- Download Button -->
                <a href="<?php echo e(route('user.payments.download-qr', $order)); ?>" class="btn-secondary mt-6 flex items-center gap-2">
                    <i class="ph ph-download-simple w-5 h-5"></i>
                    Download QR Code
                </a>
            </div>

            <!-- Payment Status -->
            <?php if($order->latestPaymentProof): ?>
                <div class="mt-8 p-4 rounded-xl <?php echo e($order->latestPaymentProof->isPending() ? 'bg-yellow-50 border border-yellow-200' : ($order->latestPaymentProof->isVerified() ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200')); ?>">
                    <div class="flex items-center gap-3">
                        <div class="shrink-0">
                            <?php if($order->latestPaymentProof->isPending()): ?>
                                <i class="ph ph-clock w-6 h-6 text-yellow-600"></i>
                            <?php elseif($order->latestPaymentProof->isVerified()): ?>
                                <i class="ph ph-check-circle w-6 h-6 text-green-600"></i>
                            <?php else: ?>
                                <i class="ph ph-x-circle w-6 h-6 text-red-600"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-semibold <?php echo e($order->latestPaymentProof->isPending() ? 'text-yellow-800' : ($order->latestPaymentProof->isVerified() ? 'text-green-800' : 'text-red-800')); ?>">
                                <?php echo e($order->latestPaymentProof->getStatusLabel()); ?>

                            </p>
                            <p class="text-sm <?php echo e($order->latestPaymentProof->isPending() ? 'text-yellow-700' : ($order->latestPaymentProof->isVerified() ? 'text-green-700' : 'text-red-700')); ?>">
                                <?php if($order->latestPaymentProof->isPending()): ?>
                                    Admin akan memverifikasi pembayaran Anda dalam 1x24 jam
                                <?php elseif($order->latestPaymentProof->isVerified()): ?>
                                    Pembayaran telah diverifikasi. Pesanan sedang diproses.
                                <?php else: ?>
                                    Pembayaran ditolak. <?php echo e($order->latestPaymentProof->admin_notes); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-info w-6 h-6 text-blue-600"></i>
                        <div>
                            <p class="font-semibold text-blue-800">Menunggu Upload Bukti</p>
                            <p class="text-sm text-blue-700">Silakan upload bukti pembayaran untuk melanjutkan</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Error State - QR Code Not Available -->
            <div id="qr-error-state" class="text-center py-8">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph ph-qr-code w-10 h-10 text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">QR Code Tidak Tersedia</h3>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">Terjadi masalah saat membuat QR Code. Silakan coba refresh halaman atau hubungi admin.</p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="window.location.reload()" class="btn-primary flex items-center justify-center gap-2">
                        <i class="ph ph-arrow-clockwise w-5 h-5"></i>
                        Coba Lagi
                    </button>
                    <a href="/user/bantuan" class="btn-secondary flex items-center justify-center gap-2">
                        <i class="ph ph-chat-circle-text w-5 h-5"></i>
                        Hubungi Admin
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Instructions -->
    <div class="card p-6 animate-fade-in-up delay-150">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ph ph-info w-5 h-5 text-primary-600"></i>
            Cara Menggunakan QR Code
        </h3>
        <div class="space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 font-bold text-sm">1</div>
                <div>
                    <p class="font-medium text-gray-900">Scan dengan Aplikasi Mobile</p>
                    <p class="text-sm text-gray-600">Buka aplikasi <?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?> di HP Anda dan scan QR code ini</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 font-bold text-sm">2</div>
                <div>
                    <p class="font-medium text-gray-900">Tunjukkan ke Admin</p>
                    <p class="text-sm text-gray-600">Admin dapat scan QR code ini untuk melihat detail pesanan Anda</p>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 font-bold text-sm">3</div>
                <div>
                    <p class="font-medium text-gray-900">Download untuk Offline</p>
                    <p class="text-sm text-gray-600">Simpan QR code di gallery HP Anda untuk akses offline</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center animate-fade-in-up delay-200">
        <a href="<?php echo e(route('user.orders.show', $order)); ?>" class="btn-secondary inline-flex items-center gap-2">
            <i class="ph ph-arrow-left w-5 h-5"></i>
            Kembali ke Detail Pesanan
        </a>
    </div>
</div>

<script>
function handleQrError() {
    const img = document.getElementById('qr-code-image');
    const container = img.parentElement.parentElement;
    
    // Replace image with error state
    container.innerHTML = `
        <div class="text-center py-8 w-64">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="ph ph-qr-code w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">QR Code Error</h3>
            <p class="text-sm text-gray-600 mb-4">Gambar tidak dapat dimuat</p>
            <button onclick="window.location.reload()" class="btn-primary text-sm px-4 py-2">
                Refresh
            </button>
        </div>
    `;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/user/payments/qr-code.blade.php ENDPATH**/ ?>