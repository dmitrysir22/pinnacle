<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAgent
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('agent.login');
        }

        $user = auth()->user();

        if (! $user->hasRole('AgentUser')) {
            abort(403);
        }

        if (! $user->is_approved) {
            auth()->logout();
            return redirect()->route('agent.login')
                ->withErrors(['email' => 'Your account is pending approval.']);
        }

        return $next($request);
    }
}