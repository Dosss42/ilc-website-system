<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $q ? 'Search results for "'.$q.'"' : 'Search' }} &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=1789758077">
    <link rel="stylesheet" href="{{ asset('css/announcements.css') }}?v=1780250000">
    <link rel="stylesheet" href="{{ asset('css/news.css') }}?v=1780250000">
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
.srch-box { background:#fff; border-radius:12px; padding:18px 20px; margin-bottom:24px; box-shadow:0 2px 10px rgba(0,0,0,.05); border:1px solid #eee; }
.srch-box form { display:flex; gap:10px; }
.srch-box input[type="text"] { flex:1; border:1.5px solid #ddd; border-radius:8px; padding:10px 14px; font-size:14px; }
.srch-box input[type="text"]:focus { outline:none; border-color:var(--ilc-gold); }
.srch-box button { background:var(--ilc-blue); color:#fff; border:none; border-radius:8px; padding:10px 22px; font-weight:600; font-size:14px; cursor:pointer; }
.srch-box button:hover { background:#142e54; }
.srch-group-title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--ilc-blue); margin:28px 0 14px; display:flex; align-items:center; gap:8px; }
.srch-group-title:first-child { margin-top:0; }
.srch-page-card { display:flex; align-items:center; gap:14px; background:#fff; border:1px solid #eee; border-radius:10px; padding:14px 16px; margin-bottom:10px; text-decoration:none; color:inherit; transition:all .15s; }
.srch-page-card:hover { border-color:var(--ilc-gold); box-shadow:0 2px 10px rgba(0,0,0,.06); }
.srch-page-card i { font-size:20px; color:var(--ilc-gold); flex-shrink:0; }
.srch-page-card .spc-title { font-weight:700; color:var(--ilc-blue); font-size:14px; margin-bottom:2px; }
.srch-page-card .spc-excerpt { font-size:12.5px; color:#666; }
.srch-empty { text-align:center; padding:60px 20px; color:#666; }
.srch-empty i { font-size:48px; color:#ddd; display:block; margin-bottom:16px; }
mark.srch-hl { background:#fff3c4; color:inherit; padding:0 2px; border-radius:2px; }
</style>
    <link rel="stylesheet" href="{{ asset('css/ilc-typography.css') }}?v=1780250000">
</head>
<body>

{{-- ═══════════════════════════════════════════════
     TOP HEADER
═══════════════════════════════════════════════ --}}
<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="school-logo">
                    <img src="/images/logo.png" alt="Logo 2">
                </div>
                <div class="school-title">
                    <h1>IEMELIF Learning Center</h1>
                    <p>General Tinio, Nueva Ecija</p>
                </div>
            </div>
            <div class="d-none d-lg-flex flex-column align-items-end gap-1">
                <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:#111;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:4px;"><i class="bi bi-calendar3" style="color:var(--ilc-gold);font-size:11px;"></i><span id="topbar-date"></span></div>
                    <div style="width:1px;height:11px;background:#ccc;"></div>
                    <div style="display:flex;align-items:center;gap:4px;"><i class="bi bi-clock" style="color:var(--ilc-gold);font-size:11px;"></i><span id="topbar-time" style="font-variant-numeric:tabular-nums;min-width:70px;"></span></div>
                </div>
                <form class="search-form" action="{{ route('search') }}" method="GET" style="display:flex;flex-direction:row;align-items:center;">
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ $q }}" style="flex:1;">
                    <button class="btn-search" type="submit" style="flex-shrink:0;"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ═══════════════════════════════════════════════
     NAVIGATION
═══════════════════════════════════════════════ --}}
<nav class="main-nav navbar navbar-expand-lg">
    <div class="container">
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('academics') }}">Academics</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admission') }}">Enrollment</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('announcements') }}">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('news') }}">News</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('aims') }}">AIMS</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Log In</a>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════
     PAGE BANNER
═══════════════════════════════════════════════ --}}
<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Search</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">Search</span>
    </p>
</div>

