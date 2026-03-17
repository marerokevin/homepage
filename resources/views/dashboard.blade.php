<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Login success alert --}}
            @if(session()->has('url.intended') || !session()->has('_previous'))
            <div id="login-alert" class="mb-6 flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl px-5 py-4 text-sm font-medium shadow-sm transition-opacity duration-700 opacity-0">
                <span class="text-lg">✅</span>
                Welcome back, {{ Auth::user()->name }}! You're now logged in.
            </div>
            <script>
                const alert = document.getElementById('login-alert');
                // Fade in
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => { alert.style.opacity = '1'; });
                });
                // Fade out after 3 seconds
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 700);
                }, 3000);
            </script>
            @endif

            {{-- Quick Access --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Car Allocation --}}
                <a href="{{ route('calendar') }}"
                    class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-stone-200 dark:border-gray-700 p-5 hover:shadow-md hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-2xl shrink-0">
                        🚗
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:underline">Car Allocation</div>
                        <div class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">Schedule & manage vehicle requests</div>
                    </div>
                </a>

                {{-- Reports (admin only) --}}
                @if(Auth::user()->is_admin)
                <a href="{{ route('reports.vehicle') }}"
                    class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-stone-200 dark:border-gray-700 p-5 hover:shadow-md hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-2xl shrink-0">
                        📋
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 group-hover:underline">Vehicle Reports</div>
                        <div class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">View & export allocation reports</div>
                    </div>
                </a>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
