<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="pt-2 pb-2 bg-gradient-to-r from-blue-700 to-blue-500 text-white text-left grid grid-cols-1 md:grid-cols-3">
    <div class="max-w-full mx-auto px-4 pr-2">
        <h1 class="text-5xl font-bold mb-6">a Global Documentation, Localization & Packaging Solutions</h1>
            <p class="text-xl mb-8 opacity-90">In a rapidly globalizing world, we turn innovation into practical solutions for our customers. Supported by research and green technology, we ensure value and reliability.</p>
            <button class="text-gray-400 hover:text-red-600 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                <a href="#" class="px-8 py-4 h-24 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-300 hover:text-blue-700 transition">Contact Us</a>
                    <img src="<?php echo e(asset('images/Contact Us-pixel.png')); ?>" class="h-14"
                                            alt="kitting"
                                            loading="lazy">
            </button>
        <a href="#" class="px-8 py-4 bg-white text-blue-700 font-semibold rounded-lg shadow hover:bg-gray-300 hover:text-blue-900 transition">Learn More</a>
    </div>
    <div class="max-w-full mx-auto px-4"></div>
    <div class="max-w-full mx-auto pt-72">
        <a href="#" class="px-8 py-4 bg-white text-blue-700 font-semibold rounded-lg shadow hover:bg-gray-300 hover:text-blue-900 transition">Learn More</a>
    </div>

</section>

<!-- Services Section -->
<section class="py-24">
    <div class="max-w-6xl mx-auto px-6 text-justify">
        <h2 class="text-4xl font-bold mb-12">Our Core Services</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl py-4 h-12 text-center font-semibold flex items-center justify-start">Printing
                    <img src="<?php echo e(asset('images/printing-pixel.png')); ?>" class="h-14"
                                            alt=""
                                            loading="lazy">
                </h3>
                <p class="text-gray-700">We provide printed materials for any type of product. By leveraging our extensive network of regional print partners, we cut delivery times and reduce costs—giving us a clear competitive edge and ensuring faster, more efficient service for our customers.</p><br/>
                <p class="text-left">• Offset, Flexo-graphic and silk-screen printing</p>
                <p class="text-left">• Document printing, binding and finishing</p>
                <p class="text-left">• Label printing</p>
            </div>
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl h-12 font-semibold py-4 flex items-center justify-start">Packaging
                    <img src="<?php echo e(asset('images/box-pixel.jpg')); ?>" class="h-9"
                                            alt="packaging"
                                            loading="lazy">
                </h3>
                <p class="text-gray-700">Our team of specialists design and develop any type of packaging for all industries. We can help you engineer and deliver at the shortest time with our "best and unbeatable price." Plus, we effectively manage our logistics to deliver these to you – anytime and whenever you need it.</p>
            </div>
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl font-semibold py-4 h-12 flex items-center justify-start">Kitting
                    <img src="<?php echo e(asset('images/kitting-pixel.png')); ?>" class="h-14"
                                            alt="kitting"
                                            loading="lazy">
                </h3>
                <p class="text-gray-700">As a part of our full documentation engineering, fulfillment, and delivery solution, as needed. Our kitting operations scale easily and leverage solid inventory management, strict quality control, and reliable just-in-time delivery. With a global network of specialists, we evaluate your requirements and build a kitting program that fits them, wherever you and your customers are.</p>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/home.blade.php ENDPATH**/ ?>