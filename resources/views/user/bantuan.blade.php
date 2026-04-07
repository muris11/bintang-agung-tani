@php
use App\Models\Setting;
@endphp

@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4 md:mt-0 relative z-10 w-full">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li><a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors">Beranda</a></li>
                        <li>
                            <div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a
                                    href="{{ route('user.profil.show') }}" class="hover:text-primary-600 transition-colors">Akun Saya</a></div>
                        </li>
                        <li>
                            <div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span
                                    class="text-gray-900 font-medium">Bantuan</span></div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Pusat Bantuan & Layanan</h1>
                <p class="text-sm text-gray-500 mt-1">Kami siap membantu menjawab pertanyaan dan menyelesaikan kendala
                    pesanan Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.dashboard') }}" class="btn-secondary text-sm h-10 shadow-sm border-gray-200">
                    <i class="ph ph-arrow-left ph-bold w-4 h-4"></i> Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Contact Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10 mt-6">

            <!-- Live Chat / WhatsApp Card -->
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', Setting::get('whatsapp_number', '082212345678')) }}" target="_blank"
                class="card p-6 hover:border-green-200 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out -z-10">
                </div>

                <div
                    class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center mb-5 group-hover:-translate-y-1 transition-transform">
                    <i class="ph ph-whatsapp-logo ph-duotone w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Hubungi via WhatsApp</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">Solusi tercepat untuk kendala pesanan, konfirmasi
                    pembayaran, atau stok.</p>
                <div class="flex items-center gap-2 mt-auto">
                    <span class="text-green-600 font-bold text-sm">Chat Sekarang</span>
                    <i
                        class="ph ph-arrow-right ph-bold w-4 h-4 text-green-600 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Email Support Card -->
            <a href="mailto:{{ Setting::get('contact_email', 'info@bintangtani.com') }}"
                class="card p-6 hover:border-blue-200 transition-all group overflow-hidden relative">
                <div
                    class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out -z-10">
                </div>

                <div
                    class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5 group-hover:-translate-y-1 transition-transform">
                    <i class="ph ph-envelope-simple ph-duotone w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Bantuan Email</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">Untuk komplain resmi, kerjasama, proposal, atau
                    pertanyaan mendetail.</p>
                <div class="flex items-center gap-2 mt-auto">
                    <span class="text-blue-600 font-bold text-sm">Kirim Email</span>
                    <i
                        class="ph ph-arrow-right ph-bold w-4 h-4 text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- Operational Hours Card -->
            <div
                class="card p-6 hover:border-orange-200 transition-all group overflow-hidden relative lg:col-start-auto md:col-span-2 lg:col-span-1">
                <div
                    class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out -z-10">
                </div>

                <div
                    class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-5 group-hover:-translate-y-1 transition-transform">
                    <i class="ph ph-clock ph-duotone w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Jam Operasional</h3>
                <div class="space-y-2 mt-4">
                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                        <span class="text-gray-500 font-medium">Senin - Jumat</span>
                        <span class="font-bold text-gray-900">{{ Setting::get('operational_hours', '08.00 - 17.00') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                        <span class="text-gray-500 font-medium">Sabtu - Minggu</span>
                        <span class="font-bold text-red-500 bg-red-50 px-2 flex items-center rounded h-6 text-xs uppercase tracking-wider">Libur</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- FAQ Section -->
        <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 mt-8 mb-4">
            <div class="bg-gray-50/80 p-6 border-b border-gray-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-200 text-primary-600 flex items-center justify-center">
                    <i class="ph ph-question ph-bold w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Pertanyaan Umum (FAQ)</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Panduan cepat mengenai cara kerja sistem aplikasi kami.</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100" x-data="{ activeAccordion: null }">

                <!-- Accordion 1 -->
                <div class="group">
                    <button @click="activeAccordion = activeAccordion === 1 ? null : 1"
                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50/30 transition-colors focus:outline-none">
                        <h3 class="font-bold text-gray-800" :class="activeAccordion === 1 ? 'text-primary-600' : ''">
                            Bagaimana cara mengetahui pesanan saya diproses?</h3>
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0 transition-transform duration-300"
                            :class="activeAccordion === 1 ? 'rotate-180 bg-primary-50 border-primary-200 text-primary-600' :
                                'text-gray-400'">
                            <i class="ph ph-caret-down ph-bold w-4 h-4"></i>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 1" x-cloak x-collapse>
                        <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-50 bg-gray-50/10">
                            Pesanan Anda akan langsung kami proses di sistem saat Anda telah mengunggah bukti pembayaran,
                            kemudian admin memverifikasi keabsahan dana yang masuk. Anda bisa terus memantau tahapannya pada
                            antarmuka "Lacak Pesanan" dalam Detail Transaksi.
                        </div>
                    </div>
                </div>

                <!-- Accordion 2 -->
                <div class="group">
                    <button @click="activeAccordion = activeAccordion === 2 ? null : 2"
                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50/30 transition-colors focus:outline-none">
                        <h3 class="font-bold text-gray-800" :class="activeAccordion === 2 ? 'text-primary-600' : ''">Apa
                            bukti yang harus ditunjukkan saat mengambil pesanan di toko?</h3>
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0 transition-transform duration-300"
                            :class="activeAccordion === 2 ? 'rotate-180 bg-primary-50 border-primary-200 text-primary-600' :
                                'text-gray-400'">
                            <i class="ph ph-caret-down ph-bold w-4 h-4"></i>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 2" x-cloak x-collapse>
                        <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-50 bg-gray-50/10">
                            Saat pesanan bersatus "Siap Diambil", sistem kami akan menerbitkan <strong
                                class="text-gray-800">Barcode QR Pengambilan</strong> di riwayat pesanan Anda. Silakan
                            tunjukkan QR tersebut kepada admin toko {{ Setting::get('store_name', 'Bintang Agung Tani') }} untuk di-_scan_ agar barang dapat
                            diserahkan.
                        </div>
                    </div>
                </div>

                <!-- Accordion 3 -->
                <div class="group">
                    <button @click="activeAccordion = activeAccordion === 3 ? null : 3"
                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50/30 transition-colors focus:outline-none">
                        <h3 class="font-bold text-gray-800" :class="activeAccordion === 3 ? 'text-primary-600' : ''">Berapa
                            lama batas waktu pembayaran via Transfer Bank?</h3>
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0 transition-transform duration-300"
                            :class="activeAccordion === 3 ? 'rotate-180 bg-primary-50 border-primary-200 text-primary-600' :
                                'text-gray-400'">
                            <i class="ph ph-caret-down ph-bold w-4 h-4"></i>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 3" x-cloak x-collapse>
                        <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-50 bg-gray-50/10">
                            Batas toleransi maksimal waktu pembayaran adalah <strong>1 x 24 Jam</strong> semenjak invoice
                            checkout terbit. Apabila Anda tidak segera mengunggah bukti pada rentang waktu ini, maka pesanan
                            tersebut otomatis akan diarsipkan/dibatalkan oleh sistem kami untuk menghindari alokasi stok
                            palsu.
                        </div>
                    </div>
                </div>

                <!-- Accordion 4 -->
                <div class="group">
                    <button @click="activeAccordion = activeAccordion === 4 ? null : 4"
                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50/30 transition-colors focus:outline-none">
                        <h3 class="font-bold text-gray-800" :class="activeAccordion === 4 ? 'text-primary-600' : ''">
                            Bagaimana jika jumlah transfer saya keliru dibandingkan tagihan?</h3>
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0 transition-transform duration-300"
                            :class="activeAccordion === 4 ? 'rotate-180 bg-primary-50 border-primary-200 text-primary-600' :
                                'text-gray-400'">
                            <i class="ph ph-caret-down ph-bold w-4 h-4"></i>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 4" x-cloak x-collapse>
                        <div class="p-6 pt-0 text-sm text-gray-600 leading-relaxed border-t border-gray-50 bg-gray-50/10">
                            Silakan hubungi WhatsApp pada jam kerja dengan mencantumkan Nomor Invoice Anda, agar tim admin
                            dapat mengecek langsung pada data rekening tabungan.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
