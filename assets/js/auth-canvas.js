/**
 * Doji Funding — Auth Modal Dot-Grid Canvas Effect
 *
 * Dot-grid drawn on canvas behind the form.
 * On input focus / mouse move: dots near cursor illuminate (#2A2A2A → #10B981)
 * and distort outward (repulsion), creating a "light pushing through a grid" effect.
 *
 * No dependencies. Initialised lazily when modal overlay becomes active.
 */

(function () {
    'use strict';

    /* ─── tunables ─────────────────────────────────────────── */
    var GRID        = 20;    /* px between dots                  */
    var DOT_BASE    = 0.75;  /* resting dot radius               */
    var DOT_MAX     = 2.6;   /* illuminated dot radius           */
    var GLOW_R      = 140;   /* illumination radius (px)         */
    var DISTORT_MAX = 12;    /* max outward push (px)            */
    var LERP_IDLE   = 0.05;  /* mouse smoothing when idle        */
    var LERP_ACT    = 0.10;  /* mouse smoothing when active      */

    /* ─── base colour  #2A2A2A, accent #10B981 ─────────────── */
    var CR = 42,  CG = 42,  CB = 42;   /* dots at rest  */
    var AR = 16,  AG = 185, AB = 129;  /* dots lit      */

    /* ─── helpers ───────────────────────────────────────────── */
    function lerp(a, b, t) { return a + (b - a) * t; }
    function clamp(v, lo, hi) { return v < lo ? lo : v > hi ? hi : v; }

    /* ─── per-modal instance ─────────────────────────────────── */
    function ModalCanvas(box) {
        var canvas = box.querySelector('.modal-dot-canvas');
        if (!canvas) return;

        var ctx    = canvas.getContext('2d');
        var mx = -1, my = -1;   /* raw mouse in modal coords   */
        var tx = -1, ty = -1;   /* smoothed target             */
        var active = false;
        var raf    = null;

        /* ── size canvas to match modal ── */
        function resize() {
            canvas.width  = box.offsetWidth;
            canvas.height = box.offsetHeight;
        }

        /* ── main render loop ── */
        function draw() {
            var w = canvas.width, h = canvas.height;
            ctx.clearRect(0, 0, w, h);

            /* smooth cursor */
            var speed = active ? LERP_ACT : LERP_IDLE;
            if (tx < 0) { tx = mx; ty = my; }
            else        { tx = lerp(tx, mx, speed); ty = lerp(ty, my, speed); }

            var cols = Math.ceil(w / GRID) + 1;
            var rows = Math.ceil(h / GRID) + 1;

            for (var r = 0; r <= rows; r++) {
                for (var c = 0; c <= cols; c++) {
                    var bx = c * GRID;
                    var by = r * GRID;

                    var dx   = bx - tx;
                    var dy   = by - ty;
                    var dist = Math.sqrt(dx * dx + dy * dy);

                    /* illumination factor  0 → 1 */
                    var t = 0;
                    if (active && tx > 0 && dist < GLOW_R) {
                        t = 1 - dist / GLOW_R;
                        t = t * t * t; /* cubic — tight centre */
                    }

                    /* distortion: push dot outward from cursor */
                    var px = bx, py = by;
                    if (t > 0.01 && dist > 1) {
                        var push = t * DISTORT_MAX;
                        px = bx + (dx / dist) * push;
                        py = by + (dy / dist) * push;
                    }

                    /* colour interpolation */
                    var rv = Math.round(lerp(CR, AR, t));
                    var gv = Math.round(lerp(CG, AG, t));
                    var bv = Math.round(lerp(CB, AB, t));
                    var dr = lerp(DOT_BASE, DOT_MAX, t);

                    ctx.beginPath();
                    ctx.arc(px, py, dr, 0, 6.2832);
                    ctx.fillStyle = 'rgb(' + rv + ',' + gv + ',' + bv + ')';
                    ctx.fill();
                }
            }

            raf = requestAnimationFrame(draw);
        }

        /* ── event handlers ── */
        function onMouseMove(e) {
            var rect = box.getBoundingClientRect();
            mx = e.clientX - rect.left;
            my = e.clientY - rect.top;
            /* passive hover — only glow if an input is focused */
        }

        function onFocusIn(e) {
            var el = e.target;
            if (!el.matches) return;
            if (el.matches('.form-input, .form-select')) {
                active = true;
                /* if mouse hasn't entered yet, anchor to input centre */
                if (mx < 0) {
                    var rect = box.getBoundingClientRect();
                    var ir   = el.getBoundingClientRect();
                    mx = ir.left + ir.width  / 2 - rect.left;
                    my = ir.top  + ir.height / 2 - rect.top;
                    tx = mx; ty = my;
                }
            }
        }

        function onFocusOut() {
            /* small delay so switching between inputs doesn't flicker */
            setTimeout(function () {
                if (!box.querySelector('.form-input:focus, .form-select:focus')) {
                    active = false;
                }
            }, 60);
        }

        function onMouseEnter() {
            /* activate illumination on any hover over modal */
            active = true;
        }
        function onMouseLeave() {
            /* keep glow while an input is still focused */
            setTimeout(function () {
                if (!box.querySelector('.form-input:focus, .form-select:focus')) {
                    active = false;
                }
            }, 60);
        }

        box.addEventListener('mousemove',  onMouseMove);
        box.addEventListener('mouseenter', onMouseEnter);
        box.addEventListener('mouseleave', onMouseLeave);
        box.addEventListener('focusin',    onFocusIn);
        box.addEventListener('focusout',   onFocusOut);

        /* resize on window change */
        var resizeObs = window.ResizeObserver
            ? new ResizeObserver(resize)
            : null;
        if (resizeObs) resizeObs.observe(box);

        resize();
        draw();

        this.destroy = function () {
            cancelAnimationFrame(raf);
            if (resizeObs) resizeObs.disconnect();
            box.removeEventListener('mousemove',  onMouseMove);
            box.removeEventListener('mouseenter', onMouseEnter);
            box.removeEventListener('mouseleave', onMouseLeave);
            box.removeEventListener('focusin',    onFocusIn);
            box.removeEventListener('focusout',   onFocusOut);
        };
    }

    /* ─── lazy init via MutationObserver ─────────────────────── */
    var instances = {};

    function initModal(overlayId) {
        if (instances[overlayId]) return;
        var overlay = document.getElementById(overlayId);
        if (!overlay) return;
        var box = overlay.querySelector('.modal');
        if (!box) return;
        instances[overlayId] = new ModalCanvas(box);
    }

    function destroyModal(overlayId) {
        if (instances[overlayId]) {
            instances[overlayId].destroy();
            delete instances[overlayId];
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['loginModal', 'signupModal', 'payoutModal', 'profitSplitModal', 'discountModal', 'purchaseModal', 'payoutDetailModal'].forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            new MutationObserver(function () {
                if (el.classList.contains('active')) {
                    initModal(id);
                } else {
                    destroyModal(id);
                }
            }).observe(el, { attributes: true, attributeFilter: ['class'] });
        });
    });

    window.AuthCanvas = { init: initModal, destroy: destroyModal };
}());
