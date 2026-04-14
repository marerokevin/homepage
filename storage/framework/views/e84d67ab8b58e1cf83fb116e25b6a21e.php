<?php $__env->startSection('title', 'Vehicle Request Report'); ?>

<?php $__env->startSection('content'); ?>

<div class="min-h-screen bg-stone-100 dark:bg-gray-950 py-10 px-4">
    <div class="max-w-7xl mx-auto">

        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Vehicle Report</h1>
                <p class="text-sm text-stone-400 dark:text-gray-500 mt-1">Administration — Vehicle Allocation Summary</p>
            </div>
            <a href="<?php echo e(route('calendar')); ?>"
                class="px-4 py-1.5 text-sm font-semibold border border-stone-300 dark:border-gray-700 rounded-full text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                ← Back to Calendar
            </a>
        </div>

        
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-stone-200 dark:border-gray-800 p-5 mb-6">
            <form method="GET" action="<?php echo e(route('reports.vehicle')); ?>" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 dark:text-gray-400 mb-1 uppercase tracking-widest">From</label>
                    <input type="date" name="from" value="<?php echo e($from); ?>"
                        class="px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 dark:text-gray-400 mb-1 uppercase tracking-widest">To</label>
                    <input type="date" name="to" value="<?php echo e($to); ?>"
                        class="px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
                <button type="submit"
                    class="px-5 py-2 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                    Filter
                </button>

                
                <div class="flex gap-2 ml-auto">
                    <a href="<?php echo e(route('reports.vehicle.csv', ['from' => $from, 'to' => $to])); ?>"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-green-400 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                        ⬇ Export CSV
                    </a>
                    <a href="<?php echo e(route('reports.vehicle.pdf', ['from' => $from, 'to' => $to])); ?>" target="_blank"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        ⬇ Export PDF
                    </a>
                </div>
            </form>
        </div>

        
        <?php
            $total     = count($trips);
            $overnight = collect($trips)->where('overnight', true)->count();
            $vehicles  = collect($trips)->pluck('plate')->unique()->count();
            $users     = collect($trips)->pluck('user_name')->unique()->count();
        ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($total); ?></div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Total Trips</div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-amber-500"><?php echo e($overnight); ?></div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Overnight</div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-blue-500"><?php echo e($vehicles); ?></div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Vehicles Used</div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-green-500"><?php echo e($users); ?></div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Requesters</div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-stone-200 dark:border-gray-800 overflow-hidden">
            <?php if(count($trips) === 0): ?>
                <div class="py-16 text-center text-stone-400 dark:text-gray-600">
                    <div class="text-4xl mb-3">📋</div>
                    <p class="text-sm">No vehicle requests found for this period.</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-900 dark:bg-gray-950 text-stone-300 text-xs uppercase tracking-widest">
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Requested By</th>
                            <th class="px-4 py-3 text-left">Trip Date</th>
                            <th class="px-4 py-3 text-left">Departure</th>
                            <th class="px-4 py-3 text-left">ETA</th>
                            <th class="px-4 py-3 text-left">Return</th>
                            <th class="px-4 py-3 text-left">Destination</th>
                            <th class="px-4 py-3 text-left">Vehicle</th>
                            <th class="px-4 py-3 text-left">Plate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 dark:divide-gray-800">
                        <?php $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-stone-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-stone-400 dark:text-gray-600"><?php echo e($i + 1); ?></td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200"><?php echo e($trip['user_name']); ?></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <?php echo e(\Carbon\Carbon::parse($trip['trip_date'])->format('M d, Y')); ?>

                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <?php echo e(\Carbon\Carbon::parse($trip['trip_date'] . ' ' . $trip['departure'])->format('g:i A')); ?>

                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <?php echo e($trip['eta'] ? \Carbon\Carbon::parse($trip['trip_date'] . ' ' . $trip['eta'])->format('g:i A') : '—'); ?>

                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <?php if($trip['return_time']): ?>
                                    <?php echo e(\Carbon\Carbon::parse($trip['return_time'])->format('g:i A')); ?>

                                    <?php if($trip['overnight']): ?>
                                        <span class="ml-1 text-xs text-amber-500">🌙 <?php echo e(\Carbon\Carbon::parse($trip['return_date'])->format('M d')); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 max-w-xs truncate"><?php echo e($trip['destination']); ?></td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300"><?php echo e($trip['vehicle']); ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full bg-stone-100 dark:bg-gray-800 text-xs font-mono text-gray-600 dark:text-gray-400">
                                    <?php echo e($trip['plate']); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <p class="text-center text-xs text-stone-400 dark:text-gray-600 mt-6">
            Report generated <?php echo e(now()->format('F d, Y g:i A')); ?> · Crestec Philippines, Inc.
        </p>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/reports/vehicle.blade.php ENDPATH**/ ?>