<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <a>test</a>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

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
