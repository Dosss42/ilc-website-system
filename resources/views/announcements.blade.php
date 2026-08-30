<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements &mdash; IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=1780250000">
    <link rel="stylesheet" href="{{ asset('css/announcements.css') }}?v=1780250000">   {{-- announcements page only --}}
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="{{ asset('css/ilc-typography.css') }}?v=1780250000">
</head>
<body class="page-loading">

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     TOP HEADER
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
             {{--<div class="school-logo">
                    <img src="/images/logo1.png" alt="Logo 1">
                </div>--}} 
                <div class="school-logo">
                    <img src="/images/logo.png" alt="Logo 2">
                </div>
                <div class="school-title">
                    {{-- CHANGE: Update school name and location --}}
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
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}" style="flex:1;">
                    <button class="btn-search" type="submit" style="flex-shrink:0;"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     NAVIGATION
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
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
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('announcements*') ? 'active' : '' }}" href="{{ route('announcements') }}">Announcements</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('news') }}">News</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('aims') ? 'active' : '' }}" href="{{ route('aims') }}">AIMS</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Log In</a>
            </div>
        </div>
    </div>
</nav>

{{-- â•â• SKELETON â•â• --}}
<div class="skel-section" id="skel-content">
    <div style="background:#1a3a6c;padding:30px 0;text-align:center;border-bottom:4px solid #f5a623;">
        <span class="skel-dark" style="height:24px;width:200px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:140px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:28px 0 0;background:#f8fafc;">
        <div class="container">
            <div style="background:#fff;border-radius:12px;padding:24px;display:flex;gap:18px;margin-bottom:24px;border-left:4px solid #f5a623;">
                <span class="skel" style="width:68px;height:80px;border-radius:8px;flex-shrink:0;display:block;"></span>
                <div style="flex:1;">
                    <span class="skel" style="height:11px;width:80px;display:block;border-radius:4px;margin-bottom:10px;"></span>
                    <span class="skel" style="height:20px;width:90%;display:block;border-radius:4px;margin-bottom:8px;"></span>
                    <span class="skel" style="height:12px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                    <span class="skel" style="height:12px;width:78%;display:block;border-radius:4px;"></span>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        @for($i=0;$i<6;$i++)
                        <div class="col-md-6">
                            <div style="background:#fff;border-radius:10px;overflow:hidden;border:1px solid #eee;">
                                <div style="background:#f0f4f8;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                                    <span class="skel" style="width:42px;height:48px;border-radius:6px;display:block;"></span>
                                    <span class="skel" style="width:80px;height:13px;border-radius:4px;display:block;"></span>
                                </div>
                                <div style="padding:14px;">
                                    <span class="skel" style="height:11px;width:65px;display:block;border-radius:20px;margin-bottom:8px;"></span>
                                    <span class="skel" style="height:15px;width:90%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                                    <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                                    <span class="skel" style="height:11px;width:76%;display:block;border-radius:4px;"></span>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="col-lg-4">
                    @for($i=0;$i<3;$i++)
                    <div style="background:#fff;border-radius:10px;padding:18px;margin-bottom:18px;border:1px solid #eee;">
                        <span class="skel" style="height:15px;width:140px;display:block;border-radius:4px;margin-bottom:13px;"></span>
                        <span class="skel" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                        <span class="skel" style="height:11px;width:88%;display:block;border-radius:4px;margin-bottom:6px;"></span>
                        <span class="skel" style="height:11px;width:72%;display:block;border-radius:4px;"></span>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
{{-- â•â• REAL â•â• --}}
<div class="real-section" id="real-content">

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     PAGE BANNER
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Announcements</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">Announcements</span>
    </p>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MAIN SECTION
     Left: Announcements | Right: Sidebar
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<section class="ann-page-section">
    <div class="container">
        <div class="row g-4">

            {{-- â•â• LEFT: ANNOUNCEMENTS â•â• --}}
            <div class="col-lg-8">

                <h2 class="ann-page-title">All Announcements</h2>
                <p class="ann-page-subtitle">Stay informed with the latest updates from IEMELIF Learning Center.</p>

                {{-- â”€â”€ FILTER TABS â”€â”€ --}}
                {{-- =============================================
                     FILTER TABS: When connected to database,
                     add ?type= query to each link and highlight
                     active: class="{{ request('type') == 'general' ? 'active' : '' }}"
                     ============================================= --}}
                <div class="ann-filter-tabs">
                    <a href="{{ route('announcements') }}" class="ann-filter-tab {{ !request('category') ? 'active' : '' }}">All</a>
                    <a href="{{ route('announcements', ['category' => 'general']) }}" class="ann-filter-tab {{ request('category') === 'general' ? 'active' : '' }}">General</a>
                    <a href="{{ route('announcements', ['category' => 'academic']) }}" class="ann-filter-tab {{ request('category') === 'academic' ? 'active' : '' }}">Academic</a>
                    <a href="{{ route('announcements', ['category' => 'enrollment']) }}" class="ann-filter-tab {{ request('category') === 'enrollment' ? 'active' : '' }}">Enrollment</a>
                    <a href="{{ route('announcements', ['category' => 'activity']) }}" class="ann-filter-tab {{ request('category') === 'activity' ? 'active' : '' }}">Activity</a>
                    <a href="{{ route('announcements', ['category' => 'reminder']) }}" class="ann-filter-tab {{ request('category') === 'reminder' ? 'active' : '' }}">Reminder</a>
                </div>

                {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                     FEATURED ANNOUNCEMENT (top big card)
                â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
                {{-- =============================================
                     CHANGE: Update date, title, and content
                     When using database:
                     Use $featuredAnnouncement->title, etc.
                     ============================================= --}}
                @if(isset($featuredAnnouncement) && $featuredAnnouncement)
                <a href="{{ route('announcements.show', $featuredAnnouncement) }}" style="text-decoration:none;color:inherit;display:block;">
                <div class="ann-featured-card" style="cursor:pointer;">
                    {{-- Show image if available, otherwise show date column --}}
                    @if($featuredAnnouncement->image)
                    <div style="width:300px;flex-shrink:0;overflow:hidden;position:relative;min-height:240px;">
                        <img src="{{ asset('storage/'.$featuredAnnouncement->image) }}" alt="{{ $featuredAnnouncement->title }}"
                            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;">
                    </div>
                    @else
                    <div class="ann-featured-date-col">
                        <span class="feat-day">{{ $featuredAnnouncement->created_at->format('d') }}</span>
                        <span class="feat-month">{{ $featuredAnnouncement->created_at->format('F') }}</span>
                        <span class="feat-year">{{ $featuredAnnouncement->created_at->format('Y') }}</span>
                    </div>
                    @endif
                    <div class="ann-featured-body">
                        <span class="ann-type-badge"><i class="bi bi-pin-fill me-1" style="color:#c5a059;font-style:normal;"></i> {{ ucfirst($featuredAnnouncement->category) }}</span>
                        <h3>{{ $featuredAnnouncement->title }}</h3>
                        <div class="ann-meta">
                            <span><i class="bi bi-calendar3"></i> {{ $featuredAnnouncement->created_at->format('F d, Y') }}</span>
                        </div>
                        <p>{{ Str::limit($featuredAnnouncement->content, 300) }}</p>
                        <span class="btn-ann-details" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;">View Details <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
                </a>
                @else
                {{-- Static fallback featured card --}}
                <div class="ann-featured-card">
                    <div class="ann-featured-date-col">
                        <span class="feat-day">08</span>
                        <span class="feat-month">June</span>
                        <span class="feat-year">2026</span>
                    </div>
                    <div class="ann-featured-body">
                        <span class="ann-type-badge"><i class="bi bi-pin-fill me-1" style="color:#c5a059;font-style:normal;"></i> Important</span>
                        <h3>Opening Block: Start of Term 1 &mdash; S.Y. 2026&ndash;2027</h3>
                        <div class="ann-meta">
                            <span><i class="bi bi-clock"></i> 7:00 AM</span>
                            <span><i class="bi bi-geo-alt"></i> School Grounds</span>
                            <span><i class="bi bi-person"></i> All Students & Parents</span>
                        </div>
                        <p>
                            The IEMELIF Learning Center officially opens Term 1 of School Year 2026&ndash;2027
                            on June 8, 2026 under the MATATAG Curriculum. Mandatory Learners' Health
                            Assessment, Learners and Parents' Orientation, and the start of BOSY
                            Assessments (CRLA, RMA, Phil-IRI) will be conducted from June 8&ndash;11.
                            All students are required to attend on the first day. Please ensure
                            all enrollment requirements are complete.
                        </p>
                    </div>
                </div>
                @endif

                {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
                     ANNOUNCEMENT GRID CARDS &mdash; Dynamic DB
                â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
                @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="row g-3">
                    @foreach($announcements as $ann)
                    <div class="col-md-6">
                        <a href="{{ route('announcements.show', $ann) }}" style="text-decoration:none;color:inherit;display:block;height:100%;">
                        <div class="ann-grid-card" style="cursor:pointer;height:100%;">
                            @if($ann->image)
                            <div style="height:195px;overflow:hidden;border-radius:10px 10px 0 0;margin:-1px -1px 0;background:#f0f6ff;position:relative;">
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
                <div class="ann-pagination">{{ $announcements->links() }}</div>
                @else
                {{-- Static fallback when no DB announcements --}}
                <div class="row g-3">

                    {{-- Card 1 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">01</span><span class="card-month">Jun</span></div>
                                <div class="ann-card-type">Enrollment</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill enrollment">Enrollment</span>
                                <h5>Brigada Eskwela & Enrollment Period &mdash; S.Y. 2026&ndash;2027</h5>
                                <p>Brigada Eskwela and the official enrollment period run from June 1&ndash;5, 2026. All incoming students must bring complete requirements to the registrar's office.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Jun 1, 2026</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">09</span><span class="card-month">Sep</span></div>
                                <div class="ann-card-type">Academic</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill academic">Academic</span>
                                <h5>Term 1 PTA Meeting & Distribution of Report Cards</h5>
                                <p>PTA meeting and distribution of Term 1 report cards on September 9, 2026. Parents are required to personally claim report cards and attend the meeting.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Sep 5, 2026</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">28</span><span class="card-month">Aug</span></div>
                                <div class="ann-card-type">Academic</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill academic">Academic</span>
                                <h5>Term 1 Examination Schedule &mdash; August 28 & September 1</h5>
                                <p>Term 1 Summative Examinations will be held on August 28 and September 1, 2026. All learners are advised to review all topics covered during Term 1.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Aug 20, 2026</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">15</span><span class="card-month">Dec</span></div>
                                <div class="ann-card-type">Academic</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill academic">Academic</span>
                                <h5>Term 2 PTA Meeting & Distribution of Progress Report</h5>
                                <p>PTA meeting and distribution of Term 2 Progress/Performance Reports on December 15, 2026 as part of the End-of-Term Block activities.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Dec 10, 2026</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 5 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">30</span><span class="card-month">Jan</span></div>
                                <div class="ann-card-type">Enrollment</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill enrollment">Enrollment</span>
                                <h5>Early Registration for Incoming Kinder, Grades 1 & Transferees</h5>
                                <p>Early registration opens January 30, 2027 for incoming Kindergarten, Grade 1, and transferee students for S.Y. 2027&ndash;2028. Registration closes February 26, 2027.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Jan 25, 2027</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6 --}}
                    <div class="col-md-6">
                        <div class="ann-grid-card">
                            <div class="ann-card-header">
                                <div class="ann-card-date"><span class="card-day">24</span><span class="card-month">Mar</span></div>
                                <div class="ann-card-type">Event</div>
                            </div>
                            <div class="ann-card-body">
                                <span class="ann-type-pill event">Event</span>
                                <h5>Announcement of Academic Excellence Awardees &mdash; Moving Up & Graduating Learners</h5>
                                <p>Academic Excellence Awardees for Moving Up and Graduating Learners will be announced on March 24, 2027 as part of End-of-Term 3 Block activities.</p>
                            </div>
                            <div class="ann-card-footer">
                                <span class="ann-posted"><i class="bi bi-calendar3"></i> Mar 20, 2027</span>
                                <a href="#" class="btn-ann-sm">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                </div>{{-- /row --}}

                {{-- â”€â”€ PAGINATION â”€â”€ --}}
                {{-- =============================================
                     When using database, replace with:
                     {{ $announcements->links() }}
                     ============================================= --}}
                <div class="ann-pagination">
                    <a href="#" class="ann-page-btn active">1</a>
                    <a href="#" class="ann-page-btn">2</a>
                    <a href="#" class="ann-page-btn">3</a>
                    <a href="#" class="ann-page-btn"><i class="bi bi-chevron-right"></i></a>
                </div>
                @endif

            </div>{{-- /col-lg-8 --}}

            {{-- â•â• RIGHT: SIDEBAR â•â• --}}
            <div class="col-lg-4">
                <div class="ann-sidebar">

                    {{-- Widget 1: Upcoming Schedule &mdash; from Superadmin (category: activity) --}}
                    <div class="ann-sidebar-widget">
                        <h6 class="ann-sidebar-title">
                            <i class="bi bi-calendar-event me-2"></i>Upcoming Schedule
                        </h6>
                        @if(isset($scheduleAnns) && $scheduleAnns->isNotEmpty())
                            @foreach($scheduleAnns as $sch)
                            <a href="{{ route('announcements.show', $sch) }}" style="text-decoration:none;color:inherit;">
                                <div class="schedule-item">
                                    <div class="schedule-date-box">
                                        <span class="s-day">{{ $sch->created_at->format('d') }}</span>
                                        <span class="s-mon">{{ $sch->created_at->format('M') }}</span>
                                    </div>
                                    <div class="schedule-info">
                                        <div class="s-title">{{ Str::limit($sch->title, 45) }}</div>
                                        <div class="s-time"><i class="bi bi-calendar3"></i> {{ $sch->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        @else
                        {{-- Static fallback --}}
                        <div class="schedule-item">
                            <div class="schedule-date-box"><span class="s-day">15</span><span class="s-mon">Jun</span></div>
                            <div class="schedule-info"><div class="s-title">School Opening Day</div><div class="s-time"><i class="bi bi-clock"></i> 7:00 AM &ndash; All day</div></div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-date-box"><span class="s-day">20</span><span class="s-mon">Jun</span></div>
                            <div class="schedule-info"><div class="s-title">General Assembly &mdash; All Parents</div><div class="s-time"><i class="bi bi-clock"></i> 8:00 AM &ndash; Auditorium</div></div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-date-box"><span class="s-day">04</span><span class="s-mon">Jul</span></div>
                            <div class="schedule-info"><div class="s-title">1st Quarter Exam</div><div class="s-time"><i class="bi bi-clock"></i> All Grade Levels</div></div>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-date-box"><span class="s-day">15</span><span class="s-mon">Aug</span></div>
                            <div class="schedule-info"><div class="s-title">Card Signing &mdash; 1st Quarter</div><div class="s-time"><i class="bi bi-clock"></i> 8:00 AM &ndash; 5:00 PM</div></div>
                        </div>
                        @endif
                    </div>

                    {{-- Widget 2: Categories --}}
                    <div class="ann-sidebar-widget">
                        <h6 class="ann-sidebar-title">
                            <i class="bi bi-tags me-2"></i>Categories
                        </h6>
                        <a href="{{ route('announcements') }}" class="ann-cat-item {{ !request('category') ? 'active' : '' }}">
                            <span><i class="bi bi-grid me-2" style="color:var(--ilc-gold);"></i>All</span>
                            <span class="cat-count">{{ isset($annCategories) ? $annCategories->sum('total') : 0 }}</span>
                        </a>
                        @if(isset($annCategories) && $annCategories->isNotEmpty())
                            @foreach($annCategories as $cat)
                            <a href="{{ route('announcements', ['category' => $cat->category]) }}" class="ann-cat-item {{ request('category') === $cat->category ? 'active' : '' }}">
                                <span><i class="bi bi-tag me-2" style="color:var(--ilc-gold);"></i>{{ ucfirst($cat->category) }}</span>
                                <span class="cat-count">{{ $cat->total }}</span>
                            </a>
                            @endforeach
                        @else
                            @foreach(['general','academic','enrollment','activity','reminder'] as $cat)
                            <a href="{{ route('announcements', ['category' => $cat]) }}" class="ann-cat-item {{ request('category') === $cat ? 'active' : '' }}">
                                <span><i class="bi bi-tag me-2" style="color:var(--ilc-gold);"></i>{{ ucfirst($cat) }}</span>
                                <span class="cat-count">0</span>
                            </a>
                            @endforeach
                        @endif
                    </div>

                    {{-- Widget 3: Important Reminders &mdash; from Superadmin (category: reminder) --}}
                    <div class="ann-sidebar-widget">
                        <h6 class="ann-sidebar-title">
                            <i class="bi bi-info-circle me-2"></i>Important Reminders
                        </h6>
                        @if(isset($reminderAnns) && $reminderAnns->isNotEmpty())
                            <div class="notice-box">
                                @foreach($reminderAnns as $rem)
                                <a href="{{ route('announcements.show', $rem) }}" style="text-decoration:none;color:inherit;">
                                    <div class="notice-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>{{ Str::limit($rem->title, 70) }}</span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                        {{-- Static fallback --}}
                        <div class="notice-box">
                            <div class="notice-item"><i class="bi bi-check-circle-fill"></i><span>Wear complete and proper school uniform every day.</span></div>
                            <div class="notice-item"><i class="bi bi-check-circle-fill"></i><span>Students must be in school by <strong>7:00 AM</strong>.</span></div>
                            <div class="notice-item"><i class="bi bi-check-circle-fill"></i><span>All enrollment requirements must be submitted within the first two weeks of classes.</span></div>
                            <div class="notice-item"><i class="bi bi-check-circle-fill"></i><span>Use of mobile phones is <strong>not allowed</strong> during class hours.</span></div>
                            <div class="notice-item"><i class="bi bi-check-circle-fill"></i><span>Chapel service is every <strong>Monday morning</strong>. All students must attend.</span></div>
                        </div>
                        @endif
                    </div>

                    {{-- Widget 4: Contact for Concerns --}}
                    <div class="ann-sidebar-widget">
                        <h6 class="ann-sidebar-title">
                            <i class="bi bi-telephone me-2"></i>For Concerns
                        </h6>
                        <div class="notice-box">
                            <div class="notice-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                {{-- CHANGE: Update with your address --}}
                                <span>Purok Pag-asa, General Tinio, Nueva Ecija</span>
                            </div>
                            <div class="notice-item">
                                <i class="bi bi-telephone-fill"></i>
                                {{-- CHANGE: Update with your phone --}}
                                <span>(044) 000-0000</span>
                            </div>
                            <div class="notice-item">
                                <i class="bi bi-envelope-fill"></i>
                                {{-- CHANGE: Update with your email --}}
                                <span>info@iemelif-ilc.edu.ph</span>
                            </div>
                            <div class="notice-item">
                                <i class="bi bi-clock-fill"></i>
                                <span>Office Hours: Mon&ndash;Fri 7:00 AM &ndash; 3:00 PM</span>
                            </div>
                        </div>
                    </div>

                </div>{{-- /sidebar --}}
            </div>{{-- /col-lg-4 --}}

        </div>{{-- /row --}}
    </div>
</section>

</div>{{-- /real-content --}}

<div class="skel-section" id="skel-footer" style="background:#1a3a6c;padding:38px 0 20px;">
    <div class="container"><div class="row g-4">
        @for($i=0;$i<4;$i++)
        <div class="col-md-3">
            <span class="skel-dark" style="height:14px;width:100px;display:block;border-radius:4px;margin-bottom:14px;"></span>
            <span class="skel-dark" style="height:11px;width:100%;display:block;border-radius:4px;margin-bottom:7px;"></span>
            <span class="skel-dark" style="height:11px;width:88%;display:block;border-radius:4px;margin-bottom:7px;"></span>
            <span class="skel-dark" style="height:11px;width:76%;display:block;border-radius:4px;"></span>
        </div>
        @endfor
    </div></div>
</div>
<div class="real-section" id="real-footer">
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     FOOTER
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
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
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="274e494148674e424a424b4e410a4e4b440942435209574f">Iemelif_learningcenter@gmail.com</a></span>
                    </p>
                </div>
                {{-- Partner Logos --}}
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
                <a href="#" onclick="ckShowAgain(event)">Cookie Settings</a>
            </div>
{{-- Col 3: Latest Articles --}}
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

            {{-- Col 4: Office Hours + Visitor Counter --}}
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

        </div>{{-- /row --}}
    </div>

    <div class="footer-bottom mt-4">
        &copy; {{ date('Y') }} IEMELIF Learning Center &mdash; General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>

</div>{{-- /real-footer --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
<script>(function(){var S=[['real-content','skel-content'],['real-footer','skel-footer']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();</script>
</body>
</html>