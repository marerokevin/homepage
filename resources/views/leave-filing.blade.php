@extends('layouts.app')

@section('title', 'Leave Filing')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">File a Leave Request</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('leave.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Leave Type --}}
            <div>
                <label for="leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Leave Type</label>
                <div class="mb-2 text-gray-700 dark:text-gray-200">
                    Available Vacation Leave: {{ auth()->user()->vacation_leave_credits }} days
                </div>
                <div class="mb-2 text-gray-700 dark:text-gray-200">
                    Available Sick Leave: {{ auth()->user()->sick_leave_credits }} days
                </div>

                <select name="leave_type" id="leave_type" class="...">
                    <option value="">Select leave type</option>
                    <option value="vacation">Vacation Leave ({{ auth()->user()->vacation_leave_credits }} days left)</option>
                    <option value="sick">Sick Leave ({{ auth()->user()->sick_leave_credits }} days left)</option>
                    <option value="emergency">Emergency Leave</option>
                    <option value="others">Others</option>
                </select>
                @error('leave_type')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Start Date --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                @error('start_date')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                @error('end_date')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Reason --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Reason</label>
                <textarea name="reason" id="reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                @error('reason')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Attachment --}}
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Attachment (optional)</label>
                <input type="file" name="attachment" id="attachment" class="mt-1 block w-full text-gray-700 dark:text-gray-100">
                @error('attachment')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow">
                    Submit Leave Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
