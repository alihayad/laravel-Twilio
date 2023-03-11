<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('signin');
    }

    public function login(Request $request)
    {


        // Validate request data
        $validatedData = $request->validate([
            'phone_number' => ['required', 'string', 'max:255', 'exists:users', 'regex:/^\+[1-9]\d{1,14}$/'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(['phone_number' => $validatedData['phone_number'], 'password' => $validatedData['password']], $remember)) {
            // Authentication was successful...
            return redirect()->route('home');
        } else {
            // Authentication failed...
            return redirect()->back()->withErrors(['invalid-credentials' => 'Invalid phone number or password.']);
        }

    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('user.loginForm');
        ;
    }
}