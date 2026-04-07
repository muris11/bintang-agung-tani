<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Store Information
            [
                'key' => 'store_name',
                'value' => 'Bintang Agung Tani',
                'type' => 'text',
                'group' => 'store',
                'label' => 'Nama Toko',
                'description' => 'Nama lengkap toko yang akan ditampilkan di seluruh aplikasi',
                'is_active' => true,
            ],
            [
                'key' => 'store_branch',
                'value' => 'Cabang Agung Tani',
                'type' => 'text',
                'group' => 'store',
                'label' => 'Nama Cabang',
                'description' => 'Nama cabang toko',
                'is_active' => true,
            ],
            [
                'key' => 'store_address',
                'value' => 'Jl. Pertanian No. 125, Jakarta Selatan, Indonesia',
                'type' => 'text',
                'group' => 'store',
                'label' => 'Alamat Toko',
                'description' => 'Alamat lengkap toko',
                'is_active' => true,
            ],
            [
                'key' => 'store_address_line1',
                'value' => 'Jl. Raya Pertanian No.12, Kec. Cisaat',
                'type' => 'text',
                'group' => 'store',
                'label' => 'Alamat Baris 1',
                'description' => 'Baris pertama alamat untuk invoice',
                'is_active' => true,
            ],
            [
                'key' => 'store_address_line2',
                'value' => 'Kabupaten Sukabumi, Jawa Barat 43152',
                'type' => 'text',
                'group' => 'store',
                'label' => 'Alamat Baris 2',
                'description' => 'Baris kedua alamat untuk invoice',
                'is_active' => true,
            ],

            // Contact Information
            [
                'key' => 'whatsapp_number',
                'value' => '0822-1234-5678',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Nomor WhatsApp',
                'description' => 'Nomor WhatsApp untuk customer service (format: 08xx-xxxx-xxxx)',
                'is_active' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@bintangtani.com',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Email Kontak',
                'description' => 'Alamat email untuk customer service',
                'is_active' => true,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@bintangagungtani.com',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Email Support',
                'description' => 'Alamat email untuk bantuan teknis',
                'is_active' => true,
            ],

            // Operational
            [
                'key' => 'operational_hours',
                'value' => '08.00 - 17.00',
                'type' => 'text',
                'group' => 'operational',
                'label' => 'Jam Operasional',
                'description' => 'Jam operasional toko',
                'is_active' => true,
            ],
            [
                'key' => 'operational_hours_full',
                'value' => '08.00 - 16.00 WIB',
                'type' => 'text',
                'group' => 'operational',
                'label' => 'Jam Operasional Lengkap',
                'description' => 'Jam operasional dengan zona waktu',
                'is_active' => true,
            ],

            // Social Media Links
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/bintangagungtani',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Facebook',
                'description' => 'Link halaman Facebook',
                'is_active' => true,
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/bintangagungtani',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Instagram',
                'description' => 'Link halaman Instagram',
                'is_active' => true,
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/bintangtani',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Twitter',
                'description' => 'Link halaman Twitter/X',
                'is_active' => true,
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/bintangagungtani',
                'type' => 'text',
                'group' => 'social',
                'label' => 'YouTube',
                'description' => 'Link channel YouTube',
                'is_active' => true,
            ],

            // Statistics Display (for login page)
            [
                'key' => 'total_farmers',
                'value' => '10K+',
                'type' => 'text',
                'group' => 'statistics',
                'label' => 'Total Petani',
                'description' => 'Statistik jumlah petani (untuk tampilan landing page)',
                'is_active' => true,
            ],
            [
                'key' => 'total_products',
                'value' => '500+',
                'type' => 'text',
                'group' => 'statistics',
                'label' => 'Total Produk',
                'description' => 'Statistik jumlah produk',
                'is_active' => true,
            ],
            [
                'key' => 'total_orders',
                'value' => '50K+',
                'type' => 'text',
                'group' => 'statistics',
                'label' => 'Total Pesanan',
                'description' => 'Statistik jumlah pesanan',
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Clear settings cache
        Setting::clearCache();

        $this->command->info('Store settings seeded successfully!');
    }
}
