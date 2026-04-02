/**
 * Doji Funding — HIW Isometric Voxel Renderer
 *
 * Wintermute-style 3D dot grid animations for the How It Works cards.
 * Each card gets a unique pixel-square isometric shape that rotates on hover.
 */
(() => {
    'use strict';

    // ── Shape builders ────────────────────────────────────────────────────────
    // Each returns an array of [x, y, z] voxel coordinates, centred at origin.

    function buildCube() {
        // Hollow wireframe cube — "Choose a Plan" (package/product)
        const pts = [];
        const N = 4;
        for (let x = 0; x <= N; x++) {
            for (let y = 0; y <= N; y++) {
                for (let z = 0; z <= N; z++) {
                    const onFaces = [x === 0, x === N, y === 0, y === N, z === 0, z === N]
                        .filter(Boolean).length;
                    if (onFaces >= 2) pts.push([x - N / 2, y - N / 2, z - N / 2]);
                }
            }
        }
        return pts;
    }

    function buildStairs() {
        // 4-step ascending staircase — "Pass the Challenge" (progression)
        const pts = [];
        const S = 4;
        for (let s = 0; s < S; s++) {
            // Top surface of each step
            for (let z = 0; z < S; z++) {
                pts.push([s, s, z]);
            }
            // Front face of each step
            for (let y = 0; y <= s; y++) {
                pts.push([s, y, 0]);
                pts.push([s, y, S - 1]);
            }
        }
        return pts.map(([x, y, z]) => [x - (S - 1) / 2, -(y - (S - 1) / 2), z - (S - 1) / 2]);
    }

    function buildScanGrid() {
        // Flat 6×6 dot grid with two elevated scan rows — "Verify Identity" (document scan)
        const pts = [];
        for (let x = 0; x <= 5; x++) {
            for (let z = 0; z <= 5; z++) {
                pts.push([x, 0, z]);
            }
        }
        // Elevated scan lines
        for (let x = 1; x <= 4; x++) {
            pts.push([x, 1, 2]);
            pts.push([x, 1, 3]);
            pts.push([x, 2, 2]);
            pts.push([x, 2, 3]);
        }
        return pts.map(([x, y, z]) => [x - 2.5, y, z - 2.5]);
    }

    function buildBarChart() {
        // Rising bar chart — "Get Funded" (growth / profit)
        const heights = [1, 2, 3, 4, 5];
        const pts = [];
        const maxH = Math.max(...heights);
        heights.forEach((h, i) => {
            const bx = i * 1.4;
            for (let y = 0; y < h; y++) {
                pts.push([bx,     y, 0]);
                pts.push([bx,     y, 1]);
                pts.push([bx + 1, y, 0]);
                pts.push([bx + 1, y, 1]);
            }
            // Top cap
            pts.push([bx,     h, 0]);
            pts.push([bx,     h, 1]);
            pts.push([bx + 1, h, 0]);
            pts.push([bx + 1, h, 1]);
        });
        const totalW = (heights.length - 1) * 1.4 + 1;
        return pts.map(([x, y, z]) => [x - totalW / 2, y - maxH / 2, z - 0.5]);
    }

    // ── Card configs ──────────────────────────────────────────────────────────

    const CONFIGS = [
        { shape: buildCube(),      color: '#10B981', scale: 13, dotSize: 2.8, startAngle: 0.5  },
        { shape: buildStairs(),    color: '#34d399', scale: 13, dotSize: 2.8, startAngle: 0.3  },
        { shape: buildScanGrid(),  color: '#4a9eff', scale: 11, dotSize: 2.8, startAngle: 0.6  },
        { shape: buildBarChart(),  color: '#f59e0b', scale: 11, dotSize: 2.8, startAngle: 0.4  },
    ];

    // ── Isometric projection with Y-axis rotation ─────────────────────────────

    function project(x, y, z, angle, scale) {
        const rx =  x * Math.cos(angle) + z * Math.sin(angle);
        const rz = -x * Math.sin(angle) + z * Math.cos(angle);
        return {
            sx:    (rx - rz) * scale * 0.866,
            sy:    (rx + rz) * scale * 0.5 - y * scale,
            depth:  rx + rz - y,
        };
    }

    // ── Per-canvas renderer ───────────────────────────────────────────────────

    function createRenderer(canvas, cfg) {
        const dpr  = Math.min(window.devicePixelRatio || 1, 2);
        const SIZE = 120;
        canvas.style.width  = SIZE + 'px';
        canvas.style.height = SIZE + 'px';
        canvas.width  = SIZE * dpr;
        canvas.height = SIZE * dpr;

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);

        const cx = SIZE / 2;
        const cy = SIZE / 2;

        let angle         = cfg.startAngle;
        let opacity       = 0;
        let targetOpacity = 0;
        let hovered       = false;
        let raf           = null;

        function draw() {
            ctx.clearRect(0, 0, SIZE, SIZE);
            if (opacity < 0.005) return;

            const ds = cfg.dotSize;

            const pts = cfg.shape.map(([x, y, z]) => project(x, y, z, angle, cfg.scale));
            pts.sort((a, b) => a.depth - b.depth);

            ctx.globalAlpha = opacity;
            ctx.shadowColor = cfg.color;
            ctx.shadowBlur  = 10;
            ctx.fillStyle   = cfg.color;

            pts.forEach(({ sx, sy }) => {
                ctx.fillRect(cx + sx - ds / 2, cy + sy - ds / 2, ds, ds);
            });

            ctx.globalAlpha = 1;
            ctx.shadowBlur  = 0;
        }

        function tick() {
            let dirty = false;

            if (hovered) {
                angle += 0.014;
                dirty = true;
            }

            const dOp = (targetOpacity - opacity) * 0.09;
            if (Math.abs(dOp) > 0.002) {
                opacity += dOp;
                dirty = true;
            } else if (targetOpacity === 0 && opacity < 0.01) {
                opacity = 0;
            }

            if (dirty || opacity > 0.005) {
                draw();
                raf = requestAnimationFrame(tick);
            } else {
                raf = null;
            }
        }

        function ensure() { if (!raf) raf = requestAnimationFrame(tick); }

        return {
            enter() { hovered = true;  targetOpacity = 0.55; ensure(); },
            leave() { hovered = false; targetOpacity = 0; ensure(); },
        };
    }

    // ── Init ──────────────────────────────────────────────────────────────────

    function init() {
        const renderers = [];

        document.querySelectorAll('.hiw-card').forEach((card, i) => {
            if (i >= CONFIGS.length) return;

            const canvas = document.createElement('canvas');
            canvas.className = 'hiw-voxel';
            card.appendChild(canvas);

            const renderer = createRenderer(canvas, CONFIGS[i]);
            renderers.push({ card, renderer });

            card.addEventListener('mouseenter', () => {
                renderers.forEach(r => r.renderer.leave());
                renderer.enter();
            });
            card.addEventListener('mouseleave', () => renderer.leave());
        });

        // Single shared observer: mobile only (no hover available)
        const isMobile = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
        if (isMobile && typeof IntersectionObserver !== 'undefined') {
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const match = renderers.find(r => r.card === entry.target);
                    if (!match) return;
                    if (entry.isIntersecting) {
                        renderers.forEach(r => r.renderer.leave());
                        match.renderer.enter();
                    } else {
                        match.renderer.leave();
                    }
                });
            }, { threshold: 0.5 });

            renderers.forEach(r => io.observe(r.card));
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
