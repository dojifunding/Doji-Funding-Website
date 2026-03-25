/**
 * Doji Funding — Interactive Pixel Particle Footer
 * v2.0.0
 *
 * Renders "DOJI FUNDING" as pixel particles on a canvas using Array Wide font.
 * Dense sampling grid + low alpha threshold = no holes in letters.
 * Particles scatter on mouse/touch interaction and reassemble
 * when the pointer moves away. Works on desktop and mobile.
 */
(function () {
    'use strict';

    // ─── Configuration ───
    var CONFIG = {
        text: 'DOJI FUNDING',
        fontSizeBase: 140,           // Large base for good sampling area
        particleSize: 3,              // Circle radius — with glow halo covers gap perfectly
        particleGap: 2.8,            // Slightly tighter grid for full coverage
        mouseRadius: 130,            // Interaction radius around cursor
        returnSpeed: 0.12,           // Fast return — particles settle quickly
        friction: 0.86,              // Velocity damping
        pushForce: 9,                // Push strength
        colorCore: '#10B981',        // Emerald green — solid core
        colorBright: '#34d399',      // Light emerald — scattered state
        colorEdge: '#0d9668',        // Mid emerald — edges
        colorDim: '#065f46',         // Dark emerald — outermost antialiased edges
        colorGlow: '#6ee7b7',        // Glow for far-scattered particles
        canvasHeight: 340,           // Canvas height
        bgColor: '#050607',          // Footer bg
        alphaThreshold: 25,          // Low — captures thin strokes & anti-aliased edges
        sampleScale: 5,              // 5x oversampling for maximum detail
        letterSpacingFactor: 0.08,   // Generous letter spacing
        fontWeight: 700              // Bold weight for Array Wide
    };

    // ─── State ───
    var canvas, ctx;
    var particles = [];
    var mouse = { x: -9999, y: -9999, isActive: false };
    var animationId = null;
    var isVisible = false;
    var dpr = 1;

    // ─── Particle class ───
    function Particle(x, y, color, size) {
        this.originX = x;
        this.originY = y;
        this.x = x;
        this.y = y;
        this.vx = 0;
        this.vy = 0;
        this.color = color;
        this.size = size;
    }

    Particle.prototype.update = function () {
        // Mouse repulsion
        var dx = this.x - mouse.x;
        var dy = this.y - mouse.y;
        var distSq = dx * dx + dy * dy;
        var radiusSq = CONFIG.mouseRadius * CONFIG.mouseRadius;

        if (distSq < radiusSq && mouse.isActive) {
            var dist = Math.sqrt(distSq);
            var force = (CONFIG.mouseRadius - dist) / CONFIG.mouseRadius;
            var angle = Math.atan2(dy, dx);
            this.vx += Math.cos(angle) * force * CONFIG.pushForce;
            this.vy += Math.sin(angle) * force * CONFIG.pushForce;
        }

        // Return to origin (spring)
        var homeX = this.originX - this.x;
        var homeY = this.originY - this.y;
        this.vx += homeX * CONFIG.returnSpeed;
        this.vy += homeY * CONFIG.returnSpeed;

        // Apply friction
        this.vx *= CONFIG.friction;
        this.vy *= CONFIG.friction;

        // Move
        this.x += this.vx;
        this.y += this.vy;
    };

    Particle.prototype.draw = function () {
        // Dynamic color based on distance from origin
        var dx = this.x - this.originX;
        var dy = this.y - this.originY;
        var distSq = dx * dx + dy * dy;

        var color;
        if (distSq > 900) {
            color = CONFIG.colorGlow;
        } else if (distSq > 400) {
            color = CONFIG.colorBright;
        } else if (distSq > 25) {
            color = CONFIG.colorCore;
        } else {
            color = this.color;
        }

        // Draw as circle with soft glow — fills gaps between particles
        var drawX = distSq < 1 ? this.originX : this.x;
        var drawY = distSq < 1 ? this.originY : this.y;
        var r = this.size;

        // Core circle
        ctx.beginPath();
        ctx.arc(drawX, drawY, r, 0, 6.2832);
        ctx.fillStyle = color;
        ctx.fill();

        // Soft glow halo when settled (fills remaining micro-gaps between circles)
        if (distSq < 4) {
            ctx.beginPath();
            ctx.arc(drawX, drawY, r * 1.6, 0, 6.2832);
            ctx.fillStyle = 'rgba(16,185,129,0.2)';
            ctx.fill();
        }
    };

    // ─── Text sampling: high-res offscreen render + dense pixel grid ───
    function sampleText() {
        particles = [];

        var w = canvas.width / dpr;
        var h = canvas.height / dpr;

        var scale = CONFIG.sampleScale;
        var offscreen = document.createElement('canvas');
        var offCtx = offscreen.getContext('2d');
        offscreen.width = w * scale;
        offscreen.height = h * scale;

        // Enable best quality rendering
        offCtx.imageSmoothingEnabled = true;
        offCtx.imageSmoothingQuality = 'high';

        // Determine font size — scale with canvas width
        var fontSize = Math.min(CONFIG.fontSizeBase, w / (CONFIG.text.length * 0.52));
        fontSize = Math.max(fontSize, 32);

        var scaledFontSize = fontSize * scale;

        // Render text with Array Wide Bold
        offCtx.fillStyle = '#ffffff';
        offCtx.font = CONFIG.fontWeight + ' ' + scaledFontSize + 'px "Array Wide", "Array", sans-serif';
        offCtx.textAlign = 'center';
        offCtx.textBaseline = 'middle';

        // Multi-pass rendering: draw text multiple times for thicker strokes
        // This fills in thin parts of Array Wide that would otherwise create holes
        var centerX = offscreen.width / 2;
        var centerY = offscreen.height / 2;

        // Multi-pass: draw text at sub-pixel offsets to thicken every stroke
        // This ensures Array Wide's thin strokes become solid particle walls
        var thickenOffsets = [
            [0, 0],
            [-0.6, 0], [0.6, 0], [0, -0.6], [0, 0.6],
            [-0.3, -0.3], [0.3, -0.3], [-0.3, 0.3], [0.3, 0.3]
        ];

        for (var ti = 0; ti < thickenOffsets.length; ti++) {
            var ox = thickenOffsets[ti][0] * scale;
            var oy = thickenOffsets[ti][1] * scale;
            drawTextWithSpacing(offCtx, offscreen.width, centerY + oy, scaledFontSize, ox);
        }

        // Sample pixels from high-res canvas
        var imageData = offCtx.getImageData(0, 0, offscreen.width, offscreen.height);
        var data = imageData.data;
        var sampleGap = CONFIG.particleGap * scale;
        var halfGap = sampleGap * 0.5;

        // TWO-PASS brick-pattern sampling:
        // Pass 1: regular grid
        // Pass 2: offset by half-gap in X and Y (fills the center of each grid cell)
        // This eliminates visible grid pattern completely.
        var passes = [
            { offX: 0, offY: 0 },
            { offX: halfGap, offY: halfGap }
        ];

        for (var pi = 0; pi < passes.length; pi++) {
            var passOffX = passes[pi].offX;
            var passOffY = passes[pi].offY;

            for (var y = passOffY; y < offscreen.height; y += sampleGap) {
                for (var x = passOffX; x < offscreen.width; x += sampleGap) {
                    var ix = Math.round(x);
                    var iy = Math.round(y);
                    if (ix < 0 || ix >= offscreen.width || iy < 0 || iy >= offscreen.height) continue;

                    var index = (iy * offscreen.width + ix) * 4;
                    var alpha = data[index + 3];

                    if (alpha > CONFIG.alphaThreshold) {
                        var displayX = x / scale;
                        var displayY = y / scale;

                        // Color by alpha density — uniform size for solid fill
                        var color;
                        if (alpha > 200) {
                            color = CONFIG.colorCore;
                        } else if (alpha > 120) {
                            color = CONFIG.colorEdge;
                        } else {
                            color = CONFIG.colorDim;
                        }

                        var p = new Particle(displayX, displayY, color, CONFIG.particleSize);

                        // Entrance animation: start scattered (moderate range for quick convergence)
                        p.x = displayX + (Math.random() - 0.5) * 200;
                        p.y = displayY + (Math.random() - 0.5) * 150;

                        particles.push(p);
                    }
                }
            }
        }
    }

    // ─── Draw text with letter spacing ───
    function drawTextWithSpacing(offCtx, canvasWidth, centerY, scaledFontSize, offsetX) {
        var letters = CONFIG.text.split('');
        var spacing = scaledFontSize * CONFIG.letterSpacingFactor;

        // Measure total width
        var totalWidth = 0;
        var letterWidths = [];
        for (var i = 0; i < letters.length; i++) {
            var lw = offCtx.measureText(letters[i]).width;
            letterWidths.push(lw);
            totalWidth += lw;
        }
        totalWidth += spacing * (letters.length - 1);

        var startX = (canvasWidth - totalWidth) / 2 + offsetX;
        var curX = startX;

        for (var j = 0; j < letters.length; j++) {
            offCtx.fillText(letters[j], curX + letterWidths[j] / 2, centerY);
            curX += letterWidths[j] + spacing;
        }
    }

    // ─── Animation loop ───
    function animate() {
        if (!isVisible) {
            animationId = null;
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.save();
        ctx.scale(dpr, dpr);

        var len = particles.length;
        for (var i = 0; i < len; i++) {
            particles[i].update();
            particles[i].draw();
        }

        ctx.restore();
        animationId = requestAnimationFrame(animate);
    }

    function startAnimation() {
        if (!animationId && isVisible) {
            animationId = requestAnimationFrame(animate);
        }
    }

    function stopAnimation() {
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
    }

    // ─── Mouse / Touch Events ───
    function getCanvasCoords(e) {
        var rect = canvas.getBoundingClientRect();
        var clientX, clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    function onPointerMove(e) {
        var coords = getCanvasCoords(e);
        mouse.x = coords.x;
        mouse.y = coords.y;
        mouse.isActive = true;
    }

    function onPointerLeave() {
        mouse.isActive = false;
        mouse.x = -9999;
        mouse.y = -9999;
    }

    function onTouchStart(e) {
        onPointerMove(e);
    }

    function onTouchMove(e) {
        e.preventDefault();
        onPointerMove(e);
    }

    function onTouchEnd() {
        onPointerLeave();
    }

    // ─── Resize handler ───
    function resize() {
        if (!canvas) return;

        var container = canvas.parentElement;
        var w = container.offsetWidth;
        var h = CONFIG.canvasHeight;

        // On mobile, reduce height slightly
        if (w < 600) {
            h = 220;
        } else if (w < 900) {
            h = 280;
        }

        dpr = window.devicePixelRatio || 1;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';

        sampleText();
    }

    // ─── Visibility observer ───
    function initVisibility() {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                isVisible = entry.isIntersecting;
                if (isVisible) {
                    startAnimation();
                } else {
                    stopAnimation();
                }
            });
        }, { threshold: 0.05 });

        observer.observe(canvas.parentElement);
    }

    // ─── Build DOM ───
    function init() {
        // Create footer section
        var footer = document.createElement('div');
        footer.className = 'doji-particle-footer';
        footer.setAttribute('aria-hidden', 'true');

        canvas = document.createElement('canvas');
        canvas.className = 'doji-particle-canvas';
        footer.appendChild(canvas);

        ctx = canvas.getContext('2d');

        // Insert after <footer> or at end of body
        var siteFooter = document.querySelector('footer');
        if (siteFooter) {
            siteFooter.parentNode.insertBefore(footer, siteFooter.nextSibling);
        } else {
            document.body.appendChild(footer);
        }

        // Setup
        resize();

        // Events
        canvas.addEventListener('mousemove', onPointerMove, { passive: true });
        canvas.addEventListener('mouseleave', onPointerLeave);
        canvas.addEventListener('touchstart', onTouchStart, { passive: true });
        canvas.addEventListener('touchmove', onTouchMove, { passive: false });
        canvas.addEventListener('touchend', onTouchEnd);

        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resize, 200);
        }, { passive: true });

        // Visibility-based animation
        initVisibility();
    }

    // ─── Boot: wait for Array Wide Bold font to load ───
    function boot() {
        if (document.fonts && document.fonts.load) {
            // Load the exact weight we need
            document.fonts.load('700 48px "Array Wide"').then(function () {
                // Small delay to ensure font metrics are stable
                setTimeout(init, 50);
            }).catch(function () {
                // Fallback if font fails — still render with whatever is available
                setTimeout(init, 200);
            });
        } else {
            // No font loading API — wait for fonts
            setTimeout(init, 500);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

})();
