<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Spatie\Permission\Middlewares\RoleMiddleware;

use App\Http\Middleware\CheckIfAgent;
use App\Http\Middleware\EnsureAgentIsActive;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/agent.php',
        ],

        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            // Spatie
            'role'          => RoleMiddleware::class,

            // Agent portal
            'agent'         => CheckIfAgent::class,
            'agent.active'  => EnsureAgentIsActive::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
