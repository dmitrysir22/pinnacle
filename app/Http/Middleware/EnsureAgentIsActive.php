<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentIsActive
{
public function handle($request, Closure $next)
{
    $user = auth()->user();

    // 🔐 ONLY for agents
    if ($user->hasRole('AgentUser')) {

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (! $user->approved) {
            auth()->logout();
            return redirect('/login')
                ->withErrors(['email' => 'Your account is awaiting admin approval.']);
        }
    }

    return $next($request);
}

}
