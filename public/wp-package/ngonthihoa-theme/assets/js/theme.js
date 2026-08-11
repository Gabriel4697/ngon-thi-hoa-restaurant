/**
 * Ngon Thi Hoa - Theme JavaScript
 * @global ngonthihoaVars
 */
/* global ngonthihoaVars */
(function() {
    'use strict';

    // ── Hero Slider ───────────────────────────────────────────────────────────
    var slides = document.querySelectorAll('.hero-slide');
    var dots   = document.querySelectorAll('.hero-dot');
    var current = 0;
    var sliderInterval;

    function goToSlide(n) {
        if (!slides.length) return;
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    function startSlider() {
        sliderInterval = setInterval(function() { goToSlide(current + 1); }, 5000);
    }

    if (slides.length) {
        startSlider();
        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() {
                clearInterval(sliderInterval);
                goToSlide(i);
                startSlider();
            });
        });
    }

    // ── Menu Group Tabs (homepage) ────────────────────────────────────────────
    var menuTabs = document.querySelectorAll('#menu-group-tabs .menu-tab');
    menuTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var group = tab.dataset.group;
            menuTabs.forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');

            // AJAX load
            var grid = document.getElementById('menu-items-grid');
            if (!grid) return;
            grid.style.opacity = '0.5';

            var data = new FormData();
            data.append('action', 'ngonthihoa_get_menu_items');
            data.append('group',  group);
            data.append('nonce',  ngonthihoaVars.nonce);

            fetch(ngonthihoaVars.ajaxUrl, { method:'POST', body: data })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        grid.innerHTML = res.data.html;
                    }
                    grid.style.opacity = '1';
                })
                .catch(function() { grid.style.opacity = '1'; });
        });
    });

    // ── Menu Page Tabs ────────────────────────────────────────────────────────
    var pageTabs = document.querySelectorAll('#menu-page-tabs .menu-tab');
    pageTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            pageTabs.forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            var group = tab.dataset.group;

            document.querySelectorAll('.menu-group-panel').forEach(function(panel) {
                panel.style.display = panel.id === 'group-' + group ? '' : 'none';
            });
        });
    });

    // ── Modal close on backdrop click ────────────────────────────────────────
    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('open');
            }
        });
    });

    // ── Keyboard close for modals ─────────────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(function(m) {
                m.classList.remove('open');
            });
            var lb = document.getElementById('media-lightbox');
            if (lb && lb.classList.contains('open')) {
                lb.classList.remove('open');
                document.body.style.overflow = '';
            }
        }
    });

    // ── Mobile nav close on outside click ────────────────────────────────────
    document.addEventListener('click', function(e) {
        var mobileNav = document.getElementById('mobile-nav');
        var toggle    = document.querySelector('.mobile-menu-toggle');
        if (mobileNav && mobileNav.classList.contains('open') &&
            !mobileNav.contains(e.target) && !toggle.contains(e.target)) {
            mobileNav.classList.remove('open');
        }
    });

    // ── Sticky header scroll effect ───────────────────────────────────────────
    var header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                header.style.boxShadow = '0 4px 24px rgba(94,71,67,0.15)';
            } else {
                header.style.boxShadow = '0 2px 20px rgba(94,71,67,0.1)';
            }
        }, { passive: true });
    }

    // ── Smooth anchor scroll ──────────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            var target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

})();
