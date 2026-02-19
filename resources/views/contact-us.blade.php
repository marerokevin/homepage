
@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')

<section class="max-w-6xl mx-auto px-6 py-16">

    <div class="grid md:grid-cols-2 gap-12 items-start">

        <!-- President -->
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                Contact Us
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                Have questions, suggestions, or partnership inquiries?
                Send us a message and we’ll get back to you as soon as possible.
            </p>

            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
        </div>


        <div class="grid md:grid-cols-3">
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3">
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Email:</span>
                    info@example.com
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Phone:</span>
                    +63 900 000 0000
                </div>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">Address:</span>
                    San Adriano, Lipa, Batangas
                </div>
            </div>
        </div>
        </div>
        <!-- RIGHT SIDE (FORM) -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}">
                @csrf

                <div class="space-y-6">

                    <div>
                        <label class="block text-sm font-bold mb-2">Full Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Email Address</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Subject</label>
                        <input type="text"
                               name="subject"
                               value="{{ old('subject') }}"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Message</label>
                        <textarea name="message"
                                  rows="5"
                                  required
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">{{ old('message') }}</textarea>
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
@endsection
