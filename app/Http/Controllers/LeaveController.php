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

        // 👤 EMPLOYEE → only own leaves
        if ($user->role === 'employee') {
            $leaves = Leave::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        // 🏥 NURSE → ALL pending clinic leaves
        elseif ($user->role === 'nurse') {
            $leaves = Leave::where('status', 'pending_clinic')
                ->latest()
                ->get();
        }

        // 🧑‍💼 SUPERVISOR → same department only
        elseif ($user->role === 'supervisor') {
            $leaves = Leave::whereHas('user', function ($q) use ($user) {
                    $q->where('department', $user->department);
                })
                ->latest()
                ->get();
        }

        else {
            $leaves = collect(); // fallback, empty
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


        // =========================
        // SUPERVISOR LOGIC
        // =========================
        if (auth()->user()->is_supervisor) {

            if ($leave->status !== 'pending') {
                return back()->withErrors(['error' => 'Already processed']);
            }

            $user = $leave->user;

            if ($leave->leave_type === 'vacation') {
                if ($user->vacation_leave_credits < $leave->days) {
                    return back()->withErrors(['error' => 'Not enough credits']);
                }
                $user->vacation_leave_credits -= $leave->days;
            }

            if ($leave->leave_type === 'sick') {
                if ($user->sick_leave_credits < $leave->days) {
                    return back()->withErrors(['error' => 'Not enough credits']);
                }
                $user->sick_leave_credits -= $leave->days;
            }

            $user->save();

            $leave->status = 'approved';
            $leave->approved_by = auth()->id();
            $leave->save();

            return back()->with('success', 'Leave approved');
        }

        abort(403);
    }

    public function clinicIndex()
    {
        if (auth()->user()->role !== 'nurse') {
            abort(403);
        }

        $leaves = Leave::where('status', 'pending_clinic')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.clinic.index', compact('leaves'));
    }

    public function clinicShow($id)
    {
        if (auth()->user()->role !== 'nurse') {
            abort(403);
        }

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending_clinic') {
            return back()->withErrors(['error' => 'Invalid state']);
        }

        return view('leaves.clinic.show', compact('leave'));
    }

    public function clinicUpdate(Request $request, $id)
    {
        if (auth()->user()->role !== 'nurse') {
            abort(403);
        }

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending_clinic') {
            return back()->withErrors(['error' => 'Invalid state']);
        }

        $request->validate([
            'result' => 'required|in:fit,not_fit',
            'notes'  => 'nullable|string'
        ]);

        $leave->clinic_notes = $request->notes;

        $leave->status = $request->result === 'fit'
            ? 'fit_to_work'
            : 'not_fit';

        $leave->save();

        return redirect()->route('leaves.clinic')
            ->with('success', 'Evaluation saved');
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

    public function clinic()
    {
        // ONLY nurse should access this
        if (auth()->user()->role !== 'nurse') {
            abort(403);
        }

        $leaves = Leave::where('status', 'pending_clinic')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.clinic', compact('leaves'));
    }
}
