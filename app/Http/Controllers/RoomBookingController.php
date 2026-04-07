<?php

namespace App\Http\Controllers;

use App\Models\RoomBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RoomBookingController extends Controller
{
    const ROOMS = [
        'ODP Conference Room',
        'Admin Conference Room',
        'Lobby - A',
        'Lobby - B',
    ];

    public function index(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year  = $request->query('year',  now()->year);

        $bookings = RoomBooking::with('user:id,name')
            ->whereYear('booking_date', $year)
            ->whereMonth('booking_date', $month)
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'user_id'      => $b->user_id,
                'user_name'    => $b->user->name,
                'room'         => $b->room,
                'title'        => $b->title,
                'booking_date' => $b->booking_date,
                'start_time'   => $b->start_time,
                'end_time'     => $b->end_time,
                'attendees'    => $b->attendees,
            ]);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user(), 403);

        $validated = $request->validate([
            'room'         => 'required|string|in:' . implode(',', self::ROOMS),
            'title'        => 'required|string|max:255',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'attendees'    => 'required|integer|min:1',
        ]);

        // Block booking if date+time is in the past
        $bookingDateTime = \Carbon\Carbon::parse($validated['booking_date'] . ' ' . $validated['start_time']);
        if ($bookingDateTime->isPast()) {
            return response()->json([
                'error' => 'You cannot book a time that has already passed.'
            ], 422);
        }

        // Overlap check: same room, same date, overlapping time
        $conflict = RoomBooking::where('room', $validated['room'])
            ->where('booking_date', $validated['booking_date'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($q2) use ($validated) {
                    $q2->where('start_time', '<', $validated['end_time'])
                       ->where('end_time',   '>', $validated['start_time']);
                });
            })
            ->first();

        if ($conflict) {
            return response()->json([
                'error' => "This room is already booked from "
                    . Carbon::parse($conflict->start_time)->format('g:i A')
                    . " to "
                    . Carbon::parse($conflict->end_time)->format('g:i A')
                    . ". Please choose a different time or room."
            ], 422);
        }

        $booking = RoomBooking::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        $booking->load('user:id,name');

        return response()->json([
            'id'           => $booking->id,
            'user_id'      => $booking->user_id,
            'user_name'    => $booking->user->name,
            'room'         => $booking->room,
            'title'        => $booking->title,
            'booking_date' => $booking->booking_date,
            'start_time'   => $booking->start_time,
            'end_time'     => $booking->end_time,
            'attendees'    => $booking->attendees,
        ], 201);
    }

    public function destroy($id)
    {
        $booking = RoomBooking::findOrFail($id);

        if ($booking->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return response()->json(['error' => 'Only the person who made this booking can cancel it.'], 403);
        }

        $booking->delete();

        return response()->json(['success' => true]);
    }
}
