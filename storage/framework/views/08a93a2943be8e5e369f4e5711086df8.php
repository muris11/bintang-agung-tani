<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['active' => false, 'icon']));

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

foreach (array_filter((['active' => false, 'icon']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$classes = $active
    ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100/40 text-primary-700 font-medium transition-all duration-200 border border-primary-100 shadow-subtle'
    : 'flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary-600 font-medium transition-all duration-200 border border-transparent hover:border-gray-100';
?>

<a <?php echo e($attributes->merge(['class' => $classes])); ?> <?php echo e($active ? 'aria-current="page"' : ''); ?>>
    <?php if(isset($icon)): ?>
        <i class="ph <?php echo e($icon); ?> w-5 h-5 shrink-0 <?php echo e($active ? 'text-primary-600 ph-bold' : 'text-gray-500'); ?>" aria-hidden="true"></i>
    <?php endif; ?>
    <span class="text-sm font-medium"><?php echo e($slot); ?></span>
</a>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/components/admin/sidebar-link.blade.php ENDPATH**/ ?>