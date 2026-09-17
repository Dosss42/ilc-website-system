<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\News;
use Illuminate\Support\Facades\Response;

/**
 * A real XML sitemap, generated live rather than a static file — so it
 * never goes stale as news/announcements get added, and always uses
 * whatever APP_URL is actually configured (critical: this must be the
 * real production domain once deployed, not the local dev URL).
 *
 * Deliberately lists only genuinely public, indexable content — no
 * portal/login/dashboard pages. Those don't belong in a sitemap even
 * though they're technically reachable, since a sitemap is specifically
 * "please index these," and a login page or student dashboard is never
 * something you want showing up in someone's Google search results.
 */
class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Static public pages
        $staticRoutes = [
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'aims', 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['route' => 'academics', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'admission', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'enrollment.process', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'enrollment.form', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'news', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'announcements', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'yearly'],
        ];

        foreach ($staticRoutes as $r) {
            if (\Illuminate\Support\Facades\Route::has($r['route'])) {
                $urls[] = [
                    'loc' => route($r['route']),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => $r['changefreq'],
                    'priority' => $r['priority'],
                ];
            }
        }

        // Published news articles
        News::where('is_active', true)->latest()->get()->each(function ($news) use (&$urls) {
            $urls[] = [
                'loc' => route('news.show', $news),
                'lastmod' => $news->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        });

        // Public announcements (audience = all only — the only ones that
        // have their own public detail route)
        Announcement::where('is_active', true)->where('audience', 'all')->latest()->get()->each(function ($ann) use (&$urls) {
            $urls[] = [
                'loc' => route('announcements.show', $ann),
                'lastmod' => $ann->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        });

        $xml = view('sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
