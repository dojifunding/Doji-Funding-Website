/**
 * Doji Funding — Liquid Wave Surface Animation
 * WebGL mesh wave with mouse interaction
 */
(function() {
    'use strict';
    var canvas = document.getElementById('waveCanvas');
    if (!canvas) return;
    try { if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return; } catch(e) { return; }

    var scene, camera, renderer, waveMesh, waveGeo, starsGroup;
    var mouseX = 0, mouseY = 0, mouseWorld = { x: 0, z: 0 };
    var width, height;
    var GREEN = 0x10B981;
    var COLS = 80, ROWS = 50;
    var SEP = 0.12;

    function init() {
        width = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(50, width / height, 0.1, 100);
        camera.position.set(0, 2.8, 4.5);
        camera.lookAt(0, 0, 0);

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        createWave();
        createStars();

        document.addEventListener('mousemove', function(e) {
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;
            // Map to wave surface coordinates
            mouseWorld.x = mouseX * (COLS * SEP * 0.5);
            mouseWorld.z = mouseY * (ROWS * SEP * 0.5);
        }, { passive: true });

        document.addEventListener('touchmove', function(e) {
            if (e.touches.length > 0) {
                mouseX = (e.touches[0].clientX / window.innerWidth) * 2 - 1;
                mouseY = (e.touches[0].clientY / window.innerHeight) * 2 - 1;
                mouseWorld.x = mouseX * (COLS * SEP * 0.5);
                mouseWorld.z = mouseY * (ROWS * SEP * 0.5);
            }
        }, { passive: true });

        window.addEventListener('resize', function() {
            width = canvas.parentElement.offsetWidth;
            height = canvas.parentElement.offsetHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        }, { passive: true });

        animate();
    }

    function createWave() {
        // Point grid as the wave surface
        var totalPoints = COLS * ROWS;
        var positions = new Float32Array(totalPoints * 3);
        var colors = new Float32Array(totalPoints * 3);
        var sizes = new Float32Array(totalPoints);

        var halfW = (COLS - 1) * SEP * 0.5;
        var halfH = (ROWS - 1) * SEP * 0.5;

        for (var row = 0; row < ROWS; row++) {
            for (var col = 0; col < COLS; col++) {
                var idx = row * COLS + col;
                positions[idx * 3] = col * SEP - halfW;
                positions[idx * 3 + 1] = 0;
                positions[idx * 3 + 2] = row * SEP - halfH;

                // Green color with slight variation
                var brightness = 0.6 + Math.random() * 0.4;
                colors[idx * 3] = 0.063 * brightness;     // R (16/255)
                colors[idx * 3 + 1] = 0.725 * brightness;  // G (185/255)
                colors[idx * 3 + 2] = 0.506 * brightness;  // B (129/255)

                sizes[idx] = 1.5 + Math.random() * 1;
            }
        }

        waveGeo = new THREE.BufferGeometry();
        waveGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        waveGeo.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        var mat = new THREE.PointsMaterial({
            size: 0.035,
            vertexColors: true,
            transparent: true,
            opacity: 0.75,
            blending: THREE.AdditiveBlending,
            depthWrite: false,
            sizeAttenuation: true
        });

        waveMesh = new THREE.Points(waveGeo, mat);
        scene.add(waveMesh);

        // Wireframe connections (horizontal + vertical lines)
        // We'll create line segments for a grid mesh
        var linePositions = [];
        for (var r = 0; r < ROWS; r++) {
            for (var c = 0; c < COLS; c++) {
                var x = c * SEP - halfW;
                var z = r * SEP - halfH;
                // Right neighbor
                if (c < COLS - 1) {
                    var nx = (c + 1) * SEP - halfW;
                    linePositions.push(x, 0, z, nx, 0, z);
                }
                // Down neighbor
                if (r < ROWS - 1) {
                    var nz = (r + 1) * SEP - halfH;
                    linePositions.push(x, 0, z, x, 0, nz);
                }
            }
        }

        var lineGeo = new THREE.BufferGeometry();
        lineGeo.setAttribute('position', new THREE.Float32BufferAttribute(linePositions, 3));
        lineGeo.name = 'waveLines';

        var lineMat = new THREE.LineBasicMaterial({
            color: GREEN,
            transparent: true,
            opacity: 0.06,
            blending: THREE.AdditiveBlending,
            depthWrite: false
        });

        var lineSegments = new THREE.LineSegments(lineGeo, lineMat);
        lineSegments.name = 'waveLinesMesh';
        scene.add(lineSegments);
    }

    function createStars() {
        var count = 1500;
        var pos = new Float32Array(count * 3);
        for (var i = 0; i < count; i++) {
            var r = 8 + Math.random() * 25;
            var t = Math.random() * Math.PI * 2;
            var p = Math.acos(2 * Math.random() - 1);
            pos[i * 3] = r * Math.sin(p) * Math.cos(t);
            pos[i * 3 + 1] = r * Math.sin(p) * Math.sin(t);
            pos[i * 3 + 2] = r * Math.cos(p);
        }
        var geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        starsGroup = new THREE.Points(geo, new THREE.PointsMaterial({
            color: 0xffffff, size: 0.05, transparent: true, opacity: 0.7,
            blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
        }));
        scene.add(starsGroup);

        // Green particles
        var gCount = 200;
        var gPos = new Float32Array(gCount * 3);
        for (var j = 0; j < gCount; j++) {
            var gr = 3 + Math.random() * 8;
            var gt = Math.random() * Math.PI * 2;
            var gp = Math.acos(2 * Math.random() - 1);
            gPos[j * 3] = gr * Math.sin(gp) * Math.cos(gt);
            gPos[j * 3 + 1] = gr * Math.sin(gp) * Math.sin(gt);
            gPos[j * 3 + 2] = gr * Math.cos(gp);
        }
        var gGeo = new THREE.BufferGeometry();
        gGeo.setAttribute('position', new THREE.BufferAttribute(gPos, 3));
        var fp = new THREE.Points(gGeo, new THREE.PointsMaterial({
            color: GREEN, size: 0.03, transparent: true, opacity: 0.3,
            blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
        }));
        fp.name = 'greenParticles';
        scene.add(fp);
    }

    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;

        // Update wave heights
        if (waveGeo) {
            var posAttr = waveGeo.getAttribute('position');
            var colorAttr = waveGeo.getAttribute('color');
            var halfW = (COLS - 1) * SEP * 0.5;
            var halfH = (ROWS - 1) * SEP * 0.5;

            for (var r = 0; r < ROWS; r++) {
                for (var c = 0; c < COLS; c++) {
                    var idx = r * COLS + c;
                    var x = c * SEP - halfW;
                    var z = r * SEP - halfH;

                    // Base wave — multiple sine layers
                    var y = 0;
                    y += Math.sin(x * 1.2 + time * 0.8) * 0.15;
                    y += Math.sin(z * 1.5 + time * 0.6) * 0.12;
                    y += Math.sin((x + z) * 0.8 + time * 1.2) * 0.08;
                    y += Math.sin(x * 2.5 - time * 1.5) * 0.05;
                    y += Math.cos(z * 2.0 + time) * 0.06;

                    // Mouse ripple
                    var dx = x - mouseWorld.x;
                    var dz = z - mouseWorld.z;
                    var dist = Math.sqrt(dx * dx + dz * dz);
                    if (dist < 3) {
                        var ripple = Math.sin(dist * 4 - time * 5) * 0.2 * Math.max(0, 1 - dist / 3);
                        y += ripple;
                    }

                    posAttr.setY(idx, y);

                    // Color intensity based on height
                    var intensity = 0.5 + y * 1.5;
                    intensity = Math.max(0.3, Math.min(1.2, intensity));
                    colorAttr.setXYZ(idx,
                        0.063 * intensity,
                        0.725 * intensity,
                        0.506 * intensity
                    );
                }
            }
            posAttr.needsUpdate = true;
            colorAttr.needsUpdate = true;
        }

        // Update line positions to match wave
        var linesMesh = scene.getObjectByName('waveLinesMesh');
        if (linesMesh && waveGeo) {
            var wavePos = waveGeo.getAttribute('position');
            var linePos = linesMesh.geometry.getAttribute('position');
            var halfW2 = (COLS - 1) * SEP * 0.5;
            var halfH2 = (ROWS - 1) * SEP * 0.5;
            var li = 0;

            for (var r2 = 0; r2 < ROWS; r2++) {
                for (var c2 = 0; c2 < COLS; c2++) {
                    var idx2 = r2 * COLS + c2;
                    var y1 = wavePos.getY(idx2);

                    if (c2 < COLS - 1) {
                        var y2 = wavePos.getY(idx2 + 1);
                        linePos.setY(li, y1); li++;
                        linePos.setY(li, y2); li++;
                    }
                    if (r2 < ROWS - 1) {
                        var y3 = wavePos.getY(idx2 + COLS);
                        linePos.setY(li, y1); li++;
                        linePos.setY(li, y3); li++;
                    }
                }
            }
            linePos.needsUpdate = true;
        }

        // Stars
        if (starsGroup) {
            starsGroup.rotation.y = time * 0.005;
            starsGroup.rotation.x = time * 0.003;
        }
        var gp2 = scene.getObjectByName('greenParticles');
        if (gp2) {
            gp2.rotation.y = -time * 0.01;
        }

        renderer.render(scene, camera);
    }

    function loadThree() {
        if (typeof THREE !== 'undefined') { init(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
        s.onload = init;
        s.onerror = function() { canvas.style.display = 'none'; };
        document.head.appendChild(s);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', loadThree);
    else loadThree();
})();
