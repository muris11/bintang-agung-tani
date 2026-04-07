<?php
use App\Models\Setting;
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Admin <?php echo e(Setting::get('store_name', 'Bintang Agung Tani')); ?></title>

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
<body class="font-sans antialiased text-gray-700 bg-gray-50 text-render-optimized">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-primary text-white px-4 py-2 rounded-lg font-medium">
        Skip to main content
    </a>
    <div class="min-h-screen flex flex-col bg-white" x-data="{ sidebarOpen: false }">

        <!-- Admin Navbar -->
        <?php echo $__env->make('components.admin.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex flex-1 w-full relative overflow-hidden">
            <!-- Admin Sidebar -->
            <?php echo $__env->make('components.admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Main Content -->
            <main id="main-content" class="flex-1 min-w-0 flex flex-col relative overflow-y-auto md:ml-72" role="main" tabindex="-1">
                <div class="container-main py-4 md:py-5 min-h-full">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

<?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views/layouts/admin.blade.php ENDPATH**/ ?>