
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'text',
    'lines' => 3,
    'class' => ''
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
    'type' => 'text',
    'lines' => 3,
    'class' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="animate-pulse <?php echo e($class); ?>" role="status" aria-label="Loading">
    <?php switch($type):
        case ('card'): ?>
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100">
                <div class="aspect-[4/5] bg-gray-200"></div>
                <div class="p-4 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="flex items-center justify-between pt-2">
                        <div class="h-6 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-8 bg-gray-200 rounded-lg w-24"></div>
                    </div>
                </div>
            </div>
            <?php break; ?>
            
        <?php case ('image'): ?>
            <div class="aspect-square bg-gray-200 rounded-2xl"></div>
            <?php break; ?>
            
        <?php case ('avatar'): ?>
            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
            <?php break; ?>
            
        <?php case ('product-grid'): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php for($i = 0; $i < 10; $i++): ?>
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100">
                        <div class="aspect-[4/5] bg-gray-200"></div>
                        <div class="p-4 space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                            <div class="flex items-center justify-between pt-2">
                                <div class="h-5 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-8 bg-gray-200 rounded-lg w-20"></div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <?php break; ?>
            
        <?php case ('dashboard-stats'): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php for($i = 0; $i < 4; $i++): ?>
                    <div class="bg-white rounded-xl p-4 border border-gray-100 space-y-3">
                        <div class="h-8 w-8 bg-gray-200 rounded-lg"></div>
                        <div class="h-6 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    </div>
                <?php endfor; ?>
            </div>
            <?php break; ?>
            
        <?php case ('banner'): ?>
            <div class="w-full h-48 md:h-64 bg-gray-200 rounded-2xl"></div>
            <?php break; ?>
            
        <?php case ('text'): ?>
        <?php default: ?>
            <div class="space-y-2">
                <?php for($i = 0; $i < $lines; $i++): ?>
                    <div class="h-4 bg-gray-200 rounded <?php echo e($i === $lines - 1 ? 'w-2/3' : 'w-full'); ?>"></div>
                <?php endfor; ?>
            </div>
            <?php break; ?>
    <?php endswitch; ?>
    <span class="sr-only">Loading...</span>
</div>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\skeleton.blade.php ENDPATH**/ ?>