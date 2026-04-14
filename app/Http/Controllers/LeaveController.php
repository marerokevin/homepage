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

        if ($user->is_supervisor) {
            $leaves = Leave::whereHas('user', function ($q) use ($user) {
                $q->where('department', $user->department);
            })->latest()->get();
        } else {
            $leaves = Leave::where('user_id', $user->id)->latest()->get();
        }

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create', [
            'user' => auth()->user()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:vacation,sick',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
            'half_day'   => 'nullable|in:none,morning,afternoon',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);

        // 🚫 BLOCK OVERLAP
        $overlap = Leave::where('user_id', Auth::id())
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']]);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['error' => 'Overlapping leave exists'])->withInput();
        }

        // ✅ CALCULATE DAYS (NO SUNDAYS)
        $days = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isSunday()) continue;
            $days++;
        }

        // ✅ HALF DAY
        if (!empty($validated['half_day']) && $validated['half_day'] !== 'none') {
            $days -= 0.5;
        }

        // ✅ STATUS FLOW
        $status = $validated['leave_type'] === 'sick'
            ? 'pending_clinic'
            : 'pending';

        Leave::create([
            'user_id'    => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days'       => $days,
            'reason'     => $validated['reason'],
            'status'     => $status,
            'half_day'   => $validated['half_day'] ?? 'none'
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave submitted.');
    }

    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $user  = $leave->user;

        // 🏥 CLINIC STEP
        if (auth()->user()->role === 'clinic' && $leave->status === 'pending_clinic') {

            $request->validate([
                'clinic_notes' => 'required|string|max:1000',
                'fit_to_work'  => 'required|boolean'
            ]);

            $leave->clinic_notes = $request->clinic_notes;

            if ($request->fit_to_work) {
                // FIT → REJECT
                $leave->status = 'rejected';
            } else {
                // NOT FIT → PROCEED
                $leave->status = 'clinic_approved';
            }

            $leave->approved_by = auth()->id();
            $leave->save();

            return back()->with('success', 'Clinic evaluation submitted');
        }

        // 🧾 HR / SUPERVISOR FINAL APPROVAL
        if (in_array(auth()->user()->role, ['hr','supervisor']) && $leave->status === 'clinic_approved') {

            if ($leave->leave_type === 'vacation') {
                if ($user->vacation_leave_credits < $leave->days) {
                    return back()->withErrors(['error' => 'Not enough vacation credits']);
                }
                $user->vacation_leave_credits -= $leave->days;
            }

            if ($leave->leave_type === 'sick') {
                if ($user->sick_leave_credits < $leave->days) {
                    return back()->withErrors(['error' => 'Not enough sick credits']);
                }
                $user->sick_leave_credits -= $leave->days;
            }

            $user->save();

            $leave->status = 'approved';
            $leave->approved_by = auth()->id();
            $leave->save();

            return back()->with('success', 'Leave approved');
        }

        return back()->withErrors(['error' => 'Invalid approval flow']);
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status === 'approved') {
            return back()->withErrors(['error' => 'Already approved']);
        }

        $leave->status = 'rejected';
        $leave->approved_by = Auth::id();
        $leave->save();

        return back()->with('success', 'Leave rejected');
    }
}
