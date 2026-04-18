<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12">

    <!-- Breadcrumb -->
    <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="<?php echo e(route('user.dashboard')); ?>" class="hover:text-primary-600 transition-colors">Dashboard</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                    <a href="<?php echo e(route('user.produk.index')); ?>" class="hover:text-primary-600 transition-colors">Produk</a>
                </div>
            </li>
            <?php if($product->category): ?>
            <li>
                <div class="flex items-center">
                    <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                    <a href="<?php echo e(route('user.produk.index', ['kategori' => $product->category->slug])); ?>" class="hover:text-primary-600 transition-colors"><?php echo e($product->category->name); ?></a>
                </div>
            </li>
            <?php endif; ?>
            <li>
                <div class="flex items-center">
                    <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                    <span class="text-gray-900 font-medium truncate max-w-xs"><?php echo e($product->name); ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="card p-6 md:p-8">
        
        <!-- Header Info -->
        <div class="mb-6 border-b border-gray-100 pb-6 flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-3 flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight"><?php echo e($product->name); ?></h1>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <?php if($product->category): ?>
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-lg font-semibold text-xs"><?php echo e($product->category->name); ?></span>
                    <?php endif; ?>
                    <div class="flex items-center gap-1 text-amber-500">
                        <?php for($i = 0; $i < 5; $i++): ?>
                            <i class="ph ph-star ph-fill w-4 h-4"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-500"><?php echo e($product->total_sold ?? 0); ?> Terjual</span>
                </div>
            </div>
            
            <div class="text-left md:text-right shrink-0">
                <div class="text-3xl font-bold text-primary-600 mb-1"><?php echo e($product->getFormattedPrice()); ?></div>
                <?php if($product->original_price && $product->original_price > $product->price): ?>
                    <div class="text-gray-400 line-through text-sm">Rp <?php echo e(number_format($product->original_price, 0, ',', '.')); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Image + Tabs -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Gallery -->
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="w-full bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-4 sm:p-6 min-h-[320px] sm:min-h-[400px]">
                        <?php
                            $images = $product->getImages() ?? [];
                            $firstImage = $images[0] ?? $product->getFirstImage();
                            $altText = $product->name . ($product->category ? ' - ' . $product->category->name : '');
                        ?>
                        <?php if($firstImage): ?>
                            <img loading="lazy" src="<?php echo e($firstImage); ?>" alt="<?php echo e($altText); ?>" class="max-w-full max-h-[280px] sm:max-h-[360px] w-auto h-auto object-contain rounded-xl" id="main-product-image">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ph ph-image ph-fill w-24 h-24 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thumbnails -->
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        <?php $__empty_1 = true; $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <button class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 rounded-lg border-2 <?php echo e($index === 0 ? 'border-primary-500' : 'border-gray-200 hover:border-primary-500'); ?> bg-gray-50 p-2 flex items-center justify-center focus:outline-none transition-colors" onclick="document.getElementById('main-product-image').src='<?php echo e($image); ?>'">
                                <img loading="lazy" src="<?php echo e($image); ?>" alt="<?php echo e($product->name); ?> - <?php echo e($index + 1); ?>" class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg">
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if($product->getFirstImage()): ?>
                                <button class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 rounded-lg border-2 border-primary-500 bg-gray-50 p-2 flex items-center justify-center focus:outline-none">
                                    <img loading="lazy" src="<?php echo e($product->getFirstImage()); ?>" alt="<?php echo e($product->name); ?>" class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg">
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabs -->
                <div x-data="{ tab: 'deskripsi' }" class="mt-6">
                    <div class="flex gap-6 border-b border-gray-200 text-sm font-semibold">
                        <button @click="tab = 'deskripsi'" 
                                :class="tab === 'deskripsi' ? 'border-primary-500 text-primary-600 border-b-2 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3 transition-colors'">
                            Deskripsi
                        </button>
                        <button @click="tab = 'spesifikasi'" 
                                :class="tab === 'spesifikasi' ? 'border-primary-500 text-primary-600 border-b-2 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3 transition-colors'">
                            Spesifikasi
                        </button>
                    </div>

                    <div class="py-6 text-gray-600 text-sm leading-relaxed">
                        <div x-show="tab === 'deskripsi'" x-cloak>
                            <p><?php echo e($product->description ?: 'Tidak ada deskripsi produk.'); ?></p>
                        </div>
                        
                        <div x-show="tab === 'spesifikasi'" x-cloak style="display: none;">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <span class="text-gray-500 text-xs uppercase tracking-wider">SKU</span>
                                    <p class="font-semibold text-gray-900 mt-1"><?php echo e($product->sku ?? '-'); ?></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <span class="text-gray-500 text-xs uppercase tracking-wider">Berat</span>
                                    <p class="font-semibold text-gray-900 mt-1"><?php echo e($product->weight); ?> kg</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <span class="text-gray-500 text-xs uppercase tracking-wider">Satuan</span>
                                    <p class="font-semibold text-gray-900 mt-1"><?php echo e($product->unit); ?></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl">
                                    <span class="text-gray-500 text-xs uppercase tracking-wider">Stok</span>
                                    <p class="font-semibold <?php echo e($product->stock > 0 ? 'text-primary-600' : 'text-red-500'); ?> mt-1"><?php echo e($product->stock); ?> unit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Action Card -->
                <div class="card p-6" x-data="{ qty: 1, maxStock: <?php echo e($product->stock); ?> }">
                    
                    <!-- Quantity Selector -->
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-sm font-medium text-gray-700">Jumlah:</span>
                        <div class="flex items-center border border-gray-300 rounded-lg bg-white overflow-hidden">
                            <button @click="if(qty > 1) qty--" type="button" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors border-r border-gray-300">
                                <i class="ph ph-minus w-4 h-4"></i>
                            </button>
                            <input type="number" x-model="qty" min="1" :max="maxStock" class="w-12 h-10 text-center text-gray-900 font-semibold border-none p-0 focus:ring-0">
                            <button @click="if(qty < maxStock) qty++" type="button" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors border-l border-gray-300">
                                <i class="ph ph-plus w-4 h-4"></i>
                            </button>
                        </div>
                        <span class="text-sm text-gray-500">Stok: <span class="font-semibold text-gray-900"><?php echo e($product->stock); ?></span></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <form action="<?php echo e(route('user.cart.add')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                            <input type="hidden" name="quantity" x-model="qty">
                            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2 py-3">
                                <i class="ph ph-shopping-cart w-5 h-5"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>
                        
                        <a href="<?php echo e(route('user.checkout.index')); ?>" class="btn-secondary w-full flex items-center justify-center gap-2 py-3">
                            <i class="ph ph-credit-card w-5 h-5"></i>
                            Beli Sekarang
                        </a>
                    </div>

                    <!-- Help Link -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3 text-sm justify-center">
                            <div class="bg-primary-100 text-primary-600 p-2 rounded-full">
                                <i class="ph ph-chat-circle-dots ph-fill w-4 h-4"></i>
                            </div>
                            <span class="text-gray-600">Butuh bantuan?</span>
                            <a href="<?php echo e(route('user.bantuan')); ?>" class="font-semibold text-primary-600 hover:text-primary-700">Hubungi Kami</a>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-primary-50/40 to-primary-50/20">
                        <h3 class="font-bold text-gray-900 text-sm">Produk Terkait</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(route('user.produk.show', $related->slug)); ?>" class="flex items-center gap-4 hover:bg-gray-50 transition-colors p-2 -m-2 rounded-lg group">
                                <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg shrink-0 flex items-center justify-center p-1.5">
                                    <?php if($related->getFirstImage()): ?>
                                        <img loading="lazy" src="<?php echo e($related->getFirstImage()); ?>" alt="<?php echo e($related->name); ?>" class="w-full h-full object-cover rounded-md">
                                    <?php else: ?>
                                        <i class="ph ph-image ph-fill w-6 h-6 text-gray-300"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800 text-sm truncate group-hover:text-primary-600 transition-colors"><?php echo e($related->name); ?></h4>
                                    <div class="text-primary-600 font-bold text-sm mt-1"><?php echo e($related->getFormattedPrice()); ?></div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-4 text-gray-500 text-sm">
                                <p>Tidak ada produk terkait</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\user\detail-produk.blade.php ENDPATH**/ ?>