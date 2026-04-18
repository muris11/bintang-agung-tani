<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo e($order->order_number); ?></title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 18px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .brand {
            max-width: 60%;
        }

        .brand h1 {
            margin: 0 0 4px 0;
            font-size: 22px;
            color: #166534;
        }

        .brand .muted {
            color: #6b7280;
        }

        .meta {
            text-align: right;
            min-width: 220px;
        }

        .meta .invoice-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 16px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td {
            vertical-align: top;
            padding: 0;
        }

        .two-col td {
            width: 50%;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #111827;
        }

        .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: block;
            margin-bottom: 2px;
        }

        .value {
            margin-bottom: 10px;
            font-weight: 600;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th,
        table.items td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 8px;
            text-align: left;
        }

        table.items th {
            background: #f9fafb;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            width: 320px;
            margin-left: auto;
            margin-top: 16px;
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .summary-row.total {
            font-size: 14px;
            font-weight: 700;
            color: #166534;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
        }

        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <?php
        $storeName = \App\Models\Setting::get('store_name', 'Bintang Agung Tani');
        $storeBranch = \App\Models\Setting::get('store_branch', 'Cabang Utama');
        $storeAddressLine1 = \App\Models\Setting::get('store_address_line1', 'Jl. Raya Pertanian No.12');
        $storeAddressLine2 = \App\Models\Setting::get('store_address_line2', 'Kabupaten Sukabumi, Jawa Barat');
        $storePhone = \App\Models\Setting::get('store_phone', '(0266) 123456');
        $storeEmail = \App\Models\Setting::get('contact_email', 'info@bintangtani.com');
    ?>

    <div class="header">
        <div class="brand">
            <h1><?php echo e($storeName); ?></h1>
            <div class="muted"><?php echo e($storeBranch); ?></div>
            <div class="muted"><?php echo e($storeAddressLine1); ?></div>
            <div class="muted"><?php echo e($storeAddressLine2); ?></div>
            <div class="muted"><?php echo e($storePhone); ?> | <?php echo e($storeEmail); ?></div>
        </div>

        <div class="meta">
            <div class="invoice-title">INVOICE</div>
            <div class="muted"><?php echo e($order->order_number); ?></div>
            <div class="muted"><?php echo e($order->created_at?->format('d M Y, H:i')); ?></div>
            <div style="margin-top: 8px;">
                <span class="badge"><?php echo e($order->getStatusLabel()); ?></span>
            </div>
        </div>
    </div>

    <table class="grid two-col" cellspacing="0" cellpadding="0">
        <tr>
            <td style="padding-right: 10px;">
                <div class="panel">
                    <div class="section-title">Pelanggan</div>
                    <span class="label">Nama</span>
                    <div class="value"><?php echo e($order->user->name); ?></div>
                    <span class="label">Email</span>
                    <div class="value"><?php echo e($order->user->email); ?></div>
                    <span class="label">Telepon</span>
                    <div class="value"><?php echo e($order->shipping_phone ?? $order->user->phone ?? '-'); ?></div>
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="panel">
                    <div class="section-title">Pengiriman</div>
                    <span class="label">Kurir</span>
                    <div class="value"><?php echo e($order->shipping_courier ?? '-'); ?></div>
                    <span class="label">Layanan</span>
                    <div class="value"><?php echo e($order->shipping_service ?? '-'); ?></div>
                    <span class="label">Alamat</span>
                    <div class="value"><?php echo e($order->shipping_address_snapshot); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <div class="panel">
        <div class="section-title">Rincian Pesanan</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?php echo e($item->product_name); ?></div>
                            <div style="color: #6b7280; font-size: 11px;">SKU: <?php echo e($item->product_sku ?? '-'); ?></div>
                        </td>
                        <td class="text-right"><?php echo e($item->quantity); ?></td>
                        <td class="text-right">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></td>
                        <td class="text-right">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp <?php echo e(number_format($order->subtotal, 0, ',', '.')); ?></span>
            </div>
            <div class="summary-row">
                <span>Diskon</span>
                <span>Rp <?php echo e(number_format($order->discount_amount, 0, ',', '.')); ?></span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp <?php echo e(number_format($order->shipping_cost, 0, ',', '.')); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>Rp <?php echo e(number_format($order->total_amount, 0, ',', '.')); ?></span>
            </div>
        </div>
    </div>

    <div class="footer">
        Invoice ini dibuat otomatis oleh sistem pada <?php echo e(now()->format('d M Y, H:i')); ?>.
    </div>
</body>
</html><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\invoices\order.blade.php ENDPATH**/ ?>