<?php

namespace App\Http\Controllers;

use App\Models\VehicleRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        // Laravel 11+ — middleware is applied via routes, not controller constructor
    }

    public function index(Request $request)
    {
        abort_if(!auth()->user()?->is_admin, 403, 'Access denied. Admins only.');

        $from   = $request->query('from', now()->startOfMonth()->toDateString());
        $to     = $request->query('to',   now()->endOfMonth()->toDateString());

        $trips = VehicleRequest::with('user:id,name')
            ->whereBetween('trip_date', [$from, $to])
            ->orderBy('trip_date')
            ->orderBy('departure')
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'user_name'   => $r->user->name,
                'pickup'      => $r->pickup,
                'destination' => $r->destination,
                'vehicle'     => $r->vehicle,
                'plate'       => $r->plate,
                'trip_date'   => $r->trip_date,
                'departure'   => $r->departure,
                'eta'         => $r->eta,
                'return_time' => $r->return_time,
                'return_date' => $r->return_date,
                'overnight'   => $r->return_date && $r->return_date !== $r->trip_date,
            ]);

        return view('reports.vehicle', compact('trips', 'from', 'to'));
    }

    public function exportPdf(Request $request)
    {
        abort_if(!auth()->user()?->is_admin, 403, 'Access denied. Admins only.');

        $from  = $request->query('from', now()->startOfMonth()->toDateString());
        $to    = $request->query('to',   now()->endOfMonth()->toDateString());

        $trips = VehicleRequest::with('user:id,name')
            ->whereBetween('trip_date', [$from, $to])
            ->orderBy('trip_date')
            ->orderBy('departure')
            ->get();

        $html = view('reports.vehicle_pdf', compact('trips', 'from', 'to'))->render();

        // Use wkhtmltopdf if available, otherwise serve as HTML
        $filename = 'vehicle-report-' . $from . '-to-' . $to . '.html';

        return response($html, 200, [
            'Content-Type'        => 'text/html',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function exportCsv(Request $request)
    {
        abort_if(!auth()->user()?->is_admin, 403, 'Access denied. Admins only.');

        $from  = $request->query('from', now()->startOfMonth()->toDateString());
        $to    = $request->query('to',   now()->endOfMonth()->toDateString());

        $trips = VehicleRequest::with('user:id,name')
            ->whereBetween('trip_date', [$from, $to])
            ->orderBy('trip_date')
            ->orderBy('departure')
            ->get();

        $filename = 'vehicle-report-' . $from . '-to-' . $to . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($trips) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                '#', 'Requested By', 'Trip Date', 'Departure',
                'ETA', 'Return Time', 'Return Date', 'Overnight',
                'Pickup', 'Destination', 'Vehicle', 'Plate'
            ]);

            foreach ($trips as $i => $r) {
                fputcsv($handle, [
                    $i + 1,
                    $r->user->name,
                    $r->trip_date,
                    $r->departure,
                    $r->eta ?? '—',
                    $r->return_time ?? '—',
                    $r->return_date ?? $r->trip_date,
                    ($r->return_date && $r->return_date !== $r->trip_date) ? 'Yes' : 'No',
                    $r->pickup,
                    $r->destination,
                    $r->vehicle,
                    $r->plate,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
