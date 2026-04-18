<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'nurse') {
            // Nurse sees only clinic cases
            $leaves = Leave::where('status', 'pending_clinic')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user->is_supervisor) {
            // Supervisor sees department leaves (non-sick approval)
            $leaves = Leave::whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            })->orderBy('created_at', 'desc')->get();

        } else {
            // Employee sees own
            $leaves = Leave::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $user = auth()->user(); // or Auth::user()

        return view('leaves.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:vacation,sick',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'duration'   => 'required|in:full,half',
            'half_day_type' => 'nullable|in:morning,afternoon',
            'reason'     => 'nullable|string|max:1000',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);

        // ✅ DAY CALCULATION
        if ($validated['duration'] === 'half') {

            if (!$start->isSameDay($end)) {
                return back()->withErrors(['error' => 'Half-day must be same date']);
            }

            $days = 0.5;

        } else {

            $days = collect(range(0, $start->diffInDays($end)))
                ->map(fn ($i) => $start->copy()->addDays($i))
                ->filter(fn ($date) => $date->dayOfWeek !== Carbon::SUNDAY)
                ->count();
        }

        // ✅ STATUS
        $status = $validated['leave_type'] === 'sick'
            ? 'pending_clinic'
            : 'pending';

        // ✅ SAVE (THIS WAS YOUR MISSING PIECE)
        Leave::create([
            'user_id'    => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days'       => $days,
            'duration'   => $validated['duration'],
            'half_day_type' => $validated['half_day_type'],
            'reason'     => $validated['reason'] ?? null,
            'status'     => $status,
        ]);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave filed successfully.');
    }

    // Supervisor approval (NON-SICK ONLY)
    public function approve($id)
    {
        $leave = Leave::findOrFail($id);
        $user = Auth::user();

        if (!$user->is_supervisor) {
            abort(403);
        }

        if ($leave->leave_type === 'sick') {
            return back()->withErrors(['error' => 'Sick leave handled by clinic']);
        }

        if ($leave->status !== 'pending') {
            return back()->withErrors(['error' => 'Already processed']);
        }

        $employee = User::findOrFail($leave->user_id);

        if ($leave->leave_type === 'vacation') {
            if ($employee->vacation_leave_credits < $leave->days) {
                return back()->withErrors(['error' => 'Not enough credits']);
            }
            $employee->vacation_leave_credits -= $leave->days;
        }

        $employee->save();

        $leave->status = 'approved';
        $leave->approved_by = Auth::id();
        $leave->save();

        return back()->with('success', 'Leave approved');
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);
        $user = Auth::user();

        if (!$user->is_supervisor) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return back()->withErrors(['error' => 'Already processed']);
        }

        $leave->status = 'rejected';
        $leave->approved_by = Auth::id();
        $leave->save();

        return back()->with('success', 'Leave rejected');
    }

    // 🔥 CLINIC EVALUATION (THIS IS THE KEY PART)
    public function clinicUpdate(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        if (Auth::user()->role !== 'nurse') {
            abort(403);
        }

        $request->validate([
            'result' => 'required|in:fit,not_fit'
        ]);

        if ($leave->status !== 'pending_clinic') {
            return back()->withErrors(['error' => 'Already evaluated']);
        }

        if ($request->result === 'fit') {
            // Employee is cleared → leave ends
            $leave->status = 'approved';
        } else {
            // Still sick
            $leave->status = 'not_fit';
        }

        $leave->save();

        return redirect()->route('leaves.index')
            ->with('success', 'Clinic evaluation submitted');
    }

    public function clinicIndex()
    {
        $leaves = Leave::where('status', 'pending_clinic')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.clinic', compact('leaves'));
    }

    public function clinicEvaluate(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $user  = $leave->user;

        $validated = $request->validate([
            'symptoms' => 'required|string',
            'medication' => 'nullable|string',
            'visited_clinic' => 'required|boolean',
            'symptoms_present' => 'required|boolean',
            'clinic_notes' => 'nullable|string',
            'decision' => 'required|in:fit,not_fit',
        ]);

        // Save evaluation history (DO NOT overwrite)
        LeaveClinicEvaluation::create([
            'leave_id' => $leave->id,
            'evaluated_by' => auth()->id(),
            'symptoms' => $validated['symptoms'],
            'medication' => $validated['medication'],
            'visited_clinic' => $validated['visited_clinic'],
            'symptoms_present' => $validated['symptoms_present'],
            'clinic_notes' => $validated['clinic_notes'],
            'decision' => $validated['decision'],
        ]);

        // 🔥 THIS IS WHAT YOU ARE MISSING
        if ($leave->leave_type === 'sick') {

            if ($user->sick_leave_credits < $leave->days) {
                return back()->withErrors(['error' => 'Not enough sick leave credits']);
            }

            // deduct ONLY ONCE
            if ($leave->status !== 'approved') {
                $user->sick_leave_credits -= $leave->days;
                $user->save();
            }
        }

        // Update leave status
        if ($validated['decision'] === 'fit') {
            $leave->status = 'approved'; // closed, employee is back
        } else {
            $leave->status = 'on_sick_leave'; // still resting
        }

        $leave->save();

        return redirect()->route('leaves.clinic')
            ->with('success', 'Clinic evaluation recorded');
    }
}
