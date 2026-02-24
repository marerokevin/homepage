@extends('layouts.app')

@section('title', 'Services')

@section('content')
<section class="max-w-5xl mx-auto">
    <div>
        <img src="{{ asset('images/print.jpg') }}" class="mt-20 h-40 w-full object-cover"
                                alt="Printing"
                                loading="lazy">
    </div>
        <section id="printing" class="grid grid-cols-2 md:grid-cols-2 mt-20">
            <h3 class="text-3xl font-bold mt-8">Printing</h3>
            <div class="py-2 px-2">
                <p class="text-lg leading-relaxed py-2 px-2">
                    We provide printed materials for any type of product. Our extensive network of local printers enables us to reduce delivery time and costs – a competitive edge to better serve our clients.
                </p>
                <ul class="list-disc list-inside space-y-2 text-lg py-2">
                    <li>Offset, Flexo-graphic and silk-screen printing</li>
                    <li>Document printing, binding and finishing</li>
                    <li>Label printing</li>
                </ul>
        </section>


    <div>
        <img src="{{ asset('images/box.jpg') }}" class="mt-20 h-40 w-full object-cover"
                                alt="Printing"
                                loading="lazy">
    </div>
    <section id="packaging" class="grid grid-cols-3 md:grid-cols-2 mt-20">
        <h2 class="text-3xl font-bold mt-8">Packaging</h2>
            <p class="text-lg leading-relaxed">
                Our team of specialists design and develop any type of packaging for all industries. We can help you engineer and deliver at the shortest time with our "best and unbeatable price." Plus, we effectively manage our logistics to deliver these to you – anytime and whenever you need it.
            </p>
    </section>

    <div>
        <img src="{{ asset('images/kitting.jpg') }}" class="mt-20 h-40 w-full object-cover"
                                alt="kitting"
                                loading="lazy">
    </div>
    <section id="packaging" class="grid grid-cols-3 md:grid-cols-2 mt-20 pb-20">
        <h2 class="text-3xl font-bold mt-8">Kitting</h2>
            <p class="text-lg leading-relaxed">
                We provide kitting as part of a complete documentation engineering, fulfillment and delivery solution or as a stand-alone service, as needed. We offer scalable kitting solutions that combine our expertise in inventory management, quality control, just-in-time delivery and total customer satisfaction. Our network of professionals at our global facilities will work with you to assess your needs and will design a kitting program that meets them, no matter where you and your customers are located.
            </p>
    </section>

</section>
@endsection

