<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h2 class="text-lg font-bold mb-4">Clinic Evaluation</h2>

    <p><strong>Employee:</strong> <?php echo e($leave->user->name); ?></p>
    <p><strong>Dates:</strong> <?php echo e($leave->start_date); ?> - <?php echo e($leave->end_date); ?></p>

    <form method="POST" action="<?php echo e(route('leaves.clinic.update', $leave->id)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <textarea name="notes" class="w-full border p-2 mt-3"
                  placeholder="Clinic notes"></textarea>

        <div class="mt-4 flex gap-2">
            <button name="result" value="fit"
                    class="bg-green-600 text-white px-3 py-1">
                Fit to Work
            </button>

            <button name="result" value="not_fit"
                    class="bg-red-600 text-white px-3 py-1">
                Not Fit
            </button>
        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/leaves/clinic/show.blade.php ENDPATH**/ ?>