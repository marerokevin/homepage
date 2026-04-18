<?php $__env->startSection('title', 'Clinic Evaluation'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">
            Clinic Evaluation (Sick Leaves)
        </h1>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="p-3">Employee</th>
                        <th class="p-3">Dates</th>
                        <th class="p-3">Reason</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b dark:border-gray-700">
                        <td class="p-3"><?php echo e($leave->user->name); ?></td>
                        <td class="p-3">
                            <?php echo e($leave->start_date); ?> → <?php echo e($leave->end_date); ?>

                        </td>
                        <td class="p-3"><?php echo e($leave->reason); ?></td>

                        <td class="p-3 flex gap-2">
                        <form method="POST" action="<?php echo e(route('leaves.clinic.evaluate', $leave->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            
                            <label class="block text-xs font-semibold">Symptoms Felt</label>
                            <textarea name="symptoms" required class="border p-1 w-full mb-2"></textarea>

                            
                            <label class="block text-xs font-semibold">Medication Taken</label>
                            <textarea name="medication" class="border p-1 w-full mb-2"></textarea>

                            
                            <label class="block text-xs font-semibold">Visited Hospital/Clinic?</label>
                            <select name="visited_clinic" class="border p-1 w-full mb-2" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>

                            
                            <label class="block text-xs font-semibold">Symptoms Still Present?</label>
                            <select name="symptoms_present" class="border p-1 w-full mb-2" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>

                            
                            <label class="block text-xs font-semibold">Clinic Remarks</label>
                            <textarea name="clinic_notes" class="border p-1 w-full mb-2"></textarea>

                            
                            <div class="flex gap-2 mt-2">
                                <button name="decision" value="fit"
                                    class="bg-green-600 text-white px-2 py-1 rounded">
                                    Fit to Work
                                </button>

                                <button name="decision" value="unfit"
                                    class="bg-red-600 text-white px-2 py-1 rounded">
                                    Not Fit
                                </button>
                            </div>
                        </form>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            No pending clinic evaluations
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/clinic.blade.php ENDPATH**/ ?>