{{-- ═══════════════════════════════════════════════
     RESULTS
═══════════════════════════════════════════════ --}}
<section class="ann-page-section">
    <div class="container">

        <div class="srch-box">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search announcements, news, and school pages...">
                <button type="submit"><i class="bi bi-search me-1"></i> Search</button>
            </form>
        </div>

        @php
            $totalResults = $pages->count() + $announcements->count() + $news->count();
        @endphp

        @if($q === '')
            <div class="srch-empty">
                <i class="bi bi-search"></i>
                <p>Type something above to search announcements, news, and school information.</p>
            </div>
        @elseif($totalResults === 0)
            <div class="srch-empty">
                <i class="bi bi-emoji-frown"></i>
                <p>No results found for &ldquo;<strong>{{ $q }}</strong>&rdquo;.</p>
                <p style="font-size:12.5px;">Try a different keyword, or browse
                    <a href="{{ route('announcements') }}">Announcements</a>,
                    <a href="{{ route('news') }}">News</a>, or
                    <a href="{{ route('academics') }}">Academics</a> directly.
                </p>
            </div>
        @else
            <p style="color:#666;font-size:13.5px;margin-bottom:0;">
                {{ $totalResults }} result{{ $totalResults === 1 ? '' : 's' }} for &ldquo;<strong>{{ $q }}</strong>&rdquo;
            </p>

            {{-- ── School Pages ── --}}
            @if($pages->isNotEmpty())
            <div class="srch-group-title"><i class="bi bi-file-earmark-text"></i> School Pages</div>
            @foreach($pages as $page)
                <a href="{{ route($page['route']) }}" class="srch-page-card">
                    <i class="bi bi-arrow-right-circle"></i>
                    <div>
                        <div class="spc-title">{{ $page['title'] }}</div>
                        <div class="spc-excerpt">{!! $page['excerpt'] !!}</div>
                    </div>
                </a>
            @endforeach
            @endif

            {{-- ── Announcements ── --}}
            @if($announcements->isNotEmpty())
            <div class="srch-group-title"><i class="bi bi-megaphone"></i> Announcements</div>
            <div class="row g-3">
                @foreach($announcements as $ann)
                <div class="col-md-6">
                    <a href="{{ route('announcements.show', $ann) }}" style="text-decoration:none;color:inherit;display:block;height:100%;">
                    <div class="ann-grid-card" style="cursor:pointer;height:100%;">
                        @if($ann->image)
                        <div style="height:160px;overflow:hidden;border-radius:10px 10px 0 0;margin:-1px -1px 0;background:#f0f6ff;position:relative;">
                            <img src="{{ asset('storage/'.$ann->image) }}" alt="{{ $ann->title }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        @endif
                        <div class="ann-card-header">
                            <div class="ann-card-date">
                                <span class="card-day">{{ $ann->created_at->format('d') }}</span>
                                <span class="card-month">{{ $ann->created_at->format('M') }}</span>
                            </div>
                            <div class="ann-card-type">{{ ucfirst($ann->category) }}</div>
                        </div>
                        <div class="ann-card-body">
                            <span class="ann-type-pill {{ $ann->category }}">{{ ucfirst($ann->category) }}</span>
                            <h5>{{ $ann->title }}</h5>
                            <p>{{ Str::limit($ann->content, 120) }}</p>
                        </div>
                        <div class="ann-card-footer">
                            <span class="ann-posted"><i class="bi bi-calendar3"></i> {{ $ann->created_at->format('M d, Y') }}</span>
                            <span class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></span>
                        </div>
                    </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── News ── --}}
            @if($news->isNotEmpty())
            <div class="srch-group-title"><i class="bi bi-newspaper"></i> News</div>
            <div class="row g-3">
                @foreach($news as $article)
                <div class="col-md-6">
                    <a href="{{ route('news.show', $article) }}" style="text-decoration:none;color:inherit;display:block;height:100%;">
                    <div class="news-grid-card" style="cursor:pointer;height:100%;">
                        <div class="grid-img">
                            @if($article->image)
                                <img src="{{ asset('storage/'.$article->image) }}" alt="{{ $article->title }}">
                            @else
                                <div class="img-placeholder"><img src="{{ asset('images/bg'.((($loop->index % 3)+1)).'.jpg') }}" alt="{{ $article->title }}"></div>
                            @endif
                        </div>
                        <div class="grid-body">
                            <span class="news-category-badge {{ $article->category }}">{{ ucfirst($article->category) }}</span>
                            <h5>{{ $article->title }}</h5>
                            <p>{{ Str::limit($article->body, 120) }}</p>
                        </div>
                        <div class="grid-footer">
                            <span class="news-date"><i class="bi bi-calendar3"></i> {{ $article->created_at->format('M d, Y') }}</span>
                            <span class="btn-read-more-sm">Read More <i class="bi bi-chevron-right"></i></span>
                        </div>
                    </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        @endif

    </div>
