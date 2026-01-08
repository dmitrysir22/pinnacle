<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AgentLoginController;
use App\Http\Controllers\Auth\AgentRegisterController;
use App\Http\Controllers\Auth\AgentPasswordResetController;

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\ShipmentController;
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



Route::middleware('guest')->group(function () {

    Route::get('/password/reset', 
        [AgentPasswordResetController::class, 'request']
    )->name('password.request');

    Route::post('/password/email', 
        [AgentPasswordResetController::class, 'email']
    )->name('password.email');

    Route::get('/password/reset/{token}', 
        [AgentPasswordResetController::class, 'reset']
    )->name('password.reset');

    Route::post('/password/reset', 
        [AgentPasswordResetController::class, 'update']
    )->name('password.update');

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
  Route::get('/dashboard', [AgentDashboardController::class, 'index'])
    ->name('agent.dashboard');
  Route::resource('shipments', ShipmentController::class);
	
  Route::post('/logout', [AgentDashboardController::class, 'logout'])->name('logout');	
});


