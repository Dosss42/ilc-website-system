<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — IEMELIF Learning Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
   
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=1780250000">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}?v=1780250000">  {{-- contact page only --}}
    <style>
        /* Custom Scrollbar Design */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #003d82;
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ffd700;
        }
        .main-nav { 
            background: #1a3a6c !important;
            display: block !important;
            position: relative !important;
        }
        .main-nav .navbar-nav .nav-link {
            color: #fff !important;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 10px 14px !important;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }
        .main-nav .navbar-nav .nav-link:hover,
        .main-nav .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
        }
    </style>
<style>
:root { --bs-font-sans-serif: 'Open Sans', sans-serif; --bs-body-font-family: 'Open Sans', sans-serif; }
body, h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, button, input, select, textarea {
    font-family: 'Open Sans', sans-serif !important;
}
</style>
    <link rel="stylesheet" href="{{ asset('css/ilc-typography.css') }}?v=1780250000">
</head>
<body class="page-loading">

{{-- ══════════════════════════════════════
     TOP HEADER
══════════════════════════════════════ --}}
<header class="top-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
          {{--  <div class="school-logo">
                    <a class="nav-link" href="{{ route('home') }}">
                    <img src="/images/logo1.png" alt="Logo 2"></a>
                </div>--}}
                <div class="school-logo">
                    <a class="nav-link" href="{{ route('home') }}">
                    <img src="/images/logo.png" alt="Logo 2"></a>
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
                    <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}" style="flex:1;">
                    <button class="btn-search" type="submit" style="flex-shrink:0;"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════
     NAVIGATION
══════════════════════════════════════ --}}
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
                <li class="nav-item"><a class="nav-link" href="{{ route('aims') }}">AIMS</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Log In</a>
            </div>
        </div>
    </div>
</nav>

{{-- ══ SKELETON ══ --}}
<div class="skel-section" id="skel-content">
    <div style="background:#1a3a6c;padding:30px 0;text-align:center;border-bottom:4px solid #f5a623;">
        <span class="skel-dark" style="height:24px;width:160px;display:inline-block;border-radius:4px;"></span>
        <div style="margin-top:8px;"><span class="skel-dark" style="height:11px;width:120px;display:inline-block;border-radius:4px;"></span></div>
    </div>
    <div style="padding:28px 0;background:#f8fafc;">
        <div class="container"><div class="row g-3">
            @for($i=0;$i<3;$i++)
            <div class="col-md-4">
                <div style="background:#fff;border-radius:12px;padding:28px;text-align:center;border:1px solid #eee;">
                    <span class="skel" style="width:52px;height:52px;border-radius:50%;display:block;margin:0 auto 14px;"></span>
                    <span class="skel" style="height:18px;width:120px;display:block;border-radius:4px;margin:0 auto 10px;"></span>
                    <span class="skel" style="height:11px;width:90%;display:block;border-radius:4px;margin-bottom:5px;"></span>
                    <span class="skel" style="height:11px;width:70%;display:block;border-radius:4px;margin:0 auto;"></span>
                </div>
            </div>
            @endfor
        </div></div>
    </div>
    <div style="padding:28px 0 48px;background:#fff;">
        <div class="container"><div class="row g-4">
            <div class="col-lg-7">
                <span class="skel" style="height:24px;width:200px;display:block;border-radius:4px;margin-bottom:8px;"></span>
                <span class="skel" style="height:12px;width:280px;display:block;border-radius:4px;margin-bottom:20px;"></span>
                @for($i=0;$i<4;$i++)<span class="skel" style="height:44px;width:100%;display:block;border-radius:8px;margin-bottom:11px;"></span>@endfor
                <span class="skel" style="height:110px;width:100%;display:block;border-radius:8px;margin-bottom:14px;"></span>
                <span class="skel" style="height:44px;width:150px;display:block;border-radius:8px;"></span>
            </div>
            <div class="col-lg-5">
                <span class="skel" style="height:240px;display:block;border-radius:12px;margin-bottom:18px;"></span>
                <span class="skel" style="height:15px;width:140px;display:block;border-radius:4px;margin-bottom:12px;"></span>
                @for($i=0;$i<3;$i++)<span class="skel" style="height:56px;width:100%;display:block;border-radius:8px;margin-bottom:10px;"></span>@endfor
            </div>
        </div></div>
    </div>
