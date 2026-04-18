<?php $__env->startSection('title', 'Leave Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-6xl mx-auto">

        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                Leave Requests
            </h1>

            <a href="<?php echo e(route('leaves.create')); ?>"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + File Leave
            </a>
        </div>

        
        <?php if(session('success')): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="p-3">User</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Start</th>
                        <th class="p-3">End</th>
                        <th class="p-3">Days</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b">

                    <td class="p-3"><?php echo e($leave->user->name ?? 'N/A'); ?></td>
                    <td class="p-3"><?php echo e(ucfirst($leave->leave_type)); ?></td>
                    <td class="p-3"><?php echo e($leave->start_date); ?></td>
                    <td class="p-3"><?php echo e($leave->end_date); ?></td>
                    <td class="p-3"><?php echo e($leave->days); ?></td>

                    <td class="p-3">
                        <?php
                            $labels = [
                                'pending' => 'Pending',
                                'pending_clinic' => 'Pending - Fit to Work',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'not_fit' => 'Not Fit',
                            ];
                        ?>

                        <?php echo e($labels[$leave->status] ?? $leave->status); ?>

                    </td>

                    <td class="p-3 flex gap-2">

                        
                        <?php if(auth()->user()->role === 'nurse' && $leave->status === 'pending_clinic'): ?>
                            <a href="<?php echo e(route('leaves.clinic', $leave->id)); ?>"
                               class="bg-blue-600 text-white px-2 py-1 rounded text-xs">
                               Evaluate
                            </a>
                        <?php endif; ?>

                        
                        <?php if(auth()->user()->is_supervisor && $leave->status === 'pending' && $leave->leave_type !== 'sick'): ?>

                            <form method="POST" action="<?php echo e(route('leaves.approve', $leave->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs">
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="<?php echo e(route('leaves.reject', $leave->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">
                                    Reject
                                </button>
                            </form>

                        <?php endif; ?>

                    </td>

                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center p-4">No leaves found</td>
                </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/index.blade.php ENDPATH**/ ?>