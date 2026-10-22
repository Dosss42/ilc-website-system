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

        // Trust Railway's edge proxy so the app correctly detects HTTPS,
        // client IP, and host from forwarded headers.
        $middleware->trustProxies(at: '*');

        // Enable Sanctum stateful API so session auth works for first-party SPA requests
        $middleware->statefulApi();

        // ── Security headers on every web response ──
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            // Logout after inactivity, checked on the server too (see the class)
            \App\Http\Middleware\EnforceIdleTimeout::class,
        ]);

        // ── Register role middleware aliases ──
        $middleware->alias([
            'guest'       => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'superadmin'  => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'teacher'     => \App\Http\Middleware\TeacherMiddleware::class,
            'student'     => \App\Http\Middleware\StudentMiddleware::class,
            'finance'     => \App\Http\Middleware\FinanceMiddleware::class,
            'cashier'     => \App\Http\Middleware\CashierMiddleware::class,
            'maintenance'      => \App\Http\Middleware\MaintenanceModeMiddleware::class,
            'site-maintenance' => \App\Http\Middleware\SiteMaintenanceModeMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Session timed out (CSRF token no longer valid, "419 Page Expired"): instead of the
        // bare error page, show "You have been logged out" and return to the right login form.
        // A login form left open too long just reloads with a note.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $expired = $e instanceof \Illuminate\Session\TokenMismatchException
                || ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && $e->getStatusCode() === 419);
            if (!$expired) {
                return null;
            }
            $portal = \App\Http\Controllers\SessionTimeoutController::portalFor($request);
            if ($request->expectsJson()) {
                return response()->json([
                    'message'         => 'Your session has expired. Please log in again.',
                    'session_expired' => true,
                    'redirect'        => route('session.expired', ['portal' => $portal, 'reason' => 'expired']),
                ], 419);
            }
            if ($request->is('login', 'register', 'finance/login', 'cashier/login', 'forgot-password', 'reset-password')) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->withErrors(['email' => 'The page was open for too long. Please try again.']);
            }
            return redirect()->route('session.expired', ['portal' => $portal, 'reason' => 'expired']);
        });
    })->create();