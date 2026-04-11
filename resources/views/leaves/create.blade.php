@extends('layouts.app')

@section('title', 'File Leave')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">File a Leave</h2>
        <div class="bg-gray-100 p-4 rounded mb-4">
            <h3 class="font-bold text-lg mb-2">Leave Credits</h3>
            <p>Vacation Leave: <strong>{{ $user->vacation_leave_credits }}</strong></p>
            <p>Sick Leave: <strong>{{ $user->sick_leave_credits }}</strong></p>
        </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('leaves.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="leave_type" class="block font-semibold mb-2">Leave Type</label>
            <select name="leave_type" id="leave_type" class="w-full border px-3 py-2 rounded">
                <option value="">-- Select Type --</option>
                <option value="vacation" {{ old('leave_type')=='vacation' ? 'selected' : '' }}>Vacation</option>
                <option value="sick" {{ old('leave_type')=='sick' ? 'selected' : '' }}>Sick</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="start_date" class="block font-semibold mb-2">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label for="end_date" class="block font-semibold mb-2">End Date</label>
            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label for="reason" class="block font-semibold mb-2">Reason (optional)</label>
            <textarea name="reason" id="reason" rows="3" class="w-full border px-3 py-2 rounded">{{ old('reason') }}</textarea>
        </div>

        <div class="flex justify-between">
            <div class="flex justify-start">
                <a href="{{ route('leaves.index') }}"class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">Cancel</a>
            </div>
            <div class="flex justify-start">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Leave</button>
            </div>
        </div>
    </form>
</div>
@endsection
