<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\AgentRegisterController;

use App\Http\Controllers\Auth\AgentLoginController;
use App\Http\Controllers\Auth\AgentEmailVerificationController;

Route::get('/agent/email/verify/{id}/{hash}', 
    [AgentEmailVerificationController::class, 'verify']
)->name('agent.verification.verify');


Route::get('/login', [AgentLoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AgentLoginController::class, 'login'])
    ->name('login.post');

Route::get('/', fn () => redirect('/login'));
