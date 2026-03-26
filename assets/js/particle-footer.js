/**
 * Doji Funding — Dot-Matrix Footer (Cotool style)
 * v4.0.0
 *
 * Renders "DOJI" as large flat dot-matrix circles.
 * Big dots, wide spacing, fewer particles = silky smooth.
 * Mouse/touch scatter effect with spring return.
 */
(function () {
    'use strict';

    // ─── Configuration ───
    var CONFIG = {
        text: 'DOJI',
        dotRadius: 6.5,
        dotGap: 14,
        mouseRadius: 120,
        returnSpeed: 0.07,
        friction: 0.86,
        pushForce: 10,
        canvasHeight: 320,
        alphaThreshold: 60,
        sampleScale: 4
    };

    // ─── Colors ───
    var COLOR_REST = 'rgba(255, 255, 255, 0.75)';
    var COLOR_SCATTER = 'rgba(16, 185, 129, 0.9)';

    // ─── State ───
    var canvas, ctx;
    var particles = [];
    var mouse = { x: -9999, y: -9999, active: false };
    var animId = null;
    var visible = false;
    var dpr = 1;

    // ─── Particle ───
    function Dot(x, y) {
        this.ox = x;
        this.oy = y;
        this.x = x + (Math.random() - 0.5) * 400;
        this.y = y + (Math.random() - 0.5) * 300;
        this.vx = 0;
        this.vy = 0;
    }

    Dot.prototype.update = function () {
        var dx = this.x - mouse.x;
        var dy = this.y - mouse.y;
        var d2 = dx * dx + dy * dy;
        var r2 = CONFIG.mouseRadius * CONFIG.mouseRadius;

        if (d2 < r2 && mouse.active) {
            var d = Math.sqrt(d2) || 1;
            var f = (CONFIG.mouseRadius - d) / CONFIG.mouseRadius;
            this.vx += (dx / d) * f * CONFIG.pushForce;
            this.vy += (dy / d) * f * CONFIG.pushForce;
        }

        this.vx += (this.ox - this.x) * CONFIG.returnSpeed;
        this.vy += (this.oy - this.y) * CONFIG.returnSpeed;
        this.vx *= CONFIG.friction;
        this.vy *= CONFIG.friction;
        this.x += this.vx;
        this.y += this.vy;
    };

    Dot.prototype.draw = function () {
        var dx = this.x - this.ox;
        var dy = this.y - this.oy;
        var dist2 = dx * dx + dy * dy;
        var px = this.x;
        var py = this.y;

        // Snap when nearly at rest
        if (dist2 < 0.25) {
            px = this.ox;
            py = this.oy;
        }

        // Flat circle — switch color when scattered
        ctx.beginPath();
        ctx.arc(px, py, CONFIG.dotRadius, 0, 6.2832);
        ctx.fillStyle = dist2 > 200 ? COLOR_SCATTER : COLOR_REST;
        ctx.fill();
    };

    // ─── Text sampling ───
    function sampleText() {
        particles = [];

        var w = canvas.width / dpr;
        var h = canvas.height / dpr;
        var scale = CONFIG.sampleScale;

        var off = document.createElement('canvas');
        var oc = off.getContext('2d');
        off.width = w * scale;
        off.height = h * scale;

        // Use bold Inter/system font — no Array Wide dependency
        var fontFamily = '"Inter", "Helvetica Neue", Arial, sans-serif';
        var fontWeight = 900;

        // Start with a large font, auto-shrink to fit 80% of canvas width
        var targetWidth = off.width * 0.85;
        var fontSize = Math.min(320, w * 0.28) * scale;

        oc.font = fontWeight + ' ' + fontSize + 'px ' + fontFamily;

        // Measure and adjust
        var measured = oc.measureText(CONFIG.text).width;
        if (measured > targetWidth) {
            fontSize = Math.floor(fontSize * (targetWidth / measured));
        }
        fontSize = Math.max(fontSize, 40 * scale);

        // Apply letter spacing manually for wide look
        var letterSpacing = fontSize * 0.15;

        oc.fillStyle = '#fff';
        oc.font = fontWeight + ' ' + fontSize + 'px ' + fontFamily;
        oc.textAlign = 'left';
        oc.textBaseline = 'middle';

        // Calculate total text width with letter spacing
        var letters = CONFIG.text.split('');
        var widths = [];
        var totalW = 0;
        for (var i = 0; i < letters.length; i++) {
            var lw = oc.measureText(letters[i]).width;
            widths.push(lw);
            totalW += lw;
        }
        totalW += letterSpacing * (letters.length - 1);

        // Re-check fit with spacing
        if (totalW > targetWidth) {
            var ratio = targetWidth / totalW;
            fontSize = Math.floor(fontSize * ratio);
            letterSpacing = fontSize * 0.12;
            oc.font = fontWeight + ' ' + fontSize + 'px ' + fontFamily;
            widths = [];
            totalW = 0;
            for (var ri = 0; ri < letters.length; ri++) {
                var rw = oc.measureText(letters[ri]).width;
                widths.push(rw);
                totalW += rw;
            }
            totalW += letterSpacing * (letters.length - 1);
        }

        // Draw text with stroke + multi-pass fill for maximum coverage (zero holes)
        var centerY = off.height / 2;

        // First: thick stroke to fill all edges
        oc.strokeStyle = '#fff';
        oc.lineWidth = scale * 4;
        oc.lineJoin = 'round';
        var baseCx = (off.width - totalW) / 2;
        var strokCx = baseCx;
        for (var si = 0; si < letters.length; si++) {
            oc.strokeText(letters[si], strokCx, centerY);
            strokCx += widths[si] + letterSpacing;
        }

        // Then: 9-pass fill on top
        var s2 = scale;
        var offsets = [
            [0, 0],
            [-s2, 0], [s2, 0], [0, -s2], [0, s2],
            [-s2, -s2], [s2, -s2], [-s2, s2], [s2, s2]
        ];

        for (var p = 0; p < offsets.length; p++) {
            var cx = baseCx + offsets[p][0];
            var cy = centerY + offsets[p][1];
            for (var j = 0; j < letters.length; j++) {
                oc.fillText(letters[j], cx, cy);
                cx += widths[j] + letterSpacing;
            }
        }

        // Sample pixels at grid intervals
        var img = oc.getImageData(0, 0, off.width, off.height);
        var data = img.data;
        var gap = CONFIG.dotGap * scale;

        for (var sy = 0; sy < off.height; sy += gap) {
            for (var sx = 0; sx < off.width; sx += gap) {
                var idx = (Math.round(sy) * off.width + Math.round(sx)) * 4;
                if (data[idx + 3] > CONFIG.alphaThreshold) {
                    particles.push(new Dot(sx / scale, sy / scale));
                }
            }
        }
    }

    // ─── Animation ───
    function animate() {
        if (!visible) { animId = null; return; }

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        ctx.scale(dpr, dpr);

        for (var i = 0, n = particles.length; i < n; i++) {
            particles[i].update();
            particles[i].draw();
        }

        ctx.restore();
        animId = requestAnimationFrame(animate);
    }

    function start() { if (!animId && visible) animId = requestAnimationFrame(animate); }
    function stop() { if (animId) { cancelAnimationFrame(animId); animId = null; } }

    // ─── Events ───
    function coords(e) {
        var r = canvas.getBoundingClientRect();
        var cx = e.touches ? e.touches[0].clientX : e.clientX;
        var cy = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: cx - r.left, y: cy - r.top };
    }

    function onMove(e) { var c = coords(e); mouse.x = c.x; mouse.y = c.y; mouse.active = true; }
    function onLeave() { mouse.active = false; mouse.x = -9999; mouse.y = -9999; }
    function onTouchMove(e) { e.preventDefault(); onMove(e); }

    // ─── Resize ───
    function resize() {
        if (!canvas) return;
        var w = canvas.parentElement.offsetWidth;
        var h = CONFIG.canvasHeight;
        if (w < 600) h = 180;
        else if (w < 900) h = 240;

        dpr = Math.min(window.devicePixelRatio || 1, 2);
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        sampleText();
    }

    // ─── Init ───
    function init() {
        var wrap = document.createElement('div');
        wrap.className = 'doji-particle-footer';
        wrap.setAttribute('aria-hidden', 'true');

        canvas = document.createElement('canvas');
        canvas.className = 'doji-particle-canvas';
        wrap.appendChild(canvas);
        ctx = canvas.getContext('2d');

        var footer = document.querySelector('footer');
        if (footer) footer.parentNode.insertBefore(wrap, footer.nextSibling);
        else document.body.appendChild(wrap);

        resize();

        canvas.addEventListener('mousemove', onMove, { passive: true });
        canvas.addEventListener('mouseleave', onLeave);
        canvas.addEventListener('touchstart', onMove, { passive: true });
        canvas.addEventListener('touchmove', onTouchMove, { passive: false });
        canvas.addEventListener('touchend', onLeave);

        var rt;
        window.addEventListener('resize', function () {
            clearTimeout(rt);
            rt = setTimeout(resize, 200);
        }, { passive: true });

        // Only animate when visible
        new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                visible = e.isIntersecting;
                visible ? start() : stop();
            });
        }, { threshold: 0.05 }).observe(wrap);
    }

    function boot() {
        // Wait for Inter font to be ready
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                setTimeout(init, 50);
            });
        } else {
            setTimeout(init, 500);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
