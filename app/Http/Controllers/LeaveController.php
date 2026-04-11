<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

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
        $user = Auth::user(); // get logged-in user

        return view('leaves.create', compact('user'));
    }

    /**
     * Handle storing a new leave request.
     */
    public function store(Request $request)
    {
        // 1. Validate first
        $validated = $request->validate([
            'leave_type' => 'required|in:vacation,sick',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        // Check for overlapping leaves
        $overlap = Leave::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'start_date' => 'You already have a leave filed within this date range.'
            ])->withInput();
        }

        // 2. Use Carbon ONCE
        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);

        // 3. Calculate days (EXCLUDE Sundays)
        $days = collect(range(0, $start->diffInDays($end)))
            ->map(fn ($i) => $start->copy()->addDays($i))
            ->filter(fn ($date) => $date->dayOfWeek !== Carbon::SUNDAY)
            ->count();

        // 4. Prevent invalid leave
        if ($days <= 0) {
            return back()->withErrors([
                'start_date' => 'Selected dates do not contain valid leave days.'
            ])->withInput();
        }

        // 5. Save (DO NOT TOUCH $days anymore)
        Leave::create([
            'user_id'    => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days'       => $days,
            'reason'     => $validated['reason'] ?? null,
            'status'     => 'pending',
        ]);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);

        // prevent double approval
        if ($leave->status !== 'pending') {
            return back()->withErrors(['error' => 'Leave already processed']);
        }

        $user = User::findOrFail($leave->user_id);

        // check credits before approving
        if ($leave->leave_type === 'vacation') {
            if ($user->vacation_leave_credits < $leave->days) {
                return back()->withErrors(['error' => 'Not enough vacation leave credits']);
            }

            $user->vacation_leave_credits -= $leave->days;
        }

        if ($leave->leave_type === 'sick') {
            if ($user->sick_leave_credits < $leave->days) {
                return back()->withErrors(['error' => 'Not enough sick leave credits']);
            }

            $user->sick_leave_credits -= $leave->days;
        }

        // save user deduction
        $user->save();

        // approve leave
        $leave->status = 'approved';
        $leave->approved_by = Auth::id();
        $leave->save();

        return back()->with('success', 'Leave approved and credits deducted');
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['error' => 'Leave already processed']);
        }

        $leave->status = 'rejected';
        $leave->approved_by = Auth::id();
        $leave->save();

        return back()->with('success', 'Leave rejected');
    }
}
