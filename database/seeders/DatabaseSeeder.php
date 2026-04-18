<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with comprehensive demo data.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive database seeding...');

        // Step 1: Create settings
        $this->command->info('Creating settings...');
        $this->call([
            SettingSeeder::class,
        ]);

        // Step 2: Create users (admin and regular users)
        $this->command->info('Creating users...');
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
        ]);

        // Get created users for later use
        $admin = User::where('is_admin', true)->first();
        $regularUsers = User::where('is_admin', false)->get();

        $this->command->info("Created {$regularUsers->count()} regular users and admin user");

        // Step 2: Create categories
        $this->command->info('Creating categories...');
        $categories = $this->createCategories();
        $this->command->info("Created {$categories->count()} categories");

        // Step 3: Create products distributed across categories
        $this->command->info('Creating products...');
        $products = $this->createProducts($categories);
        $this->command->info("Created {$products->count()} products");

        // Step 4: Create addresses for users
        $this->command->info('Creating addresses...');
        $this->createAddresses($regularUsers);
        $this->command->info('Addresses created');

        // Step 5: Create sample orders
        $this->command->info('Creating sample orders...');
        $orders = $this->createOrders($regularUsers, $products, 20);
        $this->command->info("Created {$orders->count()} orders");

        // Step 6: Create payments for orders
        $this->command->info('Creating payments...');
        $this->createPayments($orders);
        $this->command->info('Payments created');

        // Step 7: Create sample carts for some users
        $this->command->info('Creating sample carts...');
        $this->createSampleCarts($regularUsers->take(3), $products);
        $this->command->info('Sample carts created');

        // Step 8: Create sample payment methods
        $this->command->info('Creating sample payment methods...');
        $this->createSamplePaymentMethods();
        $this->command->info('Sample payment methods created');

        // Step 9: Create stock logs
        $this->command->info('Creating stock logs...');
        $this->call([
            StockLogSeeder::class,
        ]);

        // Step 10: Create payment proofs
        $this->command->info('Creating payment proofs...');
        $this->call([
            PaymentProofSeeder::class,
        ]);

        $this->command->info('========================================');
        $this->command->info('Database seeding completed successfully!');
        $this->command->info('========================================');
        $this->command->info('Summary:');
        $this->command->info("- Users: {$regularUsers->count()} regular + 2 admin");
        $this->command->info("- Categories: {$categories->count()}");
        $this->command->info("- Products: {$products->count()}");
        $this->command->info("- Orders: {$orders->count()}");
        $this->command->info('- Order Items: '.OrderItem::count());
        $this->command->info('- Payments: '.Payment::count());
        $this->command->info('- Payment Proofs: '.PaymentProof::count());
        $this->command->info('- Addresses: '.Address::count());
        $this->command->info('- Payment Methods: '.PaymentMethod::count());
        $this->command->info('- Stock Logs: '.StockLog::count());
        $this->command->info('========================================');
    }

    /**
     * Create categories with predefined names.
     */
    private function createCategories()
    {
        $categoryNames = [
            ['name' => 'Pupuk', 'icon' => 'ph-plant', 'description' => 'Berbagai jenis pupuk untuk pertanian'],
            ['name' => 'Bibit', 'icon' => 'ph-seedling', 'description' => 'Bibit tanaman berkualitas'],
            ['name' => 'Pestisida', 'icon' => 'ph-sprout', 'description' => 'Pestisida dan herbisida'],
            ['name' => 'Alat Pertanian', 'icon' => 'ph-tree', 'description' => 'Peralatan dan alat pertanian'],
            ['name' => 'Benih', 'icon' => 'ph-grains', 'description' => 'Benih padi, jagung, dan sayuran'],
            ['name' => 'Media Tanam', 'icon' => 'ph-drop', 'description' => 'Media tanam dan cocopeat'],
            ['name' => 'Nutrisi', 'icon' => 'ph-cube', 'description' => 'Nutrisi dan vitamin tanaman'],
            ['name' => 'Obat-obatan', 'icon' => 'ph-first-aid', 'description' => 'Obat dan antiseptik pertanian'],
        ];

        $categories = collect();
        foreach ($categoryNames as $index => $data) {
            $category = Category::firstOrCreate(
                ['slug' => str()->slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
            $categories->push($category);
        }

        return $categories;
    }

    /**
     * Create products distributed across categories.
     */
    private function createProducts($categories)
    {
        $products = collect();
        $productNames = [
            'Pupuk' => ['NPK Phonska', 'Urea', 'ZA', 'SP-36', 'KCl', 'Phonska Plus', 'NPK Mutiara'],
            'Bibit' => ['Bibit Padi', 'Bibit Jagung', 'Bibit Cabai', 'Bibit Tomat', 'Bibit Sayuran'],
            'Pestisida' => ['Decis', 'Bassa', 'Dursban', 'Curacron', 'Antracol', 'Dithane'],
            'Alat Pertanian' => ['Cangkul', 'Sabit', 'Sprayer', 'Sekop', 'Garpu Tanah', 'Parang'],
            'Benih' => ['Benih Padi IR64', 'Benih Jagung Manis', 'Benih Cabai Rawit', 'Benih Tomat'],
            'Media Tanam' => ['Cocopeat', 'Sekam Bakar', 'Kompos', 'Pupuk Kandang'],
            'Nutrisi' => ['Nutrisi A', 'Nutrisi B', 'Micronutrient', 'ZPT', 'Gandasil'],
            'Obat-obatan' => ['Fungsida', 'Bakterisida', 'Insektisida', 'Zpt Akar'],
        ];

        // Create 5 products per category = 40 total
        foreach ($categories as $category) {
            $names = $productNames[$category->name] ?? ['Produk '.$category->name];
            $numProducts = 5;

            for ($i = 0; $i < $numProducts; $i++) {
                $baseName = $names[$i % count($names)];
                $name = $baseName.' '.fake()->randomElement(['Premium', 'Standard', 'Plus', 'Super']);

                $price = fake()->randomElement([25000, 50000, 75000, 100000, 150000, 200000, 250000]);
                $stock = fake()->numberBetween(10, 100);

                // Create some discounted products (30% chance)
                $hasDiscount = fake()->boolean(30);
                $discountPrice = $hasDiscount ? round($price * 0.85, -3) : null;

                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => str()->slug($name.' '.fake()->unique()->numberBetween(1, 9999)),
                    'description' => fake()->paragraph(3),
                    'short_description' => fake()->sentence(),
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'stock' => $stock,
                    'min_order' => 1,
                    'max_order' => fake()->randomElement([null, 10, 20, 50]),
                    'sku' => 'SKU-'.strtoupper(fake()->lexify('????')).fake()->unique()->numberBetween(100, 999),
                    'unit' => fake()->randomElement(['pcs', 'kg', 'pack', 'liter', 'sak']),
                    'weight' => fake()->randomFloat(2, 0.5, 50),
                    'is_featured' => fake()->boolean(10),
                    'is_active' => true,
                    'view_count' => fake()->numberBetween(0, 500),
                ]);

                $products->push($product);
            }
        }

        return $products;
    }

    /**
     * Create addresses for users.
     */
    private function createAddresses($users)
    {
        foreach ($users as $user) {
            // Create 1-3 addresses per user
            $numAddresses = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $numAddresses; $i++) {
                Address::create([
                    'user_id' => $user->id,
                    'label' => fake()->randomElement(['Rumah', 'Kantor', 'Toko', 'Gudang']),
                    'recipient_name' => $user->name,
                    'phone' => fake()->phoneNumber(),
                    'full_address' => fake()->streetAddress(),
                    'province' => fake()->state(),
                    'city' => fake()->city(),
                    'district' => fake()->optional()->city(),
                    'postal_code' => fake()->postcode(),
                    'is_default' => $i === 0, // First address is default
                    'notes' => fake()->optional(0.3)->sentence(),
                ]);
            }
        }
    }

    /**
     * Create sample orders.
     */
    private function createOrders($users, $products, $count)
    {
        $orders = collect();

        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $address = $user->addresses->first();

            $status = fake()->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_MENUNGGU_VERIFIKASI,
                Order::STATUS_PROCESSING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ]);

            // Create order items first to calculate totals
            $numItems = fake()->numberBetween(1, 5);
            $orderItems = [];
            $subtotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = fake()->numberBetween(1, 5);
                $price = $product->getCurrentPrice();
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discountAmount = fake()->boolean(30) ? round($subtotal * 0.1, -3) : 0;
            $shippingCost = fake()->randomElement([0, 15000, 20000, 25000, 30000]);
            $totalAmount = $subtotal - $discountAmount + $shippingCost;

            $paidAmount = match ($status) {
                Order::STATUS_PROCESSING, Order::STATUS_COMPLETED => $totalAmount,
                Order::STATUS_MENUNGGU_VERIFIKASI => $totalAmount,
                Order::STATUS_PENDING => 0,
                Order::STATUS_CANCELLED => fake()->boolean(50) ? $totalAmount : 0,
                default => 0,
            };

            $couriers = ['JNE', 'TIKI', 'POS Indonesia', 'J&T Express', 'SiCepat'];

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => $status,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'shipping_courier' => in_array($status, [Order::STATUS_PROCESSING, Order::STATUS_COMPLETED]) ? fake()->randomElement($couriers) : null,
                'shipping_service' => fake()->optional(0.6)->randomElement(['REG', 'OKE', 'YES']),
                'tracking_number' => in_array($status, [Order::STATUS_PROCESSING, Order::STATUS_COMPLETED]) ? fake()->regexify('[A-Z]{2}[0-9]{9,12}') : null,
                'shipping_address_snapshot' => $address?->getCompleteAddressAttribute() ?? fake()->address(),
                'shipping_phone' => $address?->phone ?? fake()->phoneNumber(),
                'payment_method' => fake()->randomElement(['Transfer Bank', 'COD', 'QRIS']),
                'paid_at' => $paidAmount > 0 ? fake()->dateTimeBetween('-30 days', 'now') : null,
                'notes' => fake()->optional(0.3)->sentence(),
            ]);

            // Create order items
            foreach ($orderItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    ...$itemData,
                    'discount_amount' => 0,
                ]);
            }

            $orders->push($order);
        }

        return $orders;
    }

    /**
     * Create payments for orders.
     */
    private function createPayments($orders)
    {
        foreach ($orders as $order) {
            if ($order->paid_amount > 0 && $order->status !== Order::STATUS_CANCELLED) {
                $status = fake()->randomElement([
                    Payment::STATUS_SUCCESS,
                    Payment::STATUS_SUCCESS,
                    Payment::STATUS_SUCCESS, // 75% success rate
                    Payment::STATUS_PENDING,
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'payment_method' => $order->payment_method ?? 'Transfer Bank',
                    'provider' => 'manual',
                    'provider_transaction_id' => fake()->optional()->uuid(),
                    'amount' => $order->total_amount,
                    'status' => $status,
                    'paid_at' => $status === Payment::STATUS_SUCCESS ? $order->paid_at : null,
                    'notes' => fake()->optional(0.3)->sentence(),
                ]);
            }
        }
    }

    /**
     * Create sample carts for some users.
     */
    private function createSampleCarts($users, $products)
    {
        foreach ($users as $user) {
            if (fake()->boolean(60)) { // 60% chance to have a cart
                $cart = Cart::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'total_amount' => 0,
                        'total_items' => 0,
                    ]
                );

                // Clear existing items if any
                $cart->items()->delete();

                // Add 1-5 items to cart
                $numItems = fake()->numberBetween(1, 5);
                $usedProducts = [];
                $cartTotal = 0;
                $cartItemCount = 0;

                for ($i = 0; $i < $numItems; $i++) {
                    $product = $products->whereNotIn('id', $usedProducts)->random();
                    $usedProducts[] = $product->id;

                    $quantity = fake()->numberBetween(1, 3);
                    $subtotal = $product->getCurrentPrice() * $quantity;
                    $cartTotal += $subtotal;
                    $cartItemCount += $quantity;

                    $cart->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->getCurrentPrice(),
                        'subtotal' => $subtotal,
                    ]);
                }

                // Update cart totals
                $cart->total_amount = $cartTotal;
                $cart->total_items = $cartItemCount;
                $cart->save();
            }
        }
    }

    /**
     * Create sample payment methods.
     */
    private function createSamplePaymentMethods(): void
    {
        $paymentMethods = [
            [
                'name' => 'BRI',
                'bank_name' => 'Bank Rakyat Indonesia',
                'account_number' => '123456789012345',
                'account_name' => 'PT Bintang Agung Tani',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'BCA',
                'bank_name' => 'Bank Central Asia',
                'account_number' => '9876543210',
                'account_name' => 'PT Bintang Agung Tani',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Mandiri',
                'bank_name' => 'Bank Mandiri',
                'account_number' => '555566667777',
                'account_name' => 'PT Bintang Agung Tani',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}
