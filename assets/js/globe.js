/**
 * Doji Funding — AI Thinking Orb
 * 3D particle sphere with surface pulses + cycling status text
 */
(function() {
    'use strict';
    var canvas = document.getElementById('heroGlobe');
    if (!canvas) return;
    try { if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return; } catch(e) { return; }

    var scene, camera, renderer, orbGroup, starsGroup;
    var mouseX = 0, mouseY = 0, targetRotX = 0, targetRotY = 0;
    var width, height;
    var RADIUS = 1.4, GREEN = 0x10B981;
    var dotPositions, dotCount = 2200;

    function init() {
        width = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        camera.position.z = 4.5;
        function updateCameraOffset() {
            if (window.innerWidth > 1024) {
                camera.position.x = -1.2;
            } else {
                camera.position.x = 0;
            }
        }
        updateCameraOffset();
        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        orbGroup = new THREE.Group();
        scene.add(orbGroup);

        createOrb();
        createStars();

        document.addEventListener('mousemove', function(e) {
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;
        }, { passive: true });
        document.addEventListener('touchmove', function(e) {
            if (e.touches.length > 0) {
                mouseX = (e.touches[0].clientX / window.innerWidth) * 2 - 1;
                mouseY = (e.touches[0].clientY / window.innerHeight) * 2 - 1;
            }
        }, { passive: true });
        window.addEventListener('resize', function() {
            width = canvas.parentElement.offsetWidth;
            height = canvas.parentElement.offsetHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
            updateCameraOffset();
        }, { passive: true });

        animate();
    }

    function createOrb() {
        // Fibonacci sphere distribution for even dot placement
        var positions = new Float32Array(dotCount * 3);
        var sizes = new Float32Array(dotCount);
        var phases = new Float32Array(dotCount);

        for (var i = 0; i < dotCount; i++) {
            var phi = Math.acos(1 - 2 * (i + 0.5) / dotCount);
            var theta = Math.PI * (1 + Math.sqrt(5)) * i;

            positions[i * 3] = RADIUS * Math.sin(phi) * Math.cos(theta);
            positions[i * 3 + 1] = RADIUS * Math.sin(phi) * Math.sin(theta);
            positions[i * 3 + 2] = RADIUS * Math.cos(phi);

            sizes[i] = 0.8 + Math.random() * 1.5;
            phases[i] = Math.random() * Math.PI * 2;
        }

        dotPositions = positions;

        var geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geo.setAttribute('size', new THREE.BufferAttribute(sizes, 1));
        geo.userData = { phases: phases };

        var mat = new THREE.PointsMaterial({
            color: GREEN, size: 0.032, transparent: true, opacity: 0.85,
            blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
        });

        orbGroup.add(new THREE.Points(geo, mat));
    }

    function createStars() {
        // Big twinkly stars — varied sizes
        var count = 2000;
        var pos = new Float32Array(count * 3);
        var sizes = new Float32Array(count);
        for (var i = 0; i < count; i++) {
            var r = 6 + Math.random() * 30;
            var t = Math.random() * Math.PI * 2;
            var p = Math.acos(2 * Math.random() - 1);
            pos[i * 3] = r * Math.sin(p) * Math.cos(t);
            pos[i * 3 + 1] = r * Math.sin(p) * Math.sin(t);
            pos[i * 3 + 2] = r * Math.cos(p);
            // Varied sizes: mostly small, some large
            sizes[i] = Math.random() < 0.08 ? 0.08 + Math.random() * 0.12 : 0.02 + Math.random() * 0.04;
        }
        var geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        geo.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

        starsGroup = new THREE.Points(geo, new THREE.PointsMaterial({
            color: 0xffffff, size: 0.05, transparent: true, opacity: 0.8,
            blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
        }));
        starsGroup.userData = { baseSizes: sizes.slice() };
        scene.add(starsGroup);

        // Green particles
        var gCount = 300;
        var gPos = new Float32Array(gCount * 3);
        for (var j = 0; j < gCount; j++) {
            var gr = 3 + Math.random() * 10;
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
        fp.name = 'floatingParticles';
        scene.add(fp);
    }

    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;
        var slowTime = time * 0.15;

        // Mouse
        targetRotY += (mouseX * 0.6 - targetRotY) * 0.03;
        targetRotX += (mouseY * 0.3 - targetRotX) * 0.03;

        // Orb rotation
        if (orbGroup) {
            orbGroup.rotation.y = slowTime + targetRotY;
            orbGroup.rotation.x = 0.15 + targetRotX;
        }

        // Animate dot breathing
        var points = orbGroup.children[0];
        if (points && points.isPoints) {
            var posAttr = points.geometry.getAttribute('position');
            var phases = points.geometry.userData.phases;
            for (var i = 0; i < dotCount; i++) {
                var phase = phases[i];
                var breathe = 1 + Math.sin(time * 1.5 + phase) * 0.035;
                var idx = i * 3;
                var phi = Math.acos(1 - 2 * (i + 0.5) / dotCount);
                var theta = Math.PI * (1 + Math.sqrt(5)) * i;
                posAttr.setXYZ(i,
                    RADIUS * breathe * Math.sin(phi) * Math.cos(theta),
                    RADIUS * breathe * Math.sin(phi) * Math.sin(theta),
                    RADIUS * breathe * Math.cos(phi)
                );
            }
            posAttr.needsUpdate = true;
            // Pulse opacity
            points.material.opacity = 0.75 + Math.sin(time * 0.8) * 0.15;
        }

        // Stars twinkle — varied intensity
        if (starsGroup && starsGroup.userData.baseSizes) {
            var sizeAttr = starsGroup.geometry.getAttribute('size');
            var base = starsGroup.userData.baseSizes;
            for (var s = 0; s < Math.min(base.length, 200); s++) {
                var twinkle = base[s] * (0.6 + Math.sin(time * (2 + s * 0.01) + s) * 0.4);
                sizeAttr.setX(s, twinkle);
            }
            sizeAttr.needsUpdate = true;
        }

        // Stars slow drift
        if (starsGroup) { starsGroup.rotation.y = time * 0.008; starsGroup.rotation.x = time * 0.004; }
        var fp = scene.getObjectByName('floatingParticles');
        if (fp) { fp.rotation.y = -time * 0.015 + targetRotY * 0.2; fp.rotation.x = time * 0.01 + targetRotX * 0.15; }

        renderer.render(scene, camera);
    }

    function loadThree() {
        if (typeof THREE !== 'undefined') { init(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
        s.onload = init; s.onerror = function() { canvas.style.display = 'none'; };
        document.head.appendChild(s);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', loadThree);
    else loadThree();
})();
