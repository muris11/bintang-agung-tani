# Database Seeders - Bintang Agung Tani

## Quick Start

### Seed Everything (Fresh Database)
```bash
php artisan migrate:fresh --seed
```

### Seed Specific Seeder
```bash
# Admin only
php artisan db:seed --class=AdminSeeder

# Users only
php artisan db:seed --class=UserSeeder

# Categories only
php artisan db:seed --class=CategorySeeder

# Products only (requires categories)
php artisan db:seed --class=ProductSeeder

# Stock logs only (requires products)
php artisan db:seed --class=StockLogSeeder

# Payment proofs only (requires orders)
php artisan db:seed --class=PaymentProofSeeder
```

## Login Credentials

### Admin Accounts
- **Email:** admin@gmail.com
- **Password:** password123

- **Email:** admin@bintangagung.com
- **Password:** password123

### User Accounts
- **Email:** user@gmail.com
- **Password:** password123

Plus 5 more sample users (budi, siti, dewi, agus, rini)

## Data Summary

After running all seeders:

| Entity | Count |
|--------|-------|
| Admin Users | 2 |
| Regular Users | 6 |
| Categories | 10 |
| Products | 40 |
| Orders | 20 |
| Order Items | 59 |
| Payments | 8 |
| Payment Proofs | 8 |
| Addresses | 9 |
| Payment Methods | 3 |
| Stock Logs | 49 |
| Carts | ~3 |

## Seeder Hierarchy

```
DatabaseSeeder
├── SettingSeeder
├── AdminSeeder
├── UserSeeder
├── CategorySeeder (NEW)
├── ProductSeeder (NEW)
├── StockLogSeeder (NEW)
├── PaymentProofSeeder (NEW)
└── Internal Methods:
    ├── createAddresses()
    ├── createOrders()
    ├── createPayments()
    ├── createSampleCarts()
    └── createSamplePaymentMethods()
```

## Customization

### Add More Products
Edit `database/seeders/ProductSeeder.php`:
```php
private array $productTemplates = [
    'Pupuk' => [
        ['name' => 'Your New Product', 'price' => 99999, 'unit' => 'sak'],
        // ... more products
    ],
];
```

### Add More Categories
Edit `database/seeders/CategorySeeder.php`:
```php
private array $categories = [
    [
        'name' => 'Your Category',
        'icon' => 'ph-icon-name',
        'description' => 'Description',
    ],
];
```

## Troubleshooting

### Foreign Key Errors
Make sure to seed in correct order:
1. Categories first
2. Products (need categories)
3. Stock logs (need products)

### Unique Constraint Errors
Use `firstOrCreate()` instead of `create()` in seeders.

## Data Generated

### Realistic Data
- Real Indonesian names (Ahmad, Budi, Siti, etc.)
- Real product names (NPK Phonska, Urea, etc.)
- Realistic prices (Rp 15,000 - Rp 285,000)
- Realistic stock levels (20-150 units)

### Order Status Distribution
- pending
- payment_pending
- processing
- shipped
- delivered
- completed
- cancelled

All orders have proper relationships with users, addresses, and payments.
