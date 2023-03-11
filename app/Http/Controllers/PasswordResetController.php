<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('forgot');
    }
    public function checkPhoneExists(Request $request)
    {

        $validatedData = $request->validate([
            'phone_number' => ['required', 'string', 'max:255', 'exists:users', 'regex:/^\+[1-9]\d{1,14}$/']
        ]);

        $user = User::where('phone_number', $validatedData['phone_number'])->first();

        $verification_code = rand(1000, 9999);
        $this->sendVerificationCode($validatedData['phone_number'], $verification_code);
        $user->verification_code = $verification_code;
        $user->save();
        session(['phone_number' => $validatedData['phone_number']]);
        return view('forgotVerify');
    }



    private function sendVerificationCode($phoneNumber, $code)
    {
        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        $message = $twilio->messages->create(
            $phoneNumber,
            [
                'from' => env('TWILIO_FROM_NUMBER'),
                'body' => 'Your verification code is: ' . $code,
            ]
        );

        return $message;
    }


    public function resendResetVerificationCode()
    {
        $user = User::where('phone_number', session('phone_number'))->first();

        $secondsElapsed = $user->updated_at->diffInSeconds(now());

        if ($secondsElapsed > 120) {

            $verification_code = rand(1000, 9999);
            $this->sendVerificationCode($user->phone_number, $verification_code);
            $user->verification_code = $verification_code;
            $user->save();
            return response()->json(['message' => 'Verification code sent to Your Phone-number.', "secondsElapsed" => ($user->updated_at->diffInSeconds(now()))]);
        } //end if
        return response()->json(['message' => 'You have To wait 2 minutes until You can resend gain', "secondsElapsed" => ($user->updated_at->diffInSeconds(now()))]);
    }


    public function verifyResetCode(Request $request)
    {

        $validatedData = $request->validate([
            'verification_code' => ['required', 'string', 'max:255']
        ]);

        $user = User::where('phone_number', session('phone_number'))->where('verification_code', $validatedData['verification_code'])->first();

        if (!$user) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
        session(['verification_code' => $validatedData['verification_code']]);

        return view('password');

    }

    public function resetPassword(Request $request)
    {
        $validationData = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::where('phone_number', session('phone_number'))->where('verification_code', session('verification_code'))->first();
        if (!$user) {
            return back()->withErrors(['user_doesnt_exist' => 'Invalid request']);
        }

        $user->password = bcrypt($validationData['password']);
        $user->save();

        return redirect()->route('user.loginForm');

    }


}