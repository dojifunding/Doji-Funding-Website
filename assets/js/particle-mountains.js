/**
 * Doji Funding — Particle Mountain Range
 *
 * 3D mountain range made of green dots along ridge lines.
 * Multiple peaks at varying heights. Rotates gently following
 * the mouse with outward distortion near cursor.
 */
(function () {
    'use strict';

    var canvas = document.getElementById('heroMountains');
    if (!canvas) return;
    try {
        if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return;
    } catch (e) { return; }

    var scene, camera, renderer, mtnGroup;
    var mouseX = 0, mouseY = 0, targetRotX = 0, targetRotY = 0;
    var width, height;
    var GREEN = 0x10B981;
    var PER_EDGE = 36;   /* dots per ridge segment */

    var _mInv, _mVec;
    var MOUSE_R    = 2.2;
    var MOUSE_PUSH = 0.5;

    /* ── mountain profile — peaks defined as [x, y] in world space ── */
    var PEAKS = [
        [-4.0, -0.8],   /* far left foothills  */
        [-2.8,  0.3],   /* left shoulder       */
        [-1.8,  1.1],   /* left peak           */
        [-0.9, -0.2],   /* valley              */
        [ 0.0,  1.8],   /* main summit         */
        [ 0.8,  0.4],   /* right shoulder      */
        [ 1.6,  1.3],   /* right peak          */
        [ 2.6,  0.0],   /* right valley        */
        [ 3.5,  0.7],   /* far right foothill  */
        [ 4.2, -0.6],   /* baseline right      */
    ];
    var BASE_Y = -0.9;  /* base ground line y  */
    var DEPTH_LAYERS = [
        { z: 0,    scale: 1.0,  opacity: 0.85 },
        { z: -1.2, scale: 0.75, opacity: 0.45 },
        { z: -2.2, scale: 0.55, opacity: 0.22 },
    ];

    /* ── init ────────────────────────────────────────────── */
    function init() {
        width  = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;

        scene  = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        camera.position.set(0, 0.5, 6.5);
        camera.lookAt(0, 0.3, 0);

        _mInv = new THREE.Matrix4();
        _mVec = new THREE.Vector3();

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        mtnGroup = new THREE.Group();
        scene.add(mtnGroup);

        buildMountains();

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

    /* ── build ridge lines for all depth layers ─────────── */
    function buildLayer(layerDef) {
        var sx = layerDef.scale;
        var z  = layerDef.z;

        /* Ridge segments: peak-to-peak */
        var edges = [];
        for (var i = 0; i < PEAKS.length - 1; i++) {
            edges.push([
                [PEAKS[i][0] * sx,     PEAKS[i][1] * sx,     z],
                [PEAKS[i+1][0] * sx,   PEAKS[i+1][1] * sx,   z],
            ]);
        }
        /* Base line: connect first/last peak down to ground, then across */
        edges.push([
            [PEAKS[0][0] * sx,              BASE_Y * sx, z],
            [PEAKS[PEAKS.length-1][0] * sx, BASE_Y * sx, z],
        ]);
        /* Drop from first peak to base */
        edges.push([
            [PEAKS[0][0] * sx, PEAKS[0][1] * sx, z],
            [PEAKS[0][0] * sx, BASE_Y * sx,       z],
        ]);
        /* Drop from last peak to base */
        edges.push([
            [PEAKS[PEAKS.length-1][0] * sx, PEAKS[PEAKS.length-1][1] * sx, z],
            [PEAKS[PEAKS.length-1][0] * sx, BASE_Y * sx,                   z],
        ]);

        var totalDots = edges.length * PER_EDGE;
        var positions = new Float32Array(totalDots * 3);
        var phases    = new Float32Array(totalDots);
        var idx = 0;

        for (var e = 0; e < edges.length; e++) {
            var a = edges[e][0], b = edges[e][1];
            for (var k = 0; k < PER_EDGE; k++) {
                var t = (k + 0.5 + (Math.random() - 0.5) * 0.35) / PER_EDGE;
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

        var pts = new THREE.Points(geo, new THREE.PointsMaterial({
            color:           GREEN,
            size:            0.055,
            transparent:     true,
            opacity:         layerDef.opacity * 0.18,
            blending:        THREE.AdditiveBlending,
            depthWrite:      false,
            sizeAttenuation: true,
        }));
        pts.userData.baseOpacity = layerDef.opacity * 0.18;
        mtnGroup.add(pts);
    }

    function buildMountains() {
        for (var l = 0; l < DEPTH_LAYERS.length; l++) {
            buildLayer(DEPTH_LAYERS[l]);
        }
    }

    /* ── breathing pulse + mouse distortion ─────────────── */
    function breathe(time) {
        _mVec.set(mouseX, -mouseY, 0.5).unproject(camera);
        _mInv.copy(mtnGroup.matrixWorld).invert();
        _mVec.applyMatrix4(_mInv);
        var mx = _mVec.x, my = _mVec.y, mz = _mVec.z;

        for (var c = 0; c < mtnGroup.children.length; c++) {
            var pts = mtnGroup.children[c];
            if (!pts.isPoints) continue;

            var pos    = pts.geometry.getAttribute('position');
            var orig   = pts.geometry.userData.origPositions;
            var phases = pts.geometry.userData.phases;
            var n      = pos.count;

            for (var i = 0; i < n; i++) {
                var b  = 1 + Math.sin(time * 1.2 + phases[i]) * 0.018;
                var ox = orig[i * 3], oy = orig[i * 3 + 1], oz = orig[i * 3 + 2];
                var px = ox,          py = oy * b,           pz = oz;

                var ddx = px - mx, ddy = py - my, ddz = pz - mz;
                var md = Math.sqrt(ddx*ddx + ddy*ddy + ddz*ddz);
                if (md < MOUSE_R && md > 0.01) {
                    var mf = (1 - md / MOUSE_R);
                    mf = mf * mf * MOUSE_PUSH;
                    /* push outward from ridge (y-axis) */
                    py += mf * (oy > 0 ? 1 : -0.3);
                    px += mf * (ox / (Math.abs(ox) + 0.5)) * 0.3;
                }

                pos.setXYZ(i, px, py, pz);
            }
            pos.needsUpdate = true;
            pts.material.opacity = pts.userData.baseOpacity * (0.9 + Math.sin(time * 0.7) * 0.1);
        }
    }

    /* ── render loop ─────────────────────────────────────── */
    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;

        /* Gentle horizontal sway only — no full rotation (keeps silhouette readable) */
        targetRotY += (mouseX * 0.12 - targetRotY) * 0.012;
        targetRotX += (mouseY * 0.04 - targetRotX) * 0.012;

        mtnGroup.rotation.y = time * 0.015 + targetRotY;
        mtnGroup.rotation.x = targetRotX;

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
