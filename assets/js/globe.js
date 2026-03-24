/**
 * Doji Funding — Interactive AI Thinking Orb
 * 3D particle sphere with mouse/touch repulsion.
 * Particles scatter when cursor approaches the sphere
 * and reassemble when cursor moves away.
 * Same physics model as the pixel particle footer.
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
    var dotCount = 2200;

    // ─── Interaction — same physics as particle footer ───
    var raycaster, mouseVec, interactionSphere;
    var REPULSE_RADIUS = 1.2;        // Wide repulsion zone
    var REPULSE_FORCE = 0.25;        // Strong push (like footer pushForce)
    var RETURN_SPEED = 0.06;         // Match footer returnSpeed
    var FRICTION = 0.88;             // Match footer friction

    // Per-particle physics arrays
    var velocities;
    var isHovering = false;
    var hitPoint = { x: 0, y: 0, z: 0 };

    function init() {
        width = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        camera.position.z = 4.5;

        function updateCameraOffset() {
            camera.position.x = window.innerWidth > 1024 ? -1.2 : 0;
        }
        updateCameraOffset();

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        orbGroup = new THREE.Group();
        scene.add(orbGroup);

        // Raycaster for mouse→3D intersection
        raycaster = new THREE.Raycaster();
        mouseVec = new THREE.Vector2(-999, -999);

        // Invisible sphere for raycasting (larger for easier hover detection)
        var sphereGeo = new THREE.SphereGeometry(RADIUS * 1.4, 32, 32);
        var sphereMat = new THREE.MeshBasicMaterial({ visible: false });
        interactionSphere = new THREE.Mesh(sphereGeo, sphereMat);
        orbGroup.add(interactionSphere);

        createOrb();
        createStars();

        // ─── Mouse events on canvas ───
        canvas.addEventListener('mousemove', function(e) {
            var rect = canvas.getBoundingClientRect();
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;
            mouseVec.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            mouseVec.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
        }, { passive: true });

        canvas.addEventListener('mouseleave', function() {
            isHovering = false;
            mouseVec.set(-999, -999);
        });

        // ─── Touch events ───
        canvas.addEventListener('touchstart', function(e) {
            if (e.touches.length > 0) updateTouch(e.touches[0]);
        }, { passive: true });

        canvas.addEventListener('touchmove', function(e) {
            if (e.touches.length > 0) updateTouch(e.touches[0]);
        }, { passive: true });

        canvas.addEventListener('touchend', function() {
            isHovering = false;
            mouseVec.set(-999, -999);
        });

        function updateTouch(touch) {
            var rect = canvas.getBoundingClientRect();
            mouseX = (touch.clientX / window.innerWidth) * 2 - 1;
            mouseY = (touch.clientY / window.innerHeight) * 2 - 1;
            mouseVec.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
            mouseVec.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;
        }

        // Global mouse for rotation
        document.addEventListener('mousemove', function(e) {
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;
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
        var positions = new Float32Array(dotCount * 3);
        var sizes = new Float32Array(dotCount);
        var phases = new Float32Array(dotCount);

        // Fibonacci sphere distribution
        for (var i = 0; i < dotCount; i++) {
            var phi = Math.acos(1 - 2 * (i + 0.5) / dotCount);
            var theta = Math.PI * (1 + Math.sqrt(5)) * i;

            positions[i * 3]     = RADIUS * Math.sin(phi) * Math.cos(theta);
            positions[i * 3 + 1] = RADIUS * Math.sin(phi) * Math.sin(theta);
            positions[i * 3 + 2] = RADIUS * Math.cos(phi);

            sizes[i] = 0.8 + Math.random() * 1.5;
            phases[i] = Math.random() * Math.PI * 2;
        }

        // Physics arrays
        velocities = new Float32Array(dotCount * 3); // initialized to 0

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
        var count = 2000;
        var pos = new Float32Array(count * 3);
        var sizes = new Float32Array(count);
        for (var i = 0; i < count; i++) {
            var r = 6 + Math.random() * 30;
            var t = Math.random() * Math.PI * 2;
            var p = Math.acos(2 * Math.random() - 1);
            pos[i * 3]     = r * Math.sin(p) * Math.cos(t);
            pos[i * 3 + 1] = r * Math.sin(p) * Math.sin(t);
            pos[i * 3 + 2] = r * Math.cos(p);
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

        // Green floating particles
        var gCount = 300;
        var gPos = new Float32Array(gCount * 3);
        for (var j = 0; j < gCount; j++) {
            var gr = 3 + Math.random() * 10;
            var gt = Math.random() * Math.PI * 2;
            var gp = Math.acos(2 * Math.random() - 1);
            gPos[j * 3]     = gr * Math.sin(gp) * Math.cos(gt);
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

    // ─── Raycast to find hit point on sphere ───
    function updateHitPoint() {
        if (mouseVec.x < -10) { isHovering = false; return; }

        raycaster.setFromCamera(mouseVec, camera);
        var intersects = raycaster.intersectObject(interactionSphere, false);

        if (intersects.length > 0) {
            isHovering = true;
            var localPoint = orbGroup.worldToLocal(intersects[0].point.clone());
            hitPoint.x = localPoint.x;
            hitPoint.y = localPoint.y;
            hitPoint.z = localPoint.z;
        } else {
            isHovering = false;
        }
    }

    // ─── Update particle physics (footer-style scatter & return) ───
    function updateParticlePhysics(time) {
        var pointsObj = null;
        for (var c = 0; c < orbGroup.children.length; c++) {
            if (orbGroup.children[c].isPoints) { pointsObj = orbGroup.children[c]; break; }
        }
        if (!pointsObj) return;

        var posAttr = pointsObj.geometry.getAttribute('position');
        var phases = pointsObj.geometry.userData.phases;

        for (var i = 0; i < dotCount; i++) {
            var idx = i * 3;

            // Breathing origin (subtle pulse like resting state)
            var phase = phases[i];
            var breathe = 1 + Math.sin(time * 1.5 + phase) * 0.035;
            var phi = Math.acos(1 - 2 * (i + 0.5) / dotCount);
            var theta = Math.PI * (1 + Math.sqrt(5)) * i;

            var origX = RADIUS * breathe * Math.sin(phi) * Math.cos(theta);
            var origY = RADIUS * breathe * Math.sin(phi) * Math.sin(theta);
            var origZ = RADIUS * breathe * Math.cos(phi);

            // Current position
            var cx = posAttr.getX(i);
            var cy = posAttr.getY(i);
            var cz = posAttr.getZ(i);

            // Mouse repulsion — same model as footer
            if (isHovering) {
                var dx = cx - hitPoint.x;
                var dy = cy - hitPoint.y;
                var dz = cz - hitPoint.z;
                var dist = Math.sqrt(dx * dx + dy * dy + dz * dz);

                if (dist < REPULSE_RADIUS && dist > 0.001) {
                    // Force ramps up as particle gets closer to cursor (like footer)
                    var force = (REPULSE_RADIUS - dist) / REPULSE_RADIUS;
                    velocities[idx]     += (dx / dist) * force * REPULSE_FORCE;
                    velocities[idx + 1] += (dy / dist) * force * REPULSE_FORCE;
                    velocities[idx + 2] += (dz / dist) * force * REPULSE_FORCE;
                }
            }

            // Return to origin (spring)
            velocities[idx]     += (origX - cx) * RETURN_SPEED;
            velocities[idx + 1] += (origY - cy) * RETURN_SPEED;
            velocities[idx + 2] += (origZ - cz) * RETURN_SPEED;

            // Friction damping
            velocities[idx]     *= FRICTION;
            velocities[idx + 1] *= FRICTION;
            velocities[idx + 2] *= FRICTION;

            // Apply velocity
            posAttr.setXYZ(i,
                cx + velocities[idx],
                cy + velocities[idx + 1],
                cz + velocities[idx + 2]
            );
        }

        posAttr.needsUpdate = true;

        // Pulse opacity
        pointsObj.material.opacity = 0.75 + Math.sin(time * 0.8) * 0.15;
    }

    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;
        var slowTime = time * 0.15;

        // Mouse rotation
        targetRotY += (mouseX * 0.6 - targetRotY) * 0.03;
        targetRotX += (mouseY * 0.3 - targetRotX) * 0.03;

        if (orbGroup) {
            orbGroup.rotation.y = slowTime + targetRotY;
            orbGroup.rotation.x = 0.15 + targetRotX;
        }

        // Raycast + particle physics
        updateHitPoint();
        updateParticlePhysics(time);

        // Stars twinkle
        if (starsGroup && starsGroup.userData.baseSizes) {
            var sizeAttr = starsGroup.geometry.getAttribute('size');
            var base = starsGroup.userData.baseSizes;
            for (var s = 0; s < Math.min(base.length, 200); s++) {
                sizeAttr.setX(s, base[s] * (0.6 + Math.sin(time * (2 + s * 0.01) + s) * 0.4));
            }
            sizeAttr.needsUpdate = true;
        }

        // Stars drift
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
