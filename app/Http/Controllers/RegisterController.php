<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'string', 'email', 'max:255', 'unique:users',
                'regex:/@crestecphil\.com\.ph$/',
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.regex' => 'Use your @crestecphil.com.ph email only.',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified'    => false,
        ]);

        Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Your OTP Code');
        });

        return redirect()->route('otp.form')->with('email', $user->email);
    }

    public function create()
    {
        return view('auth.register');
    }
}
