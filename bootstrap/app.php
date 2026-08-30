<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Enable Sanctum stateful API so session auth works for first-party SPA requests
        $middleware->statefulApi();

        // ── Security headers on every web response ──
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // ── Register role middleware aliases ──
        $middleware->alias([
            'superadmin'  => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'teacher'     => \App\Http\Middleware\TeacherMiddleware::class,
            'student'     => \App\Http\Middleware\StudentMiddleware::class,
            'finance'     => \App\Http\Middleware\FinanceMiddleware::class,
            'cashier'     => \App\Http\Middleware\CashierMiddleware::class,
            'maintenance' => \App\Http\Middleware\MaintenanceModeMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();