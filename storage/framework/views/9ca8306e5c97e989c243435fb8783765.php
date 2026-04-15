<?php $__env->startSection('title', 'File Leave'); ?>
<?php $__env->startSection('content'); ?>

<div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">File a Leave</h2>

    
    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-xl mb-6">
        <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">Leave Credits</h3>
        <p class="text-gray-700 dark:text-gray-300">Vacation Leave: <strong><?php echo e($user->vacation_leave_credits); ?></strong></p>
        <p class="text-gray-700 dark:text-gray-300">Sick Leave: <strong><?php echo e($user->sick_leave_credits); ?></strong></p>
    </div>

    
    <?php if($errors->any()): ?>
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-xl">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>- <?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?php echo e(route('leaves.store')); ?>" method="POST" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div>
            <label for="leave_type" class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Leave Type</label>
            <select name="leave_type" id="leave_type"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                <option value="">-- Select Type --</option>
                <option value="vacation" <?php echo e(old('leave_type')=='vacation' ? 'selected' : ''); ?>>Vacation</option>
                <option value="sick" <?php echo e(old('leave_type')=='sick' ? 'selected' : ''); ?>>Sick</option>
            </select>
        </div>

        
        <div>
            <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Duration</label>
            <select name="duration" id="duration" required
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                <option value="full">Full Day</option>
                <option value="half">Half Day</option>
            </select>
        </div>

        
        <div id="halfDayOptions" class="hidden">
            <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Half Day Period</label>
            <select name="half_day_type"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
            </select>
        </div>

        
        <div>
            <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Day Type</label>
            <select name="day_period"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                <option value="full">Full Day</option>
                <option value="am">Half Day (Morning)</option>
                <option value="pm">Half Day (Afternoon)</option>
            </select>
        </div>

        
        <div>
            <label for="start_date" class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo e(old('start_date')); ?>"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
        </div>

        
        <div>
            <label for="end_date" class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">End Date</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo e(old('end_date')); ?>"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
        </div>

        
        <div>
            <label for="reason" class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Reason <span class="font-normal text-stone-400">(optional)</span></label>
            <textarea name="reason" id="reason" rows="3"
                class="w-full border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100"><?php echo e(old('reason')); ?></textarea>
        </div>

        
        <div class="flex justify-between pt-2">
            <a href="<?php echo e(route('leaves.index')); ?>"
                class="bg-red-600 text-white px-6 py-2 rounded-xl hover:bg-red-700 transition text-sm font-semibold">
                Cancel
            </a>
            <button type="submit"
                class="bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 px-6 py-2 rounded-xl hover:opacity-80 transition text-sm font-semibold">
                Submit Leave
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('duration').addEventListener('change', function () {
        document.getElementById('halfDayOptions').classList.toggle('hidden', this.value !== 'half');
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/create.blade.php ENDPATH**/ ?>