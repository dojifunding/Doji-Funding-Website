/**
 * Doji Funding — Particle Diamond (Brilliant Cut)
 *
 * Matches reference image:
 * - Wide flat table (~65% of girdle width)
 * - Very shallow crown (~25% of total height)
 * - Deep pavilion (~75% of total height) converging to bottom point
 * - 8-sided octagonal symmetry
 * - Clean kite facets on crown, triangular facets on pavilion
 */
(function () {
    'use strict';

    var canvas = document.getElementById('heroDiamond');
    if (!canvas) return;
    try {
        if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return;
    } catch (e) { return; }

    var scene, camera, renderer, gemGroup;
    var mouseX = 0, mouseY = 0, targetRotX = 0, targetRotY = 0;
    var width, height;
    var GREEN    = 0x10B981;
    var PER_EDGE = 26;

    var _mInv, _mVec;
    var MOUSE_R    = 2.2;
    var MOUSE_PUSH = 0.55;

    /* ── ring helper: n-sided polygon at height y, radius r, rotated rot ── */
    function ring(n, y, r, rot) {
        var pts = [];
        for (var i = 0; i < n; i++) {
            var a = rot + i * (Math.PI * 2 / n);
            pts.push([Math.cos(a) * r, y, Math.sin(a) * r]);
        }
        return pts;
    }

    function buildDiamond() {
        /* ── Proportions matching reference image ──
           Total height ≈ 4.2 units
           Crown:   0.9 units (top 21%)  — very shallow
           Girdle:  widest point
           Pavilion: 3.3 units (bottom 79%) — deep, pointed
        */
        var TABLE_Y  =  1.65;  /* top of crown           */
        var GIRDLE_Y =  0.75;  /* widest ring             */
        var CULET    = [0, -2.55, 0]; /* bottom point     */

        var TABLE_R  = 0.95;   /* wide table              */
        var GIRDLE_R = 1.50;   /* widest                  */

        /* Octagonal rings — table and girdle offset by 22.5° relative to each other */
        var ROT_T = Math.PI / 8;   /* 22.5° */
        var ROT_G = 0;

        var tbl = ring(8, TABLE_Y,  TABLE_R,  ROT_T);
        var grd = ring(8, GIRDLE_Y, GIRDLE_R, ROT_G);

        var edges = [];

        /* ── Table octagon (top flat face) ── */
        for (var i = 0; i < 8; i++)
            edges.push([tbl[i], tbl[(i + 1) % 8]]);

        /* ── Crown: each table corner → two adjacent girdle corners (kite facets) ── */
        for (var i = 0; i < 8; i++) {
            edges.push([tbl[i], grd[i]]);
            edges.push([tbl[i], grd[(i + 1) % 8]]);
        }

        /* ── Girdle ring ── */
        for (var i = 0; i < 8; i++)
            edges.push([grd[i], grd[(i + 1) % 8]]);

        /* ── Pavilion: each girdle corner → culet ── */
        for (var i = 0; i < 8; i++)
            edges.push([grd[i], CULET]);

        /* ── Pavilion cross-facet lines: alternate girdle corners connected
               (creates the triangular facets visible in reference) ── */
        for (var i = 0; i < 8; i += 2) {
            var mid = [
                (grd[i][0] + grd[(i + 2) % 8][0]) * 0.5,
                GIRDLE_Y - 0.85,
                (grd[i][2] + grd[(i + 2) % 8][2]) * 0.5,
            ];
            edges.push([grd[i],       mid]);
            edges.push([grd[(i+2)%8], mid]);
            edges.push([mid,          CULET]);
        }

        /* ── Build geometry ── */
        var totalDots = edges.length * PER_EDGE;
        var positions = new Float32Array(totalDots * 3);
        var phases    = new Float32Array(totalDots);
        var idx = 0;

        for (var e = 0; e < edges.length; e++) {
            var a = edges[e][0], b = edges[e][1];
            for (var k = 0; k < PER_EDGE; k++) {
                var t = (k + 0.5 + (Math.random() - 0.5) * 0.4) / PER_EDGE;
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

        gemGroup.add(new THREE.Points(geo, new THREE.PointsMaterial({
            color:           GREEN,
            size:            0.055,
            transparent:     true,
            opacity:         0.18,
            blending:        THREE.AdditiveBlending,
            depthWrite:      false,
            sizeAttenuation: true,
        })));
    }

    /* ── init ────────────────────────────────────────────── */
    function init() {
        width  = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;

        scene  = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        /* Slightly above centre so crown is visible, matching ref image angle */
        camera.position.set(0, 0.6, 5.5);
        camera.lookAt(0, -0.3, 0);

        _mInv = new THREE.Matrix4();
        _mVec = new THREE.Vector3();

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        gemGroup = new THREE.Group();
        scene.add(gemGroup);

        buildDiamond();

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

    /* ── breathing pulse + mouse distortion ─────────────── */
    function breathe(time) {
        var pts = null;
        for (var c = 0; c < gemGroup.children.length; c++) {
            if (gemGroup.children[c].isPoints) { pts = gemGroup.children[c]; break; }
        }
        if (!pts) return;

        var pos    = pts.geometry.getAttribute('position');
        var orig   = pts.geometry.userData.origPositions;
        var phases = pts.geometry.userData.phases;
        var n      = pos.count;

        _mVec.set(mouseX, -mouseY, 0.5).unproject(camera);
        _mInv.copy(gemGroup.matrixWorld).invert();
        _mVec.applyMatrix4(_mInv);
        var mx = _mVec.x, my = _mVec.y, mz = _mVec.z;

        for (var i = 0; i < n; i++) {
            var b  = 1 + Math.sin(time * 1.5 + phases[i]) * 0.020;
            var ox = orig[i * 3], oy = orig[i * 3 + 1], oz = orig[i * 3 + 2];
            var px = ox * b,      py = oy * b,           pz = oz * b;

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
        targetRotX += (mouseY * 0.10 - targetRotX) * 0.015;

        gemGroup.rotation.y = time * 0.04 + targetRotY;
        /* Tilt slightly so crown is visible from above — matches ref angle */
        gemGroup.rotation.x = 0.25 + targetRotX;

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
