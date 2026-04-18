

<?php $__env->startSection('title', 'Ubah Password'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4 md:mt-0 relative z-10 w-full">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="<?php echo e(route('user.dashboard')); ?>" class="hover:text-primary-600 transition-colors">Beranda</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="<?php echo e(route('user.profil.show')); ?>" class="hover:text-primary-600 transition-colors">Akun Saya</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Ubah Password</span></div></li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Kemanan Akun</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="<?php echo e(route('user.profil.show')); ?>" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali ke Profil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        <!-- Left Sidebar Navigation -->
        <div class="lg:col-span-3 space-y-2">
            <div class="card p-3">
                <nav class="flex flex-col space-y-1">
                    <a href="<?php echo e(route('user.profil.show')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        <i class="ph ph-user-circle ph-duotone w-5 h-5 text-gray-400"></i>
                        Profil Saya
                    </a>
                    <a href="<?php echo e(route('user.profil.password.form')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-primary-700 bg-primary-50 rounded-xl transition-colors relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary-500 rounded-r-full"></div>
                        <i class="ph ph-lock-key ph-duotone w-5 h-5 text-primary-500"></i>
                        Ubah Password
                    </a>
                    <a href="<?php echo e(route('user.orders.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        <i class="ph ph-receipt ph-duotone w-5 h-5 text-gray-400"></i>
                        Riwayat Belanja
                    </a>
                </nav>
            </div>
            
            <div class="card p-5 bg-gradient-to-br from-primary-50 to-white border-primary-100">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                        <i class="ph ph-shield-check ph-fill w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Tips Keamanan</h4>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Gunakan kombinasi huruf, angka, dan simbol untuk password yang kuat.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Form Area -->
        <div class="lg:col-span-9 space-y-6">
            <div class="card p-0 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-200 text-gray-600 flex items-center justify-center">
                            <i class="ph ph-password ph-fill w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Formulir Ganti Password</h2>
                            <p class="text-xs text-gray-500 font-medium">Pastikan Anda mengingat password baru Anda dengan baik.</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 md:p-8">
                    <form action="<?php echo e(route('user.profil.password')); ?>" method="POST" class="max-w-xl space-y-6" x-data="{ showPass1: false, showPass2: false, showPass3: false }">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <!-- Password Lama -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Password Lama <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-lock-key w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input :type="showPass1 ? 'text' : 'password'" name="current_password" placeholder="Masukkan password lama Anda" class="form-input w-full pl-11 pr-12 h-12 text-sm bg-gray-50 focus:bg-white transition-colors" required>
                                <button type="button" @click="showPass1 = !showPass1" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none h-full">
                                    <i class="ph ph-eye w-5 h-5" x-show="!showPass1" x-cloak></i>
                                    <i class="ph ph-eye-slash w-5 h-5" x-show="showPass1" x-cloak style="display: none;"></i>
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Password Baru -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Password Baru <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-lock-key w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input :type="showPass2 ? 'text' : 'password'" name="password" placeholder="Buat password baru" class="form-input w-full pl-11 pr-12 h-12 text-sm bg-gray-50 focus:bg-white transition-colors" required minlength="8">
                                <button type="button" @click="showPass2 = !showPass2" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none h-full">
                                    <i class="ph ph-eye w-5 h-5" x-show="!showPass2" x-cloak></i>
                                    <i class="ph ph-eye-slash w-5 h-5" x-show="showPass2" x-cloak style="display: none;"></i>
                                </button>
                            </div>
                            
                            <!-- Password Strength Indicator (Visual Only) -->
                            <div class="flex gap-1 mt-2">
                                <div class="h-1.5 w-full bg-red-500 rounded-full"></div>
                                <div class="h-1.5 w-full bg-orange-500 rounded-full"></div>
                                <div class="h-1.5 w-full bg-gray-200 rounded-full"></div>
                                <div class="h-1.5 w-full bg-gray-200 rounded-full"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Sandi harus terdiri dari minimal 8 karakter.</p>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="space-y-2 pb-4">
                            <label class="block text-sm font-bold text-gray-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ph ph-lock-key w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input :type="showPass3 ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password baru" class="form-input w-full pl-11 pr-12 h-12 text-sm bg-gray-50 focus:bg-white transition-colors" required>
                                <button type="button" @click="showPass3 = !showPass3" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none h-full">
                                    <i class="ph ph-eye w-5 h-5" x-show="!showPass3" x-cloak></i>
                                    <i class="ph ph-eye-slash w-5 h-5" x-show="showPass3" x-cloak style="display: none;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                            <button type="submit" class="btn-primary flex-1 sm:flex-none sm:w-48 h-12 shadow-md text-sm">
                                Perbarui Password
                            </button>
                            <a href="<?php echo e(route('user.profil.show')); ?>" class="btn-secondary h-12 px-6 text-sm border-gray-200 inline-flex items-center justify-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\user\ubah-password.blade.php ENDPATH**/ ?>