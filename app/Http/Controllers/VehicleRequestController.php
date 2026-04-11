<?php

namespace App\Http\Controllers;

use App\Models\VehicleRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VehicleRequestController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year  = $request->query('year', now()->year);

        $requests = VehicleRequest::with('user:id,name')
            ->where(function ($q) use ($year, $month) {
                $q->whereYear('trip_date', $year)->whereMonth('trip_date', $month);
            })
            ->orWhere(function ($q) use ($year, $month) {
                $q->whereYear('return_date', $year)->whereMonth('return_date', $month);
            })
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'trip_number'  => $r->trip_number,
                'user_id'      => $r->user_id,
                'user_name'    => $r->user->name,
                'booked_for'   => $r->booked_for,

                'pickup'       => $r->pickup,
                'destination'  => $r->destination,

                'vehicle'      => $r->vehicle,
                'plate'        => $r->plate,
                'driver'       => $r->driver,

                'trip_date'    => $r->trip_date,
                'departure'    => $r->departure,
                'eta'          => $r->eta,
                'return_time'  => $r->return_time,
                'return_date'  => $r->return_date,
            ]);

        return response()->json($requests);
    }

        public function resources()
        {
            $vehicles = Vehicle::select('id', 'name')->get(); // just id & name for select
            $drivers  = Driver::select('id', 'name')->get();

            return response()->json([
                'vehicle' => $vehicles,
                'driver'  => $drivers,
            ]);
        }

    public function users()
    {
        abort_if(!Auth::user()->is_vehicle_admin, 403);

        return response()->json(
            User::orderBy('name')->get(['id','name'])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup'      => 'required|string',
            'destination' => 'required|string',
            'vehicle_id'  => 'required|exists:vehicle,id',
            'driver_id'   => 'required|exists:driver,id',
            'trip_date'   => 'required|date',
            'departure'   => 'required',
            'eta'         => 'nullable',
            'return_time' => 'nullable',
            'booked_for'  => 'nullable|string|max:255',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // ← ADD HERE
        $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);
        $driver  = \App\Models\Driver::findOrFail($validated['driver_id']);

        $tripDate   = $validated['trip_date'];
        $departure  = $validated['departure'];
        $returnTime = $validated['return_time'] ?? null;

        if (Carbon::parse("$tripDate $departure")->isPast()) {
            return response()->json(['error' => 'Cannot book past trips.'], 422);
        }

        $returnDate = $returnTime && $returnTime < $departure
            ? Carbon::parse($tripDate)->addDay()->toDateString()
            : $tripDate;

        $newStart = Carbon::parse("$tripDate $departure");
        $newEnd   = $returnTime
            ? Carbon::parse("$returnDate $returnTime")
            : Carbon::parse("$returnDate 23:59:59");

        // Vehicle conflict
        $existing = VehicleRequest::where('vehicle_id', $validated['vehicle_id'])->get();

        foreach ($existing as $trip) {
            $existStart = Carbon::parse($trip->trip_date . ' ' . $trip->departure);
            $existEnd   = $trip->return_time
                ? Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' ' . $trip->return_time)
                : Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' 23:59:59');

            if ($newStart->lt($existEnd) && $newEnd->gt($existStart)) {
                return response()->json(['error' => 'Vehicle already booked for this time.'], 422);
            }
        }

        // Driver conflict (NEW)
        $driverTrips = VehicleRequest::where('driver_id', $validated['driver_id'])->get();

        foreach ($driverTrips as $trip) {
            $existStart = Carbon::parse($trip->trip_date . ' ' . $trip->departure);
            $existEnd   = $trip->return_time
                ? Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' ' . $trip->return_time)
                : Carbon::parse(($trip->return_date ?? $trip->trip_date) . ' 23:59:59');

            if ($newStart->lt($existEnd) && $newEnd->gt($existStart)) {
                return response()->json(['error' => 'Driver already assigned for this time.'], 422);
            }
        }

        $tripNumber = VehicleRequest::whereYear('trip_date', Carbon::parse($tripDate)->year)
            ->whereMonth('trip_date', Carbon::parse($tripDate)->month)
            ->max('trip_number') + 1;

        $req = VehicleRequest::create([
            'user_id'     => Auth::id(),
            'plate'       => $vehicle->plate,
            'vehicle_id'  => $validated['vehicle_id'],
            'driver_id'   => $validated['driver_id'],
            'driver'      => $driver->name,
            'pickup'      => $validated['pickup'],
            'destination' => $validated['destination'],
            'trip_date'   => $tripDate,
            'departure'   => $departure,
            'eta'         => $validated['eta'],
            'return_time' => $returnTime,
            'return_date' => $returnDate,
            'trip_number' => $tripNumber,
            'booked_for'  => $validated['booked_for'] ?? null,
        ]);

        $req->load(['user','vehicle','driver']);

        return response()->json([
            'id'           => $req->id,
            'user_id'      => $req->user_id,
            'user_name'    => $req->user->name,
            'vehicle'      => $req->vehicle->name,
            'plate'        => $req->vehicle->plate,
            'driver_name'  => $req->driver->name,
            'destination'  => $req->destination,
            'trip_date'    => $req->trip_date,
            'departure'    => $req->departure,
            'return_time'  => $req->return_time,
            'return_date'  => $req->return_date,
        ], 201);
    }

    public function destroy($id)
    {
        $req = VehicleRequest::findOrFail($id);

        if ($req->user_id !== Auth::id() && !Auth::user()->is_vehicle_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $req->delete();

        return response()->json(['success' => true]);
    }
}
