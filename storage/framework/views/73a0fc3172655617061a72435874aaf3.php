<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'image' => null,
    'title',
    'price',
    'originalPrice' => null,
    'discount' => null,
    'rating' => null,
    'soldCount' => null,
    'stock' => null,
    'href' => '#',
    'productId' => null,
    'buttonVariant' => 'primary',
    'category' => null,
    'variant' => null,
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
    'image' => null,
    'title',
    'price',
    'originalPrice' => null,
    'discount' => null,
    'rating' => null,
    'soldCount' => null,
    'stock' => null,
    'href' => '#',
    'productId' => null,
    'buttonVariant' => 'primary',
    'category' => null,
    'variant' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$isOutOfStock = $stock === 0;
$isLowStock = $stock !== null && $stock <= 5 && $stock > 0;

// Build comprehensive alt text
$altText = $title;
if ($category) {
    $altText .= ' - ' . $category;
}
if ($variant) {
    $altText .= ' - ' . $variant;
}
?>

<div class="card-featured card-featured-hover card-interactive group flex flex-col h-full" tabindex="0" role="article" aria-label="<?php echo e($title); ?>">
    
    <div class="relative aspect-product bg-gray-50 overflow-hidden rounded-t-2xl">
        
        <?php if($discount): ?>
            <div class="absolute top-3 right-3 z-10 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg animate-bounce-subtle">
                -<?php echo e($discount); ?>%
            </div>
        <?php endif; ?>
        
        
        <button type="button" 
                class="btn absolute top-3 left-3 z-10 min-w-[44px] min-h-[44px] w-11 h-11 bg-white rounded-full flex items-center justify-center text-gray-400 shadow-md border border-gray-200 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-gray-50 hover:text-red-500 hover:scale-110 focus-visible:opacity-100"
                aria-label="Tambah ke wishlist"
                tabindex="0">
            <i class="ph ph-heart text-lg"></i>
        </button>
        
        
        <a href="<?php echo e($href); ?>" class="block w-full h-full overflow-hidden">
            <?php if($image): ?>
                <img src="<?php echo e($image); ?>" 
                     alt="<?php echo e($altText); ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                     loading="lazy"
                     width="400"
                     height="300">
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="ph ph-package text-3xl text-gray-400"></i>
                    </div>
                </div>
            <?php endif; ?>
        </a>
        
        
        <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0">
            <a href="<?php echo e($href); ?>" class="block w-full py-3 bg-white text-gray-900 text-sm font-semibold rounded-xl hover:bg-primary-50 transition-all duration-200 text-center shadow-lg flex items-center justify-center gap-2">
                <i class="ph ph-eye text-lg"></i>
                Lihat Detail
            </a>
        </div>
        
        
        <?php if($isLowStock): ?>
            <div class="absolute bottom-3 left-3 z-10 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
                <i class="ph ph-warning-circle"></i>
                Stok <?php echo e($stock); ?>

            </div>
        <?php elseif($isOutOfStock): ?>
            <div class="absolute inset-0 bg-white flex items-center justify-center z-20">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="ph ph-package-x text-gray-400 text-3xl"></i>
                    </div>
                    <span class="bg-gray-800 text-white text-sm font-bold px-4 py-2 rounded-xl">Stok Habis</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    
    <div class="p-4 flex flex-col flex-1">
        
        <div class="flex items-center gap-3 mb-3">
            <?php if($rating): ?>
                <div class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-lg">
                    <i class="ph-fill ph-star text-amber-400 text-sm"></i>
                    <span class="text-sm font-semibold text-amber-700"><?php echo e(number_format($rating, 1)); ?></span>
                </div>
            <?php endif; ?>
            <?php if($soldCount): ?>
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="ph ph-shopping-bag"></i>
                    Terjual <?php echo e($soldCount); ?>

                </span>
            <?php endif; ?>
        </div>
        
        
        <a href="<?php echo e($href); ?>" class="block mb-4 flex-1">
            <h3 class="text-gray-900 font-semibold text-sm leading-relaxed line-clamp-2 group-hover:text-primary-600 transition-colors">
                <?php echo e($title); ?>

            </h3>
        </a>
        
        
        <div class="flex items-end gap-2 mb-4">
            <span class="price-display text-primary-600 text-xl"><?php echo e($price); ?></span>
            <?php if($originalPrice): ?>
                <span class="price-original"><?php echo e($originalPrice); ?></span>
            <?php endif; ?>
        </div>
        
        
        <?php if($productId && !$isOutOfStock): ?>
            <form action="<?php echo e(route('user.cart.add')); ?>" method="POST" class="block mt-auto" x-data="{ loading: false }" @submit.prevent="loading = true; $el.submit();">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?php echo e($productId); ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" 
                        :disabled="loading"
                        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed hover:shadow-md active:scale-[0.98]"
                        tabindex="0">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            <i class="ph ph-plus-circle text-lg"></i>
                            Tambah ke Keranjang
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <i class="ph ph-spinner animate-spin text-lg"></i>
                            Menambahkan...
                        </span>
                    </template>
                </button>
            </form>
        <?php elseif($isOutOfStock): ?>
            <button disabled class="w-full py-3 px-4 bg-gray-100 text-gray-400 text-sm font-semibold rounded-xl cursor-not-allowed flex items-center justify-center gap-2 mt-auto" tabindex="0">
                <i class="ph ph-package-x text-lg"></i>
                Stok Habis
            </button>
        <?php else: ?>
            <a href="<?php echo e($href); ?>" class="block w-full py-3 px-4 bg-primary-50 text-primary-700 text-sm font-semibold rounded-xl hover:bg-primary-100 transition-all duration-200 text-center mt-auto" tabindex="0">
                Lihat Detail
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
/* Card Animations */
@keyframes bounce-subtle {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-bounce-subtle {
    animation: bounce-subtle 0.5s ease-in-out;
}

/* Product Image Zoom */
.product-card img {
    transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card:hover img {
    transform: scale(1.1);
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .animate-bounce-subtle {
        animation: none;
    }
    
    .product-card img {
        transition: none;
    }
    
    .product-card:hover img {
        transform: none;
    }
}
</style>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\product-card.blade.php ENDPATH**/ ?>