</div>
{{-- ══ REAL ══ --}}
<div class="real-section" id="real-content">

{{-- ══════════════════════════════════════
     PAGE BANNER
══════════════════════════════════════ --}}
<div style="background:var(--ilc-blue); padding:30px 0; text-align:center; border-bottom:4px solid var(--ilc-gold);">
    <h2 style="color:#fff; font-size:24px; font-weight:700; text-transform:uppercase; letter-spacing:2px; margin:0;">Contact Us</h2>
    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin:6px 0 0;">
        <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.5); text-decoration:none;">Home</a>
        <span style="margin:0 8px; color:rgba(255,255,255,0.4);">&mdash;</span>
        <span style="color:#fff;">Contact</span>
    </p>
</div>

{{-- ══════════════════════════════════════
     SECTION 1: 3 INFO CARDS
     Address | Phone & Email | Office Hours
══════════════════════════════════════ --}}
<section class="contact-info-section">
    <div class="container">
        <div class="row g-3">

            {{-- Card 1: Address --}}
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="card-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <h5>Our Location</h5>
                    {{-- CHANGE: Update with your actual address --}}
                    <p>
                        Brgy Poblacion Central, General Tinio,<br>
                        Nueva Ecija, Philippines
                    </p>
                </div>
            </div>

            {{-- Card 2: Phone & Email --}}
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="card-icon"><i class="bi bi-telephone-fill"></i></div>
                    <h5>Phone &amp; Email</h5>
                    {{-- CHANGE: Update with your actual phone and email --}}
                    <p>
                        <a href="tel:046000000">0951-989-9685</a><br>
                        <a href="Iemelif_learningcenter@gmail.com">Iemelif_learningcenter@gmail.com</a>
                    </p>
                </div>
            </div>

            {{-- Card 3: Office Hours --}}
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="card-icon"><i class="bi bi-clock-fill"></i></div>
                    <h5>Office Hours</h5>
                    {{-- CHANGE: Update with your actual hours --}}
                    <p>
                        Mon – Fri: 7:30 AM – 3:00 PM<br>
                        Saturday - Sunday: <strong style="color:var(--ilc-gold);">Closed</strong>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     SECTION 2: CONTACT FORM (left) + MAP (right)
