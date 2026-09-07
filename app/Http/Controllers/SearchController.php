<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Every public informational page, rendered and turned into plain text so
     * ANY real word on the page is searchable — not a hand-picked keyword
     * list. Each entry supplies whatever dummy view data that page's own
     * route normally passes it, just enough to render without error; the
     * values themselves don't matter since only the text is kept.
     */
    private function pageViews(): array
    {
        // A plain empty Collection (not []), since home.blade.php calls
        // ->isNotEmpty() on latestNews/latestAnnouncements — a const array
        // can't hold `new` expressions, hence this being a method instead.
        $emptyCollection = new \Illuminate\Database\Eloquent\Collection();

        return [
            ['route' => 'home',       'title' => 'Home',                    'view' => 'home',       'data' => ['enrollmentOpen' => true, 'visitorCount' => 0, 'latestNews' => $emptyCollection, 'latestAnnouncements' => $emptyCollection]],
            ['route' => 'about',      'title' => 'About Us',                'view' => 'about',      'data' => ['visitorCount' => 0]],
            ['route' => 'aims',       'title' => 'AIMS',                    'view' => 'aims',       'data' => []],
            ['route' => 'academics',  'title' => 'Academics',               'view' => 'academics',  'data' => ['visitorCount' => 0]],
            ['route' => 'admission',  'title' => 'Admission & Enrollment',  'view' => 'admission',  'data' => ['enrollmentOpen' => true, 'visitorCount' => 0]],
            ['route' => 'contact',    'title' => 'Contact Us',              'view' => 'contact',    'data' => ['visitorCount' => 0]],
            ['route' => 'terms',      'title' => 'Terms & Conditions',      'view' => 'terms_and_conditions', 'data' => []],
            ['route' => 'privacy',    'title' => 'Privacy Policy',          'view' => 'privacy_policy',       'data' => []],
        ];
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $visitorCount = (int) Setting::get('visitor_count', 0);

        if ($q === '') {
            return view('search', [
                'q' => $q,
                'announcements' => collect(),
                'news' => collect(),
                'pages' => collect(),
                'visitorCount' => $visitorCount,
            ]);
        }

        // Public-only, same visibility rules the Announcements/News pages already use.
        $announcements = Announcement::where('is_active', true)
            ->where('audience', 'all')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%");
            })
            ->latest()
            ->limit(20)
            ->get();

        $news = News::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('body', 'like', "%{$q}%");
            })
            ->latest()
            ->limit(20)
            ->get();

        $pages = collect($this->pageIndex())
            ->filter(fn ($page) => mb_stripos($page['text'], $q) !== false)
            ->map(fn ($page) => [
                'route' => $page['route'],
                'title' => $page['title'],
                'excerpt' => $this->highlightExcerpt($page['text'], $q),
            ])
            ->values();

        return view('search', compact('q', 'announcements', 'news', 'pages', 'visitorCount'));
    }

    /**
     * Every public page rendered down to plain text, cached so a search
     * doesn't re-render 8 full pages on every request. Rebuilds automatically
     * 6 hours after last computed — these pages change rarely, so a little
     * staleness is a fair trade for not re-rendering on every keystroke.
     */
    private function pageIndex(): array
    {
        return Cache::remember('public_search_page_index', now()->addHours(6), function () {
            $pages = [];
            foreach ($this->pageViews() as $page) {
                try {
                    $html = view($page['view'], $page['data'])->render();
                } catch (\Throwable $e) {
                    // A page that fails to render (missing asset, view error) is
                    // skipped rather than breaking search for every other page.
                    continue;
                }
                $pages[] = [
                    'route' => $page['route'],
                    'title' => $page['title'],
                    'text' => $this->toPlainText($html),
                ];
            }
            return $pages;
        });
    }

    private function toPlainText(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * A short snippet around the first match, with the match itself wrapped
     * in <mark>. Everything is escaped BEFORE the <mark> tags are added, so
     * the query text can never inject markup — only our own <mark> tag is
     * ever unescaped HTML.
     */
    private function highlightExcerpt(string $text, string $query, int $context = 90): string
    {
        $pos = mb_stripos($text, $query);
        if ($pos === false) {
            return e(Str::limit($text, $context * 2));
        }

        $start  = max(0, $pos - $context);
        $length = mb_strlen($query) + ($context * 2);
        $snippet = mb_substr($text, $start, $length);
        $prefix  = $start > 0 ? '&hellip; ' : '';
        $suffix  = ($start + $length) < mb_strlen($text) ? ' &hellip;' : '';

        $escapedSnippet = e($snippet);
        $escapedQuery   = preg_quote(e($query), '/');
        $highlighted    = preg_replace('/(' . $escapedQuery . ')/i', '<mark class="srch-hl">$1</mark>', $escapedSnippet);

        return $prefix . $highlighted . $suffix;
    }
}
