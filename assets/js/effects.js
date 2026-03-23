/**
 * Doji Funding — Visual Effects Engine
 * v2.8.0
 *
 * IntersectionObserver for scroll reveals,
 * price counter animation, smooth number transitions.
 */

(function() {
    'use strict';

    // ─── Scroll Reveal Observer ───
    function initScrollReveal() {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    // Don't unobserve — allows re-entry if needed
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -40px 0px'
        });

        // Observe all scroll-reveal elements
        document.querySelectorAll('.scroll-reveal, .stagger-children').forEach(function(el) {
            observer.observe(el);
        });
    }

    // ─── Auto-tag elements for scroll animation ───
    function autoTagScrollElements() {
        // Tag common page elements that should animate on scroll
        var selectors = [
            '.rule-card',
            '.scale-card',
            '.scale-step',
            '.aff-stat',
            '.aff-step',
            '.aff-tier',
            '.level-item',
            '.contact-card',
            '.contact-social-card',
            '.contact-form-wrap',
            '.legal-block',
            '.platform-card',
            '.compare-table',
            '.comp-card',
            '.comp-rules',
            '.comp-prizes-section',
            '.comp-faq',
            '.comp-disclaimer',
        ];

        selectors.forEach(function(sel) {
            document.querySelectorAll(sel).forEach(function(el, i) {
                if (!el.classList.contains('scroll-reveal')) {
                    el.classList.add('scroll-reveal');
                    el.style.transitionDelay = (i * 0.06) + 's';
                }
            });
        });

        // Tag grids for stagger effect
        document.querySelectorAll('.symbol-grid, .scale-progression, .aff-stats, .contact-social-grid').forEach(function(el) {
            if (!el.classList.contains('stagger-children')) {
                el.classList.add('stagger-children');
            }
        });
    }

    // ─── Animated Price Counter ───
    // Smoothly animate price changes instead of instant swap
    var lastPrice = null;
    var priceAnimFrame = null;

    function animatePrice(newPrice) {
        var priceEl = document.getElementById('priceVal');
        if (!priceEl) return;

        // Parse current displayed price
        var currentText = priceEl.textContent || priceEl.innerText;
        var current = parseFloat(currentText.replace(/[^0-9.]/g, '')) || 0;
        var target = parseFloat(String(newPrice).replace(/[^0-9.]/g, '')) || 0;

        if (current === target) return;
        if (priceAnimFrame) cancelAnimationFrame(priceAnimFrame);

        var startTime = null;
        var duration = 300; // ms

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);

            // Ease out cubic
            var eased = 1 - Math.pow(1 - progress, 3);
            var value = Math.round(current + (target - current) * eased);

            priceEl.textContent = '$' + value.toLocaleString('en-US');

            if (progress < 1) {
                priceAnimFrame = requestAnimationFrame(step);
            }
        }

        priceAnimFrame = requestAnimationFrame(step);
    }

    // Expose for configurator to use
    window.DojiEffects = {
        animatePrice: animatePrice,
    };

    // ─── Parallax on mouse (subtle, desktop only) ───
    function initParallax() {
        if (window.innerWidth < 900) return;

        var orbs = document.querySelectorAll('.cfg-layout::before, .cfg-layout::after');
        
        document.addEventListener('mousemove', function(e) {
            var x = (e.clientX / window.innerWidth - 0.5) * 2;
            var y = (e.clientY / window.innerHeight - 0.5) * 2;

            // Move the layout's pseudo-elements via CSS custom properties
            var layout = document.querySelector('.cfg-layout');
            if (layout) {
                layout.style.setProperty('--mouse-x', x * 15 + 'px');
                layout.style.setProperty('--mouse-y', y * 15 + 'px');
            }
        }, { passive: true });
    }

    // ─── Custom Scrollbar — grows as you scroll down ───
    function initDynamicScrollbar() {
        // Don't run on mobile/touch devices — they use overlay scrollbars
        if ('ontouchstart' in window && window.innerWidth < 1024) return;

        // Hide native scrollbar
        var hideStyle = document.createElement('style');
        hideStyle.textContent =
            'html { scrollbar-width: none !important; }' +
            'html::-webkit-scrollbar { display: none !important; }' +
            'body { -ms-overflow-style: none; }';
        document.head.appendChild(hideStyle);

        // Create custom scrollbar elements
        var track = document.createElement('div');
        track.className = 'doji-sb-track';
        var thumb = document.createElement('div');
        thumb.className = 'doji-sb-thumb';
        track.appendChild(thumb);
        document.body.appendChild(track);

        var isDragging = false;
        var dragStartY = 0;
        var dragStartScroll = 0;
        var hideTimer = null;

        function update() {
            var root = document.documentElement;
            var scrollTop = window.pageYOffset || root.scrollTop;
            var docHeight = root.scrollHeight;
            var viewHeight = window.innerHeight;

            if (docHeight <= viewHeight) {
                track.style.display = 'none';
                return;
            }
            track.style.display = '';

            var progress = scrollTop / (docHeight - viewHeight);

            // Thumb grows in LENGTH: starts tiny, grows as you scroll
            var minH = 20;
            var maxH = Math.min(viewHeight * 0.45, 350);
            var thumbH = minH + progress * (maxH - minH);

            var maxTop = viewHeight - thumbH;
            var thumbTop = progress * maxTop;

            // Glow grows with scroll
            var glow = 0.1 + progress * 0.4;

            thumb.style.height = thumbH + 'px';
            thumb.style.top = thumbTop + 'px';
            thumb.style.boxShadow = '0 0 ' + (4 + progress * 10) + 'px rgba(16,185,129,' + glow.toFixed(2) + ')';

            // Show on scroll
            track.classList.add('visible');
            clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {
                if (!isDragging) track.classList.remove('visible');
            }, 1200);
        }

        // Drag support
        thumb.addEventListener('mousedown', function(e) {
            isDragging = true;
            dragStartY = e.clientY;
            dragStartScroll = window.pageYOffset;
            track.classList.add('dragging');
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        window.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            var root = document.documentElement;
            var viewHeight = window.innerHeight;
            var docHeight = root.scrollHeight;
            var delta = e.clientY - dragStartY;
            var scrollRange = docHeight - viewHeight;
            // Use current thumb height from DOM
            var currentThumbH = thumb.offsetHeight;
            var trackRange = viewHeight - currentThumbH;
            window.scrollTo(0, dragStartScroll + (delta / trackRange) * scrollRange);
        });

        window.addEventListener('mouseup', function() {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove('dragging');
            document.body.style.userSelect = '';
        });

        // Click on track to jump
        track.addEventListener('click', function(e) {
            if (e.target === thumb) return;
            var rect = track.getBoundingClientRect();
            var clickRatio = (e.clientY - rect.top) / rect.height;
            var docHeight = document.documentElement.scrollHeight;
            var viewHeight = window.innerHeight;
            window.scrollTo({ top: clickRatio * (docHeight - viewHeight), behavior: 'smooth' });
        });

        // Hover to expand
        track.addEventListener('mouseenter', function() {
            track.classList.add('hover');
            track.classList.add('visible');
            clearTimeout(hideTimer);
        });
        track.addEventListener('mouseleave', function() {
            track.classList.remove('hover');
            if (!isDragging) {
                hideTimer = setTimeout(function() {
                    track.classList.remove('visible');
                }, 800);
            }
        });

        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    update();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        window.addEventListener('resize', update, { passive: true });
        update();
    }

    // ─── Wintermute Accordion ───
    function initAccordion() {
        var accordion = document.getElementById('wmAccordion');
        if (!accordion) return;

        var items = accordion.querySelectorAll('.wm-item');
        items.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                items.forEach(function(it) { it.classList.remove('active'); });
                item.classList.add('active');
            });
        });
    }

    // ─── Initialize ───
    function init() {
        autoTagScrollElements();
        initScrollReveal();
        initParallax();
        initDynamicScrollbar();
        initAccordion();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
