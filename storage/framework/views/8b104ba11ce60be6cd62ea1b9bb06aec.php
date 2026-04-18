<?php $__env->startSection('title', 'Notifikasi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-4 md:p-6 max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua notifikasi Anda</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <form action="<?php echo e(route('admin.notifications.mark-all-read')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
                    <i class="ph ph-check-circle w-4 h-4"></i>
                    Tandai Semua Dibaca
                </button>
            </form>

            <a href="<?php echo e(route('admin.notifications.unread')); ?>"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium">
                <i class="ph ph-bell-simple w-4 h-4"></i>
                Belum Dibaca
            </a>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <?php if($notifications->count() > 0): ?>
                <div class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 md:p-5 hover:bg-gray-50 transition-colors <?php echo e($notification->unread() ? 'bg-blue-50/30' : ''); ?>">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <?php if(isset($notification->data['icon'])): ?>
                                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                                            <i class="ph <?php echo e($notification->data['icon']); ?> w-5 h-5"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                                            <i class="ph ph-bell w-5 h-5"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo e($notification->data['title'] ?? 'Notifikasi'); ?>

                                    </p>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        <?php echo e($notification->data['message'] ?? 'Tidak ada pesan'); ?>

                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        <?php echo e($notification->created_at->diffForHumans()); ?>

                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if($notification->unread()): ?>
                                        <form action="<?php echo e(route('admin.notifications.mark-read', $notification->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                                title="Tandai dibaca">
                                                <i class="ph ph-check w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('admin.notifications.destroy', $notification->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                            <i class="ph ph-trash w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="p-4 border-t border-gray-100">
                    <?php echo e($notifications->links()); ?>

                </div>
            <?php else: ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ph ph-bell-slash w-8 h-8 text-gray-400"></i>
                    </div>
                    <p class="text-gray-500">Belum ada notifikasi</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\admin\notifications\index.blade.php ENDPATH**/ ?>