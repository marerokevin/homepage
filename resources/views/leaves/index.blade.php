@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                Leave Requests
            </h1>

            <a href="{{ route('leaves.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                + File Leave
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="p-3">User</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Start</th>
                        <th class="p-3">End</th>
                        <th class="p-3">Days</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-3">
                                {{ $leave->user->name ?? 'N/A' }}
                            </td>

                            <td class="p-3 capitalize">
                                {{ $leave->type }}
                            </td>

                            <td class="p-3">
                                {{ $leave->start_date }}
                            </td>

                            <td class="p-3">
                                {{ $leave->end_date }}
                            </td>

                            <td class="p-3">
                                {{ $leave->days }}
                            </td>

                            <td class="p-3">
                                <span class="
                                    px-2 py-1 rounded text-xs
                                    @if($leave->status === 'approved') bg-green-200 text-green-800
                                    @elseif($leave->status === 'rejected') bg-red-200 text-red-800
                                    @else bg-yellow-200 text-yellow-800
                                    @endif
                                ">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>

                            <td class="p-3 flex gap-2">

                                {{-- Approve --}}
                                @if($leave->status === 'pending')
                                    <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('leaves.reject', $leave->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">
                                            Reject
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No leave requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
