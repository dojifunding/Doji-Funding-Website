/**
 * Doji Funding — Polish & UX Enhancements
 * v1.0.0
 *
 * Toast notifications, animated stat counters,
 * nav scroll behavior, enhanced interactions.
 */

(function() {
    'use strict';

    // ─── Toast Notification System ───
    var toastContainer = null;

    function getToastContainer() {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'doji-toast-container';
            document.body.appendChild(toastContainer);
        }
        return toastContainer;
    }

    var toastIcons = {
        success: '<svg class="doji-toast-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#10B981" stroke-width="1.5"/><path d="M6 10l3 3 5-6" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        error: '<svg class="doji-toast-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#ff3b3b" stroke-width="1.5"/><path d="M7 7l6 6M13 7l-6 6" stroke="#ff3b3b" stroke-width="1.5" stroke-linecap="round"/></svg>',
        info: '<svg class="doji-toast-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#4a9eff" stroke-width="1.5"/><path d="M10 9v5M10 6.5v0" stroke="#4a9eff" stroke-width="1.5" stroke-linecap="round"/></svg>'
    };

    function showToast(message, type, duration) {
        type = type || 'info';
        duration = duration || 4000;

        var container = getToastContainer();
        var toast = document.createElement('div');
        toast.className = 'doji-toast doji-toast-' + type;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');

        toast.innerHTML =
            (toastIcons[type] || toastIcons.info) +
            '<span>' + message + '</span>' +
            '<button class="doji-toast-close" aria-label="Dismiss">&times;</button>';

        container.appendChild(toast);

        // Close on click
        var closeBtn = toast.querySelector('.doji-toast-close');
        closeBtn.addEventListener('click', function() { dismissToast(toast); });

        // Auto-dismiss
        var timer = setTimeout(function() { dismissToast(toast); }, duration);
        toast._timer = timer;

        return toast;
    }

    function dismissToast(toast) {
        if (toast._dismissed) return;
        toast._dismissed = true;
        clearTimeout(toast._timer);
        toast.classList.add('hiding');
        setTimeout(function() {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 250);
    }

    // Expose globally
    window.DojiToast = {
        success: function(msg, dur) { return showToast(msg, 'success', dur); },
        error: function(msg, dur) { return showToast(msg, 'error', dur); },
        info: function(msg, dur) { return showToast(msg, 'info', dur); }
    };


    // ─── Nav Scroll Behavior ───
    function initNavScroll() {
        var nav = document.querySelector('.nav');
        if (!nav) return;

        var lastScroll = 0;
        var ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    var scrollY = window.pageYOffset || document.documentElement.scrollTop;

                    // Add shadow when scrolled
                    if (scrollY > 20) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }

                    lastScroll = scrollY;
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }


    // ─── Animated Stat Counters ───
    function animateCounter(el, target, suffix, prefix) {
        suffix = suffix || '';
        prefix = prefix || '';

        var startTime = null;
        var duration = 1200;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);

            // Ease out cubic
            var eased = 1 - Math.pow(1 - progress, 3);
            var value = Math.round(target * eased);

            el.textContent = prefix + value.toLocaleString('en-US') + suffix;
            el.classList.add('counting');

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.classList.remove('counting');
            }
        }

        requestAnimationFrame(step);
    }

    function initCounters() {
        var counters = document.querySelectorAll('[data-count]');
        if (!counters.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !entry.target._counted) {
                    entry.target._counted = true;
                    var target = parseInt(entry.target.getAttribute('data-count'), 10);
                    var suffix = entry.target.getAttribute('data-suffix') || '';
                    var prefix = entry.target.getAttribute('data-prefix') || '';
                    animateCounter(entry.target, target, suffix, prefix);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach(function(el) { observer.observe(el); });
    }


    // ─── Enhanced Promo Code Copy Feedback ───
    function enhanceCopyFeedback() {
        var origCopyFn = window.copyPromoCode;
        if (!origCopyFn) return;

        window.copyPromoCode = function(code) {
            origCopyFn(code);
            DojiToast.success('Promo code <strong>' + code + '</strong> copied!', 3000);
        };
    }


    // ─── Keyboard Navigation Enhancements ───
    function initKeyboardNav() {
        // Escape closes mobile menu
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close mobile menu
                var menu = document.getElementById('mobileMenu');
                var burger = document.getElementById('navHamburger');
                if (menu && menu.classList.contains('open')) {
                    menu.classList.remove('open');
                    if (burger) burger.classList.remove('open');
                    document.body.classList.remove('menu-open');
                }

                // Close dropdowns
                document.querySelectorAll('.nav-dropdown.open').forEach(function(d) {
                    d.classList.remove('open');
                    var trigger = d.closest('.nav-dropdown-wrap, .lang-wrap');
                    if (trigger) {
                        var btn = trigger.querySelector('.nav-dropdown-trigger, .lang-btn');
                        if (btn) btn.classList.remove('open');
                    }
                });
            }
        });
    }


    // ─── Disclaimer Persistence ───
    // (Cooldown is set in app.js — 7 days)
    function fixDisclaimerPersistence() {
        // No-op — cooldown handled in app.js
    }


    // ─── Button Loading State Helper ───
    window.DojiButton = {
        loading: function(btn) {
            if (!btn) return;
            btn._origText = btn.textContent;
            btn.classList.add('btn-loading');
            btn.disabled = true;
        },
        done: function(btn, text) {
            if (!btn) return;
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            if (text) {
                btn.textContent = text;
            } else if (btn._origText) {
                btn.textContent = btn._origText;
            }
        }
    };


    // ─── Initialize Everything ───
    function init() {
        initNavScroll();
        initCounters();
        initKeyboardNav();
        enhanceCopyFeedback();
        fixDisclaimerPersistence();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
