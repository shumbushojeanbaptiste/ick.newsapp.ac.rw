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

    // Like / dislike on the single-article page (AJAX, instant). Posts to
    // react.php and updates the counts + active state without a page reload.
    function initReactions() {
        var box = document.getElementById('kz-reactions');
        if (!box) { return; }
        var articleId = box.getAttribute('data-article');
        box.addEventListener('click', function (e) {
            var btn = e.target.closest('.kz-react');
            if (!btn) { return; }
            var reaction = btn.getAttribute('data-reaction');
            btn.disabled = true;
            var data = new URLSearchParams();
            data.append('article_id', articleId);
            data.append('reaction', reaction);
            fetch('react', { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (j) {
                    if (!j.ok) { return; }
                    box.querySelector('[data-count="like"]').textContent = j.likes;
                    box.querySelector('[data-count="dislike"]').textContent = j.dislikes;
                    box.querySelector('.kz-like').classList.toggle('is-active', j.mine === 'like');
                    box.querySelector('.kz-dislike').classList.toggle('is-active', j.mine === 'dislike');
                })
                .catch(function () {})
                .then(function () { btn.disabled = false; });
        });
    }

    // Auto-dismiss the comment notice (the "submitted/error" message) after 5s.
    function initNoticeDismiss() {
        var note = document.querySelector('.kz-note');
        if (!note) { return; }
        setTimeout(function () {
            note.classList.add('kz-fade-out');
            setTimeout(function () { note.remove(); }, 600);
        }, 5000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { init(); initReactions(); initNoticeDismiss(); });
    } else {
        init();
        initReactions();
        initNoticeDismiss();
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
