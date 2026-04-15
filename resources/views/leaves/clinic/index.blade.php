@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">Clinic Queue</h1>

    @foreach($leaves as $leave)
        <div class="p-4 mb-2 bg-white shadow rounded">
            <div>
                <strong>{{ $leave->user->name }}</strong>
            </div>

            <div>
                {{ $leave->start_date }} → {{ $leave->end_date }}
            </div>

            <a href="{{ route('leaves.clinic.show', $leave->id) }}"
               class="text-blue-600">
                Evaluate
            </a>
        </div>
    @endforeach

</div>
@endsection
