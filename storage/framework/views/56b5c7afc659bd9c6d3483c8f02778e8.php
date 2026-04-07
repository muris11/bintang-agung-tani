<?php $__env->startSection('title', 'Pilih Metode Pembayaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6 pb-12">
    <!-- Header -->
    <div class="animate-fade-in-up">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li><a href="/user/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/user/riwayat" class="hover:text-primary-600 transition-colors">Pesanan</a></div></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Pembayaran</span></div></li>
            </ol>
        </nav>
        <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pilih Metode Pembayaran</h1>
        <p class="text-gray-500 mt-1 text-sm">Order: <span class="font-mono font-semibold text-primary-600"><?php echo e($order->order_number); ?></span></p>
    </div>

    <!-- Order Summary Card -->
    <div class="card p-6 animate-fade-in-up delay-100">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ph ph-receipt w-5 h-5 text-primary-600"></i>
            Ringkasan Pesanan
        </h2>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Total Pembayaran</span>
                <span class="text-2xl font-bold text-primary-600"><?php echo e($order->getFormattedTotal()); ?></span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600">Status</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200">
                    <i class="ph ph-clock w-3.5 h-3.5"></i>
                    <?php echo e($order->getStatusLabel()); ?>

                </span>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="animate-fade-in-up delay-150">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ph ph-credit-card w-5 h-5 text-primary-600"></i>
            Metode Pembayaran Tersedia
        </h2>

        <?php if(isset($paymentMethods) && count($paymentMethods) > 0): ?>
            <form action="<?php echo e(route('user.payments.store-method', $order)); ?>" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                
                <div class="grid gap-3" role="radiogroup" aria-label="Pilih metode pembayaran">
                    <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="card p-4 cursor-pointer hover:border-primary-300 hover:bg-primary-50/20 transition-all group relative">
                            <input 
                                type="radio" 
                                name="payment_method_id" 
                                value="<?php echo e($method['id']); ?>" 
                                class="sr-only peer"
                                required
                                aria-describedby="method-<?php echo e($method['id']); ?>-info"
                            >
                            
                            <div class="flex items-start gap-4">
                                <!-- Radio indicator -->
                                <div class="mt-0.5 w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-primary-600 peer-checked:bg-primary-600 flex items-center justify-center shrink-0 transition-colors">
                                    <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </div>
                                
                                <!-- Method details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <?php if($method['logo_url']): ?>
                                            <img src="<?php echo e($method['logo_url']); ?>" alt="" class="h-8 w-auto object-contain" loading="lazy">
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                                <i class="ph ph-bank w-5 h-5"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="font-semibold text-gray-900"><?php echo e($method['name']); ?></h3>
                                            <p class="text-sm text-gray-500"><?php echo e($method['bank_name']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div id="method-<?php echo e($method['id']); ?>-info" class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3 mt-2">
                                        <p class="font-mono font-semibold text-gray-900"><?php echo e($method['account_number']); ?></p>
                                        <p>a.n. <?php echo e($method['account_name']); ?></p>
                                    </div>
                                    
                                    <?php if($method['instructions']): ?>
                                        <p class="text-xs text-gray-500 mt-2"><?php echo e($method['instructions']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Selected indicator -->
                            <div class="absolute inset-0 rounded-2xl border-2 border-transparent peer-checked:border-primary-600 pointer-events-none transition-colors"></div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php $__errorArgs = ['payment_method_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="alert-error mt-4" role="alert">
                        <i class="ph ph-warning-circle w-5 h-5 shrink-0"></i>
                        <span><?php echo e($message); ?></span>
                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-2">
                        <i class="ph ph-arrow-right w-5 h-5"></i>
                        Lanjutkan ke Upload Bukti
                    </button>
                    <a href="<?php echo e(route('user.orders.show', $order)); ?>" class="btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-state card">
                <div class="empty-state-icon">
                    <i class="ph ph-credit-card w-8 h-8"></i>
                </div>
                <h3 class="empty-state-title">Tidak Ada Metode Pembayaran</h3>
                <p class="empty-state-desc">Saat ini tidak ada metode pembayaran yang tersedia. Silakan hubungi admin.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/user/payments/select-method.blade.php ENDPATH**/ ?>