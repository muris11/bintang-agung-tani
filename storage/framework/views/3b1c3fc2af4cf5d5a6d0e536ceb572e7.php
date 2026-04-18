<?php
    $user = auth()->user();
    $joinDate = $user->created_at ? $user->created_at->locale('id')->isoFormat('D MMMM YYYY') : '-';
?>



<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="<?php echo e(route('user.dashboard')); ?>"
                                class="hover:text-gray-800 transition-colors">Dashboard</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                                <span class="text-gray-900 font-medium">Profil Saya</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Profil Saya</h1>
                <p class="text-gray-500 mt-1 text-sm">Kelola informasi data diri dan kontak Anda.</p>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="card p-5 md:p-8 mx-4 sm:mx-0 relative z-10 mt-6">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

                <!-- Kolom Kiri: Form Detail Profil -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- Avatar Section -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
                        <div
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white shrink-0 ring-2 ring-gray-100">
                            <img loading="lazy" src="<?php echo e($user->profile_photo_url); ?>" alt="<?php echo e($user->name); ?>"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="text-center sm:text-left space-y-2">
                            <h3 class="font-bold text-gray-900 text-2xl"><?php echo e($user->name); ?></h3>
                            <div
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-200 shadow-sm">
                                <i class="ph ph-crown ph-fill w-3.5 h-3.5 text-amber-500"></i>
                                Member Unggulan
                            </div>
                            <div class="mt-3 flex items-center justify-center sm:justify-start gap-3">
                                <label for="profile_photo"
                                    class="btn-secondary text-sm py-2 shadow-sm cursor-pointer inline-flex items-center justify-center">
                                    Ubah Foto
                                </label>
                                <?php if($user->profile_photo_path): ?>
                                    <form action="<?php echo e(route('user.profil.photo.destroy')); ?>" method="POST"
                                        onsubmit="return confirm('Hapus foto profil ini?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                            class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline transition-colors px-2 py-2">
                                            Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Form Inputs -->
                    <form action="<?php echo e(route('user.profil.update')); ?>" method="POST" enctype="multipart/form-data"
                        class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="md:col-span-2 space-y-1.5 text-left">
                            <label for="profile_photo" class="form-label block text-left">Foto Profil</label>
                            <input type="file" id="profile_photo" name="profile_photo"
                                accept="image/png,image/jpeg,image/jpg,image/webp" class="form-input w-full">
                            <p class="text-xs text-gray-500">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            <?php $__errorArgs = ['profile_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="space-y-1.5 md:col-span-2 text-left">
                            <label for="namaLengkap" class="form-label block text-left">Nama Lengkap</label>
                            <input type="text" id="namaLengkap" name="name" value="<?php echo e($user->name); ?>"
                                class="form-input w-full">
                        </div>

                        <div class="space-y-1.5 text-left">
                            <label for="email" class="form-label block text-left">Alamat Email</label>
                            <input type="email" id="email" name="email" value="<?php echo e($user->email); ?>"
                                class="form-input w-full">
                        </div>

                        <div class="space-y-1.5 text-left">
                            <label for="telepon" class="form-label block text-left">No. Telepon / WhatsApp</label>
                            <input type="tel" id="telepon" name="phone" value="<?php echo e($user->phone ?? ''); ?>"
                                class="form-input w-full">
                        </div>

                        <div class="space-y-1.5 md:col-span-2 text-left">
                            <label class="form-label block text-left">Alamat Utama</label>
                            <div class="form-input w-full min-h-[88px] bg-gray-50 text-gray-700 leading-relaxed">
                                <?php echo e($user->defaultAddress?->getCompleteAddressAttribute() ?? 'Alamat utama belum diatur. Silakan kelola di menu Alamat.'); ?>

                            </div>
                            <p class="text-xs text-gray-500">Alamat utama dikelola dari menu Alamat agar tidak dobel data.
                            </p>
                        </div>

                        <div
                            class="md:col-span-2 flex flex-col sm:flex-row items-center justify-between pt-6 mt-4 border-t border-gray-100 gap-4">
                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                <i class="ph ph-calendar-check w-4.5 h-4.5 text-gray-400"></i>
                                Bergabung sejak: <span class="font-bold text-gray-900"><?php echo e($joinDate); ?></span>
                            </div>
                            <button type="submit" class="btn-primary w-full sm:w-auto px-6 py-3 shadow-md">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Kolom Kanan: Summary Cards -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- Ringkasan Pesanan Card -->
                    <div class="card overflow-hidden shadow-sm">
                        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                            <i class="ph ph-chart-bar-line ph-bold w-5 h-5 text-primary-600"></i>
                            <h3 class="font-bold text-gray-900 text-sm tracking-wide uppercase">Statistik Belanja</h3>
                        </div>
                        <div class="p-5 space-y-4 text-sm">
                            <div class="flex items-start justify-between">
                                <span class="text-gray-500 font-medium">Total Pesanan Sukses</span>
                                <span
                                    class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded"><?php echo e(auth()->user()->orders()->where('status', 'completed')->count()); ?>

                                    Transaksi</span>
                            </div>
                            <div class="flex items-start justify-between">
                                <span class="text-gray-500 font-medium">Total Belanja (Bulan ini)</span>
                                <span
                                    class="font-bold text-gray-900"><?php echo e(auth()->user()->getFormattedMonthlySpending()); ?></span>
                            </div>

                            <div class="border-t border-dashed border-gray-200 auto-w-full my-4"></div>

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-gray-600 font-medium">Status Member</span>
                                <span class="font-bold text-amber-600 text-base">Unggulan <i
                                        class="ph ph-check-circle ph-fill w-4 h-4 inline text-amber-500"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Alamat Card -->
                    <div class="card p-5 shadow-sm bg-gradient-to-br from-white to-gray-50/50">
                        <h3 class="font-bold text-gray-900 text-base mb-4 flex items-center gap-2">
                            <i class="ph ph-address-book ph-bold w-5 h-5 text-gray-400"></i>
                            Info Kontak Utama
                        </h3>
                        <div class="space-y-3.5 text-sm text-gray-600">
                            <div class="flex items-start gap-3">
                                <i class="ph ph-envelope w-4.5 h-4.5 text-primary-600 shrink-0 mt-0.5 ph-fill"></i>
                                <span class="font-medium text-gray-800"><?php echo e($user->email); ?></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="ph ph-phone w-4.5 h-4.5 text-primary-600 shrink-0 mt-0.5 ph-fill"></i>
                                <span class="font-medium text-gray-800"><?php echo e($user->phone ?? '-'); ?></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="ph ph-map-pin w-4.5 h-4.5 text-primary-600 shrink-0 mt-0.5 ph-fill"></i>
                                <span
                                    class="leading-relaxed font-medium text-gray-800"><?php echo e($user->defaultAddress?->getCompleteAddressAttribute() ?? 'Alamat utama belum diisi'); ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/user/profil.blade.php ENDPATH**/ ?>