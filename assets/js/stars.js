/**
 * Doji Funding — Full-page Stars Background
 * Shows on alternating sections for visual rhythm
 */
(function() {
    'use strict';
    var canvas = document.createElement('canvas');
    canvas.className = 'stars-bg';
    canvas.setAttribute('aria-hidden', 'true');
    document.body.insertBefore(canvas, document.body.firstChild);

    var ctx = canvas.getContext('2d');
    var stars = [], greenParts = [];
    var w, h;

    function resize() {
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w;
        canvas.height = h;
    }

    function createStars() {
        stars = []; greenParts = [];
        var starCount = Math.floor((w * h) / 3000);
        var greenCount = Math.floor(starCount / 5);

        for (var i = 0; i < starCount; i++) {
            stars.push({
                x: Math.random() * w, y: Math.random() * h,
                r: 0.3 + Math.random() * 0.8,
                baseAlpha: 0.15 + Math.random() * 0.45,
                speed: 0.5 + Math.random() * 2,
                phase: Math.random() * Math.PI * 2
            });
        }
        for (var j = 0; j < greenCount; j++) {
            greenParts.push({
                x: Math.random() * w, y: Math.random() * h,
                r: 0.4 + Math.random() * 1.2,
                baseAlpha: 0.06 + Math.random() * 0.12,
                dx: (Math.random() - 0.5) * 0.12,
                dy: (Math.random() - 0.5) * 0.08,
                phase: Math.random() * Math.PI * 2
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, w, h);
        var time = Date.now() * 0.001;

        for (var i = 0; i < stars.length; i++) {
            var s = stars[i];
            var alpha = s.baseAlpha + Math.sin(time * s.speed + s.phase) * 0.18;
            if (alpha < 0.05) alpha = 0.05;
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.r, 0, 6.2832);
            ctx.fillStyle = 'rgba(255,255,255,' + alpha.toFixed(3) + ')';
            ctx.fill();
        }
        for (var j = 0; j < greenParts.length; j++) {
            var p = greenParts[j];
            p.x += p.dx; p.y += p.dy;
            if (p.x < -5) p.x = w + 5;
            if (p.x > w + 5) p.x = -5;
            if (p.y < -5) p.y = h + 5;
            if (p.y > h + 5) p.y = -5;
            var a = p.baseAlpha + Math.sin(time * 0.7 + p.phase) * 0.05;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, 6.2832);
            ctx.fillStyle = 'rgba(16,185,129,' + a.toFixed(3) + ')';
            ctx.fill();
        }
        requestAnimationFrame(draw);
    }

    resize();
    createStars();
    draw();
    window.addEventListener('resize', function() { resize(); createStars(); });
})();
