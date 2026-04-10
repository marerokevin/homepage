<?php

namespace App\Http\Controllers;

use App\Mail\DeletionOtpMail;
use App\Models\DeletionOtp;
use App\Models\RoomBooking;
use App\Models\VehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    // Step 1: Request OTP
    public function send(Request $request)
    {
        $request->validate([
            'type'      => 'required|in:vehicle,room,post',
            'target_id' => 'required|integer',
        ]);

        $type     = $request->type;
        $targetId = $request->target_id;
        $user     = Auth::user();

        if ($type === 'vehicle') {
            $record = VehicleRequest::findOrFail($targetId);
            abort_if($record->user_id !== $user->id && !$user->is_vehicle_admin, 403);
            $label = "Vehicle Trip — {$record->destination} on " . \Carbon\Carbon::parse($record->trip_date)->format('M d, Y');
        } elseif ($type === 'room') {
            $record = RoomBooking::findOrFail($targetId);
            abort_if($record->user_id !== $user->id && !$user->is_admin, 403);
            $label = "{$record->room} — {$record->title} on " . \Carbon\Carbon::parse($record->booking_date)->format('M d, Y');
        } else {
            $record = \App\Models\Post::findOrFail($targetId);
            abort_if($record->user_id !== $user->id, 403);
            $label = "Post — {$record->title}";
        }

        // Clear old OTPs for this user+record
        DeletionOtp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('target_id', $targetId)
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DeletionOtp::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'target_id'  => $targetId,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new DeletionOtpMail($code, $type, $label));

        $maskedEmail = substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1];

        return response()->json([
            'success' => true,
            'email'   => $maskedEmail,
            'message' => "A 6-digit cancellation code has been sent to {$maskedEmail}.",
        ]);
    }

    // Step 2: Verify OTP and delete
    public function verify(Request $request)
    {
        $request->validate([
            'type'      => 'required|in:vehicle,room,post',
            'target_id' => 'required|integer',
            'code'      => 'required|string|size:6',
        ]);

        $user = Auth::user();

        $otp = DeletionOtp::where('user_id',   $user->id)
            ->where('type',      $request->type)
            ->where('target_id', $request->target_id)
            ->where('code',      $request->code)
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Invalid code. Please check and try again.'], 422);
        }

        if ($otp->isExpired()) {
            $otp->delete();
            return response()->json(['error' => 'This code has expired. Please request a new one.'], 422);
        }

        if ($request->type === 'vehicle') {
            $record = VehicleRequest::findOrFail($request->target_id);
            abort_if($record->user_id !== $user->id && !$user->is_vehicle_admin, 403);
            $record->delete();
        } elseif ($request->type === 'room') {
            $record = RoomBooking::findOrFail($request->target_id);
            abort_if($record->user_id !== $user->id && !$user->is_admin, 403);
            $record->delete();
        } else {
            $record = \App\Models\Post::findOrFail($request->target_id);
            abort_if($record->user_id !== $user->id, 403);
            $record->delete();
        }

        $otp->delete();

        return response()->json(['success' => true]);
    }
}
