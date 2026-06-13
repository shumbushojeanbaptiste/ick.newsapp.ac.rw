/* ============================================================
   UR KAMINUZA - small public-site behaviour tweaks.
   Layered on top of the theme JS; we do NOT edit theme files.
   ============================================================ */
(function () {
    'use strict';

    function closeSidenav() {
        var sidenav = document.querySelector('.sidenav');
        if (sidenav) {
            sidenav.classList.remove('show');
        }
        document.body.classList.remove('sidenav-no-scroll');
        var overlay = document.querySelector('[data-sidenav-overlay], .sidenav-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    function init() {
        // Reliable mobile sidenav CLOSE. The theme copies the #sidenav-toggle id
        // onto every .navbar-toggle (duplicate IDs), so the close X inside the
        // sidenav sometimes fails to register with the theme plugin. We close it
        // explicitly here. Use capture-phase delegation so nothing can swallow it.
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.sidenav .navbar-toggle');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                closeSidenav();
                return;
            }
            // Tapping the dark overlay closes the menu too.
            if (e.target.closest('[data-sidenav-overlay], .sidenav-overlay')) {
                closeSidenav();
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Hero slider: rotate the top featured stories using the theme's own
    // owl-carousel (same fade config as the news slider). jQuery + owl are
    // already loaded by the footer before this script runs.
    if (window.jQuery && jQuery.fn.owlCarousel) {
        jQuery(function ($) {
            var $hero = $('#hero-slider');
            if ($hero.length && $hero.children().length > 1) {
                $hero.owlCarousel({
                    autoPlay: 6000,
                    stopOnHover: true,
                    navigation: true,
                    navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
                    pagination: true,
                    singleItem: true,
                    transitionStyle: 'fade'
                });
            }
        });
    }
})();
