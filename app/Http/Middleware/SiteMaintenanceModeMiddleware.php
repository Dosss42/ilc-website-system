<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class SiteMaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::get('site_maintenance_mode', false)) {
            return $next($request);
        }

        // Admins and super admins bypass maintenance mode, unless they ask to preview the visitor view
        $user = $request->user();
        if ($user && in_array($user->role, ['admin', 'superadmin', 'registrar'])) {
            if ($request->boolean('preview_maintenance')) {
                return response()->view('maintenance', ['type' => 'site'], 503);
            }
            return $this->withAdminBanner($request, $next($request));
        }

        return response()->view('maintenance', ['type' => 'site'], 503);
    }

    /**
     * Without this, an admin who turns maintenance on and opens the site in the
     * same browser sees it working normally and assumes the switch did nothing.
     */
    private function withAdminBanner(Request $request, $response)
    {
        $type = (string) $response->headers->get('Content-Type');
        if ($request->isMethod('GET') && str_contains($type, 'text/html') && method_exists($response, 'getContent')) {
            $html = $response->getContent();
            $pos  = strripos($html, '</body>');
            if ($pos !== false) {
                $previewUrl = e($request->fullUrlWithQuery(['preview_maintenance' => 1]));
                $banner = '<div style="position:fixed;left:50%;bottom:16px;transform:translateX(-50%);z-index:2147483647;'
                    . 'display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:center;max-width:calc(100% - 32px);'
                    . 'background:#1f2937;color:#fff;padding:10px 16px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.25);'
                    . 'font:600 13px/1.4 system-ui,-apple-system,Segoe UI,sans-serif;">'
                    . '<span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></span>'
                    . '<span>Website maintenance is ON. Visitors see the maintenance page; you can see the site because you are logged in as an admin.</span>'
                    . '<a href="' . $previewUrl . '" style="color:#fbbf24;text-decoration:underline;white-space:nowrap;">Preview visitor view</a>'
                    . '</div>';
                $response->setContent(substr_replace($html, $banner, $pos, 0));
            }
        }
        return $response;
    }
}
