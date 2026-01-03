<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AgentLoginController;
use App\Http\Controllers\Auth\AgentRegisterController;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Agent Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {


    Route::get('/register', [AgentRegisterController::class, 'show'])
        ->name('agent.register');

    Route::post('/register', [AgentRegisterController::class, 'register'])
        ->name('agent.register.post');

    Route::get('/otp', [OtpController::class, 'show'])
        ->name('otp.show');

    Route::post('/otp', [OtpController::class, 'verify'])
        ->name('otp.verify');

    Route::post('/otp/resend', [OtpController::class, 'resend'])
        ->name('otp.resend');
});

/*
|--------------------------------------------------------------------------
| Email Verification (Agents)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/otp')->with('status', 'Email verified successfully.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification email sent.');
    })->middleware('throttle:6,1')->name('verification.send');

});

Route::middleware(['auth', 'agent', 'agent.active'])->group(function () {
    Route::get('/dashboard', fn () => view('agent.dashboard'))
        ->name('agent.dashboard');
});
