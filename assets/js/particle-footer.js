/**
 * Doji Funding — Interactive Pixel Particle Footer
 * v1.0.0
 *
 * Renders "DOJI FUNDING" as pixel particles on a canvas.
 * Particles scatter on mouse/touch interaction and reassemble
 * when the pointer moves away. Works on desktop and mobile.
 */
(function () {
    'use strict';

    // ─── Configuration ───
    var CONFIG = {
        text: 'DOJI FUNDING',
        fontSizeBase: 120,           // Base font size for sampling text
        particleSize: 3,             // Pixel particle size
        particleGap: 4,              // Gap between sampled pixels (tighter = sharper letters)
        mouseRadius: 120,            // Interaction radius around cursor
        returnSpeed: 0.06,           // How fast particles return (0-1, lower = slower)
        friction: 0.88,              // Velocity damping (0-1, higher = less friction)
        pushForce: 8,                // How hard particles get pushed
        colorPrimary: '#10B981',     // Emerald green
        colorSecondary: '#34d399',   // Light emerald
        colorDim: '#064e3b',         // Dark emerald
        canvasHeight: 320,           // Canvas height in px
        bgColor: '#050607'           // Footer bg (slightly darker than page bg)
    };

    // ─── State ───
    var canvas, ctx;
    var particles = [];
    var mouse = { x: -9999, y: -9999, isActive: false };
    var animationId = null;
    var isVisible = false;
    var dpr = 1;

    // ─── Particle class ───
    function Particle(x, y, color) {
        this.originX = x;
        this.originY = y;
        this.x = x;
        this.y = y;
        this.vx = 0;
        this.vy = 0;
        this.color = color;
        this.size = CONFIG.particleSize;
    }

    Particle.prototype.update = function () {
        // Mouse repulsion
        var dx = this.x - mouse.x;
        var dy = this.y - mouse.y;
        var dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < CONFIG.mouseRadius && mouse.isActive) {
            var force = (CONFIG.mouseRadius - dist) / CONFIG.mouseRadius;
            var angle = Math.atan2(dy, dx);
            this.vx += Math.cos(angle) * force * CONFIG.pushForce;
            this.vy += Math.sin(angle) * force * CONFIG.pushForce;
        }

        // Return to origin
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
        // Color based on distance from origin (scattered = brighter)
        var dx = this.x - this.originX;
        var dy = this.y - this.originY;
        var dist = Math.sqrt(dx * dx + dy * dy);

        if (dist > 20) {
            ctx.fillStyle = CONFIG.colorSecondary;
        } else if (dist > 5) {
            ctx.fillStyle = CONFIG.colorPrimary;
        } else {
            ctx.fillStyle = this.color;
        }

        ctx.fillRect(this.x - this.size / 2, this.y - this.size / 2, this.size, this.size);
    };

    // ─── Text sampling: render text at 2x then downsample for crisp edges ───
    function sampleText() {
        particles = [];

        var w = canvas.width / dpr;
        var h = canvas.height / dpr;

        // Render text at 2x resolution for sharper sampling
        var scale = 2;
        var offscreen = document.createElement('canvas');
        var offCtx = offscreen.getContext('2d');
        offscreen.width = w * scale;
        offscreen.height = h * scale;

        // Determine font size based on canvas width
        var fontSize = Math.min(CONFIG.fontSizeBase, w / (CONFIG.text.length * 0.55));
        fontSize = Math.max(fontSize, 28);

        offCtx.fillStyle = '#ffffff';
        offCtx.font = '700 ' + (fontSize * scale) + 'px "Array Wide", Array, Inter, sans-serif';
        offCtx.textAlign = 'center';
        offCtx.textBaseline = 'middle';
        offCtx.fillText(CONFIG.text, offscreen.width / 2, offscreen.height / 2);

        // Sample pixels from high-res canvas, map back to display coords
        var imageData = offCtx.getImageData(0, 0, offscreen.width, offscreen.height);
        var data = imageData.data;
        var sampleGap = CONFIG.particleGap * scale;

        for (var y = 0; y < offscreen.height; y += sampleGap) {
            for (var x = 0; x < offscreen.width; x += sampleGap) {
                var index = (y * offscreen.width + x) * 4;
                var alpha = data[index + 3];

                if (alpha > 80) {
                    // Map back to display coordinates
                    var displayX = x / scale;
                    var displayY = y / scale;

                    // Color variation based on alpha for edge softness
                    var color;
                    if (alpha > 200) {
                        color = CONFIG.colorPrimary;
                    } else if (alpha > 140) {
                        color = CONFIG.colorSecondary;
                    } else {
                        color = CONFIG.colorDim;
                    }

                    var p = new Particle(displayX, displayY, color);
                    // Edge particles slightly smaller for softer look
                    if (alpha < 160) {
                        p.size = CONFIG.particleSize * 0.7;
                    }
                    // Start scattered for entrance animation
                    p.x = displayX + (Math.random() - 0.5) * 300;
                    p.y = displayY + (Math.random() - 0.5) * 200;
                    particles.push(p);
                }
            }
        }
    }

    // ─── Animation loop ───
    function animate() {
        if (!isVisible) {
            animationId = null;
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Scale for DPR
        ctx.save();
        ctx.scale(dpr, dpr);

        for (var i = 0; i < particles.length; i++) {
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

        // Insert before the closing </body> or after .footer
        var siteFooter = document.querySelector('.footer');
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

    // ─── Boot ───
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
