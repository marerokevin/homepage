<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">Clinic Queue</h1>

    <?php $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="p-4 mb-2 bg-white shadow rounded">
            <div>
                <strong><?php echo e($leave->user->name); ?></strong>
            </div>

            <div>
                <?php echo e($leave->start_date); ?> → <?php echo e($leave->end_date); ?>

            </div>

            <a href="<?php echo e(route('leaves.clinic.show', $leave->id)); ?>"
               class="text-blue-600">
                Evaluate
            </a>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/clinic/index.blade.php ENDPATH**/ ?>