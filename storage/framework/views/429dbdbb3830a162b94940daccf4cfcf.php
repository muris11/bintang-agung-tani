<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'spinner',
    'size' => 'md',
    'text' => null,
    'fullScreen' => false,
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
    'type' => 'spinner',
    'size' => 'md',
    'text' => null,
    'fullScreen' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$sizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    'lg' => 'w-8 h-8',
    'xl' => 'w-12 h-12',
];

$textSizes = [
    'sm' => 'text-xs',
    'md' => 'text-sm',
    'lg' => 'text-base',
    'xl' => 'text-lg',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$textSizeClass = $textSizes[$size] ?? $textSizes['md'];
?>

<?php if($fullScreen): ?>
<div class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50">
<?php endif; ?>

<div class="flex flex-col items-center justify-center gap-3" role="status" aria-label="Loading">
    <?php if($type === 'spinner'): ?>
        <div class="inline-block <?php echo e($sizeClass); ?> border-2 border-gray-300 border-t-primary-600 rounded-full animate-spin" aria-hidden="true"></div>
    <?php elseif($type === 'skeleton'): ?>
        <div class="space-y-3 w-full max-w-md">
            <div class="skeleton-title"></div>
            <div class="skeleton-text"></div>
            <div class="skeleton-text w-3/4"></div>
        </div>
    <?php endif; ?>
    
    <?php if($text): ?>
        <span class="text-gray-500 <?php echo e($textSizeClass); ?>"><?php echo e($text); ?></span>
    <?php else: ?>
        <span class="sr-only">Loading...</span>
    <?php endif; ?>
</div>

<?php if($fullScreen): ?>
</div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\loading.blade.php ENDPATH**/ ?>