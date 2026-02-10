@extends('layouts.app')

@section('title', 'Company Profile')

@section('content')
<section class="max-w-5xl mx-auto space-y-12">
    <nav class="bg-hite shadow">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <h1 class="text-3xl font-bold mb-6">Company Profile</h1>
            <ul class="flex space-x-6 text-gray-700">
                <li><a href="{{ route('company.profile') }}" class="hover:text-blue-600">About Us</a></li>
                <li><a href="{{ route('local.office') }}" class="hover:text-blue-600">Local Office</a></li>
                <li><a href="#services" class="hover:text-blue-600">Download</a></li>
                <!-- <li><a href="#contact" class="hover:text-blue-600">Contact</a></li> -->
            </ul>
        </div>
    </nav>
    <h3 class="text-3xl font-bold mt-8">Local Office</h3>
    <p class="text-lg leading-relaxed">
        Block 19 Lot 2 Units 1-4 Centereach Resources, Inc. Building 5
        </br>Lima Technology Center
        </br>Lipa City, Batangas 4217 Philippines
        </br>Phone: (6343) 455-6907
</section>
@endsection

