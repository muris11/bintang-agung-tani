<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Dashboard - Bintang Agung Tani</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #10b981;
            margin: 0;
        }
        .date {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #10b981;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #10b981;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bintang Agung Tani</h1>
        <h2>Laporan Dashboard</h2>
        <p class="date">Dicetak pada: <?php echo e(now()->format('d M Y H:i')); ?></p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo e($totalProducts); ?></div>
            <div class="stat-label">Total Produk Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo e($ordersThisMonth); ?></div>
            <div class="stat-label">Pesanan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
        </div>
    </div>

    <h3>Pesanan Terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pesanan</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($order->order_number); ?></td>
                <td><?php echo e($order->user->name ?? 'Unknown'); ?></td>
                <td><?php echo e($order->created_at->format('d M Y')); ?></td>
                <td>Rp <?php echo e(number_format($order->total_amount, 0, ',', '.')); ?></td>
                <td><?php echo e($order->status); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; <?php echo e(date('Y')); ?> Bintang Agung Tani - Semua Hak Dilindungi</p>
        <p>Laporan ini dibuat secara otomatis dari sistem.</p>
    </div>
</body>
</html><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\admin\exports\dashboard-pdf.blade.php ENDPATH**/ ?>