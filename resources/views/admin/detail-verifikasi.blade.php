@extends('layouts.admin')

@section('title', 'Detail Bukti Verifikasi')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0" x-data="{ showImageModal: false }">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/admin/verifikasi" class="hover:text-primary-600 transition-colors">List Verifikasi</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Detail Dokumen</span></div></li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Dokumen Bukti Transfer</h1>
                <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $payment->isPending() ? 'bg-orange-100 text-orange-700 border border-orange-200' : ($payment->isSuccess() ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200') }} mt-1.5 shadow-sm">
                    {{ $payment->isPending() ? 'Review Tertunda' : ($payment->isSuccess() ? 'Terverifikasi' : 'Ditolak') }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Lakukan konfirmasi mutasi rekening sebelum pesanan diteruskan ke bagian pengiriman.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.verifikasi.index') }}" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <!-- Kolom Kiri: Bukti Pembayaran -->
        <div class="space-y-6">
            <div class="card p-0 overflow-hidden border-t-4 border-t-orange-500">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                            <i class="ph ph-receipt ph-bold w-4 h-4"></i>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Lampiran Pelanggan</h2>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn-secondary h-8 px-3 py-0 text-xs shadow-sm bg-white" @click="showImageModal = true"><i class="ph ph-arrows-out w-3.5 h-3.5 mr-1"></i> Fullscreen</button>
                        <a href="{{ $payment->order && $payment->order->latestPaymentProof ? $payment->order->latestPaymentProof->getImageUrl() : '#' }}" download class="btn-secondary h-8 px-3 py-0 text-xs shadow-sm bg-white {{ !$payment->order || !$payment->order->latestPaymentProof ? 'opacity-50 cursor-not-allowed' : '' }}"><i class="ph ph-download-simple w-3.5 h-3.5 mr-1"></i> Unduh</a>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Image Preview Area -->
                    <div class="relative w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center" style="min-height: 400px; max-height: 600px;">
                        @if($payment->order && $payment->order->latestPaymentProof && $payment->order->latestPaymentProof->image_url)
                            <img loading="lazy" src="{{ $payment->order->latestPaymentProof->image_url }}" alt="Bukti Transfer" class="max-w-full max-h-[600px] object-contain shadow-sm border border-gray-200">
                        @else
                            <div class="text-center text-gray-400">
                                <i class="ph ph-image w-16 h-16 mx-auto mb-2"></i>
                                <p>Bukti transfer tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Detail Pesanan & Aksi -->
        <div class="space-y-6">
            
            <!-- Ringkasan Info Pesanan -->
            <div class="card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100 flex items-center justify-between">
                    Data Referensi Transaksi
                    <a href="/admin/detail-pesanan" class="text-xs font-semibold text-primary-600 flex items-center gap-1 hover:text-primary-700 bg-primary-50 px-2 py-1 rounded-md transition-colors"><i class="ph ph-note w-4 h-4"></i> Detail Cart</a>
                </h3>
                
                <!-- Tagihan Box -->
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nilai Mutasi Rekening</p>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ $payment->getFormattedAmount() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                        <i class="ph ph-money ph-duotone w-6 h-6"></i>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-gray-500 font-medium text-sm">Nomor Invoice</span>
                        <span class="font-bold text-gray-900">{{ $payment->order->order_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-gray-500 font-medium text-sm">Waktu Transaksi</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $payment->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                        <span class="text-gray-500 font-medium text-sm mt-0.5">Identitas Pelanggan</span>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 block">{{ $payment->user->name ?? 'N/A' }}</span>
                            <span class="text-xs text-gray-500 mt-0.5 flex items-center justify-end gap-1"><i class="ph ph-envelope-simple w-3 h-3"></i> {{ $payment->user->email ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-gray-100">
                        <span class="text-gray-500 font-medium text-sm">Kanal Pembayaran</span>
                        <span class="font-bold text-gray-900 flex items-center gap-2 text-sm text-right">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-black italic border border-blue-100">{{ strtoupper($payment->payment_method) }}</span> Transfer Bank
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tindakan Keputusan -->
            <div class="card p-6 border-t-4 border-t-primary-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i class="ph ph-gavel ph-bold w-4 h-4"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Putusan Verifikasi</h2>
                </div>
                
                <p class="text-sm text-gray-600 mb-6 font-medium leading-relaxed">
                    Pastikan dana <strong class="text-gray-900">{{ $payment->getFormattedAmount() }}</strong> sudah efektif masuk ke rekening mutasi Bank {{ strtoupper($payment->payment_method) }} sebelum menyetujui. Tindakan ini tidak dapat dibatalkan dan akan langsung meneruskan pesanan ke tahap Pengepakan.
                </p>
                
                <form action="{{ route('admin.verifikasi.approve', $payment) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="btn-primary flex-1 h-12 shadow-md bg-green-600 hover:bg-green-700 hover:border-green-600 border-green-600 focus:ring-green-500/30" {{ !$payment->isPending() ? 'disabled' : '' }}>
                            <i class="ph ph-check-circle ph-bold w-5 h-5 mr-1.5"></i> Sesuai & Lanjutkan
                        </button>
                    </div>
                </form>
                    
                <form action="{{ route('admin.verifikasi.reject', $payment) }}" method="POST" class="pt-4 border-t border-gray-100 border-dashed">
                    @csrf
                    <label class="form-label block mb-1.5 text-xs font-bold text-gray-500 uppercase tracking-widest">Tunda atau Tolak Pembayaran</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="reason" placeholder="Alasan penolakan (misal: Nominal kurang)" class="form-input flex-1 text-sm bg-gray-50/50" {{ !$payment->isPending() ? 'disabled' : '' }}>
                        <button type="submit" class="btn-primary h-10 shadow-md bg-red-600 hover:bg-red-700 hover:border-red-600 border-red-600 focus:ring-red-500/30 px-6 sm:w-auto w-full" {{ !$payment->isPending() ? 'disabled' : '' }}>
                            <i class="ph ph-x-circle ph-bold w-4 h-4 mr-1"></i> Tolak
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <!-- Fullscreen Image Modal -->
    <div x-show="showImageModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <button @click="showImageModal = false" class="absolute top-4 right-4 text-white hover:text-gray-300 w-12 h-12 flex items-center justify-center bg-black/50 rounded-full hover:bg-black/70 transition-colors z-10">
            <i class="ph ph-x ph-bold w-6 h-6"></i>
        </button>
        
        <div @click.away="showImageModal = false" class="relative max-w-4xl w-full flex items-center justify-center h-full"
             x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            @if($payment->order && $payment->order->latestPaymentProof && $payment->order->latestPaymentProof->image_url)
                <img loading="lazy" src="{{ $payment->order->latestPaymentProof->image_url }}" class="max-w-full max-h-full object-contain rounded shadow-2xl">
            @else
                <div class="text-center text-white">
                    <i class="ph ph-image w-16 h-16 mx-auto mb-4"></i>
                    <p>Bukti transfer tidak tersedia</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
