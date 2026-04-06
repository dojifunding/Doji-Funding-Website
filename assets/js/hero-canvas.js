/**
 * Doji Funding — Hero Dot-Grid Distortion Canvas
 *
 * Same algorithm as auth-canvas.js, scaled to the full hero section.
 * Dot grid rests as #1E1E1E dots on black. On mouse move, dots near
 * the cursor illuminate (#10B981) and distort outward (repulsion).
 *
 * Perf notes:
 *  - Grid spacing 24px → ~2,000 dots at 1920×600 (fast enough for 60fps)
 *  - IntersectionObserver pauses the RAF loop when hero is off-screen
 *  - Touch devices skip interaction (static dot-grid only)
 */

(function () {
    'use strict';

    /* ─── tunables ─────────────────────────────────────────── */
    var GRID        = 24;    /* px between dots                  */
    var DOT_BASE    = 0.85;  /* resting dot radius               */
    var DOT_MAX     = 3.2;   /* illuminated dot radius           */
    var GLOW_R      = 200;   /* illumination radius (px)         */
    var DISTORT_MAX = 16;    /* max outward push (px)            */
    var LERP_SPEED  = 0.07;  /* cursor smoothing                 */

    /* base #1E1E1E, accent #10B981 */
    var CR = 30,  CG = 30,  CB = 30;
    var AR = 16,  AG = 185, AB = 129;

    function lerp(a, b, t) { return a + (b - a) * t; }

    /* ─── main ─────────────────────────────────────────────── */
    var hero = document.querySelector('.hero');
    if (!hero) return;

    /* Create a dedicated 2D canvas — heroGlobe is used by Three.js (WebGL) */
    var canvas = document.createElement('canvas');
    canvas.setAttribute('aria-hidden', 'true');
    canvas.style.cssText = [
        'position:absolute', 'inset:0',
        'width:100%', 'height:100%',
        'display:block', 'pointer-events:none',
        'z-index:0',    /* behind globe (z-index:1) and content (z-index:3) */
    ].join(';');
    /* Insert as first child so it sits below everything */
    hero.insertBefore(canvas, hero.firstChild);

    var ctx = canvas.getContext('2d');
    if (!ctx) return;

    var mx = -999, my = -999;   /* raw mouse in hero coords   */
    var tx = -999, ty = -999;   /* smoothed target            */
    var active = false;          /* true while mouse is in hero */
    var visible = true;          /* IntersectionObserver flag   */
    var raf = null;

    /* ── size canvas to match hero ── */
    function resize() {
        canvas.width  = hero.offsetWidth  || window.innerWidth;
        canvas.height = hero.offsetHeight || 600;
    }

    /* ── render loop ── */
    function draw() {
        if (!visible) { raf = null; return; }

        var w = canvas.width, h = canvas.height;
        ctx.clearRect(0, 0, w, h);

        /* smooth cursor toward target */
        if (tx < -900) { tx = mx; ty = my; }
        else           { tx = lerp(tx, mx, LERP_SPEED);
                         ty = lerp(ty, my, LERP_SPEED); }

        var cols = Math.ceil(w / GRID) + 1;
        var rows = Math.ceil(h / GRID) + 1;

        for (var r = 0; r <= rows; r++) {
            for (var c = 0; c <= cols; c++) {
                var bx = c * GRID;
                var by = r * GRID;

                var dx   = bx - tx;
                var dy   = by - ty;
                var dist = Math.sqrt(dx * dx + dy * dy);

                /* illumination factor 0 → 1 (cubic falloff) */
                var t = 0;
                if (active && dist < GLOW_R) {
                    t = 1 - dist / GLOW_R;
                    t = t * t * t;
                }

                /* distort dot outward from cursor */
                var px = bx, py = by;
                if (t > 0.01 && dist > 1) {
                    var push = t * DISTORT_MAX;
                    px = bx + (dx / dist) * push;
                    py = by + (dy / dist) * push;
                }

                /* colour + size interpolation */
                var rv = Math.round(lerp(CR, AR, t));
                var gv = Math.round(lerp(CG, AG, t));
                var bv = Math.round(lerp(CB, AB, t));
                var dr = lerp(DOT_BASE, DOT_MAX, t);

                ctx.fillStyle = 'rgb(' + rv + ',' + gv + ',' + bv + ')';

                if (t > 0.02) {
                    /* illuminated — sharp square (Nothing precision aesthetic) */
                    ctx.fillRect(px - dr, py - dr, dr * 2, dr * 2);
                } else {
                    /* resting — small circle */
                    ctx.beginPath();
                    ctx.arc(px, py, dr, 0, 6.2832);
                    ctx.fill();
                }
            }
        }

        raf = requestAnimationFrame(draw);
    }

    /* ── event listeners ── */
    hero.addEventListener('mousemove', function (e) {
        var rect = hero.getBoundingClientRect();
        mx = e.clientX - rect.left;
        my = e.clientY - rect.top;
    });

    hero.addEventListener('mouseenter', function () {
        active = true;
    });

    hero.addEventListener('mouseleave', function () {
        active = false;
    });

    /* ── IntersectionObserver — pause when hero scrolled away ── */
    if (window.IntersectionObserver) {
        new IntersectionObserver(function (entries) {
            visible = entries[0].isIntersecting;
            if (visible && !raf) raf = requestAnimationFrame(draw);
        }, { threshold: 0 }).observe(hero);
    }

    /* ── resize handling ── */
    window.addEventListener('resize', function () {
        resize();
    });

    /* ── init ── */
    resize();
    raf = requestAnimationFrame(draw);

}());
