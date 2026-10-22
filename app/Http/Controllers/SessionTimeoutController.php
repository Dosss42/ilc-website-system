<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Support\SecurityPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Automatic logout after inactivity (Admin › Settings › Security › Session Timeout), used by
 * public/js/idle-logout.js on every portal page:
 *  - ping:    refreshes the session while the user is active
 *  - expire:  logs out (every portal's login) and records it in the audit trail
 *  - expired: back to the right login form, which shows a "You have been logged out" notice
 */
class SessionTimeoutController extends Controller
{
    /** Portal → its login page */
    public const PORTALS = ['web' => 'login', 'finance' => 'finance.login', 'cashier' => 'cashier.login'];

    public function ping()
    {
        // Touching the session is enough. Answers 401 once logged out (e.g. by another window),
        // so a page still showing the old content goes to "You have been logged out".
        // The current timeout comes back too, so open pages follow a change made in Settings.
        $signedIn = Auth::check() || Auth::guard('finance')->check() || Auth::guard('cashier')->check();

        return $signedIn
            ? response()->json(['minutes' => SecurityPolicy::sessionMinutes()])
            : response()->json(['session_expired' => true], 401);
    }

    public function expire(Request $request)
    {
        self::logoutForInactivity($request);

        return response()->json(['ok' => true]);
    }

    /** Log out of every portal and record why (also used by EnforceIdleTimeout). */
    public static function logoutForInactivity(Request $request): void
    {
        $user = Auth::user() ?? Auth::guard('finance')->user() ?? Auth::guard('cashier')->user();
        if ($user) {
            ActivityLogger::log('logout', $user->name . ' was logged out automatically after '
                . SecurityPolicy::sessionMinutesLabel() . ' of inactivity', 'User', $user->id);
        }
        foreach (array_keys(self::PORTALS) as $guard) {
            Auth::guard($guard)->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /** After an automatic logout: back to this portal's login form, which shows the notice
     * (partials/session-notice) in front of the form until it is clicked away. */
    public function expired(Request $request)
    {
        $portal = array_key_exists($request->query('portal'), self::PORTALS) ? $request->query('portal') : 'web';

        return redirect()->route(self::PORTALS[$portal])->with('session_notice', [
            'reason'  => $request->query('reason') === 'expired' ? 'expired' : 'idle',
            'minutes' => SecurityPolicy::sessionMinutes(),
        ]);
    }

    /** Which portal a URL belongs to (for the login page to return to). */
    public static function portalFor(Request $request): string
    {
        return match (true) {
            $request->is('finance', 'finance/*') => 'finance',
            $request->is('cashier', 'cashier/*') => 'cashier',
            default => 'web',
        };
    }
}
