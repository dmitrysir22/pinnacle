<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\AgentOtpNotification;

class AgentLoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        $user = Auth::user();

        if (! $user->is_approved) {
            Auth::logout();
            return back()->withErrors(['email' => 'Account pending admin approval']);
        }

        // Generate OTP
        $user->otp_code = rand(100000, 999999);
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        $user->notify(new AgentOtpNotification());


        Auth::logout();
        session(['otp_user_id' => $user->id]);

        return redirect('/otp');
    }
}
