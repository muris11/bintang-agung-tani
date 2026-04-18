<?php
use App\Models\Setting;

$storeName = Setting::get('store_name', 'Bintang Agung Tani');
$currentYear = date('Y');
?>

<footer class="mt-auto w-full bg-gradient-to-br from-primary-800 via-primary-700 to-primary-800 relative overflow-hidden">
    <!-- Animated Background Decoration -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-20 right-20 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute bottom-1/2 left-1/4 w-48 h-48 bg-primary-400/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <!-- Subtle Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 32px 32px;"></div>
    </div>

    <div class="relative z-10">
        <!-- Main Footer Content -->
        <div class="px-6 py-16 md:px-12 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">
                
                <!-- Brand Section - Takes 5 columns -->
                <div class="lg:col-span-5">
                    <a href="/user/dashboard" class="flex items-center gap-3 mb-6 group">
                        <?php if(file_exists(public_path('images/logo.png'))): ?>
                            <div class="relative">
                                <div class="absolute inset-0 bg-white/20 blur-xl rounded-full scale-150 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <img loading="lazy" src="/images/logo.png" alt="<?php echo e($storeName); ?>" class="h-12 w-auto relative z-10">
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/30">
                                <i class="ph ph-plant text-white text-2xl"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <p class="text-primary-100 text-sm leading-relaxed mb-8 max-w-md">
                        Solusi lengkap kebutuhan pertanian Anda. Menyediakan pupuk berkualitas, pestisida aman, dan peralatan modern untuk hasil panen terbaik.
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex items-center gap-3">
                        <?php if(Setting::get('social_facebook')): ?>
                        <a href="<?php echo e(Setting::get('social_facebook')); ?>" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-primary-700 hover:scale-110 transition-all duration-300 shadow-lg">
                            <i class="ph ph-facebook-logo text-lg"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(Setting::get('social_instagram')): ?>
                        <a href="<?php echo e(Setting::get('social_instagram')); ?>" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-primary-700 hover:scale-110 transition-all duration-300 shadow-lg">
                            <i class="ph ph-instagram-logo text-lg"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(Setting::get('social_twitter')): ?>
                        <a href="<?php echo e(Setting::get('social_twitter')); ?>" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-primary-700 hover:scale-110 transition-all duration-300 shadow-lg">
                            <i class="ph ph-twitter-logo text-lg"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(Setting::get('social_youtube')): ?>
                        <a href="<?php echo e(Setting::get('social_youtube')); ?>" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-primary-700 hover:scale-110 transition-all duration-300 shadow-lg">
                            <i class="ph ph-youtube-logo text-lg"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(Setting::get('social_tiktok')): ?>
                        <a href="<?php echo e(Setting::get('social_tiktok')); ?>" target="_blank" rel="noopener noreferrer" 
                           class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-primary-700 hover:scale-110 transition-all duration-300 shadow-lg">
                            <i class="ph ph-tiktok-logo text-lg"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links - Takes 2 columns -->
                <div class="lg:col-span-2">
                    <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-6 flex items-center gap-2">
                        <i class="ph ph-list text-primary-300"></i>
                        Menu Utama
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/user/dashboard" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/user/produk" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Produk
                            </a>
                        </li>
                        <li>
                            <a href="/user/keranjang" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Keranjang
                            </a>
                        </li>
                        <li>
                            <a href="/user/riwayat" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="/user/profil" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Profil
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Help & Support - Takes 2 columns -->
                <div class="lg:col-span-2">
                    <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-6 flex items-center gap-2">
                        <i class="ph ph-question text-primary-300"></i>
                        Bantuan
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/user/bantuan" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Pusat Bantuan
                            </a>
                        </li>
                        <li>
                            <a href="/user/faq" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="/user/kontak" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Hubungi Kami
                            </a>
                        </li>
                        <li>
                            <a href="/user/syarat-ketentuan" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Syarat & Ketentuan
                            </a>
                        </li>
                        <li>
                            <a href="/user/kebijakan-privasi" class="text-primary-100 hover:text-white text-sm transition-all duration-200 flex items-center gap-2 group">
                                <i class="ph ph-caret-right text-primary-300 group-hover:translate-x-1 transition-transform"></i> 
                                Kebijakan Privasi
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info - Takes 3 columns -->
                <div class="lg:col-span-3">
                    <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-6 flex items-center gap-2">
                        <i class="ph ph-address-book text-primary-300"></i>
                        Kontak Kami
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph ph-phone text-white"></i>
                            </div>
                            <div class="text-sm">
                                <div class="text-white font-semibold mb-0.5">WhatsApp</div>
                                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', Setting::get('whatsapp_number', '082212345678'))); ?>" 
                                   class="text-primary-100 hover:text-white transition-colors">
                                    <?php echo e(Setting::get('whatsapp_number', '0822-1234-5678')); ?>

                                </a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph ph-envelope-simple text-white"></i>
                            </div>
                            <div class="text-sm">
                                <div class="text-white font-semibold mb-0.5">Email</div>
                                <a href="mailto:<?php echo e(Setting::get('contact_email', 'info@bintangtani.com')); ?>" 
                                   class="text-primary-100 hover:text-white transition-colors">
                                    <?php echo e(Setting::get('contact_email', 'info@bintangtani.com')); ?>

                                </a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph ph-map-pin text-white"></i>
                            </div>
                            <div class="text-sm">
                                <div class="text-white font-semibold mb-0.5">Alamat</div>
                                <p class="text-primary-100 leading-relaxed">
                                    <?php echo e(Setting::get('store_address', 'Jl. Pertanian No. 125, Jakarta Selatan, Indonesia')); ?>

                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="ph ph-clock text-white"></i>
                            </div>
                            <div class="text-sm">
                                <div class="text-white font-semibold mb-0.5">Jam Operasional</div>
                                <p class="text-primary-100 leading-relaxed">
                                    Senin - Sabtu<br>08:00 - 17:00 WIB
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter Section -->
            <div class="mt-12 p-6 glass rounded-2xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="ph ph-envelope-open text-white text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-lg">Dapatkan Info Terbaru</h4>
                            <p class="text-primary-100 text-sm">Berlangganan newsletter untuk promo dan tips pertanian</p>
                        </div>
                    </div>
                    <form class="flex gap-3 w-full md:w-auto" onsubmit="event.preventDefault(); alert('Fitur newsletter akan segera hadir!');">
                        <input type="email" 
                               placeholder="Masukkan email Anda" 
                               class="flex-1 md:w-64 px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/30 rounded-xl text-white placeholder-primary-200 focus:outline-none focus:bg-white/20 transition-all">
                        <button type="submit" 
                                class="px-6 py-3 bg-white text-primary-700 font-semibold rounded-xl hover:bg-primary-50 transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                            <span>Berlangganan</span>
                            <i class="ph ph-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/10">
            <div class="px-6 py-6 md:px-12 max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-copyright text-primary-300"></i>
                        <p class="text-primary-200 text-sm">
                            <?php echo e($currentYear); ?> <?php echo e($storeName); ?>. Seluruh hak cipta dilindungi.
                        </p>
                    </div>
                    <div class="flex items-center gap-6 text-sm">
                        <a href="/user/syarat-ketentuan" class="text-primary-200 hover:text-white transition-colors flex items-center gap-1.5">
                            <i class="ph ph-file-text"></i>
                            Syarat
                        </a>
                        <span class="w-1 h-1 bg-primary-400 rounded-full"></span>
                        <a href="/user/kebijakan-privasi" class="text-primary-200 hover:text-white transition-colors flex items-center gap-1.5">
                            <i class="ph ph-shield"></i>
                            Privasi
                        </a>
                        <span class="w-1 h-1 bg-primary-400 rounded-full"></span>
                        <a href="/user/kontak" class="text-primary-200 hover:text-white transition-colors flex items-center gap-1.5">
                            <i class="ph ph-envelope"></i>
                            Kontak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float 8s ease-in-out infinite;
    animation-delay: 4s;
}

/* Glass Effect for Newsletter */
.glass {
    @apply bg-white/10 backdrop-blur-md border border-white/20;
}

/* Link Hover Effects */
a {
    @apply transition-all duration-200;
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .animate-float,
    .animate-float-delayed {
        animation: none;
    }
}
</style>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\components\footer.blade.php ENDPATH**/ ?>