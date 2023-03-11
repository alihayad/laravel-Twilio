<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required',
        ]);

        $user = User::where('phone_number', auth()->user()->phone_number)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->verified = true;
        $user->save();

        return redirect()->route('home');
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

    public function resendVerificationPhone()
    {
        $user = auth()->user();
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

    public function showVerificationForm()
    {
        return view('auth.verify');
    }

}
