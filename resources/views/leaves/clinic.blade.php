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

                            {{-- FIT TO WORK (NO LEAVE) --}}
                            <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="clinic_result" value="fit">

                                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs">
                                    Fit to Work
                                </button>
                            </form>

                            {{-- NOT FIT (CONTINUE LEAVE) --}}
                            <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="clinic_result" value="not_fit">

                                <button class="bg-yellow-600 text-white px-2 py-1 rounded text-xs">
                                    Not Fit
                                </button>
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
