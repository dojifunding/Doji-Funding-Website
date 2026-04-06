/**
 * Doji Funding — Laser Burn Auth Transition
 *
 * A #10B981 laser sweeps top → bottom inside the active auth modal,
 * burning it to OLED black. The modal's overflow:hidden clips the
 * effect naturally. Navigates to target URL after the sweep.
 *
 * Usage:  LaserBurn.trigger('dashboard.php');
 */

(function () {
    'use strict';

    /* ─── tunables ──────────────────────────────────────── */
    var DURATION     = 680;   /* sweep duration (ms)          */
    var HOLD         = 160;   /* hold black before navigate   */
    var SPARKS_FRAME = 7;     /* sparks emitted per frame     */
    var LASER_HALF   = 16;    /* glow half-height (px)        */
    var ATM_HALF     = 60;    /* atmospheric bloom half-h     */

    /* #10B981 components */
    var LR = 16, LG = 185, LB = 129;

    /* ─── state ─────────────────────────────────────────── */
    var canvas, ctx, modal;
    var particles   = [];
    var raf         = null;
    var t0          = null;
    var dest        = null;
    var _onComplete = null;

    /* ─── spark pool ────────────────────────────────────── */
    function spawnSparks(W, y) {
        for (var i = 0; i < SPARKS_FRAME; i++) {
            particles.push({
                x:     Math.random() * W,
                y:     y + (Math.random() - 0.5) * 3,
                vx:    (Math.random() - 0.5) * 4,
                vy:    -(1.5 + Math.random() * 4.5),
                life:  0.8 + Math.random() * 0.2,
                decay: 0.045 + Math.random() * 0.04,
                size:  Math.random() > 0.6 ? 2 : 1,
                hot:   Math.random() > 0.5,
            });
        }
    }

    /* ─── render loop ───────────────────────────────────── */
    function draw(ts) {
        if (!t0) t0 = ts;
        var elapsed  = ts - t0;
        var progress = Math.min(elapsed / DURATION, 1.0);

        /* ease-in-out quad */
        var e = progress < 0.5
            ? 2 * progress * progress
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;

        var W  = modal.offsetWidth;
        var H  = modal.offsetHeight;
        canvas.width  = W;
        canvas.height = H;

        var ly = e * H;

        ctx.clearRect(0, 0, W, H);

        /* ── burned area above laser ── */
        ctx.fillStyle = '#000000';
        ctx.fillRect(0, 0, W, Math.ceil(ly));

        /* ── laser beam ── */
        if (progress < 1) {
            /* atmospheric bloom */
            var atm = ctx.createLinearGradient(0, ly - ATM_HALF, 0, ly + ATM_HALF);
            atm.addColorStop(0,   'rgba(0,0,0,0)');
            atm.addColorStop(0.5, 'rgba(' + LR + ',' + LG + ',' + LB + ',0.08)');
            atm.addColorStop(1,   'rgba(0,0,0,0)');
            ctx.fillStyle = atm;
            ctx.fillRect(0, ly - ATM_HALF, W, ATM_HALF * 2);

            /* tight glow + white-hot core */
            var beam = ctx.createLinearGradient(0, ly - LASER_HALF, 0, ly + LASER_HALF);
            beam.addColorStop(0,    'rgba(0,0,0,0)');
            beam.addColorStop(0.28, 'rgba(' + LR + ',' + LG + ',' + LB + ',0.25)');
            beam.addColorStop(0.46, 'rgba(' + LR + ',' + LG + ',' + LB + ',0.90)');
            beam.addColorStop(0.50, 'rgba(255,255,255,1.00)');
            beam.addColorStop(0.54, 'rgba(' + LR + ',' + LG + ',' + LB + ',0.90)');
            beam.addColorStop(0.72, 'rgba(' + LR + ',' + LG + ',' + LB + ',0.25)');
            beam.addColorStop(1,    'rgba(0,0,0,0)');
            ctx.fillStyle = beam;
            ctx.fillRect(0, ly - LASER_HALF, W, LASER_HALF * 2);

            spawnSparks(W, ly);
        }

        /* ── particles ── */
        for (var i = particles.length - 1; i >= 0; i--) {
            var p = particles[i];
            p.x    += p.vx;
            p.y    += p.vy;
            p.vy   *= 0.94;
            p.life -= p.decay;
            if (p.life <= 0) { particles.splice(i, 1); continue; }

            ctx.globalAlpha = p.life * p.life;
            ctx.fillStyle   = p.hot
                ? 'rgba(255,255,255,1)'
                : 'rgba(' + LR + ',' + LG + ',' + LB + ',1)';
            ctx.fillRect(p.x - p.size * 0.5, p.y - p.size * 0.5, p.size, p.size);
        }
        ctx.globalAlpha = 1;

        if (progress < 1) {
            raf = requestAnimationFrame(draw);
        } else {
            raf = null;
            setTimeout(function () {
                if (dest) {
                    window.location.href = dest;
                } else if (_onComplete) {
                    _onComplete();
                    _onComplete = null;
                }
            }, HOLD);
        }
    }

    /* ─── shared setup ──────────────────────────────────── */
    function setupCanvas(container) {
        canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:absolute;inset:0;z-index:50;pointer-events:all;';
        container.appendChild(canvas);
        ctx = canvas.getContext('2d');
        modal = container;
    }

    /* ─── trigger: auth modal → page navigation ─────────── */
    function trigger(url) {
        if (raf) return;
        var overlay = document.querySelector('.modal-overlay.active');
        if (!overlay) { window.location.href = url; return; }
        modal = overlay.querySelector('.modal');
        if (!modal) { window.location.href = url; return; }

        dest      = url;
        _onComplete = null;
        particles = [];
        t0        = null;

        setupCanvas(modal);
        document.body.style.overflow = 'hidden';
        raf = requestAnimationFrame(draw);
    }

    /* ─── triggerInElement: burn inside any element → callback ─ */
    function triggerInElement(el, onComplete) {
        if (raf) { if (onComplete) onComplete(); return; }

        /* Create a fixed overlay exactly covering el's visible rect */
        var rect = el.getBoundingClientRect();
        var wrap = document.createElement('div');
        wrap.style.cssText = [
            'position:fixed',
            'top:'    + Math.round(rect.top)    + 'px',
            'left:'   + Math.round(rect.left)   + 'px',
            'width:'  + Math.round(rect.width)  + 'px',
            'height:' + Math.round(rect.height) + 'px',
            'z-index:9000', 'overflow:hidden', 'pointer-events:all',
        ].join(';');
        document.body.appendChild(wrap);

        dest        = null;
        _onComplete = function() {
            document.body.removeChild(wrap);
            if (onComplete) onComplete();
        };
        particles = [];
        t0        = null;

        setupCanvas(wrap);
        raf = requestAnimationFrame(draw);
    }

    window.LaserBurn = { trigger: trigger, triggerInElement: triggerInElement };
}());