══════════════════════════════════════ --}}
<section class="contact-main-section">
    <div class="container">
        <div class="row g-4">

            {{-- LEFT: Contact Form --}}
            <div class="col-lg-7">
                <h2 class="contact-section-title">Send Us a Message</h2>
                <p class="contact-section-subtitle">Have a question? Fill out the form and we'll get back to you.</p>

                <div class="contact-form-box">

                    {{-- =============================================
                         SUCCESS / ERROR MESSAGES
                         These show after form submission
                         Add this in your ContactController:
                         return back()->with('success', 'Message sent!');
                         ============================================= --}}
                    @if(session('success'))
                        <div class="alert-contact success">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert-contact error">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- =============================================
                         FORM ACTION:
                         Change action to: {{ route('contact.send') }}
                         Then add in web.php:
                         Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
                         ============================================= --}}
                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf

                        {{-- Validation errors --}}
                        @if($errors->any())
                        <div class="alert-contact error" style="margin-bottom:16px;">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <ul style="margin:0;padding-left:18px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Full Name <span style="color:red;">*</span></label>
                                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                       placeholder="Juan Dela Cruz"
                                       value="{{ old('name') }}" required>
                                @error('name')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email Address <span style="color:red;">*</span></label>
                                <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                       placeholder="juandelacruz@email.com"
                                       value="{{ old('email') }}" required>
                                @error('email')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="09XX XXX XXXX"
                                       value="{{ old('phone') }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Subject <span style="color:red;">*</span></label>
                                <select name="subject" class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" required>
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a subject</option>
                                    <option value="Enrollment Inquiry" {{ old('subject') == 'Enrollment Inquiry' ? 'selected' : '' }}>Enrollment Inquiry</option>
                                    <option value="Academics"          {{ old('subject') == 'Academics'          ? 'selected' : '' }}>Academics</option>
                                    <option value="Finance / Tuition"  {{ old('subject') == 'Finance / Tuition'  ? 'selected' : '' }}>Finance / Tuition</option>
                                    <option value="Guidance & Counseling" {{ old('subject') == 'Guidance & Counseling' ? 'selected' : '' }}>Guidance &amp; Counseling</option>
                                    <option value="General Inquiry"    {{ old('subject') == 'General Inquiry'    ? 'selected' : '' }}>General Inquiry</option>
                                    <option value="Other"              {{ old('subject') == 'Other'              ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('subject')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message <span style="color:red;">*</span></label>
                            <textarea name="message" class="form-control {{ $errors->has('message') ? 'is-invalid' : '' }}"
                                      placeholder="Write your message here..."
                                      rows="5" required>{{ old('message') }}</textarea>
                            @error('message')<div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn-send" id="contactSubmitBtn">
                            <i class="bi bi-send-fill"></i> Send Message
                        </button>

                    </form>
                </div>
            </div>

            {{-- RIGHT: Google Map --}}
            <div class="col-lg-5">
                <h2 class="contact-section-title">Find Us</h2>
                <p class="contact-section-subtitle">We are located Beside of Lou Mart in Brgy.Poblacion Central, General Tinio N.E</p>

                <div class="map-box">
                    {{-- =============================================
                         GOOGLE MAP EMBED:
                         1. Go to https://www.google.com/maps
                         2. Search: "IEMELIF Learning Center General Tinio"
                         3. Click the location pin → Click "Share"
                         4. Click "Embed a map" tab (NOT just "Copy link")
                         5. Click "COPY HTML"
                         6. Paste only the src URL here (inside the quotes)
                         ============================================= --}}
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d480.93682593048544!2d121.04371561176056!3d15.349676691814107!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397249d452e2185%3A0xd0a00a270bac36a9!2sPhilippine%20Business%20Bank%20-%20(Gen.%20Tinio)!5e0!3m2!1sen!2sph!4v1776705841401!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        width="100%"
                        height="300"
                        style="border:0; border-radius:8px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>

                    <div class="map-info">
                        {{-- CHANGE: Update with your actual address details --}}
                        <div class="map-info-row">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Brgy Poblacion Central, General Tinio, Nueva Ecija, Philippines</span>
                        </div>
                        <div class="map-info-row">
                            <i class="bi bi-telephone-fill"></i>
                            <span>(046) 000-0000</span>
                        </div>
                        <div class="map-info-row">
                            <i class="bi bi-envelope-fill"></i>
                            <span>info@iemelif-ilc.edu.ph</span>
                        </div>
                        <div class="map-info-row">
                            <i class="bi bi-clock-fill"></i>
                            <span>Mon–Fri 7:00 AM–3:00 PM &nbsp;|&nbsp; Saturday - Sunday: <strong style="color:var(--ilc-gold);">Closed</strong></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     SECTION 3: OFFICE HOURS (left) + SOCIAL MEDIA (right)
══════════════════════════════════════ --}}
<section class="hours-social-section">
    <div class="container">
        <div class="row g-4">

            {{-- Office Hours Table --}}
            <div class="col-md-6">
                <div class="hours-box">
                    <h5><i class="bi bi-clock me-2"></i> Detailed Office Hours</h5>
                    {{-- CHANGE: Update days and times as needed --}}
                    <div class="hours-row">
                        <span class="day">Monday</span>
                        <span class="time">7:30 AM – 3:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Tuesday</span>
                        <span class="time">7:30 AM – 3:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Wednesday</span>
                        <span class="time">7:30 AM – 3:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Thursday</span>
                        <span class="time">7:30 AM – 3:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Friday</span>
                        <span class="time">7:30 AM – 3:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Saturday</span>
                        <span class="time closed">Closed</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">Sunday</span>
                        <span class="time closed">Closed</span>
                    </div>
                </div>
            </div>

            {{-- Social Media Links --}}
            <div class="col-md-6">
                <div class="social-box">
                    <h5><i class="bi bi-share-fill me-2"></i> Follow &amp; Connect</h5>

                    {{-- CHANGE: Replace all # links with your actual social media URLs --}}
                    <a href="#" class="social-link" target="_blank">
                        <i class="bi bi-facebook fb"></i>
                        <div>
                            <div class="social-link-title">Facebook Page</div>
                            <div class="social-link-sub">IEMELIF Learning Center — General Tinio</div>
                        </div>
                    </a>

                    <a href="#" class="social-link" target="_blank">
                        <i class="bi bi-messenger msg"></i>
                        <div>
                            <div class="social-link-title">Facebook Messenger</div>
                            <div class="social-link-sub">Message us directly anytime</div>
                        </div>
                    </a>

                    <a href="#" class="social-link" target="_blank">
                        <i class="bi bi-youtube yt"></i>
                        <div>
                            <div class="social-link-title">YouTube Channel</div>
                            <div class="social-link-sub">Watch school events and activities</div>
                        </div>
                    </a>

                    {{-- CHANGE: Update email address --}}
                    <a href="mailto:info@iemelif-ilc.edu.ph" class="social-link">
                        <i class="bi bi-envelope-fill em"></i>
                        <div>
                            <div class="social-link-title">Email Us</div>
                            <div class="social-link-sub">info@iemelif-ilc.edu.ph</div>
                        </div>
                    </a>

                </div>
            </div>

        </div>
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
{{-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">

            {{-- Col 1: Contact Details --}}
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

            {{-- Col 2: Quick Links --}}
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
                        Science Education Affair 2025 — A Successful Event
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
                <div class="office-hours-row"><span>Monday – Friday</span><span>7:30 AM – 5:00 PM</span></div>
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
        &copy; {{ date('Y') }} IEMELIF Learning Center — General Tinio, Nueva Ecija ILC. All rights reserved.
    </div>
</footer>

</div>{{-- /real-footer --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>(function(){var days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],months=['January','February','March','April','May','June','July','August','September','October','November','December'];function pad(n){return n<10?'0'+n:n;}function tick(){var now=new Date(),d=days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear(),h=now.getHours(),ampm=h>=12?'PM':'AM';h=h%12||12;var t=h+':'+pad(now.getMinutes())+':'+pad(now.getSeconds())+' '+ampm;var de=document.getElementById('topbar-date'),te=document.getElementById('topbar-time');if(de)de.textContent=d;if(te)te.textContent=t;}tick();setInterval(tick,1000);})();</script>
<script>(function(){var S=[['real-content','skel-content'],['real-footer','skel-footer']];function go(i){if(i>=S.length){document.body.classList.remove('page-loading');return;}var r=document.getElementById(S[i][0]),s=document.getElementById(S[i][1]);if(s)s.style.display='none';if(r){r.style.display='block';void r.offsetWidth;r.style.transition='opacity .38s ease';r.style.opacity='1';}setTimeout(function(){go(i+1);},160);}function start(){setTimeout(function(){go(0);},200);}if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',start);}else{start();}})();</script>
</body>
</html>