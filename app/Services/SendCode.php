<?php
namespace App\Services;
use Twilio\Rest\Client;

class SendCode
{
    
    public function sendVerificationCode($phoneNumber, $code)
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
}
?>