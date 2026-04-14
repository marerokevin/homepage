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
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-3">
                                <?php echo e($leave->user->name ?? 'N/A'); ?>

                            </td>

                            <td class="p-3 capitalize">
                                <?php echo e($leave->leave_type); ?>

                            </td>

                            <td class="p-3">
                                <?php echo e($leave->start_date); ?>

                            </td>

                            <td class="p-3">
                                <?php echo e($leave->end_date); ?>

                            </td>

                            <td class="p-3">
                                <?php echo e($leave->days); ?>

                            </td>

                            <td class="p-3">
                                <span class="
                                    px-2 py-1 rounded text-xs
                                    <?php if($leave->status === 'approved'): ?> bg-green-200 text-green-800
                                    <?php elseif($leave->status === 'rejected'): ?> bg-red-200 text-red-800
                                    <?php else: ?> bg-yellow-200 text-yellow-800
                                    <?php endif; ?>
                                ">
                                    <?php echo e(ucfirst($leave->status)); ?>

                                </span>
                            </td>


                            <td class="p-3 flex gap-2">

                                <?php if(auth()->user()->role === 'clinic' && $leave->status === 'pending' && $leave->leave_type === 'sick'): ?>
                                    <form method="POST" action="<?php echo e(route('leaves.approve', $leave->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <input type="text" name="clinic_notes" placeholder="Clinic remarks" required class="border p-1 text-xs">

                                        <button class="bg-blue-600 text-white px-2 py-1 text-xs rounded">
                                            Fit to work
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if(in_array(auth()->user()->role, ['hr','supervisor']) && $leave->status !== 'approved'): ?>
                                    <form method="POST" action="<?php echo e(route('leaves.approve', $leave->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <button class="bg-green-600 text-white px-2 py-1 text-xs rounded">
                                            Approve
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($leave->status !== 'approved'): ?>
                                    <form method="POST" action="<?php echo e(route('leaves.reject', $leave->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <button class="bg-red-600 text-white px-2 py-1 text-xs rounded">
                                            Reject
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </td>

                            <td class="p-3 flex gap-2">

                                
                                <?php if($leave->status === 'pending'): ?>
                                    <form method="POST" action="<?php echo e(route('leaves.approve', $leave->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                    </form>

                                    
                                    <form method="POST" action="<?php echo e(route('leaves.reject', $leave->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">
                                            Reject
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No leave requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/index.blade.php ENDPATH**/ ?>