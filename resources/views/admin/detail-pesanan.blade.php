@php
use App\Models\Setting;
@endphp

@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/admin/pesanan" class="hover:text-primary-600 transition-colors">Manajemen Pesanan</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Detail</span></div></li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pesanan #{{ $order->order_number }}</h1>
                <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full {{ $order->getStatusBadgeClass() }} border mt-1.5 shadow-sm">
                    {{ $order->getStatusLabel() }}
                </span>
            </div>
            <p class="text-sm border-gray-500 mt-1">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div class="flex items-center gap-3 hide-on-print">
            <a href="{{ route('admin.orders.index') }}" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn-primary text-sm h-10 shadow-md">
                <i class="ph ph-printer ph-bold w-4 h-4"></i> Cetak Invoice
            </button>
        </div>
    </div>

    <!-- Main Content Group -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Kolom Utama (Kiri): Detail Pesanan & Produk -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Dokumen Invoice Utama -->
            <div class="card p-0 overflow-hidden w-full printable-card shadow-sm border border-gray-200" id="invoiceArea">
                
                <!-- Ribbon Header Invoice -->
                <div class="bg-primary-700 p-6 md:p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-5 rounded-full"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 relative z-10 w-full">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-primary-700 font-black text-xl shadow-inner">
                                    B
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black tracking-tight leading-none text-white">INVOICE</h2>
                                </div>
                            </div>
                            <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-lg border border-white/30">
                                <i class="ph ph-receipt w-4 h-4 text-primary-100"></i>
                                <span class="font-mono text-sm font-bold text-white tracking-wider">{{ $order->order_number }}</span>
                            </div>
                        </div>
                        <div class="text-left md:text-right">
                            <h3 class="font-bold text-lg text-white">{{ Setting::get('store_name', 'Bintang Agung Tani') }}</h3>
                            <p class="text-sm text-primary-100 mt-1 leading-relaxed max-w-xs md:ml-auto">
                                {{ Setting::get('store_address_line1', 'Jl. Raya Pertanian No.12, Kec. Cisaat') }}<br>
                                {{ Setting::get('store_address_line2', 'Kabupaten Sukabumi, Jawa Barat 43152') }}
                            </p>
                            <p class="text-sm font-medium text-primary-200 mt-2">
                                <i class="ph ph-phone inline w-4 h-4 mr-1"></i> {{ Setting::get('store_phone', '(0266) 123456') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Pelanggan & Pengiriman -->
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100 bg-white">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="ph ph-user ph-fill w-4 h-4"></i>
                            </div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Kepada Yth.</h4>
                        </div>
                        <p class="font-bold text-gray-900 text-lg">{{ $order->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 font-medium mt-0.5">{{ $order->user->group_name ?? '-' }}</p>
                        <div class="mt-3 space-y-1">
                            <p class="text-sm text-gray-600 flex items-start gap-2">
                                <i class="ph ph-map-pin w-4 h-4 text-gray-400 shrink-0 mt-0.5"></i>
                                <span>{{ $order->address->full_address ?? $order->shipping_address_snapshot ?? 'Alamat tidak tersedia' }}</span>
                            </p>
                            <p class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="ph ph-phone w-4 h-4 text-gray-400 shrink-0"></i>
                                <span>{{ $order->shipping_phone ?? $order->user->phone ?? '-' }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="md:text-right">
                        <div class="flex items-center md:justify-end gap-2 mb-3">
                            <div class="w-6 h-6 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                                <i class="ph ph-info ph-fill w-4 h-4"></i>
                            </div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Detail Transaksi</h4>
                        </div>
                        
                        <div class="space-y-2 inline-block text-left md:text-right">
                            <div class="flex justify-between md:justify-end gap-4">
                                <span class="text-sm text-gray-500">Tanggal Transaksi:</span>
                                <span class="text-sm font-bold text-gray-900">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between md:justify-end gap-4">
                                <span class="text-sm text-gray-500">Metode Pengiriman:</span>
                                <span class="text-sm font-bold text-gray-900">{{ $order->shipping_courier ?? '-' }} {{ $order->shipping_service ? '('.$order->shipping_service.')' : '' }}</span>
                            </div>
                            <div class="flex justify-between md:justify-end gap-4 mt-2 pt-2 border-t border-gray-100">
                                <span class="text-sm text-gray-500">Status Pembayaran:</span>
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded {{ $order->getPaymentStatusClass() }}">
                                    {{ $order->getPaymentStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Item -->
                <div class="p-0 bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/80 border-b border-gray-200 text-gray-600 text-[11px] uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">No</th>
                                    <th class="px-6 py-4">Nama Produk & Deskripsi</th>
                                    <th class="px-6 py-4 text-right">Harga Satuan</th>
                                    <th class="px-6 py-4 text-center w-24">Kuantitas</th>
                                    <th class="px-6 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($order->items as $index => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 text-gray-500 text-center font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-5">
                                        <p class="font-bold text-gray-900 text-base">{{ $item->product_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1 font-medium">SKU: {{ $item->product_sku ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-6 py-5 text-right font-medium text-gray-600">{{ $item->getFormattedUnitPrice() }}</td>
                                    <td class="px-6 py-5 text-center font-bold text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-5 text-right font-bold text-gray-900">{{ $item->getFormattedSubtotal() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="ph ph-package w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                        <p>Tidak ada item dalam pesanan ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Total Section -->
                <div class="p-6 md:p-8 bg-gray-50/50 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="w-full md:w-1/2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Catatan Pesanan:</p>
                            <p class="text-sm text-gray-600 bg-white p-4 rounded-xl border border-gray-100 italic shadow-sm">
                                {{ $order->notes ?: 'Tidak ada catatan untuk pesanan ini.' }}
                            </p>
                        </div>
                        
                        <div class="w-full md:w-1/3 min-w-[250px] space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Subtotal Produk</span>
                                <span class="font-bold text-gray-900">{{ $order->getFormattedSubtotal() }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Ongkos Kirim ({{ $order->shipping_courier ?? '-' }})</span>
                                <span class="font-bold text-gray-900">{{ $order->getFormattedShippingCost() }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Diskon</span>
                                <span class="font-bold text-green-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                                <span class="text-base font-black text-gray-900 uppercase">Total Tagihan</span>
                                <span class="text-2xl font-black text-primary-600">{{ $order->getFormattedTotal() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <!-- Kolom Kanan (Sidebar): Aksi & Timeline -->
        <div class="lg:col-span-4 space-y-6 hide-on-print">
            
            <!-- Tindakan Utama (Update Status) -->
            <div class="card p-6 border-t-4 border-t-blue-500">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="ph ph-arrows-clockwise ph-bold w-4 h-4"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Update Status Resi</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-start gap-3">
                        <i class="ph ph-info ph-fill w-5 h-5 text-gray-400 shrink-0 mt-0.5"></i>
                        <p class="text-xs text-gray-600 leading-relaxed font-medium">Pesanan ini telah lunas dan <strong>sedang diproses paking</strong>. Silakan input nomor resi pengiriman setelah barang diserahkan ke kurir.</p>
                    </div>

                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="form-label block mb-1.5">Nomor Resi Pengiriman</label>
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="Masukkan No. Resi (Cth: JNE12345...)" class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label block mb-1.5">Ubah Status Ke</label>
                                <select name="status" class="form-input w-full font-medium text-gray-900">
                                    <option value="" disabled>-- Pilih Status Baru --</option>
                                    @foreach(['processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full mt-5 h-10 shadow-md">
                            <i class="ph ph-paper-plane-tilt ph-bold w-4 h-4 mr-1"></i> Simpan & Kirim Notif
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Riwayat Aktivitas & Timeline -->
            <div class="card p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-calendar-blank w-5 h-5 text-gray-500"></i> Log Aktivitas Pesanan
                    </h2>
                </div>
                <div class="p-6">
                    <div class="relative pl-6 border-l-2 border-gray-100 space-y-8 ml-2">
                        @forelse($order->statusHistories as $history)
                        <div class="relative">
                            @php
                                $color = match($history->status) {
                                    'completed' => 'green',
                                    'shipped', 'delivered' => 'blue',
                                    'processing' => 'orange',
                                    'cancelled', 'failed' => 'red',
                                    default => 'gray'
                                };
                            @endphp
                            <div class="absolute w-4 h-4 rounded-full bg-{{ $color }}-500 border-[3px] border-white -left-[33px] top-1 shadow-sm"></div>
                            <h3 class="font-bold text-gray-900 text-sm">{{ $order::STATUS_LABELS[$history->status] ?? $history->status }}</h3>
                            @if($history->changed_by)
                            <p class="text-xs font-medium text-gray-500 mt-1 bg-gray-50 w-fit px-2 py-1 rounded">Oleh: {{ $history->changed_by_user->name ?? 'Admin' }}</p>
                            @else
                            <p class="text-xs font-medium text-gray-500 mt-1 bg-gray-50 w-fit px-2 py-1 rounded">Sistem Web E-Commerce</p>
                            @endif
                            <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1"><i class="ph ph-clock w-3 h-3"></i> {{ $history->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @empty
                        <div class="relative">
                            <div class="absolute w-4 h-4 rounded-full bg-gray-300 border-[3px] border-white -left-[33px] top-1 shadow-sm"></div>
                            <h3 class="font-bold text-gray-900 text-sm">Pesanan Dibuat (Checkout)</h3>
                            <p class="text-xs font-medium text-gray-500 mt-1 bg-gray-50 w-fit px-2 py-1 rounded">Sistem Web E-Commerce</p>
                            <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1"><i class="ph ph-clock w-3 h-3"></i> {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activity Log (Admin Actions) -->
            <div class="card p-0 overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-list-dashes w-5 h-5 text-gray-500"></i> Riwayat Tindakan Admin
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($order->activityLogs as $log)
                        <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                            <div class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                <i class="ph ph-user-circle w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $log->description }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">Oleh: {{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-xs text-gray-500">{{ $log->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>
                                @if($log->metadata)
                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                    @if(isset($log->metadata['notes']))
                                        <span class="font-medium">Catatan:</span> {{ $log->metadata['notes'] }}
                                    @elseif(isset($log->metadata['previous_status']) && isset($log->metadata['new_status']))
                                        <span class="font-medium">Perubahan:</span> {{ $order::STATUS_LABELS[$log->metadata['previous_status']] ?? $log->metadata['previous_status'] }} &rarr; {{ $order::STATUS_LABELS[$log->metadata['new_status']] ?? $log->metadata['new_status'] }}
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <i class="ph ph-clipboard-text w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                            <p class="text-sm text-gray-500">Belum ada tindakan admin pada pesanan ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Customer Contacts Card -->
            <div class="card p-0 overflow-hidden mt-6 border border-gray-100 shadow-sm">
                 <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-1"><i class="ph ph-chats w-4 h-4"></i> Hubungi Pembeli</span>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3">
                    @if($order->user->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user->phone) }}" target="_blank" class="flex flex-col items-center justify-center p-3 rounded-xl border border-green-100 bg-green-50/50 hover:bg-green-100 transition-colors text-center group cursor-pointer">
                        <i class="ph ph-whatsapp-logo ph-fill w-6 h-6 text-green-600 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold text-green-700">WhatsApp</span>
                    </a>
                    @endif
                    @if($order->user->email)
                    <a href="mailto:{{ $order->user->email }}" class="flex flex-col items-center justify-center p-3 rounded-xl border border-blue-100 bg-blue-50/50 hover:bg-blue-100 transition-colors text-center group cursor-pointer">
                        <i class="ph ph-envelope-simple ph-fill w-6 h-6 text-blue-600 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold text-blue-700">Email</span>
                    </a>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; padding: 0 !important; margin: 0 !important; }
    #sidebar, .navbar, .hide-on-print { display: none !important; }
    .printable-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; border-radius: 0 !important;}
    main { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
}
</style>
@endsection
