<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $uiSettings = Setting::getByGroup('ui');
        $storeSettings = Setting::getByGroup('store');
        $contactSettings = Setting::getByGroup('contact');
        $operationalSettings = Setting::getByGroup('operational');
        $socialSettings = Setting::getByGroup('social');
        $statisticsSettings = Setting::getByGroup('statistics');

        $categories = Category::active()->get();

        // Default UI settings
        $defaultUiSettings = [
            // Dashboard Sections
            'show_welcome_banner' => true,
            'show_categories_grid' => true,
            'show_stats_overview' => true,
            'show_best_sellers' => true,
            'show_new_arrivals' => true,
            'show_promo_banners' => true,

            // Product Page
            'show_category_filter' => true,
            'show_price_filter' => true,
            'products_per_page' => 20,

            // Features
            'enable_cart_drawer' => true,
            'enable_toast_notifications' => true,
            'enable_wishlist' => false,

            // Sidebar Categories
            'sidebar_category_count' => 8,
            'sidebar_categories' => [],

            // Navbar
            'show_search_bar' => true,
            'show_cart_icon' => true,
            'show_user_menu' => true,
        ];

        // Default Store settings
        $defaultStoreSettings = [
            'store_name' => 'Bintang Agung Tani',
            'store_branch' => 'Cabang Agung Tani',
            'store_address' => 'Jl. Pertanian No. 125, Jakarta Selatan, Indonesia',
            'store_address_line1' => 'Jl. Raya Pertanian No.12, Kec. Cisaat',
            'store_address_line2' => 'Kabupaten Sukabumi, Jawa Barat 43152',
            'store_phone' => '(0266) 123456',
        ];

        // Default Contact settings
        $defaultContactSettings = [
            'whatsapp_number' => '0822-1234-5678',
            'contact_email' => 'info@bintangtani.com',
            'support_email' => 'support@bintangagungtani.com',
        ];

        // Default Operational settings
        $defaultOperationalSettings = [
            'operational_hours' => '08.00 - 17.00',
            'operational_hours_full' => '08.00 - 16.00 WIB',
        ];

        // Default Social settings
        $defaultSocialSettings = [
            'social_facebook' => 'https://facebook.com/bintangagungtani',
            'social_instagram' => 'https://instagram.com/bintangagungtani',
            'social_twitter' => 'https://twitter.com/bintangtani',
            'social_youtube' => 'https://youtube.com/bintangagungtani',
        ];

        // Default Statistics settings
        $defaultStatisticsSettings = [
            'total_farmers' => '10K+',
            'total_products' => '500+',
            'total_orders' => '50K+',
        ];

        // Merge with saved settings
        $uiSettings = array_merge($defaultUiSettings, $uiSettings);
        $storeSettings = array_merge($defaultStoreSettings, $storeSettings);
        $contactSettings = array_merge($defaultContactSettings, $contactSettings);
        $operationalSettings = array_merge($defaultOperationalSettings, $operationalSettings);
        $socialSettings = array_merge($defaultSocialSettings, $socialSettings);
        $statisticsSettings = array_merge($defaultStatisticsSettings, $statisticsSettings);

        return view('admin.settings.index', compact(
            'uiSettings',
            'storeSettings',
            'contactSettings',
            'operationalSettings',
            'socialSettings',
            'statisticsSettings',
            'categories'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // UI Settings - Boolean toggles
            'show_welcome_banner' => 'nullable|boolean',
            'show_categories_grid' => 'nullable|boolean',
            'show_stats_overview' => 'nullable|boolean',
            'show_best_sellers' => 'nullable|boolean',
            'show_new_arrivals' => 'nullable|boolean',
            'show_promo_banners' => 'nullable|boolean',
            'show_category_filter' => 'nullable|boolean',
            'show_price_filter' => 'nullable|boolean',
            'enable_cart_drawer' => 'nullable|boolean',
            'enable_toast_notifications' => 'nullable|boolean',
            'enable_wishlist' => 'nullable|boolean',
            'show_search_bar' => 'nullable|boolean',
            'show_cart_icon' => 'nullable|boolean',
            'show_user_menu' => 'nullable|boolean',

            // UI Settings - Numbers
            'products_per_page' => 'nullable|integer|min:8|max:100',
            'sidebar_category_count' => 'nullable|integer|min:1|max:20',

            // UI Settings - Array
            'sidebar_categories' => 'nullable|array',
            'sidebar_categories.*' => 'exists:categories,id',

            // Store Settings
            'store_name' => 'nullable|string|max:100',
            'store_branch' => 'nullable|string|max:100',
            'store_address' => 'nullable|string|max:255',
            'store_address_line1' => 'nullable|string|max:255',
            'store_address_line2' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:20',

            // Contact Settings
            'whatsapp_number' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'support_email' => 'nullable|email|max:100',

            // Operational Settings
            'operational_hours' => 'nullable|string|max:50',
            'operational_hours_full' => 'nullable|string|max:50',

            // Social Settings
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',

            // Statistics Settings
            'total_farmers' => 'nullable|string|max:20',
            'total_products' => 'nullable|string|max:20',
            'total_orders' => 'nullable|string|max:20',
        ]);

        // Save each setting
        foreach ($validated as $key => $value) {
            if ($value === null) {
                continue;
            }

            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'number' : (is_array($value) ? 'json' : 'text'));

            // Determine group based on key prefix
            $group = match (true) {
                str_starts_with($key, 'show_') || str_starts_with($key, 'enable_') => 'ui',
                str_starts_with($key, 'store_') => 'store',
                str_starts_with($key, 'social_') => 'social',
                in_array($key, ['total_farmers', 'total_products', 'total_orders']) => 'statistics',
                in_array($key, ['operational_hours', 'operational_hours_full']) => 'operational',
                default => 'contact',
            };

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'type' => $type,
                    'group' => $group,
                    'label' => $this->getSettingLabel($key),
                    'description' => $this->getSettingDescription($key),
                    'is_active' => true,
                ]
            );
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function reset()
    {
        // Delete all UI settings
        Setting::where('group', 'ui')->delete();

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil direset ke default.');
    }

    private function getSettingLabel(string $key): string
    {
        return match ($key) {
            // UI Settings
            'show_welcome_banner' => 'Tampilkan Banner Selamat Datang',
            'show_categories_grid' => 'Tampilkan Grid Kategori',
            'show_stats_overview' => 'Tampilkan Ringkasan Statistik',
            'show_best_sellers' => 'Tampilkan Produk Terlaris',
            'show_new_arrivals' => 'Tampilkan Produk Terbaru',
            'show_promo_banners' => 'Tampilkan Banner Promo',
            'show_category_filter' => 'Tampilkan Filter Kategori',
            'show_price_filter' => 'Tampilkan Filter Harga',
            'enable_cart_drawer' => 'Aktifkan Cart Drawer',
            'enable_toast_notifications' => 'Aktifkan Notifikasi Toast',
            'enable_wishlist' => 'Aktifkan Wishlist',
            'products_per_page' => 'Produk Per Halaman',
            'sidebar_category_count' => 'Jumlah Kategori di Sidebar',
            'sidebar_categories' => 'Kategori Tertentu di Sidebar',
            'show_search_bar' => 'Tampilkan Search Bar',
            'show_cart_icon' => 'Tampilkan Icon Cart',
            'show_user_menu' => 'Tampilkan Menu User',

            // Store Settings
            'store_name' => 'Nama Toko',
            'store_branch' => 'Nama Cabang',
            'store_address' => 'Alamat Toko (Lengkap)',
            'store_address_line1' => 'Alamat Baris 1 (Invoice)',
            'store_address_line2' => 'Alamat Baris 2 (Invoice)',
            'store_phone' => 'Nomor Telepon Toko',

            // Contact Settings
            'whatsapp_number' => 'Nomor WhatsApp',
            'contact_email' => 'Email Kontak',
            'support_email' => 'Email Support',

            // Operational Settings
            'operational_hours' => 'Jam Operasional',
            'operational_hours_full' => 'Jam Operasional Lengkap',

            // Social Settings
            'social_facebook' => 'Facebook URL',
            'social_instagram' => 'Instagram URL',
            'social_twitter' => 'Twitter URL',
            'social_youtube' => 'YouTube URL',

            // Statistics Settings
            'total_farmers' => 'Total Petani (Display)',
            'total_products' => 'Total Produk (Display)',
            'total_orders' => 'Total Pesanan (Display)',

            default => $key,
        };
    }

    private function getSettingDescription(string $key): string
    {
        return match ($key) {
            'products_per_page' => 'Jumlah produk yang ditampilkan per halaman (8-100)',
            'sidebar_category_count' => 'Jumlah kategori yang muncul di sidebar user',
            'store_name' => 'Nama lengkap toko yang akan ditampilkan di seluruh aplikasi',
            'store_branch' => 'Nama cabang toko untuk ditampilkan di checkout',
            'whatsapp_number' => 'Nomor WhatsApp untuk customer service (format: 08xx-xxxx-xxxx)',
            'contact_email' => 'Alamat email untuk customer service',
            'support_email' => 'Alamat email untuk bantuan teknis (lupa password, dll)',
            'operational_hours' => 'Jam operasional toko (contoh: 08.00 - 17.00)',
            'operational_hours_full' => 'Jam operasional dengan zona waktu (contoh: 08.00 - 16.00 WIB)',
            'social_facebook' => 'URL halaman Facebook toko',
            'social_instagram' => 'URL halaman Instagram toko',
            'total_farmers' => 'Statistik jumlah petani untuk tampilan landing page',
            'total_products' => 'Statistik jumlah produk untuk tampilan landing page',
            default => '',
        };
    }
}
