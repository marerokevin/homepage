@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h2 class="text-lg font-bold mb-4">Clinic Evaluation</h2>

    <p><strong>Employee:</strong> {{ $leave->user->name }}</p>
    <p><strong>Dates:</strong> {{ $leave->start_date }} - {{ $leave->end_date }}</p>

    <form method="POST" action="{{ route('leaves.clinic.update', $leave->id) }}">
        @csrf
        @method('PATCH')

        <textarea name="notes" class="w-full border p-2 mt-3"
                  placeholder="Clinic notes"></textarea>

        <div class="mt-4 flex gap-2">
            <button name="result" value="fit"
                    class="bg-green-600 text-white px-3 py-1">
                Fit to Work
            </button>

            <button name="result" value="not_fit"
                    class="bg-red-600 text-white px-3 py-1">
                Not Fit
            </button>
        </div>
    </form>

</div>
@endsection
