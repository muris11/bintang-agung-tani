<?php
$authUser = auth()->user();
?>



<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="relative bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl overflow-hidden shadow-lg">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="ph ph-hand-waving text-white text-2xl"></i>
                    </div>
                    <span class="text-white/80 font-medium">Selamat Datang Kembali</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    <?php echo e(explode(' ', $authUser->name)[0]); ?>!
                </h1>
                <p class="text-white/80 text-lg mb-6 max-w-xl">
                    Temukan produk pertanian berkualitas untuk hasil panen terbaik Anda.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('user.produk.index')); ?>" class="btn bg-white text-primary-700 hover:bg-primary-50 border-0 shadow-lg">
                        <i class="ph ph-magnifying-glass w-5 h-5"></i>
                        Jelajahi Produk
                    </a>
                    <a href="<?php echo e(route('user.orders.index')); ?>" class="btn bg-white/20 text-white hover:bg-white/30 border border-white/30">
                        <i class="ph ph-receipt w-5 h-5"></i>
                        Lihat Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="ph ph-shopping-cart text-emerald-600 text-xl"></i>
                </div>
                <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Real-time</span>
            </div>
            <div class="space-y-1">
                <p class="text-2xl font-bold text-gray-900"><?php echo e($cartCount ?? 0); ?></p>
                <p class="text-sm text-gray-500">Produk di Keranjang</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('user.cart.index')); ?>" class="text-sm font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    Lihat Keranjang 
                    <i class="ph ph-arrow-right w-4 h-4"></i>
                </a>
            </div>
        </div>
        
        
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="ph ph-clock text-amber-600 text-xl"></i>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">Menunggu</span>
            </div>
            <div class="space-y-1">
                <p class="text-2xl font-bold text-gray-900"><?php echo e($pendingPaymentCount ?? 0); ?></p>
                <p class="text-sm text-gray-500">Pembayaran Pending</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">Rp <?php echo e(number_format($pendingPaymentTotal ?? 0, 0, ',', '.')); ?></span>
                </p>
            </div>
        </div>
        
        
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="ph ph-arrows-clockwise text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Diproses</span>
            </div>
            <div class="space-y-1">
                <p class="text-2xl font-bold text-gray-900"><?php echo e($processingCount ?? 0); ?></p>
                <p class="text-sm text-gray-500">Pesanan Aktif</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">Sedang dipersiapkan</p>
            </div>
        </div>
        
        
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="ph ph-wallet text-purple-600 text-xl"></i>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold <?php echo e(($growthPercentage ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600'); ?> bg-gray-50 px-2 py-1 rounded-lg">
                    <i class="ph ph-trend-up w-3 h-3"></i>
                    <?php echo e(($growthPercentage ?? 0) >= 0 ? '+' : ''); ?><?php echo e($growthPercentage ?? 0); ?>%
                </div>
            </div>
            <div class="space-y-1">
                <p class="text-xl font-bold text-gray-900">Rp <?php echo e(number_format($totalSpentThisMonth ?? 0, 0, ',', '.')); ?></p>
                <p class="text-sm text-gray-500">Belanja Bulan Ini</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">vs bulan lalu</p>
            </div>
        </div>
    </section>
    
    
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Kategori Produk</h2>
                <p class="text-gray-500 text-sm">Jelajahi produk berdasarkan kategori</p>
            </div>
            <a href="<?php echo e(route('user.produk.index')); ?>" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Lihat Semua
                <i class="ph ph-arrow-right w-4 h-4"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-3">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('user.produk.index', ['kategori' => $category->slug])); ?>" class="flex flex-col items-center gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-primary-200 hover:shadow-md transition-all duration-300 group">
                <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                    <i class="ph <?php echo e($category->icon); ?> text-xl text-primary-600"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700 text-center line-clamp-1"><?php echo e($category->name); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    
    
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Produk Terlaris</h2>
                <p class="text-gray-500 text-sm">Produk paling diminati bulan ini</p>
            </div>
            <a href="<?php echo e(route('user.produk.index', ['sort' => 'terlaris'])); ?>" class="flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Lihat Semua
                <i class="ph ph-arrow-right w-4 h-4"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php $__currentLoopData = $bestSellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['image' => $product->getFirstImage(),'title' => $product->name,'price' => $product->getFormattedPrice(),'originalPrice' => $product->hasDiscount() ? $product->getFormattedOriginalPrice() : null,'discount' => $product->hasDiscount() ? $product->getDiscountPercentage() : null,'href' => route('user.produk.show', $product->slug),'productId' => $product->id,'stock' => $product->stock,'rating' => $product->rating,'soldCount' => $product->sold_count]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getFirstImage()),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name),'price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getFormattedPrice()),'originalPrice' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->hasDiscount() ? $product->getFormattedOriginalPrice() : null),'discount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->hasDiscount() ? $product->getDiscountPercentage() : null),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('user.produk.show', $product->slug)),'productId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->id),'stock' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->stock),'rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->rating),'soldCount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->sold_count)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    
    
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Produk Terbaru</h2>
                <p class="text-gray-500 text-sm">Produk terbaru yang baru ditambahkan</p>
            </div>
            <a href="<?php echo e(route('user.produk.index')); ?>" class="flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Lihat Semua
                <i class="ph ph-arrow-right w-4 h-4"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php $__currentLoopData = $newArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['image' => $product->getFirstImage(),'title' => $product->name,'price' => $product->getFormattedPrice(),'originalPrice' => $product->hasDiscount() ? $product->getFormattedOriginalPrice() : null,'discount' => $product->hasDiscount() ? $product->getDiscountPercentage() : null,'href' => route('user.produk.show', $product->slug),'productId' => $product->id,'stock' => $product->stock]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getFirstImage()),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name),'price' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->getFormattedPrice()),'originalPrice' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->hasDiscount() ? $product->getFormattedOriginalPrice() : null),'discount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->hasDiscount() ? $product->getDiscountPercentage() : null),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('user.produk.show', $product->slug)),'productId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->id),'stock' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->stock)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    
    
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <div class="card bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white overflow-hidden group hover:shadow-xl transition-all">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-full text-sm font-semibold mb-4 text-blue-700">
                    <i class="ph ph-truck w-4 h-4"></i>
                    Gratis Ongkir
                </div>
                <h3 class="text-xl font-bold mb-2">Pengiriman Gratis</h3>
                <p class="text-blue-100 text-sm mb-4">Untuk pembelian di atas Rp 100.000</p>
                <a href="<?php echo e(route('user.produk.index')); ?>" class="inline-flex items-center gap-2 text-sm font-bold hover:underline">
                    Belanja Sekarang
                    <i class="ph ph-arrow-right w-4 h-4"></i>
                </a>
            </div>
        </div>
        
        
        <div class="card bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white overflow-hidden group hover:shadow-xl transition-all">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-full text-sm font-semibold mb-4 text-amber-700">
                    <i class="ph ph-crown w-4 h-4"></i>
                    Member Special
                </div>
                <h3 class="text-xl font-bold mb-2">Diskon 20%</h3>
                <p class="text-amber-100 text-sm mb-4">Diskon eksklusif untuk member</p>
                <a href="<?php echo e(route('user.produk.index')); ?>" class="inline-flex items-center gap-2 text-sm font-bold hover:underline">
                    Lihat Produk
                    <i class="ph ph-arrow-right w-4 h-4"></i>
                </a>
            </div>
        </div>
        
        
        <div class="card bg-gradient-to-br from-primary-500 to-primary-600 p-6 text-white overflow-hidden group hover:shadow-xl transition-all">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-full text-sm font-semibold mb-4 text-primary-700">
                    <i class="ph ph-headset w-4 h-4"></i>
                    24/7 Support
                </div>
                <h3 class="text-xl font-bold mb-2">Butuh Bantuan?</h3>
                <p class="text-primary-100 text-sm mb-4">Tim ahli kami siap membantu</p>
                <a href="<?php echo e(route('user.bantuan')); ?>" class="inline-flex items-center gap-2 text-sm font-bold hover:underline">
                    Hubungi Kami
                    <i class="ph ph-arrow-right w-4 h-4"></i>
                </a>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\user\dashboard.blade.php ENDPATH**/ ?>