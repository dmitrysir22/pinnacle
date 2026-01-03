<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = User::find(session('otp_user_id'));

        if (! $user) {
            return back()->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        /* 1️⃣ Account locked */
        if ($user->isLocked()) {
            return back()->withErrors([
                'otp' => 'Account locked. Try again later.',
            ]);
        }

        /* 2️⃣ Approval + email verification */
        if (! $user->is_approved) {
            return back()->withErrors([
                'otp' => 'Account pending admin approval.',
            ]);
        }

if (! $user->hasVerifiedEmail()) {
    return back()->withErrors([
        'otp' => 'Please verify your email before continuing.',
    ]);
}

        /* 3️⃣ OTP expired */
        if (! $user->otp_code || now()->gt($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP expired. Please request a new one.',
            ]);
        }

        /* 4️⃣ Wrong OTP */
        if ($user->otp_code !== $request->otp) {

            $user->increment('otp_attempts');

if ($user->otp_attempts >= config('otp.max_attempts')) {

    $lockedUntil = now()->addMinutes(config('otp.lock_minutes'));

    $user->update([
        'locked_until' => $lockedUntil,
        'otp_attempts' => 0,
        'otp_code' => null,
    ]);

    session(['locked_until' => $lockedUntil->toIso8601String()]);

    return back()->withErrors([
        'otp' => 'Too many attempts. Account locked.',
    ]);
}
            return back()->withErrors([
                'otp' => 'Invalid code.',
            ]);
        }

        /* ✅ SUCCESS */
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'locked_until' => null,
        ]);

        Auth::login($user);
        session()->forget('otp_user_id');
        session()->forget('locked_until');

        return redirect('/dashboard');
    }
	
public function resend()
{
    $user = User::find(session('otp_user_id'));

    if (! $user) {
        abort(403);
    }

    if (! $user->canResendOtp()) {
        return back()->withErrors([
            'otp' => 'Please wait before requesting a new code.',
        ]);
    }

    $user->update([
        'otp_code' => rand(100000, 999999),
        'otp_expires_at' => now()->addMinutes(config('otp.expires_minutes')),
        'otp_last_sent_at' => now(),
        'otp_attempts' => 0,
    ]);

    $user->notify(new \App\Notifications\AgentOtpNotification($user->otp_code));

    return back()->with('status', 'New OTP sent.');
}
	
}
