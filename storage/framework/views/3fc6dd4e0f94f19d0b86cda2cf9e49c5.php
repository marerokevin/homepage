<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 dark:bg-gray-900 dark:text-white">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(isset($header)): ?>
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <main>
                <?php echo $__env->yieldContent('content'); ?>
                <?php echo e($slot ?? ''); ?>

            </main>
        </div>

        <script>
            // Apply theme immediately on page load (before DOM ready is fine for this part)
            if (localStorage.getItem('color-theme') === 'dark' ||
                (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            document.addEventListener('DOMContentLoaded', function () {
                const buttons = document.querySelectorAll('#theme-toggle, #theme-toggle-mobile');

                buttons.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const isDark = document.documentElement.classList.toggle('dark');
                        localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
                    });
                });
            });
        </script>

        
        <script>
            function toggleCompany() {
                document.getElementById('companyMenu').classList.toggle('hidden');
            }
            function showSide() {
                document.getElementById('sideMenu').classList.remove('hidden');
            }
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#companyBtn')) {
                    document.getElementById('companyMenu')?.classList.add('hidden');
                    document.getElementById('sideMenu')?.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
<?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/layouts/app.blade.php ENDPATH**/ ?>