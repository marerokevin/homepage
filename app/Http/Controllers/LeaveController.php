<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Show the list of leaves for the logged-in user and subordinates.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->is_supervisor) {
            // Supervisor sees all department leaves
            $leaves = Leave::whereHas('user', function($query) use ($user) {
                $query->where('department', $user->department);
            })->orderBy('created_at', 'desc')->get();
        } else {
            // Normal user sees only their own leaves
            $leaves = Leave::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('leaves.index', compact('leaves'));
    }

    /**
     * Show the form to create a new leave.
     */
    public function create()
    {
        $users = User::all(); // for supervisor selection if needed
        return view('leaves.create', compact('users'));
    }

    /**
     * Handle storing a new leave request.
     */
    public function store(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'leave_type' => 'required|in:vacation,sick',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        // Calculate the number of days (inclusive)
        $start = strtotime($validated['start_date']);
        $end = strtotime($validated['end_date']);
        $days = ($end - $start) / 86400 + 1; // 86400 seconds in a day

        // Save to database
        Leave::create([
            'user_id'    => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days'       => $days,
            'reason'     => $validated['reason'] ?? null,
            'status'     => 'pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }
}
