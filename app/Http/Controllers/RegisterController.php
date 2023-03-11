<?php

namespace App\Http\Controllers;
use App\Services\SendCode;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }
    public function register(Request $request)
    {

        // Validate request data
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\+[1-9]\d{1,14}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Generate verification code      
        $user = User::create([
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'verification_code' => rand(1000, 9999),
            'password' => bcrypt($validatedData['password'])
        ]);

        // Send verification code to the user's phone number
        $sendCode = new SendCode();
        $sendCode->sendVerificationCode($user->phone_number, $user->verification_code);

        // Log in the user
        Auth::login($user);

        // Redirect the user to the verify page
        return redirect()->route('verification.notice');
    }
}
