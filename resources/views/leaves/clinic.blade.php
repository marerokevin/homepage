@extends('layouts.app')

@section('title', 'Clinic Evaluation')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">
            Clinic Evaluation (Sick Leaves)
        </h1>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="p-3">Employee</th>
                        <th class="p-3">Dates</th>
                        <th class="p-3">Reason</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($leaves as $leave)
                    <tr class="border-b dark:border-gray-700">
                        <td class="p-3">{{ $leave->user->name }}</td>
                        <td class="p-3">
                            {{ $leave->start_date }} → {{ $leave->end_date }}
                        </td>
                        <td class="p-3">{{ $leave->reason }}</td>

                        <td class="p-3 flex gap-2">
                        <form method="POST" action="{{ route('leaves.clinic.evaluate', $leave->id) }}">
                            @csrf
                            @method('PATCH')

                            {{-- Symptoms --}}
                            <label class="block text-xs font-semibold">Symptoms Felt</label>
                            <textarea name="symptoms" required class="border p-1 w-full mb-2"></textarea>

                            {{-- Medication --}}
                            <label class="block text-xs font-semibold">Medication Taken</label>
                            <textarea name="medication" class="border p-1 w-full mb-2"></textarea>

                            {{-- Visited Clinic --}}
                            <label class="block text-xs font-semibold">Visited Hospital/Clinic?</label>
                            <select name="visited_clinic" class="border p-1 w-full mb-2" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>

                            {{-- Symptoms still present --}}
                            <label class="block text-xs font-semibold">Symptoms Still Present?</label>
                            <select name="symptoms_present" class="border p-1 w-full mb-2" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>

                            {{-- Remarks --}}
                            <label class="block text-xs font-semibold">Clinic Remarks</label>
                            <textarea name="clinic_notes" class="border p-1 w-full mb-2"></textarea>

                            {{-- Decision --}}
                            <div class="flex gap-2 mt-2">
                                <button name="decision" value="fit"
                                    class="bg-green-600 text-white px-2 py-1 rounded">
                                    Fit to Work
                                </button>

                                <button name="decision" value="unfit"
                                    class="bg-red-600 text-white px-2 py-1 rounded">
                                    Not Fit
                                </button>
                            </div>
                        </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            No pending clinic evaluations
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
