<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/@crestecphil\.com\.ph$/',
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.regex' => 'Registration is only allowed for Crestec Philippines employees. Please use your @crestecphil.com.ph email address.',
        ]);

        // 2. Create User
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 3. Log them in and redirect
        auth()->login($user);
        return redirect('/dashboard');
    }
}
