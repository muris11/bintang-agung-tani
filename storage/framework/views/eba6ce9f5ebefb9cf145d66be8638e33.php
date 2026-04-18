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
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-xl bg-primary-700 text-white font-bold transition-all duration-300 shadow-sm translate-x-1'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-primary-50 hover:text-primary-700 font-medium transition-all duration-300 hover:translate-x-1 active:scale-95 group';
?>

<a <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(isset($icon)): ?>
        <<?php echo e($icon); ?> class="text-[1.125rem] shrink-0 <?php echo e($active ? 'text-white' : 'text-gray-400 group-hover:text-primary-700 transition-colors duration-300'); ?>"></<?php echo e($icon); ?>>
    <?php endif; ?>
    <span class="text-[0.9rem]"><?php echo e($slot); ?></span>
</a>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\sidebar-link.blade.php ENDPATH**/ ?>