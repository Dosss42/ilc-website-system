/*
 * Public pages: the blue menu bar (.main-nav) sticks to the top while scrolling (css/styles.css).
 * This adds the school logo + name to its left side and shows them once the white header above has
 * scrolled away. It also shares the bar's height as --ilc-nav-h so other sticky things sit below it.
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
})();
