<?php $__env->startSection('title', 'Contact Us'); ?>

<?php $__env->startSection('content'); ?>

<section id="management-team" class="max-w-6xl mx-auto px-6 py-16">

    <div class="gap-12 items-start">

        <!-- Contact us-->
        <div class="py-2">
            <h1 class="py-2 text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                Contact Us
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                Have questions, suggestions, or partnership inquiries?
                Send us a message and we’ll get back to you as soon as possible.
            </p>

            <!-- President -->
            <div class="py-9 space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-950 dark:text-white">Moritomi Sakai</span>
                </div>
                <div>
                    President
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907
                </div>
            </div>
        </div>

        <div class="grid py-9 md:grid-cols-3">
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Ikuma Furuya</span>
                </div>
                <div>
                    Package Design and Development
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    ikuma.furuya@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 129
                </div>
            </div>
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Takashi Sarukawa</span>
                </div>
                <div>
                    General Manager
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    takashi.sarukawa@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 111
                </div>
            </div>
        </div>

        <div class="py-9 grid md:grid-cols-4 gap1.5">
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Bonamarie Manaloto</span>
                </div>
                <div>
                    Accounting Manager
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    bonamarie.manaloto@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 108
                </div>
            </div>
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Jerlyn Salay</span>
                </div>
                <div>
                    Administration Manager
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    jerlyn.salay@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 104
                </div>
            </div>
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Marivic Villamayor</span>
                </div>
                <div>
                    Procurement/Sales Manager
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    marivic.villamayor@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 115
                </div>
            </div>
            <div class="space-y-0 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-xl text-gray-800 dark:text-white">Cathee Espejon</span>
                </div>
                <div>
                    QA/Sales Manager
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    cathee.espejon@crestecphil.com.ph
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +(6343) 455 6907 loc. 115
                </div>
            </div>
        </div>
        <!-- RIGHT SIDE (FORM) -->
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-8 text-left">
            Contact Form
        </h2>
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8">

            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="text-sm list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('contact.submit')); ?>">
                <?php echo csrf_field(); ?>

                <div class="space-y-6">

                    <div>
                        <label class="block text-sm font-bold mb-2">Full Name</label>
                        <input type="text"
                               name="name"
                               value="<?php echo e(old('name')); ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Email Address</label>
                        <input type="email"
                               name="email"
                               value="<?php echo e(old('email')); ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Subject</label>
                        <input type="text"
                               name="subject"
                               value="<?php echo e(old('subject')); ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Message</label>
                        <textarea name="message"
                                  rows="5"
                                  required
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"><?php echo e(old('message')); ?></textarea>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-black font-bold py-3 rounded-lg shadow-md transition">
                            Send Message
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>

    <!-- OUR OFFICE -->
    <section id="local-offices" class="max-w-6xl mx-auto px-6 py-16">
    <div class="mt-20 border-t border-gray-200 dark:border-gray-700 pt-16">

        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-8 text-left">
            Our Office
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <!-- Office Details -->
            <div class="space-y-6 text-gray-600 dark:text-gray-400">

                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Crestec Philippines, Inc.
                    </h3>
                    <p>
                        Blk 19 Lot 2<br>
                        Units 1-4, CRI Bldg. 5<br>
                        Lima Technology Center<br>
                        San Lucas, Lipa City<br>
                        Batangas, Philippines
                    </p>
                </div>

                <div>
                    <p>
                        <span class="font-semibold text-gray-900 dark:text-white">Phone:</span>
                        +63 (43) 455 6907
                    </p>
                    <p>
                        <span class="font-semibold text-gray-900 dark:text-white">Email:</span>
                        noreply@crestecphil.com.ph
                    </p>
                </div>

                <div>
                    <p>
                        <span class="font-semibold text-gray-900 dark:text-white">Office Hours:</span><br>
                        Monday – Friday<br>
                        8:00 AM – 5:00 PM
                    </p>
                </div>

            </div>

            <!-- Google Map Embed -->
            <div class="w-full h-80 rounded-xl overflow-hidden shadow-lg">
                <iframe
                    src="https://www.google.com/maps?q=Crestec%20Philippines%20Inc%20Lipa%20Batangas&output=embed"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>

        </div>

</section>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/contact-us.blade.php ENDPATH**/ ?>