</section>

{{-- ═══════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <h6>Contact Details</h6>
                <div class="footer-contact">
                    <p>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Brgy Poblacion Central, General Tinio, Nueva Ecija, Philippines</span>
                    </p>
                    <p>
                        <i class="bi bi-telephone-fill"></i>
                        <span>0951-989-9685</span>
                    </p>
                    <p>
                        <i class="bi bi-envelope-fill"></i>
                        <span>Iemelif_learningcenter@gmail.com</span>
                    </p>
                </div>
                <div class="footer-logos mt-3">
                    <div class="footer-logo-img">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo">
                    </div>
                    <div class="footer-logo-img">
                        <img src="{{ asset('images/logoo.jpg') }}" alt="Logo">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <h6>Quick Links</h6>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('academics') }}">Academics</a>
                <a href="{{ route('admission') }}">Enrollment</a>
                <a href="{{ route('announcements') }}">Announcements</a>
                <a href="{{ route('news') }}">News</a>
                <a href="{{ route('aims') }}">AIMS</a>
                <a href="{{ route('contact') }}">Contact Us</a>
                <a href="{{ route('terms') }}">Terms &amp; Conditions</a>
                <a href="{{ route('privacy') }}">Privacy Policy</a>
            </div>
            <div class="col-md-3">
                <h6>Latest Articles</h6>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="{{ asset('images/bg4.jpg') }}" alt="News 1">
                    </div>
                    <div class="footer-news-text">
                        Celebrating 32 Years of Excellence in Education
                    </div>
                </div>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="{{ asset('images/bg2.jpg') }}" alt="News 1">
                    </div>
                    <div class="footer-news-text">
                        Science Education Affair 2025 &mdash; A Successful Event
                    </div>
                </div>
                <div class="footer-news-item">
                    <div class="footer-news-img">
                        <img src="{{ asset('images/bg3.jpg') }}" alt="News 1">
                    </div>
                    <div class="footer-news-text">
                        Welcome Back to School, ILCians!
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h6>Office Hours</h6>
                <div class="office-hours-row"><span>Monday &ndash; Friday</span><span>7:30 AM &ndash; 5:00 PM</span></div>
                <div class="office-hours-row"><span>Saturday - Sunday</span><span style="color:var(--ilc-gold);">Closed</span></div>
                <h6 class="mt-3">Visitor Counter</h6>
                <div style="display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px 14px;margin-top:8px;">
                    <i class="bi bi-people-fill" style="font-size:20px;color:var(--ilc-gold);flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.8px;">Total Visitors</div>
                        <div style="font-size:20px;font-weight:800;color:#fff;line-height:1.2;">{{ number_format($visitorCount ?? 0) }}</div>
                    </div>
                    <div style="margin-left:auto;display:flex;align-items:center;gap:5px;font-size:10px;color:rgba(255,255,255,.4);">
                        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 1.5s ease-in-out infinite;"></span>Live
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom mt-4">
        &copy; {{ date('Y') }} IEMELIF Learning Center &mdash; General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
</body>
</html>
