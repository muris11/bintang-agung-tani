<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src',
    'alt',
    'class' => '',
    'loading' => 'lazy',
    'width' => null,
    'height' => null,
    'srcset' => null,
    'sizes' => null,
    'objectFit' => 'cover',
    'placeholder' => null,
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
    'src',
    'alt',
    'class' => '',
    'loading' => 'lazy',
    'width' => null,
    'height' => null,
    'srcset' => null,
    'sizes' => null,
    'objectFit' => 'cover',
    'placeholder' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
// Determine sizes attribute if not provided
if (!$sizes && $width) {
    $sizes = "(max-width: 640px) 100vw, (max-width: 1024px) 50vw, {$width}px";
}

// Build srcset if array provided
$srcsetAttr = '';
if (is_array($srcset)) {
    $srcsetParts = [];
    foreach ($srcset as $size => $url) {
        $srcsetParts[] = "{$url} {$size}w";
    }
    $srcsetAttr = implode(', ', $srcsetParts);
} elseif ($srcset) {
    $srcsetAttr = $srcset;
}

// Object fit class
$objectFitClass = match($objectFit) {
    'contain' => 'object-contain',
    'fill' => 'object-fill',
    'none' => 'object-none',
    'scale-down' => 'object-scale-down',
    default => 'object-cover',
};

// Placeholder color
$placeholderColor = $placeholder ?? 'bg-gray-100';
?>

<div class="relative <?php echo e($placeholderColor); ?> <?php echo e($class); ?>" 
     style="<?php echo e($width ? 'max-width: ' . $width . 'px;' : ''); ?>">
    <?php if($src): ?>
        <img 
            src="<?php echo e($src); ?>"
            alt="<?php echo e($alt); ?>"
            class="w-full h-full <?php echo e($objectFitClass); ?> transition-opacity duration-300"
            loading="<?php echo e($loading); ?>"
            decoding="async"
            <?php echo e($width ? 'width=' . $width : ''); ?>

            <?php echo e($height ? 'height=' . $height : ''); ?>

            <?php echo e($srcsetAttr ? 'srcset="' . $srcsetAttr . '"' : ''); ?>

            <?php echo e($sizes ? 'sizes="' . $sizes . '"' : ''); ?>

            onload="this.classList.add('opacity-100'); this.previousElementSibling?.remove();"
            onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
        >
    <?php endif; ?>
    
    
    <div class="<?php echo e($src ? 'hidden' : 'flex'); ?> absolute inset-0 items-center justify-center bg-gray-50">
        <i class="ph ph-image text-gray-300 w-10 h-10"></i>
    </div>
</div>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\optimized-image.blade.php ENDPATH**/ ?>