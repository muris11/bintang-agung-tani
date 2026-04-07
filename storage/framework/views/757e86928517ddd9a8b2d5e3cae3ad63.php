<?php
use App\Models\Setting;
?>



<?php $__env->startSection('title', 'Reset Password'); ?>

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
                    Reset Password<br>
                    <span class="text-primary-200">Akun Anda</span>
                </h2>
                <p class="text-primary-100 text-center mt-4 text-sm xl:text-base max-w-xs">
                    Buat password baru untuk mengamankan akun Anda
                </p>
            </div>
            
            <!-- Bottom: Security Info -->
            <div class="pt-6 border-t border-white/10">
                <div class="flex items-center gap-3 text-white/80">
                    <svg class="w-5 h-5 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="text-sm">Keamanan akun Anda adalah prioritas kami</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side: Reset Password Form -->
    <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-8 lg:p-12 xl:p-16 bg-gradient-to-b from-gray-50 via-white to-primary-50/30">
        
        <!-- Mobile Logo -->
        <div class="lg:hidden mb-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <img src="/images/logo.png" 
                     alt="<?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?>" 
                     class="w-full h-auto">
            </div>
            <h1 class="text-lg font-bold text-gray-900"><?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?></h1>
            <p class="text-primary-600 text-sm">Reset Password</p>
        </div>
        
        <!-- Form Container -->
        <div class="w-full max-w-[400px]">
            
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Buat Password Baru</h2>
                <p class="text-gray-500">Masukkan password baru untuk akun Anda</p>
            </div>
            
            <!-- Success Message -->
            <?php if(session('status')): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div class="text-sm text-green-700"><?php echo e(session('status')); ?></div>
                </div>
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
            
            <!-- Reset Password Form -->
            <form action="/reset-password" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                
                <!-- Hidden Token -->
                <input type="hidden" name="token" value="<?php echo e($token); ?>">
                
                <!-- Email Field (Readonly) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" value="<?php echo e($email); ?>" readonly
                               class="w-full pl-12 pr-4 py-3.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-700 focus:outline-none cursor-not-allowed">
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Email akun Anda</p>
                </div>
                
                <!-- New Password Field -->
                <div x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" 
                               placeholder="Minimal 8 karakter" required minlength="8"
                               class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Minimal 8 karakter dengan kombinasi huruf dan angka</p>
                </div>
                
                <!-- Confirm Password Field -->
                <div x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" 
                               placeholder="Ulangi password baru" required minlength="8"
                               class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Pastikan password sama dengan di atas</p>
                </div>
                
                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Reset Password</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </form>
            
            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <a href="/login" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>