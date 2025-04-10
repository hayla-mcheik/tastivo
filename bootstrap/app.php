<?php

use App\Http\Middleware\EnsureSessionForGuestCart;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\MigrateGuestCart;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // make sure this line exists
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 👇 Add this for web routes
        $middleware->web(append: [
            HandleInertiaRequests::class,
            MigrateGuestCart::class,
           EnsureSessionForGuestCart::class,
        ]);

        // 👇 Add this for API routes (important for Sanctum in SPAs)
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
            EnsureSessionForGuestCart::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
