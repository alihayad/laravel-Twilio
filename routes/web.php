<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PhoneVerificationController;


Route::get('/home', function () {
    return view('auth.home');
})->name('home')->middleware('verified');
Route::get('/', function () {
    return redirect()->route('user.loginForm');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('user.loginForm')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('user.login')->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout')->middleware('auth');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('user.registerForm')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('user.register')->middleware('guest');

// Phone Verification Routes
Route::get('/verify-phone', [PhoneVerificationController::class, 'showVerificationForm'])->name('verification.notice')->middleware('auth');
Route::post('/verify-phone', [PhoneVerificationController::class, 'verify'])->name('verification.verify')->middleware(['auth']);
Route::post('/verify-phone/resend', [PhoneVerificationController::class, 'resendVerificationPhone'])->name('verification.resend')->middleware(['auth', 'throttle:sms']);

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot.show')->middleware('guest');
Route::post('/forgot-password/resend-code', [PasswordResetController::class, 'resendResetVerificationCode'])->name('password.forgot.resend-code')->middleware('guest');
Route::post('/forgot-password/check-phone', [PasswordResetController::class, 'checkPhoneExists'])->name('password.forgot.check-phone')->middleware(['guest', 'throttle:sms']);
Route::post('/reset-password/verify-code', [PasswordResetController::class, 'verifyResetCode'])->name('password.reset.verify-code')->middleware(['guest', 'throttle:verify']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset.submit')->middleware('guest');


?>