<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SessionTimeoutController;
use App\Support\SecurityPolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-side half of the inactivity logout (the browser half is public/js/idle-logout.js).
 * The browser timer alone isn't enough: background tabs get their timers slowed or frozen,
 * and the portals reload themselves when a tab becomes visible again — which used to count
 * as fresh activity and let someone back in after being away longer than the limit.
 *
 * So the session remembers when the user last *really* did something — opened a page,
 * submitted/saved something, or was active in the page (GET /session/ping, at most once a
 * minute). Background requests (polling, lazy loads) don't count. Before anything else, a
 * signed-in request that comes later than the limit is logged out and sent to
 * "You have been logged out".
 */
class EnforceIdleTimeout
{
    public const KEY = 'idle_last_activity';

    /** Requests that must never be blocked or counted (the logout flow itself, recovery). */
    private const SKIP = ['session.expire', 'session.expired', 'logout', 'finance.logout', 'cashier.logout'];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasSession() || $request->routeIs(...self::SKIP) || $request->is('recovery', 'recovery/*')) {
            return $next($request);
        }

        $signedIn = Auth::check() || Auth::guard('finance')->check() || Auth::guard('cashier')->check();
        $last = (int) $request->session()->get(self::KEY, 0);

        if ($signedIn && $last > 0 && time() - $last > SecurityPolicy::sessionMinutes() * 60) {
            $portal = SessionTimeoutController::portalFor($request);
            SessionTimeoutController::logoutForInactivity($request);
            $to = route('session.expired', ['portal' => $portal]);

            return $request->expectsJson() || $request->ajax()
                ? response()->json(['message' => 'You were logged out because of inactivity.', 'session_expired' => true, 'redirect' => $to], 401)
                : redirect($to);
        }

        if ($this->isUserActivity($request)) {
            $request->session()->put(self::KEY, time());
        }

        return $next($request);
    }

    /** Opening a page, sending a form/saving (any non-GET), or the in-page activity ping. */
    private function isUserActivity(Request $request): bool
    {
        if ($request->routeIs('session.ping') || !$request->isMethod('GET')) {
            return true;
        }
        $mode = $request->header('Sec-Fetch-Mode');
        if ($mode !== null) {
            return $mode === 'navigate';         // browsers mark real page loads this way; fetch() isn't
        }
        return !$request->ajax() && !$request->expectsJson(); // older browsers / tests
    }
}
