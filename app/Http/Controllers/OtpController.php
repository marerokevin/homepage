<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class OtpController extends Controller
{
    public function showForm()
    {
        return view('auth.verify-otp'); // Create this blade
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        $user->is_verified = true;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        auth()->login($user);

        return redirect('/home')->with('success', 'Registration complete!');
    }
}
