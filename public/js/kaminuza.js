/* ============================================================
   ICK News - small public-site behaviour tweaks.
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

    // Search box: the theme opened the input on HOVER, which on a touch screen
    // fires from any tap near the icon and leaves the field hanging open next
    // to the menu button. Now the field only opens when the magnifier is
    // clicked: first click opens + focuses it, and only once it holds text does
    // the button actually submit the search. Clicking outside closes it again.
    function initSearch() {
        var form = document.querySelector('.kz-search');
        if (!form) { return; }
        var btn   = form.querySelector('.search-icon-btn');
        var input = form.querySelector('.search-bar');
        if (!btn || !input) { return; }

        // Keep it open on a results page so the query stays visible/editable.
        if (input.value.trim() !== '') {
            form.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        }

        btn.addEventListener('click', function (e) {
            if (!form.classList.contains('is-open')) {
                e.preventDefault();               // open instead of submitting
                form.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
                input.focus();
                return;
            }
            if (input.value.trim() === '') {
                e.preventDefault();               // nothing to search for yet
                input.focus();
            }
        });

        // Tapping/clicking anywhere outside closes the empty field again.
        document.addEventListener('click', function (e) {
            if (form.contains(e.target)) { return; }
            if (input.value.trim() === '') {
                form.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        // Escape closes it too.
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                form.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                input.blur();
            }
        });
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

    // On-site radio: clicking the header "ICK Radio" link opens a small
    // floating player and starts the stream (instead of leaving the site).
    // The player still has an "open in new tab" link as a fallback.
    // Before the station launches there is no stream URL, so the bar opens
    // showing a "coming soon" note and there is no <audio> element to play.
    function initRadio() {
        var player = document.getElementById('kz-radio-player');
        if (!player) { return; }
        var audio = document.getElementById('kz-radio-audio');

        document.addEventListener('click', function (e) {
            var link = e.target.closest('.kz-radio-link');
            if (link) {
                e.preventDefault();
                player.hidden = false;
                if (audio) {
                    var play = audio.play();
                    if (play && play.catch) { play.catch(function () {}); }
                }
                return;
            }
            if (e.target.closest('.kz-radio-player__close')) {
                if (audio) { audio.pause(); }
                player.hidden = true;
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { init(); initSearch(); initReactions(); initNoticeDismiss(); initRadio(); });
    } else {
        init();
        initSearch();
        initReactions();
        initNoticeDismiss();
        initRadio();
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
