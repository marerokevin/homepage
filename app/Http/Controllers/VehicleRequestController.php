<?php

namespace App\Http\Controllers;

use App\Models\VehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VehicleRequestController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year  = $request->query('year',  now()->year);

        $requests = VehicleRequest::with('user:id,name')
            ->where(function ($q) use ($year, $month) {
                $q->whereYear('trip_date', $year)->whereMonth('trip_date', $month);
            })
            ->orWhere(function ($q) use ($year, $month) {
                $q->whereYear('return_date', $year)->whereMonth('return_date', $month);
            })
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'trip_number' => $r->trip_number,
                'user_id'     => $r->user_id,
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
            ]);

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup'      => 'required|string',
            'destination' => 'required|string',
            'vehicle'     => 'required|string',
            'plate'       => 'required|string',
            'trip_date'   => 'required|date',
            'departure'   => 'required',
            'eta'         => 'nullable',
            'return_time' => 'nullable',
            'return_date' => 'nullable|sometimes|date',
        ]);

        $tripDate   = $validated['trip_date'];
        $departure  = $validated['departure'];
        $returnTime = $validated['return_time'] ?? null;

        // Always compute return_date server-side
        if ($returnTime) {
            $returnDate = ($returnTime < $departure)
                ? Carbon::parse($tripDate)->addDay()->toDateString()
                : $tripDate;
        } else {
            $returnDate = $tripDate;
        }

        // Build full datetime for overlap comparison
        $newStart = Carbon::parse("$tripDate $departure");
        $newEnd   = $returnTime
            ? Carbon::parse("$returnDate $returnTime")
            : Carbon::parse("$returnDate 23:59:59");

        // Overlap check for same plate
        $existing = VehicleRequest::where('plate', $validated['plate'])->get();

        foreach ($existing as $trip) {
            $existStart = Carbon::parse($trip->trip_date . ' ' . $trip->departure);
            $existEnd   = $trip->return_time
                ? Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' ' . $trip->return_time)
                : Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' 23:59:59');

            if ($newStart->lt($existEnd) && $newEnd->gt($existStart)) {
                return response()->json([
                    'error' => 'This vehicle is already scheduled during that time ('
                        . $existStart->format('M d, Y g:i A') . ' – '
                        . $existEnd->format('M d, Y g:i A')
                        . '). Please choose a different time or vehicle.'
                ], 422);
            }
        }

        // Auto-increment trip number, resetting each month
        $lastTrip   = VehicleRequest::whereYear('trip_date', Carbon::parse($tripDate)->year)
            ->whereMonth('trip_date', Carbon::parse($tripDate)->month)
            ->max('trip_number');
        $tripNumber = ($lastTrip ?? 0) + 1;

        $vehicleRequest = VehicleRequest::create([
            ...$validated,
            'user_id'     => Auth::id(),
            'return_date' => $returnDate,
            'trip_number' => $tripNumber,
        ]);

        $vehicleRequest->load('user:id,name');

        return response()->json([
            'id'          => $vehicleRequest->id,
            'trip_number' => $vehicleRequest->trip_number,
            'user_id'     => $vehicleRequest->user_id,
            'user_name'   => $vehicleRequest->user->name,
            'pickup'      => $vehicleRequest->pickup,
            'destination' => $vehicleRequest->destination,
            'vehicle'     => $vehicleRequest->vehicle,
            'plate'       => $vehicleRequest->plate,
            'trip_date'   => $vehicleRequest->trip_date,
            'departure'   => $vehicleRequest->departure,
            'eta'         => $vehicleRequest->eta,
            'return_time' => $vehicleRequest->return_time,
            'return_date' => $vehicleRequest->return_date,
        ], 201);
    }

    public function destroy($id)
    {
        $vehicleRequest = VehicleRequest::findOrFail($id);

        if ($vehicleRequest->user_id !== Auth::id()) {
            return response()->json(['error' => 'Only the person who created this request can cancel it.'], 403);
        }

        $vehicleRequest->delete();

        return response()->json(['success' => true]);
    }
}
