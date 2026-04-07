<?php
    $authName = auth()->user()->name ?? 'Admin';
    $avatarName = urlencode($authName);
?>

<!-- Mobile Overlay -->
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-gray-900/60 z-40 md:hidden transition-opacity duration-300"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="w-72 bg-white border-r border-gray-200 flex-shrink-0 fixed inset-y-0 left-0 z-40 md:z-30 transform transition-transform duration-300 ease-natural flex flex-col -translate-x-full md:translate-x-0 h-screen shadow-subtle"
    :class="{ 'translate-x-0': sidebarOpen }"
    role="navigation">

    <!-- Profile Section -->
    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50/50">
        <div class="flex items-center gap-3 w-full">
            <img loading="lazy" src="https://ui-avatars.com/api/?name=<?php echo e($avatarName); ?>&background=ecfdf5&color=059669" alt="<?php echo e($authName); ?>" class="w-10 h-10 rounded-xl object-cover shrink-0 ring-2 ring-white shadow-soft">
            <div class="min-w-0 flex-1">
                <h3 class="font-bold text-gray-900 text-sm leading-tight truncate"><?php echo e($authName); ?></h3>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-0.5 rounded-lg bg-primary-50 text-primary-700 font-medium text-[10px] border border-primary-100">
                    <i class="ph-fill ph-shield-check w-3 h-3"></i>
                    Administrator
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="px-2 py-3 flex-1 overflow-y-auto w-full" aria-label="Admin menu">
        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-1 px-3">Menu Utama</div>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/dashboard','icon' => 'ph-chart-pie-slice','active' => request()->is('admin/dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/dashboard','icon' => 'ph-chart-pie-slice','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/dashboard'))]); ?>Dashboard <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/produk','icon' => 'ph-package','active' => request()->is('admin/produk*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/produk','icon' => 'ph-package','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/produk*'))]); ?>Kelola Produk <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/kategori','icon' => 'ph-tag','active' => request()->is('admin/kategori*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/kategori','icon' => 'ph-tag','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/kategori*'))]); ?>Kelola Kategori <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/stok','icon' => 'ph-warehouse','active' => request()->is('admin/stok*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/stok','icon' => 'ph-warehouse','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/stok*'))]); ?>Kelola Stok <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Pengguna</div>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/users','icon' => 'ph-users','active' => request()->is('admin/users*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/users','icon' => 'ph-users','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/users*'))]); ?>Kelola User <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Transaksi</div>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/pesanan','icon' => 'ph-clipboard-text','active' => request()->is('admin/pesanan*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/pesanan','icon' => 'ph-clipboard-text','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/pesanan*'))]); ?>Pesanan Masuk <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/verifikasi','icon' => 'ph-seal-check','active' => request()->is('admin/verifikasi*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/verifikasi','icon' => 'ph-seal-check','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/verifikasi*'))]); ?>Verifikasi Pembayaran <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/payment-methods','icon' => 'ph-wallet','active' => request()->is('admin/payment-methods*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/payment-methods','icon' => 'ph-wallet','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/payment-methods*'))]); ?>Metode Pembayaran <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/scan','icon' => 'ph-scan','active' => request()->is('admin/scan*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/scan','icon' => 'ph-scan','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/scan*'))]); ?>Scan QR Pengambilan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>

        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 px-3">Sistem</div>
        <?php if (isset($component)) { $__componentOriginal25b36b426f30fd196f9d947e60e48c56 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal25b36b426f30fd196f9d947e60e48c56 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.sidebar-link','data' => ['href' => '/admin/settings','icon' => 'ph-gear','active' => request()->is('admin/settings*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.sidebar-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/admin/settings','icon' => 'ph-gear','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->is('admin/settings*'))]); ?>Pengaturan Tampilan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $attributes = $__attributesOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__attributesOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal25b36b426f30fd196f9d947e60e48c56)): ?>
<?php $component = $__componentOriginal25b36b426f30fd196f9d947e60e48c56; ?>
<?php unset($__componentOriginal25b36b426f30fd196f9d947e60e48c56); ?>
<?php endif; ?>
    </nav>

    <!-- Bottom Actions -->
    <div class="px-2 py-2 border-t border-gray-100 bg-gray-50/50">
        <form action="/logout" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit"
                class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all touch-target group">
                <i class="ph ph-power w-5 h-5 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/components/admin/sidebar.blade.php ENDPATH**/ ?>