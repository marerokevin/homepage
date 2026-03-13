<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <a>test</a>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <?php echo e(__("You're logged in!")); ?>

                </div>
            </div>

            
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                
                <a href="<?php echo e(route('calendar')); ?>"
                    class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-stone-200 dark:border-gray-700 p-5 hover:shadow-md hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-2xl shrink-0">
                        🚗
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:underline">Car Allocation</div>
                        <div class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">Schedule & manage vehicle requests</div>
                    </div>
                </a>

                
                <?php if(Auth::user()->is_admin): ?>
                <a href="<?php echo e(route('reports.vehicle')); ?>"
                    class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-stone-200 dark:border-gray-700 p-5 hover:shadow-md hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-2xl shrink-0">
                        📋
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:underline">Vehicle Reports</div>
                        <div class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">View & export allocation reports</div>
                    </div>
                </a>
                <?php endif; ?>

            </div>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/dashboard.blade.php ENDPATH**/ ?>