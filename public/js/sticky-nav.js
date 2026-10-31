/*
 * Public pages: the blue menu bar (.main-nav) sticks to the top while scrolling (css/styles.css).
 * This adds the school logo + name to its left side and shows them once the white header above has
 * scrolled away. It also shares the bar's height as --ilc-nav-h so other sticky things sit below it.
 * On phones/tablets it turns the ☰ menu into a side panel that slides in from the left.
 */
(function () {
    var nav = document.querySelector('nav.main-nav');
    if (!nav) return;
    var box = nav.querySelector('.container') || nav;
    var logo = document.querySelector('.top-header .school-logo img');

    var brand = document.createElement('a');
    brand.className = 'nav-brand-mini';
    brand.href = '/';
    brand.setAttribute('aria-label', 'IEMELIF Learning Center — Home');
    brand.innerHTML = '<img alt=""><span>IEMELIF<small>Learning Center</small></span>';
    brand.querySelector('img').src = logo ? logo.getAttribute('src') : '/images/logo.png';
    box.insertBefore(brand, box.firstChild);

    var root = document.documentElement;
    var header = document.querySelector('.top-header');
    function setHeight() { root.style.setProperty('--ilc-nav-h', nav.offsetHeight + 'px'); }
    function update() { nav.classList.toggle('is-stuck', nav.getBoundingClientRect().top <= 0 && window.scrollY > 0); }

    // Stuck = the white header is fully off screen (no scroll math needed); scroll check as a fallback
    if (header && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            nav.classList.toggle('is-stuck', !entries[0].isIntersecting);
        }).observe(header);
    } else {
        window.addEventListener('scroll', update, { passive: true });
        update();
    }
    window.addEventListener('resize', setHeight);
    setHeight();

    // ── Phones/tablets: the menu slides in from the left (css/styles.css "SIDE MENU") ──
    // The ☰ button no longer drops the links down (Bootstrap collapse); it opens the same links as a
    // side panel with the school logo and a close button on top, over a dark backdrop.
    var toggler = nav.querySelector('.navbar-toggler');
    var panel   = nav.querySelector('.navbar-collapse');
    if (!toggler || !panel) return;
    toggler.removeAttribute('data-bs-toggle'); // Bootstrap would otherwise also drop it down
    toggler.setAttribute('aria-expanded', 'false');
    toggler.setAttribute('aria-label', 'Open menu');

    var head = document.createElement('div');
    head.className = 'nav-side-head';
    head.innerHTML = '<a href="/" class="nav-side-brand"><img alt=""><span>IEMELIF<small>Learning Center</small></span></a>'
        + '<button type="button" class="nav-side-close" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>';
    head.querySelector('img').src = logo ? logo.getAttribute('src') : '/images/logo.png';
    panel.insertBefore(head, panel.firstChild);

    // Inside the nav, not <body>: the sticky nav is its own stacking layer, so a backdrop outside it
    // would sit on top of the panel
    var backdrop = document.createElement('div');
    backdrop.className = 'nav-side-backdrop';
    nav.appendChild(backdrop);

    function setOpen(open) {
        nav.classList.toggle('side-open', open);
        backdrop.classList.toggle('show', open);
        document.body.classList.toggle('nav-side-lock', open);
        toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) head.querySelector('.nav-side-close').focus();
    }
    toggler.addEventListener('click', function () { setOpen(!nav.classList.contains('side-open')); });
    head.querySelector('.nav-side-close').addEventListener('click', function () { setOpen(false); toggler.focus(); });
    backdrop.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && nav.classList.contains('side-open')) { setOpen(false); toggler.focus(); } });
    panel.addEventListener('click', function (e) { if (e.target.closest('.nav-link, .btn')) setOpen(false); });
    window.addEventListener('resize', function () { if (window.innerWidth >= 992) setOpen(false); });
})();
