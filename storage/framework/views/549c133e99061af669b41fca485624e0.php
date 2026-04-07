<?php $__env->startSection('title', 'Edit Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <a href="/admin/produk" class="hover:text-primary-600 transition-colors">Kelola Produk</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i>
                            <span class="text-gray-900 font-medium">Edit Produk</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Edit Produk</h1>
            <p class="text-gray-500 mt-1 text-sm">Perbarui informasi, harga, dan ketersediaan produk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/produk" class="btn-secondary text-sm h-10 shadow-sm">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Batal
            </a>
            <button type="submit" form="edit-product-form" class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-floppy-disk ph-bold w-4 h-4"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <form id="edit-product-form" action="<?php echo e(route('admin.produk.update', $product)); ?>" method="POST" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <!-- Kolom Kiri: Informasi Utama & Harga -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Informasi Umum -->
            <div class="card p-6 border-t-4 border-t-primary-500 rounded-t-xl">
                <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-info ph-fill w-5 h-5 text-primary-600"></i>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Umum</h2>
                    </div>
                    <span class="text-xs font-mono text-gray-400 bg-gray-50 px-2 py-1 flex items-center gap-1 border border-gray-200 rounded">
                        <i class="ph ph-barcode w-3.5 h-3.5"></i> SKU: <?php echo e($product->sku); ?>

                    </span>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label for="name" class="form-label mb-1.5 block">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="form-input w-full" value="<?php echo e(old('name', $product->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category_id" class="form-label mb-1.5 block">Kategori <span class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id" class="form-input w-full bg-gray-50/50" required>
                                <option value="" disabled>Pilih Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="brand" class="form-label mb-1.5 block">Merek / Brand <span class="text-gray-400 font-normal ml-1">(Opsional)</span></label>
                            <input type="text" id="brand" name="brand" class="form-input w-full" value="<?php echo e(old('brand', $product->brand ?? '')); ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="form-label mb-1.5 block">Deskripsi Produk <span class="text-red-500">*</span></label>
                        <div class="w-full mb-1 border border-gray-200 rounded-t-xl bg-gray-50 flex items-center p-2 gap-1 overflow-x-auto">
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors bg-white shadow-sm border border-gray-200"><i class="ph ph-text-b ph-bold w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-italic w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-text-underline w-4 h-4"></i></button>
                            <div class="w-px h-4 bg-gray-300 mx-1"></div>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-bullets w-4 h-4"></i></button>
                            <button type="button" class="icon-button text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded transition-colors"><i class="ph ph-list-numbers w-4 h-4"></i></button>
                        </div>
                        <textarea id="description" name="description" rows="6" class="form-input w-full rounded-t-none resize-y" required><?php echo e(old('description', $product->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
            
            <!-- Harga & Inventaris -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <i class="ph ph-currency-circle-dollar ph-fill w-5 h-5 text-amber-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Harga & Inventaris</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="price" class="form-label mb-1.5 block">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-gray-500">Rp</span>
                            <input type="number" id="price" name="price" class="form-input w-full pl-10" value="<?php echo e(old('price', $product->price)); ?>" required>
                        </div>
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div>
                        <label for="stock" class="form-label mb-1.5 block">Stok Tersedia <span class="text-red-500">*</span></label>
                        <input type="number" id="stock" name="stock" class="form-input w-full" value="<?php echo e(old('stock', $product->stock)); ?>" required>
                        <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div>
                        <label for="weight" class="form-label mb-1.5 block">Berat/Volume (Pengiriman) <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input type="number" id="weight" name="weight" step="0.01" class="form-input rounded-r-none border-r-0 w-full" value="<?php echo e(old('weight', $product->weight)); ?>" required>
                            <select name="weight_unit" class="form-input rounded-l-none bg-gray-50 w-28 shrink-0">
                                <option value="kg" selected>Kg</option>
                                <option value="g">Gram</option>
                                <option value="l">Liter</option>
                                <option value="ml">Ml</option>
                            </select>
                        </div>
                        <?php $__errorArgs = ['weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div>
                        <label for="unit" class="form-label mb-1.5 block">Satuan Jual <span class="text-red-500">*</span></label>
                        <select id="unit" name="unit" class="form-input w-full bg-gray-50/50" required>
                            <option value="zak" <?php echo e(old('unit', $product->unit) == 'zak' ? 'selected' : ''); ?>>Zak / Karung</option>
                            <option value="botol" <?php echo e(old('unit', $product->unit) == 'botol' ? 'selected' : ''); ?>>Botol</option>
                            <option value="pcs" <?php echo e(old('unit', $product->unit) == 'pcs' ? 'selected' : ''); ?>>Pcs / Buah</option>
                            <option value="paket" <?php echo e(old('unit', $product->unit) == 'paket' ? 'selected' : ''); ?>>Paket</option>
                        </select>
                        <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Kolom Kanan: Media & Setting -->
        <div class="space-y-6">
            
            <!-- Media Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-image ph-fill w-5 h-5 text-blue-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Media Produk</h2>
                </div>
                
                <!-- Gambar Saat Ini -->
                <div class="mb-4 text-center">
                    <div class="relative w-full aspect-square rounded-2xl bg-gray-50 border border-gray-200 overflow-hidden group">
                        <?php if($product->getFirstImage()): ?>
                            <img loading="lazy" src="<?php echo e($product->getFirstImage()); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover mix-blend-multiply transition-transform group-hover:scale-105">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ph ph-image ph-fill w-16 h-16 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                        <button type="button" class="absolute top-2 right-2 bg-white text-red-600 p-2 rounded-lg shadow-sm hover:bg-red-50 hover:text-red-700 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="Hapus Gambar">
                            <i class="ph ph-trash ph-bold w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                
                <button type="button" class="btn-secondary w-full justify-center text-sm shadow-sm flex items-center gap-2">
                    <i class="ph ph-upload-simple ph-bold w-4 h-4"></i> Ganti Gambar Utama
                </button>
            </div>
            
            <!-- Status Produk -->
            <div class="card p-6">
                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-4">
                    <i class="ph ph-toggle-right ph-fill w-5 h-5 text-primary-500"></i>
                    <h2 class="text-lg font-bold text-gray-900">Visibilitas Produk</h2>
                </div>
                
                <label class="inline-flex items-center cursor-pointer bg-primary-50 border border-primary-200 p-4 rounded-xl w-full hover:bg-primary-100/50 transition-colors">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?>>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <div class="ms-4">
                        <span class="block text-sm font-bold text-gray-900">Aktifkan Produk</span>
                        <span class="block text-xs text-primary-700 mt-0.5 font-medium"><?php echo e($product->is_active ? 'Sedang tampil di katalog' : 'Tidak tampil di katalog'); ?></span>
                    </div>
                </label>

                <!-- Warning untuk Delete -->
                <div class="mt-6 border-t border-red-500/20 pt-4 text-center">
                    <form action="<?php echo e(route('admin.produk.destroy', $product)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini secara permanen?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700 hover:underline">
                            Hapus Permanen Produk Ini
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/admin/edit-produk.blade.php ENDPATH**/ ?>