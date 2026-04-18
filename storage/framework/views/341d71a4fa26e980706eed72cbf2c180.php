
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'magnifying-glass',
    'title' => 'Tidak Ada Data',
    'description' => 'Maaf, kami tidak menemukan data yang sesuai.',
    'actionText' => null,
    'actionUrl' => null,
    'actionHref' => null,
    'actionIcon' => 'arrow-counter-clockwise'
]));

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

foreach (array_filter(([
    'icon' => 'magnifying-glass',
    'title' => 'Tidak Ada Data',
    'description' => 'Maaf, kami tidak menemukan data yang sesuai.',
    'actionText' => null,
    'actionUrl' => null,
    'actionHref' => null,
    'actionIcon' => 'arrow-counter-clockwise'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$url = $actionUrl ?? $actionHref;
?>

<div class="col-span-full py-16">
    <div class="flex flex-col items-center justify-center text-center max-w-md mx-auto">
        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
            <i class="ph ph-<?php echo e($icon); ?> text-5xl text-gray-300"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo e($title); ?></h3>
        <p class="text-gray-500 mb-6"><?php echo e($description); ?></p>
        <?php if($actionText && $url): ?>
            <a href="<?php echo e($url); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="ph ph-<?php echo e($actionIcon); ?> text-lg"></i>
                <?php echo e($actionText); ?>

            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\empty-state.blade.php ENDPATH**/ ?>