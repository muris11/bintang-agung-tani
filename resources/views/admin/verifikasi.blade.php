@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 relative z-10 w-full px-4 sm:px-0 mt-4 md:mt-0">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard Admin</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/admin/pesanan" class="hover:text-primary-600 transition-colors">Pesanan</a></div></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Verifikasi</span></div></li>
                </ol>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Verifikasi Pembayaran</h1>
                @php
                    $statusColors = [
                        'pending' => 'orange',
                        'verified' => 'green',
                        'rejected' => 'red'
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak'
                    ];
                    $currentColor = $statusColors[$status] ?? 'gray';
                    $currentLabel = $statusLabels[$status] ?? $status;
                @endphp
                <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-{{ $currentColor }}-100 text-{{ $currentColor }}-700 border border-{{ $currentColor }}-200 mt-1.5 shadow-sm">
                    {{ $currentLabel }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Periksa bukti transfer dan pastikan dana sudah masuk ke rekening toko.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/pesanan" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-4">
        <a href="?status=pending" class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'pending' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <i class="ph ph-clock mr-1"></i> Menunggu
        </a>
        <a href="?status=verified" class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'verified' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <i class="ph ph-check-circle mr-1"></i> Terverifikasi
        </a>
        <a href="?status=rejected" class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'rejected' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <i class="ph ph-x-circle mr-1"></i> Ditolak
        </a>
    </div>

    <!-- Payments List -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-gray-900">{{ $payment->order->order_number ?? 'N/A' }}</span>
                                @if($payment->isPending())
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($payment->isVerified())
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-green-100 text-green-700">Verified</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700">Rejected</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center font-bold text-primary-700 text-xs">
                                    {{ strtoupper(substr($payment->user->name ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $payment->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900">{{ $payment->order->getFormattedTotal() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                {{ strtoupper($payment->paymentMethod->name ?? $payment->paymentMethod->code ?? '-') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $payment->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $payment->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.verifikasi.show', $payment) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-sm font-medium hover:bg-primary-100 transition-colors">
                                <i class="ph ph-eye w-4 h-4"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="ph ph-receipt w-12 h-12 mx-auto mb-3"></i>
                                <p class="text-lg font-medium text-gray-600">Tidak ada pembayaran {{ $status === 'pending' ? 'menunggu verifikasi' : ($status === 'verified' ? 'terverifikasi' : 'ditolak') }}</p>
                                <p class="text-sm mt-1">Pembayaran akan muncul di sini setelah pelanggan mengunggah bukti transfer.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
