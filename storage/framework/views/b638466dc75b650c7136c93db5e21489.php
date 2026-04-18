<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['type' => 'user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$navItems = $type === 'admin' ? [
    ['label' => 'Dashboard', 'icon' => 'ph-chart-pie-slice', 'href' => '/admin/dashboard'],
    ['label' => 'Produk', 'icon' => 'ph-package', 'href' => '/admin/produk'],
    ['label' => 'Pesanan', 'icon' => 'ph-clipboard-text', 'href' => '/admin/pesanan'],
    ['label' => 'Akun', 'icon' => 'ph-user', 'href' => '/admin/profile'],
] : [
    ['label' => 'Dashboard', 'icon' => 'ph-squares-four', 'href' => '/user/dashboard'],
    ['label' => 'Produk', 'icon' => 'ph-storefront', 'href' => '/user/produk'],
    ['label' => 'Keranjang', 'icon' => 'ph-shopping-cart', 'href' => '/user/keranjang'],
    ['label' => 'Akun', 'icon' => 'ph-user', 'href' => '/user/profile'],
];
?>

<!-- Mobile Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden safe-area-inset-bottom">
    <div class="flex items-center justify-around h-16">
        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = request()->is($item['href'] . '*') || request()->is($item['href']);
            ?>
            <a href="<?php echo e($item['href']); ?>"
               class="flex flex-col items-center justify-center flex-1 h-full relative transition-colors duration-200 <?php echo e($isActive ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600'); ?>">

                <div class="relative">
                    <i class="ph <?php echo e($item['icon']); ?> text-2xl <?php echo e($isActive ? 'ph-fill' : 'ph'); ?>"></i>

                    <?php if($item['label'] === 'Keranjang' && isset($cartCount) && $cartCount > 0): ?>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            <?php echo e($cartCount > 9 ? '9+' : $cartCount); ?>

                        </span>
                    <?php endif; ?>
                </div>

                <span class="text-[10px] font-medium mt-0.5 <?php echo e($isActive ? 'text-primary-600' : ''); ?>"><?php echo e($item['label']); ?></span>

                <?php if($isActive): ?>
                    <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-primary-500 rounded-full"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</nav>

<!-- Spacer for mobile bottom nav -->
<div class="h-16 md:hidden"></div>

<style>
    /* Safe area support for modern mobile devices */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .safe-area-inset-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
    }
</style>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\mobile-nav.blade.php ENDPATH**/ ?>