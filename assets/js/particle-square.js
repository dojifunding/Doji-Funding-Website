/**
 * Doji Funding — Particle Cube
 *
 * 3D cube surface of green dots. Rotates gently following
 * the mouse. Subtle breathing pulse on each dot.
 * Mirrors globe.js structure — no repulsion (dot-grid handles that).
 */
(function () {
    'use strict';

    var canvas = document.getElementById('heroSquare');
    if (!canvas) return;
    try {
        if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return;
    } catch (e) { return; }

    var scene, camera, renderer, cubeGroup;
    var mouseX = 0, mouseY = 0, targetRotX = 0, targetRotY = 0;
    var width, height;
    var HALF      = 1.5;        /* half-size of cube side          */
    var GREEN     = 0x10B981;
    var PER_EDGE  = 28;         /* dots per edge (12 edges)        */

    /* ── init ────────────────────────────────────────────── */
    function init() {
        width  = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;

        scene  = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        camera.position.z = 5.5;

        camera.position.x = 0;

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        _mInv = new THREE.Matrix4();
        _mVec = new THREE.Vector3();

        cubeGroup = new THREE.Group();
        scene.add(cubeGroup);

        buildCube();

        document.addEventListener('mousemove', function (e) {
            mouseX = (e.clientX / window.innerWidth)  * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;
        }, { passive: true });

        window.addEventListener('resize', function () {
            width  = canvas.parentElement.offsetWidth;
            height = canvas.parentElement.offsetHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        }, { passive: true });

        animate();
    }

    /* ── cube edges — 12 edges, dots evenly spaced along each ── */
    function buildCube() {
        var H = HALF;
        /* 12 edges defined as [start, end] pairs */
        var edges = [
            /* bottom face */
            [[-H,-H,-H],[H,-H,-H]], [[H,-H,-H],[H,-H,H]],
            [[H,-H,H],[-H,-H,H]],   [[-H,-H,H],[-H,-H,-H]],
            /* top face */
            [[-H,H,-H],[H,H,-H]],   [[H,H,-H],[H,H,H]],
            [[H,H,H],[-H,H,H]],     [[-H,H,H],[-H,H,-H]],
            /* vertical pillars */
            [[-H,-H,-H],[-H,H,-H]], [[H,-H,-H],[H,H,-H]],
            [[H,-H,H],[H,H,H]],     [[-H,-H,H],[-H,H,H]],
        ];

        var totalDots = edges.length * PER_EDGE;
        var positions = new Float32Array(totalDots * 3);
        var phases    = new Float32Array(totalDots);
        var idx = 0;

        for (var e = 0; e < edges.length; e++) {
            var a = edges[e][0], b = edges[e][1];
            for (var i = 0; i < PER_EDGE; i++) {
                /* evenly spaced t with slight jitter, avoid endpoints (already covered by adjacent edge) */
                var t = (i + 0.5 + (Math.random() - 0.5) * 0.4) / PER_EDGE;
                positions[idx * 3]     = a[0] + (b[0] - a[0]) * t;
                positions[idx * 3 + 1] = a[1] + (b[1] - a[1]) * t;
                positions[idx * 3 + 2] = a[2] + (b[2] - a[2]) * t;
                phases[idx] = Math.random() * Math.PI * 2;
                idx++;
            }
        }

        var geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geo.userData.phases        = phases;
        geo.userData.origPositions = positions.slice();

        cubeGroup.add(new THREE.Points(geo, new THREE.PointsMaterial({
            color:           GREEN,
            size:            0.065,
            transparent:     true,
            opacity:         0.18,
            blending:        THREE.AdditiveBlending,
            depthWrite:      false,
            sizeAttenuation: true,
        })));
    }

    /* ── breathing pulse + mouse distortion ─────────────── */
    var MOUSE_R    = 1.8;
    var MOUSE_PUSH = 0.55;
    var _mInv, _mVec;

    function breathe(time) {
        var pts = null;
        for (var c = 0; c < cubeGroup.children.length; c++) {
            if (cubeGroup.children[c].isPoints) { pts = cubeGroup.children[c]; break; }
        }
        if (!pts) return;

        var pos    = pts.geometry.getAttribute('position');
        var orig   = pts.geometry.userData.origPositions;
        var phases = pts.geometry.userData.phases;
        var n      = pos.count;

        /* transform mouse NDC → object space so distortion follows rotation */
        _mVec.set(mouseX, -mouseY, 0.5).unproject(camera);
        _mInv.copy(cubeGroup.matrixWorld).invert();
        _mVec.applyMatrix4(_mInv);
        var mx = _mVec.x, my = _mVec.y, mz = _mVec.z;

        for (var i = 0; i < n; i++) {
            var b  = 1 + Math.sin(time * 1.5 + phases[i]) * 0.025;
            var ox = orig[i * 3],     oy = orig[i * 3 + 1], oz = orig[i * 3 + 2];
            var px = ox * b,          py = oy * b,           pz = oz * b;

            /* distort outward from cube centre when near cursor */
            var ddx = px - mx, ddy = py - my, ddz = pz - mz;
            var md = Math.sqrt(ddx*ddx + ddy*ddy + ddz*ddz);
            if (md < MOUSE_R && md > 0.01) {
                var mf = (1 - md / MOUSE_R);
                mf = mf * mf * MOUSE_PUSH;
                var len = Math.sqrt(ox*ox + oy*oy + oz*oz) || 1;
                px += (ox / len) * mf;
                py += (oy / len) * mf;
                pz += (oz / len) * mf;
            }

            pos.setXYZ(i, px, py, pz);
        }
        pos.needsUpdate = true;
        pts.material.opacity = 0.72 + Math.sin(time * 0.8) * 0.15;
    }

    /* ── render loop ─────────────────────────────────────── */
    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;

        targetRotY += (mouseX * 0.25 - targetRotY) * 0.015;
        targetRotX += (mouseY * 0.12 - targetRotX) * 0.015;

        cubeGroup.rotation.y = time * 0.04 + targetRotY;
        cubeGroup.rotation.x = 0.20 + targetRotX;

        breathe(time);
        renderer.render(scene, camera);
    }

    /* ── load Three.js on demand ─────────────────────────── */
    function loadThree() {
        if (typeof THREE !== 'undefined') { init(); return; }
        var s    = document.createElement('script');
        s.src    = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
        s.onload = init;
        s.onerror = function () { canvas.style.display = 'none'; };
        document.head.appendChild(s);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', loadThree);
    else loadThree();
}());
