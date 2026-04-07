<?php $__env->startSection('title', 'Tambah User Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <a href="/admin/users" class="hover:text-primary-600 transition-colors">Kelola User</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Tambah Baru</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Tambah User Baru</h1>
            <p class="text-gray-500 mt-1 text-sm">Tambahkan pengguna baru ke sistem.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/users" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <form action="/admin/users" method="POST" class="max-w-2xl">
        <?php echo csrf_field(); ?>

        <div class="card p-6 space-y-6">
            <div class="flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                <i class="ph ph-user ph-fill w-5 h-5 text-primary-600"></i>
                <h2 class="text-lg font-bold text-gray-900">Informasi User</h2>
            </div>

            <!-- Error Messages -->
            <?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3">
                    <i class="ph ph-warning-circle w-5 h-5 mt-0.5"></i>
                    <ul class="list-disc list-inside text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="space-y-5">
                <div>
                    <label for="name" class="form-label mb-1.5 block">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" class="form-input w-full" placeholder="Masukkan nama lengkap" required>
                </div>

                <div>
                    <label for="email" class="form-label mb-1.5 block">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" class="form-input w-full" placeholder="nama@email.com" required>
                </div>

                <div>
                    <label for="phone" class="form-label mb-1.5 block">No. Telepon <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                    <input type="tel" id="phone" name="phone" value="<?php echo e(old('phone')); ?>" class="form-input w-full" placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label for="address" class="form-label mb-1.5 block">Alamat Lengkap <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                    <textarea id="address" name="address" rows="3" class="form-input w-full resize-y" placeholder="Masukkan alamat lengkap"><?php echo e(old('address')); ?></textarea>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="ph ph-lock-key ph-fill w-4 h-4 text-amber-500"></i>
                        Password
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="form-label mb-1.5 block">Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" class="form-input w-full" placeholder="••••••••" required>
                            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label mb-1.5 block">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input w-full" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="/admin/users" class="btn-secondary text-sm shadow-sm">Batal</a>
            <button type="submit" class="btn-primary text-sm shadow-md">
                <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan User
            </button>
        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/admin/tambah-user.blade.php ENDPATH**/ ?>