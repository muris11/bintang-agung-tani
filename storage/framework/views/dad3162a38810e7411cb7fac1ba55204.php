<?php
use App\Models\Setting;
?>



<?php $__env->startSection('title', 'Lupa Password'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex flex-col lg:flex-row bg-white">
    
    <!-- Left Side: Brand Showcase with Logo -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/3 relative overflow-hidden">
        <!-- Background: Same gradient as navbar -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-800"></div>
        
        <!-- Subtle Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.2) 1px, transparent 0); background-size: 40px 40px;"></div>
        
        <!-- Content Container -->
        <div class="relative z-10 flex flex-col justify-between h-full p-8 xl:p-12">
            
            <!-- Top: Small Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <span class="text-white font-medium text-sm tracking-wide"><?php echo e(strtoupper(Setting::get('store_name', 'Bintang Agung Tani'))); ?></span>
            </div>
            
            <!-- Center: Logo Display -->
            <div class="flex-1 flex flex-col justify-center items-center py-8">
                <div class="w-full max-w-[260px] xl:max-w-[300px] mb-6">
                    <img src="/images/logo.png" 
                         alt="<?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?>" 
                         class="w-full h-auto"
                         style="filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                </div>
                <h2 class="text-2xl xl:text-3xl font-bold text-white text-center leading-tight">
                    Lupa Password?<br>
                    <span class="text-primary-200">Jangan Khawatir</span>
                </h2>
                <p class="text-primary-100 text-center mt-4 text-sm xl:text-base max-w-xs">
                    Kami akan membantu Anda mengembalikan akses ke akun
                </p>
            </div>
            
            <!-- Bottom: Security Info -->
            <div class="pt-6 border-t border-white/10">
                <div class="flex items-center gap-3 text-white/80">
                    <svg class="w-5 h-5 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="text-sm">Link reset berlaku selama 60 menit</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side: Forgot Password Form -->
    <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-8 lg:p-12 xl:p-16 bg-gradient-to-b from-gray-50 via-white to-primary-50/30">
        
        <!-- Mobile Logo -->
        <div class="lg:hidden mb-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <img src="/images/logo.png" 
                     alt="<?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?>" 
                     class="w-full h-auto">
            </div>
            <h1 class="text-lg font-bold text-gray-900"><?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?></h1>
            <p class="text-primary-600 text-sm">Lupa Password</p>
        </div>
        
        <!-- Form Container -->
        <div class="w-full max-w-[400px]" x-data="{ sent: <?php echo e(session('status') ? 'true' : 'false'); ?> }">
            
            <!-- Header -->
            <div class="mb-8" x-show="!sent">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
                <p class="text-gray-500">Masukkan email Anda untuk menerima link reset password</p>
            </div>
            
            <!-- Success State Header -->
            <div class="mb-8 text-center" x-show="sent" style="display: none;">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Link Terkirim!</h2>
                <p class="text-gray-500">Silakan cek email Anda untuk melanjutkan</p>
            </div>
            
            <!-- Success Message -->
            <?php if(session('status')): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-green-700"><?php echo e(session('status')); ?></div>
                    </div>
                </div>
                
                <!-- Development: Show Reset Link -->
                <?php if(session('reset_url')): ?>
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="text-sm text-blue-800 mb-2 font-semibold">Link Reset Password (Dev Mode):</div>
                        <a href="<?php echo e(session('reset_url')); ?>" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">
                            <?php echo e(session('reset_url')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Error Messages -->
            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-red-700"><?php echo e($errors->first()); ?></div>
                </div>
            <?php endif; ?>
            
            <!-- Forgot Password Form -->
            <form x-show="!sent" action="/forgot-password" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                
                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Terdaftar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" 
                               placeholder="nama@email.com" required
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Masukkan email yang terdaftar di akun Anda</p>
                </div>
                
                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Kirim Link Reset</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
            
            <!-- Tips Section (Only show when not sent) -->
            <div x-show="!sent" class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-blue-800 text-sm mb-1">Petunjuk</div>
                        <ul class="text-xs text-blue-600 space-y-1">
                            <li>• Pastikan email yang dimasukkan benar</li>
                            <li>• Link reset berlaku selama 60 menit</li>
                            <li>• Periksa folder spam/junk jika tidak menemukan email</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Back to Login -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <a href="/login" class="flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition-colors group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke halaman login
                </a>
            </div>
            
            <!-- Footer -->
            <p class="text-center text-xs text-gray-400 mt-8">
                © <?php echo e(date('Y')); ?> <?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?>

            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>