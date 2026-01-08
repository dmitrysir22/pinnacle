<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AgentVerifyEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Notifications\NewAgentRegistered;
use Illuminate\Support\Facades\Notification;

class AgentRegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => false,
        ]);

        // assign Agent role
        $user->assignRole('AgentUser');
		
   try {
        $user->notify(new AgentVerifyEmail());

        Notification::route('mail', 'hello@onelinedesigns.co.uk')->notify(new NewAgentRegistered($user));		
    } catch (TransportExceptionInterface $e) {

        // Optional but recommended:
        // delete user so DB stays clean
        $user->delete();

        return back()
            ->withInput()
            ->withErrors([
                'email' => 'We could not send a verification email to this address. Please check the email and try again.',
            ]);
    }

         return view('auth.verify-email', ['email' => $user->email]);

    }
}
