<?php $__env->startSection('title', 'Kelola Alamat'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Alamat</h1>
        <button onclick="document.getElementById('addAddressModal').classList.remove('hidden')" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Alamat
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($addresses->isEmpty()): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada alamat</h3>
            <p class="text-gray-500 mb-4">Tambahkan alamat pengiriman untuk memudahkan proses checkout.</p>
            <button onclick="document.getElementById('addAddressModal').classList.remove('hidden')" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                Tambah Alamat Baru
            </button>
        </div>
    <?php else: ?>
        <div class="grid gap-4">
            <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow p-6 <?php echo e($address->is_default ? 'border-2 border-green-500' : ''); ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-semibold text-gray-800"><?php echo e($address->label); ?></span>
                                <?php if($address->is_default): ?>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Utama</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-700 font-medium"><?php echo e($address->recipient_name); ?></p>
                            <p class="text-gray-600"><?php echo e($address->phone); ?></p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editAddress(<?php echo e(json_encode($address)); ?>)" 
                                    class="text-blue-600 hover:text-blue-800 p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <form action="<?php echo e(route('user.alamat.destroy', $address)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus alamat ini?')" 
                                        class="text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4"><?php echo e($address->full_address); ?></p>
                    <p class="text-gray-500 text-sm"><?php echo e($address->city); ?>, <?php echo e($address->postal_code); ?></p>
                    
                    <?php if(!$address->is_default): ?>
                        <form action="<?php echo e(route('user.alamat.default', $address)); ?>" method="POST" class="mt-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                Jadikan Alamat Utama
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-screen overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Tambah Alamat Baru</h2>
                <button onclick="document.getElementById('addAddressModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form action="<?php echo e(route('user.alamat.store')); ?>" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Label Alamat</label>
                    <input type="text" name="label" placeholder="Contoh: Rumah, Kantor" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nama Penerima</label>
                    <input type="text" name="recipient_name" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">No. Telepon</label>
                    <input type="text" name="phone" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Alamat Lengkap</label>
                    <textarea name="full_address" rows="3" required
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Kota</label>
                        <input type="text" name="city" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Provinsi</label>
                        <input type="text" name="province" required placeholder="Contoh: Jawa Barat"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="is_default_add" value="1"
                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="is_default_add" class="text-gray-700">Jadikan alamat utama</label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('addAddressModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Address Modal -->
<div id="editAddressModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-screen overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Edit Alamat</h2>
                <button onclick="document.getElementById('editAddressModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="editAddressForm" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Label Alamat</label>
                    <input type="text" name="label" id="edit_label" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nama Penerima</label>
                    <input type="text" name="recipient_name" id="edit_recipient_name" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">No. Telepon</label>
                    <input type="text" name="phone" id="edit_phone" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Alamat Lengkap</label>
                    <textarea name="full_address" id="edit_full_address" rows="3" required
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Kota</label>
                        <input type="text" name="city" id="edit_city" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Provinsi</label>
                        <input type="text" name="province" id="edit_province" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Kode Pos</label>
                    <input type="text" name="postal_code" id="edit_postal_code"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" id="edit_is_default" value="1"
                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="edit_is_default" class="text-gray-700">Jadikan alamat utama</label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('editAddressModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editAddress(address) {
    document.getElementById('editAddressForm').action = `<?php echo e(route('user.alamat.index')); ?>/${address.id}`;
    document.getElementById('edit_label').value = address.label;
    document.getElementById('edit_recipient_name').value = address.recipient_name;
    document.getElementById('edit_phone').value = address.phone;
    document.getElementById('edit_full_address').value = address.full_address;
    document.getElementById('edit_city').value = address.city;
    document.getElementById('edit_province').value = address.province ?? '';
    document.getElementById('edit_postal_code').value = address.postal_code ?? '';
    document.getElementById('edit_is_default').checked = address.is_default;
    document.getElementById('editAddressModal').classList.remove('hidden');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\user\alamat.blade.php ENDPATH**/ ?>