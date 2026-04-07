<?php
use App\Models\Setting;
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?> - <?php echo $__env->yieldContent('title', 'Solusi Pertanian'); ?></title>

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

    <style>
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.2s ease-out; }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-gradient-to-br from-gray-50 via-primary-50/10 to-primary-50/20">
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 via-primary-50/10 to-primary-50/20">
        <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main id="main-content" class="flex-1 w-full bg-gradient-to-br from-gray-50 via-primary-50/10 to-primary-50/20" role="main">
            <div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
        
        <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('components.toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('components.cart-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/layouts/app.blade.php ENDPATH**/ ?>