/**
 * Doji Funding — Interactive Dot-Matrix Footer
 * v3.0.0
 *
 * Renders "DOJI FUNDING" as large 3D sphere dots on a canvas.
 * Inspired by Cotool-style dot-matrix footer.
 * Few particles, big dots, clean look, low CPU usage.
 * Dots scatter on mouse/touch and reassemble smoothly.
 */
(function () {
    'use strict';

    // ─── Configuration ───
    var CONFIG = {
        text: 'DOJI FUNDING',
        fontSizeBase: 120,           // Fits "DOJI FUNDING" within any desktop viewport
        dotRadius: 6,                // Big visible dots (like the reference)
        dotGap: 14,                  // Large gap = fewer dots = great performance
        mouseRadius: 150,            // Interaction radius
        returnSpeed: 0.08,           // Smooth spring return
        friction: 0.85,              // Velocity damping
        pushForce: 12,               // Strong push for satisfying interaction
        canvasHeight: 360,           // Canvas height desktop
        alphaThreshold: 80,          // Only solid parts of the font
        sampleScale: 2,              // 2x is enough with large gaps
        letterSpacingFactor: 0.06,   // Letter spacing
        fontWeight: 700              // Bold Array Wide
    };

    // ─── Precomputed colors ───
    // Emerald 3D sphere: highlight → core → shadow
    var COLOR_HIGHLIGHT = '#6ee7b7';  // Top-left highlight
    var COLOR_CORE = '#10B981';       // Main fill
    var COLOR_SHADOW = '#065f46';     // Bottom-right shadow
    var COLOR_SCATTER = '#34d399';    // When scattered

    // ─── State ───
    var canvas, ctx;
    var particles = [];
    var mouse = { x: -9999, y: -9999, active: false };
    var animId = null;
    var visible = false;
    var dpr = 1;
    var sphereGradientCache = null;

    // ─── Particle ───
    function Dot(x, y) {
        this.ox = x;
        this.oy = y;
        this.x = x;
        this.y = y;
        this.vx = 0;
        this.vy = 0;
    }

    Dot.prototype.update = function () {
        var dx = this.x - mouse.x;
        var dy = this.y - mouse.y;
        var d2 = dx * dx + dy * dy;
        var r2 = CONFIG.mouseRadius * CONFIG.mouseRadius;

        if (d2 < r2 && mouse.active) {
            var d = Math.sqrt(d2);
            var f = (CONFIG.mouseRadius - d) / CONFIG.mouseRadius;
            var a = Math.atan2(dy, dx);
            this.vx += Math.cos(a) * f * CONFIG.pushForce;
            this.vy += Math.sin(a) * f * CONFIG.pushForce;
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
        var d2 = dx * dx + dy * dy;
        var r = CONFIG.dotRadius;
        var px = this.x;
        var py = this.y;

        // Snap when at rest
        if (d2 < 0.5) {
            px = this.ox;
            py = this.oy;
        }

        // 3D sphere gradient — cached for performance
        var grad = ctx.createRadialGradient(
            px - r * 0.3, py - r * 0.3, r * 0.1,
            px, py, r
        );

        if (d2 > 400) {
            // Far scattered — bright
            grad.addColorStop(0, '#a7f3d0');
            grad.addColorStop(0.5, COLOR_SCATTER);
            grad.addColorStop(1, COLOR_SHADOW);
        } else {
            // Normal / at rest — emerald 3D sphere
            grad.addColorStop(0, COLOR_HIGHLIGHT);
            grad.addColorStop(0.4, COLOR_CORE);
            grad.addColorStop(1, COLOR_SHADOW);
        }

        ctx.beginPath();
        ctx.arc(px, py, r, 0, 6.2832);
        ctx.fillStyle = grad;
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

        // Auto-fit: start with base size, measure actual text width, shrink to fit 85% of canvas
        var targetWidth = off.width * 0.85;
        var fs = Math.min(CONFIG.fontSizeBase, w * 0.08); // Initial guess
        var sfs = fs * scale;

        oc.font = CONFIG.fontWeight + ' ' + sfs + 'px "Array Wide", "Array", sans-serif';

        // Measure actual width with letter spacing
        var letters = CONFIG.text.split('');
        var sp = sfs * CONFIG.letterSpacingFactor;
        var measuredWidth = 0;
        for (var mi = 0; mi < letters.length; mi++) {
            measuredWidth += oc.measureText(letters[mi]).width;
        }
        measuredWidth += sp * (letters.length - 1);

        // Scale font to fit target width
        if (measuredWidth > targetWidth) {
            var ratio = targetWidth / measuredWidth;
            sfs = Math.floor(sfs * ratio);
            fs = sfs / scale;
        }
        sfs = Math.max(sfs, 20 * scale);

        oc.fillStyle = '#fff';
        oc.font = CONFIG.fontWeight + ' ' + sfs + 'px "Array Wide", "Array", sans-serif';
        oc.textAlign = 'center';
        oc.textBaseline = 'middle';

        // Thicken strokes: 5-pass rendering
        var centerY = off.height / 2;
        var offsets = [[0,0], [-1,0], [1,0], [0,-1], [0,1]];
        for (var t = 0; t < offsets.length; t++) {
            drawSpacedText(oc, off.width, centerY + offsets[t][1] * scale, sfs, offsets[t][0] * scale);
        }

        // Sample
        var img = oc.getImageData(0, 0, off.width, off.height);
        var data = img.data;
        var gap = CONFIG.dotGap * scale;

        for (var y = 0; y < off.height; y += gap) {
            for (var x = 0; x < off.width; x += gap) {
                var idx = (Math.round(y) * off.width + Math.round(x)) * 4;
                if (data[idx + 3] > CONFIG.alphaThreshold) {
                    var dispX = x / scale;
                    var dispY = y / scale;
                    var dot = new Dot(dispX, dispY);
                    // Entrance scatter
                    dot.x = dispX + (Math.random() - 0.5) * 300;
                    dot.y = dispY + (Math.random() - 0.5) * 200;
                    particles.push(dot);
                }
            }
        }
    }

    function drawSpacedText(oc, cw, cy, sfs, ox) {
        var letters = CONFIG.text.split('');
        var sp = sfs * CONFIG.letterSpacingFactor;
        var widths = [];
        var total = 0;
        for (var i = 0; i < letters.length; i++) {
            var lw = oc.measureText(letters[i]).width;
            widths.push(lw);
            total += lw;
        }
        total += sp * (letters.length - 1);
        var cx = (cw - total) / 2 + ox;
        for (var j = 0; j < letters.length; j++) {
            oc.fillText(letters[j], cx + widths[j] / 2, cy);
            cx += widths[j] + sp;
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
        if (w < 600) h = 200;
        else if (w < 900) h = 260;

        dpr = window.devicePixelRatio || 1;
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

        // Visibility observer — only animate when in viewport
        new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                visible = e.isIntersecting;
                visible ? start() : stop();
            });
        }, { threshold: 0.05 }).observe(wrap);
    }

    function boot() {
        if (document.fonts && document.fonts.load) {
            document.fonts.load('700 48px "Array Wide"').then(function () {
                setTimeout(init, 50);
            }).catch(function () {
                setTimeout(init, 200);
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
