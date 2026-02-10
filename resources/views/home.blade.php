@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="pt-32 pb-24 bg-gradient-to-r from-blue-700 to-blue-500 text-white text-center">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-5xl font-bold mb-6">a Global Documentation, Localization & Packaging Solutions</h1>
        <p class="text-xl mb-8 opacity-90">what to put here?</p>
        <a href="#" class="px-8 py-4 bg-white text-blue-700 font-semibold rounded-lg shadow hover:bg-gray-100 transition">Learn More</a>
    </div>
</section>

<!-- Services Section -->
<section class="py-24">
    <div class="max-w-6xl mx-auto px-6 text-justify">
        <h2 class="text-4xl font-bold mb-12">Our Core Services</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl font-semibold mb-4">Printing</h3>
                <p class="text-gray-700">We provide printed materials for any type of product. By leveraging our extensive network of regional print partners, we cut delivery times and reduce costs—giving us a clear competitive edge and ensuring faster, more efficient service for our customers.</p><br/>
                <p class="text-left">• Offset, Flexo-graphic and silk-screen printing</p>
                <p class="text-left">• Document printing, binding and finishing</p>
                <p class="text-left">• Label printing</p>
            </div>
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl font-semibold mb-4">Packaging</h3>
                <p class="text-gray-700">Our team of specialists design and develop any type of packaging for all industries. We can help you engineer and deliver at the shortest time with our "best and unbeatable price." Plus, we effectively manage our logistics to deliver these to you – anytime and whenever you need it.</p>
            </div>
            <div class="bg-white p-8 shadow rounded-xl">
                <h3 class="text-2xl font-semibold mb-4">Kitting</h3>
                <p class="text-gray-700">As a part of our full documentation engineering, fulfillment, and delivery solution, as needed. Our kitting operations scale easily and leverage solid inventory management, strict quality control, and reliable just-in-time delivery. With a global network of specialists, we evaluate your requirements and build a kitting program that fits them, wherever you and your customers are.</p>
            </div>
        </div>
    </div>
</section>
@endsection
