@php
use App\Models\Setting;

$cartItems = $cart->items ?? collect();
$cartSubtotal = $cart->getFormattedSubtotal() ?? 'Rp 0';
$cartShipping = $cart->getFormattedShipping() ?? 'Rp 0';
$cartTotal = $cart->getFormattedTotal() ?? 'Rp 0';
$addressesCount = $addresses->count() ?? 0;
@endphp

@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                            <i class="ph ph-house text-xs"></i>
                            Dashboard
                        </a>
                    </li>
                    <li><i class="ph ph-caret-right text-gray-300 text-xs"></i></li>
                    <li>
                        <a href="{{ route('user.cart.index') }}" class="hover:text-primary-600 transition-colors">Keranjang</a>
                    </li>
                    <li><i class="ph ph-caret-right text-gray-300 text-xs"></i></li>
                    <li class="text-gray-900 font-semibold">Checkout</li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Checkout</h1>
                <div class="bg-gradient-to-br from-primary-500 to-primary-600 text-white p-2 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="ph ph-bag-check ph-fill text-xl"></i>
                </div>
            </div>
            <p class="text-gray-500 mt-2">Selesaikan pesanan Anda dengan aman dan cepat</p>
        </div>
        
        <!-- Progress Steps -->
        <div class="flex items-center gap-4 bg-white px-6 py-3 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="ph ph-check text-primary-600 font-bold"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Keranjang</span>
            </div>
            <i class="ph ph-caret-right text-gray-300"></i>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">2</span>
                </div>
                <span class="text-sm font-bold text-primary-600">Checkout</span>
            </div>
            <i class="ph ph-caret-right text-gray-300"></i>
            <div class="flex items-center gap-2 opacity-50">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="text-gray-400 font-bold text-sm">3</span>
                </div>
                <span class="text-sm font-medium text-gray-500">Pembayaran</span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        
        <!-- Left Column: Address & Order Summary -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Address Section -->
            <div class="card-featured overflow-hidden">
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="ph ph-map-pin text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Alamat Pengiriman</h2>
                            <p class="text-primary-100 text-sm">Pastikan alamat pengiriman sudah benar</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Address Form -->
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <i class="ph ph-user text-gray-400"></i>
                                    Nama Penerima
                                </label>
                                <input type="text" value="{{ auth()->user()->name }}" readonly class="form-input bg-gray-50 cursor-not-allowed text-gray-600">
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <i class="ph ph-phone text-gray-400"></i>
                                    Nomor Telepon
                                </label>
                                <input type="text" value="{{ auth()->user()->phone ?? '-' }}" readonly class="form-input bg-gray-50 cursor-not-allowed text-gray-600">
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-sm font-bold text-gray-700 flex items-center gap-2">
                                    <i class="ph ph-map-pin text-gray-400"></i>
                                    Alamat Lengkap
                                </label>
                                <textarea readonly rows="3" class="form-input bg-gray-50 cursor-not-allowed text-gray-600 resize-none">{{ auth()->user()->defaultAddress ? auth()->user()->defaultAddress->full_address : 'Belum ada alamat default' }}</textarea>
                            </div>
                            
                            <div x-data="{ 
                                showAddressModal: false, 
                                selectedAddress: {{ auth()->user()->defaultAddress ? auth()->user()->defaultAddress->id : 'null' }}
                            }">
                                
                                <button type="button" 
                                        @click="showAddressModal = true"
                                        class="btn-secondary w-full flex items-center justify-center gap-2">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                    Ubah Alamat
                                </button>
                                
                                <!-- Hidden input for form submission -->
                                <input type="hidden" name="address_id" x-model="selectedAddress">
                                
                                <!-- Address Selection Modal -->
                                <div x-show="showAddressModal" 
                                     x-cloak
                                     class="fixed inset-0 z-50 overflow-y-auto"
                                     style="display: none;">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <!-- Background overlay -->
                                        <div x-show="showAddressModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             @click="showAddressModal = false"
                                             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                        
                                        <!-- Modal panel -->
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="showAddressModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Alamat Pengiriman</h3>
                                                
                                                <div class="space-y-3 max-h-80 overflow-y-auto">
                                                    @forelse($addresses as $address)
                                                    <div class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition-colors"
                                                         :class="selectedAddress == {{ $address->id }} ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-primary-300'"
                                                         @click="selectedAddress = {{ $address->id }}">
                                                        <input type="radio" 
                                                               value="{{ $address->id }}"
                                                               x-model="selectedAddress"
                                                               class="mt-1 mr-3 text-primary-600 focus:ring-primary-500"
                                                               required>
                                                        <div class="flex-1">
                                                            <p class="font-bold text-gray-900">{{ $address->label ?? 'Alamat' }}</p>
                                                            <p class="text-sm text-gray-600 mt-1">{{ $address->full_address }}</p>
                                                            <p class="text-sm text-gray-500">{{ $address->phone }}</p>
                                                            @if($address->is_default)
                                                                <span class="inline-block mt-2 text-xs font-bold text-primary-600 bg-primary-100 px-2 py-1 rounded">Default</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @empty
                                                    <div class="text-center py-8">
                                                        <p class="text-gray-500 mb-4">Belum ada alamat tersimpan</p>
                                                        <a href="{{ route('user.alamat.index') }}" class="btn-primary">
                                                            <i class="ph ph-plus"></i> Tambah Alamat
                                                        </a>
                                                    </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                                <button type="button" 
                                                        @click="showAddressModal = false"
                                                        class="btn-primary w-full sm:w-auto">
                                                    Pilih Alamat
                                                </button>
                                                <button type="button" 
                                                        @click="showAddressModal = false"
                                                        class="btn-secondary w-full sm:w-auto mt-3 sm:mt-0">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Address Card -->
                        <div class="bg-gradient-to-br from-primary-50 to-primary-100/50 border-2 border-primary-200 rounded-2xl p-6 h-fit">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                    Alamat Utama
                                </span>
                            </div>
                            
                            <h3 class="font-bold text-gray-900 text-lg mb-4">{{ auth()->user()->name }}</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                        <i class="ph ph-phone text-primary-500"></i>
                                    </div>
                                    <span class="text-gray-700 pt-1.5">{{ auth()->user()->phone ?? '-' }}</span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                        <i class="ph ph-map-pin text-primary-500"></i>
                                    </div>
                                    <span class="text-gray-700 leading-relaxed pt-1">{{ auth()->user()->defaultAddress ? auth()->user()->defaultAddress->full_address : 'Belum ada alamat default' }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between mt-6 pt-4 border-t border-primary-200">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                                            <i class="ph ph-storefront text-white"></i>
                                        </div>
                                        <span class="font-bold text-gray-800">{{ Setting::get('store_branch', 'Cabang Utama') }}</span>
                                    </div>
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-primary-500"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Table -->
            <div class="card-featured overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="ph ph-receipt text-primary-600 text-xl"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50 text-gray-500 font-semibold border-b border-gray-100 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 min-w-[300px]">Produk</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Harga</th>
                                <th class="px-6 py-4 text-right whitespace-nowrap">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($cartItems as $cartItem)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex gap-4 items-start">
                                        <div class="w-16 h-16 shrink-0 bg-gray-50 border border-gray-100 rounded-xl p-1.5 flex items-center justify-center overflow-hidden">
                                            @if($cartItem->product->getFirstImage())
                                                <img loading="lazy" src="{{ $cartItem->product->getFirstImage() }}" alt="{{ $cartItem->product_name }}" class="w-full h-full object-cover rounded-lg mix-blend-multiply group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <i class="ph ph-package text-2xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-gray-900 text-sm mb-1 line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors">{{ $cartItem->product_name }}</h4>
                                            <p class="text-xs text-gray-500">Qty: {{ $cartItem->quantity }} item</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center align-middle">
                                    <div class="font-bold text-primary-600">{{ $cartItem->getFormattedUnitPrice() }}</div>
                                </td>
                                <td class="px-6 py-5 text-right align-middle">
                                    <div class="font-bold text-gray-900 text-lg">{{ $cartItem->getFormattedTotal() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="ph ph-shopping-cart text-4xl text-gray-300"></i>
                                        </div>
                                        <p class="text-gray-500">Keranjang kosong</p>
                                        <a href="{{ route('user.produk.index') }}" class="text-primary-600 font-semibold hover:text-primary-700">Mulai Belanja</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <tr>
                                <td colspan="2" class="px-6 pt-5 pb-2 text-right font-medium text-gray-500">Subtotal Produk</td>
                                <td class="px-6 pt-5 pb-2 text-right font-bold text-gray-900 text-base">{{ $cartSubtotal }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-6 py-2 text-right font-medium text-gray-500">Ongkos Kirim</td>
                                <td class="px-6 py-2 text-right font-bold text-gray-900">{{ $cartShipping }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-6 pt-2 pb-5 text-right font-bold text-gray-900 text-lg border-t border-dashed border-gray-200">Grand Total</td>
                                <td class="px-6 pt-2 pb-5 text-right font-bold text-primary-600 text-2xl border-t border-dashed border-gray-200">{{ $cartTotal }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Final Summary -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="card-featured p-6 sticky top-24">
                <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i class="ph ph-credit-card text-amber-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Ringkasan Pembayaran</h2>
                </div>
                
                <!-- Info Box -->
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                            <i class="ph ph-info text-amber-600"></i>
                        </div>
                        <p class="leading-relaxed">Pembayaran dan pengambilan barang dilakukan langsung di toko kami dengan menunjukkan <strong>Barcode Pesanan</strong>.</p>
                    </div>
                </div>
                
                <!-- Total Summary -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-gray-600 text-sm">
                        <span>Subtotal</span>
                        <span class="font-semibold">{{ $cartSubtotal }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600 text-sm">
                        <span>Ongkir</span>
                        <span class="font-semibold">{{ $cartShipping }}</span>
                    </div>
                    <hr class="border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-lg">Total Bayar</span>
                        <span class="font-bold text-primary-600 text-2xl">{{ $cartTotal }}</span>
                    </div>
                </div>
                
                <!-- Checkout Form -->
                <form action="{{ route('user.checkout.store') }}" method="POST" class="space-y-4" x-data="{ loading: false }" @submit.prevent="loading = true; $el.submit();">
                    @csrf
                    <!-- address_id is managed by Alpine.js in the address section above -->
                    <input type="hidden" name="shipping_cost" value="0">
                    <input type="hidden" name="shipping_courier" value="Self Pickup">
                    <input type="hidden" name="shipping_service" value="Ambil di Toko">
                    
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full relative overflow-hidden bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl py-4 shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 group">
                        <span x-show="!loading" x-cloak class="relative z-10 flex items-center gap-2">
                            <i class="ph ph-check-circle text-xl"></i>
                            Buat Pesanan
                        </span>
                        <span x-show="loading" x-cloak class="relative z-10 flex items-center gap-2">
                            <i class="ph ph-spinner animate-spin text-xl"></i>
                            Memproses...
                        </span>
                        <!-- Shine Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    </button>
                </form>
                
                <p class="text-xs text-gray-500 mt-4 text-center leading-relaxed">
                    Setelah membuat pesanan, Anda akan dipindahkan ke halaman pemilihan metode pembayaran.
                </p>
            </div>
            
        </div>
    </div>
    
    <!-- Value Props Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mt-8">
        <div class="card-featured p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl flex items-center justify-center shrink-0 border border-green-200">
                <i class="ph ph-storefront text-green-600 text-2xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1">Ambil di Toko</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Pesan online, bayar dan ambil barang langsung secara aman di toko kami.</p>
            </div>
        </div>
        <div class="card-featured p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl flex items-center justify-center shrink-0 border border-blue-200">
                <i class="ph ph-shield-check text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1">Transaksi Aman</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Invoice dan bukti pemesanan digital otomatis terbit untuk setiap pembelian.</p>
            </div>
        </div>
        <div class="card-featured p-6 flex items-start gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl flex items-center justify-center shrink-0 border border-amber-200">
                <i class="ph ph-headset text-amber-600 text-2xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-1">Layanan Pelanggan</h4>
                <p class="text-xs text-gray-500 leading-relaxed">Hubungi Kami Setiap Jam Kerja {{ Setting::get('operational_hours_full', '08.00 - 16.00 WIB') }} untuk bantuan pesanan.</p>
            </div>
        </div>
    </div>

</div>

<style>
/* Progress Steps Animation */
@keyframes pulse-ring {
    0% { transform: scale(0.8); opacity: 1; }
    100% { transform: scale(1.5); opacity: 0; }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .animate-ping {
        animation: none;
    }
}

/* Table Hover Effect */
tr:hover td {
    background-color: rgba(249, 250, 251, 0.5);
}
</style>
@endsection
