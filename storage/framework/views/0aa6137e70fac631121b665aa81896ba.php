<?php
use App\Models\Setting;
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?> - <?php echo $__env->yieldContent('title', 'Login'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons (local to avoid CORB issues) -->
    <link rel="stylesheet" href="<?php echo e(asset('fonts/phosphor/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('fonts/phosphor/bold/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('fonts/phosphor/fill/style.css')); ?>">
    
    <!-- Scripts & Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
</head>
<body class="font-sans antialiased text-gray-800 bg-white">
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>

<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/layouts/auth.blade.php ENDPATH**/ ?>