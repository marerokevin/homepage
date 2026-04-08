<?php $__env->startSection('title', 'File Leave'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">File a Leave</h2>

    <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>- <?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('leaves.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="mb-4">
            <label for="leave_type" class="block font-semibold mb-2">Leave Type</label>
            <select name="leave_type" id="leave_type" class="w-full border px-3 py-2 rounded">
                <option value="">-- Select Type --</option>
                <option value="vacation" <?php echo e(old('leave_type')=='vacation' ? 'selected' : ''); ?>>Vacation</option>
                <option value="sick" <?php echo e(old('leave_type')=='sick' ? 'selected' : ''); ?>>Sick</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="start_date" class="block font-semibold mb-2">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo e(old('start_date')); ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label for="end_date" class="block font-semibold mb-2">End Date</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo e(old('end_date')); ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label for="reason" class="block font-semibold mb-2">Reason (optional)</label>
            <textarea name="reason" id="reason" rows="3" class="w-full border px-3 py-2 rounded"><?php echo e(old('reason')); ?></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Leave</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/create.blade.php ENDPATH**/ ?>