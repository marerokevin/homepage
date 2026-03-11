<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Request Report — {{ $from }} to {{ $to }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #f1f0ef; padding: 32px; }

        /* Print bar */
        .print-bar { background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; padding: 10px 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .print-bar span { font-size: 13px; color: #555; }
        .print-bar button { background: #111; color: #fff; border: none; padding: 7px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; }

        /* Header */
        .header { background: #fff; border-radius: 16px; padding: 20px 24px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .header-left h1 { font-size: 24px; font-weight: 700; color: #111; letter-spacing: -0.5px; }
        .header-left p  { font-size: 11px; color: #888; margin-top: 4px; }
        .header-right   { text-align: right; font-size: 11px; color: #666; }
        .header-right strong { display: block; font-size: 13px; color: #111; margin-bottom: 2px; }

        /* Summary cards */
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .summary-card { background: #fff; border-radius: 14px; padding: 14px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
        .summary-card .num   { font-size: 28px; font-weight: 700; color: #111; }
        .summary-card .label { font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px; }
        .summary-card.overnight .num { color: #d97706; }
        .summary-card.vehicles  .num { color: #2563eb; }
        .summary-card.users     .num { color: #16a34a; }

        /* Month section */
        .month-section { margin-bottom: 28px; }
        .month-title { font-size: 13px; font-weight: 700; color: #111; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; padding-left: 2px; }

        /* Calendar grid */
        .cal-wrapper { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .cal-header  { display: grid; grid-template-columns: repeat(7, 1fr); background: #111; }
        .cal-header-cell { text-align: center; padding: 8px 4px; font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #ccc; }
        .cal-header-cell.sun { color: #f87171; }
        .cal-grid    { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: #e5e5e5; }
        .cal-cell    { background: #fff; min-height: 90px; padding: 6px; }
        .cal-cell.other-month { background: #fafafa; }
        .cal-cell.today       { background: #fffbeb; }

        .day-num { width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 11px; font-weight: 600; margin-bottom: 3px; color: #374151; }
        .day-num.today-num    { background: #111; color: #fff; }
        .day-num.other-num    { color: #ccc; }
        .day-num.sun-num      { color: #ef4444; }
        .day-num.other-sun    { color: #fca5a5; }

        .trip-tag { font-size: 9px; padding: 3px 5px; border-radius: 5px; margin-bottom: 3px; background: #fef3c7; color: #92400e; font-weight: 600; line-height: 1.4; }
        .trip-tag.overnight-tag { background: #dbeafe; color: #1e40af; }
        .trip-tag .trip-dest  { font-size: 9px; font-weight: 700; margin-bottom: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .trip-tag .trip-meta  { font-size: 8px; font-weight: 400; opacity: 0.85; line-height: 1.5; }
        .more-tag { font-size: 9px; color: #aaa; padding-left: 2px; }

        /* Footer */
        .footer { margin-top: 20px; display: flex; justify-content: space-between; font-size: 10px; color: #aaa; }

        @media print {
            body { background: #fff; padding: 16px; }
            .print-bar { display: none; }
            .cal-wrapper { box-shadow: none; border: 1px solid #e5e5e5; }
            .header, .summary-card { box-shadow: none; border: 1px solid #f0f0f0; }
        }
    </style>
</head>
<body>

<div class="print-bar no-print">
    <span>Press <strong>Ctrl+P</strong> to print or save as PDF</span>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>

{{-- Header --}}
<div class="header">
    <div class="header-left">
        <h1>Vehicle Allocation Report</h1>
        <p>Crestec Philippines, Inc. — Lima Technology Center, Lipa City, Batangas</p>
    </div>
    <div class="header-right">
        <strong>Period Covered</strong>
        {{ \Carbon\Carbon::parse($from)->format('F d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('F d, Y') }}
        <br>Generated: {{ now()->format('F d, Y g:i A') }}
    </div>
</div>

@php
    $total     = $trips->count();
    $overnight = $trips->filter(fn($r) => $r->return_date && $r->return_date !== $r->trip_date)->count();
    $vehicles  = $trips->pluck('plate')->unique()->count();
    $users     = $trips->pluck('user_id')->unique()->count();
@endphp

{{-- Summary --}}
<div class="summary">
    <div class="summary-card">
        <div class="num">{{ $total }}</div>
        <div class="label">Total Trips</div>
    </div>
    <div class="summary-card overnight">
        <div class="num">{{ $overnight }}</div>
        <div class="label">Overnight</div>
    </div>
    <div class="summary-card vehicles">
        <div class="num">{{ $vehicles }}</div>
        <div class="label">Vehicles Used</div>
    </div>
    <div class="summary-card users">
        <div class="num">{{ $users }}</div>
        <div class="label">Requesters</div>
    </div>
</div>

@php
    // Build trip lookup keyed by date: ['YYYY-MM-DD' => [trips...]]
    $tripsByDate = [];
    foreach ($trips as $trip) {
        $start   = \Carbon\Carbon::parse($trip->trip_date);
        $end     = \Carbon\Carbon::parse($trip->return_date ?? $trip->trip_date);
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->toDateString();
            if (!isset($tripsByDate[$key])) $tripsByDate[$key] = [];
            $tripsByDate[$key][] = $trip;
            $current->addDay();
        }
    }

    // Get all months in the range
    $fromMonth = \Carbon\Carbon::parse($from)->startOfMonth();
    $toMonth   = \Carbon\Carbon::parse($to)->startOfMonth();
    $months    = [];
    $cursor    = $fromMonth->copy();
    while ($cursor->lte($toMonth)) {
        $months[] = $cursor->copy();
        $cursor->addMonth();
    }

    $today = \Carbon\Carbon::today()->toDateString();
    $days  = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
@endphp

{{-- One calendar per month --}}
@foreach($months as $month)
@php
    $firstDay    = $month->copy()->firstOfMonth()->dayOfWeek;
    $daysInMonth = $month->daysInMonth;
    $daysInPrev  = $month->copy()->subMonth()->daysInMonth;
    $cells       = [];

    // Previous month padding
    for ($i = $firstDay - 1; $i >= 0; $i--) {
        $cells[] = ['day' => $daysInPrev - $i, 'date' => null, 'other' => true];
    }
    // Current month days
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $date    = $month->copy()->setDay($d)->toDateString();
        $cells[] = ['day' => $d, 'date' => $date, 'other' => false];
    }
    // Next month padding
    $remaining = (7 - (count($cells) % 7)) % 7;
    for ($d = 1; $d <= $remaining; $d++) {
        $cells[] = ['day' => $d, 'date' => null, 'other' => true];
    }
@endphp

<div class="month-section">
    <div class="month-title">{{ $month->format('F Y') }}</div>
    <div class="cal-wrapper">
        <div class="cal-header">
            @foreach($days as $idx => $day)
            <div class="cal-header-cell {{ $idx === 0 ? 'sun' : '' }}">{{ $day }}</div>
            @endforeach
        </div>
        <div class="cal-grid">
            @foreach($cells as $colIdx => $cell)
            @php
                $isSunday  = ($colIdx % 7) === 0;
                $isToday   = $cell['date'] === $today;
                $cellTrips = $cell['date'] ? ($tripsByDate[$cell['date']] ?? []) : [];
            @endphp
            <div class="cal-cell {{ $cell['other'] ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                <div class="day-num
                    {{ $isToday ? 'today-num' : '' }}
                    {{ !$isToday && $cell['other'] && $isSunday ? 'other-sun' : '' }}
                    {{ !$isToday && $cell['other'] && !$isSunday ? 'other-num' : '' }}
                    {{ !$isToday && !$cell['other'] && $isSunday ? 'sun-num' : '' }}
                ">{{ $cell['day'] }}</div>

                @foreach(array_slice($cellTrips, 0, 2) as $t)
                @php
                    $isON       = $t->return_date && $t->return_date !== $t->trip_date;
                    $departure  = \Carbon\Carbon::parse($t->trip_date . ' ' . $t->departure)->format('g:i A');
                    $returnStr  = $t->return_time
                        ? \Carbon\Carbon::parse($t->return_time)->format('g:i A') . ($isON ? ' +1d' : '')
                        : '—';
                @endphp
                <div class="trip-tag {{ $isON ? 'overnight-tag' : '' }}">
                    <div class="trip-dest">🚗 {{ $t->destination }}</div>
                    <div class="trip-meta">
                        {{ $t->vehicle }} · {{ $t->plate }}<br>
                        🕐 {{ $departure }} → 🔁 {{ $returnStr }}<br>
                        👤 {{ $t->user->name }}
                    </div>
                </div>
                @endforeach
                @if(count($cellTrips) > 2)
                <div class="more-tag">+{{ count($cellTrips) - 2 }} more</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

<div class="footer">
    <span>Crestec Philippines, Inc. — Vehicle Allocation Report</span>
    <span>{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</span>
</div>

</body>
</html>
