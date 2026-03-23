/**
 * Doji Funding — Neural Molecular Network
 * Spread-out molecular connections for Affiliate hero
 */
(function() {
    'use strict';
    var canvas = document.getElementById('circuitCanvas');
    if (!canvas) return;
    try { if (!(canvas.getContext('webgl') || canvas.getContext('experimental-webgl'))) return; } catch(e) { return; }

    var scene, camera, renderer, netGroup, starsGroup;
    var mouseX = 0, mouseY = 0, targetRotX = 0, targetRotY = 0;
    var width, height;
    var GREEN = 0x10B981;

    var nodes = [], connections = [], pulses = [];
    var maxNodes = 60, growTimer = 0, growInterval = 0.12;
    var buildDone = false;
    var SPREAD_X = 6, SPREAD_Y = 3, SPREAD_Z = 2;
    var CONNECT_DIST = 2.2;

    function Node(pos) {
        this.pos = pos.clone();
        this.basePos = pos.clone();
        this.radius = 0;
        this.targetRadius = 0.04 + Math.random() * 0.025;
        this.conns = 0;
        this.phase = Math.random() * Math.PI * 2;
        this.floatSpeed = 0.4 + Math.random() * 0.4;
        this.floatAmp = 0.03 + Math.random() * 0.03;
        this.mesh = null;
        this.glowMesh = null;
        this.outerGlow = null;
    }

    function Connection(a, b) {
        this.a = a; this.b = b;
        this.progress = 0;
        this.speed = 0.012 + Math.random() * 0.012;
        this.opacity = 0;
        this.line = null;
        this.curve = null;
        this.synapseFired = false;
    }

    function Pulse(conn, dir) {
        this.conn = conn;
        this.progress = dir > 0 ? 0 : 1;
        this.dir = dir;
        this.speed = (0.006 + Math.random() * 0.008) * dir;
        this.mesh = null;
        this.glowMesh = null;
        this.trailMesh = null;
    }

    function init() {
        width = canvas.parentElement.offsetWidth;
        height = canvas.parentElement.offsetHeight;
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(55, width / height, 0.1, 100);
        camera.position.set(0, 0, 7);

        renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0x000000, 0);

        netGroup = new THREE.Group();
        scene.add(netGroup);

        // Seed with a few spread nodes
        addNode(new THREE.Vector3(0, 0, 0));
        addNode(new THREE.Vector3(-2, 0.5, 0.3));
        addNode(new THREE.Vector3(2, -0.3, -0.2));
        addNode(new THREE.Vector3(0, 1.5, 0.5));
        addNode(new THREE.Vector3(0, -1.2, -0.4));

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
        }, { passive: true });

        animate();
    }

    function addNode(pos) {
        if (nodes.length >= maxNodes) return null;
        var n = new Node(pos);

        // Bright core
        var geo = new THREE.SphereGeometry(1, 10, 10);
        n.mesh = new THREE.Mesh(geo, new THREE.MeshBasicMaterial({
            color: 0xffffff, transparent: true, opacity: 0.85
        }));
        n.mesh.position.copy(pos);
        n.mesh.scale.set(0.001, 0.001, 0.001);
        netGroup.add(n.mesh);

        // Inner glow
        n.glowMesh = new THREE.Mesh(
            new THREE.SphereGeometry(1, 10, 10),
            new THREE.MeshBasicMaterial({ color: GREEN, transparent: true, opacity: 0.35, blending: THREE.AdditiveBlending })
        );
        n.glowMesh.position.copy(pos);
        n.glowMesh.scale.set(0.001, 0.001, 0.001);
        netGroup.add(n.glowMesh);

        // Outer soft halo
        n.outerGlow = new THREE.Mesh(
            new THREE.SphereGeometry(1, 10, 10),
            new THREE.MeshBasicMaterial({ color: GREEN, transparent: true, opacity: 0.06, blending: THREE.AdditiveBlending, depthWrite: false })
        );
        n.outerGlow.position.copy(pos);
        n.outerGlow.scale.set(0.001, 0.001, 0.001);
        netGroup.add(n.outerGlow);

        nodes.push(n);
        return n;
    }

    function growNetwork() {
        if (nodes.length >= maxNodes) { buildDone = true; return; }

        var parent = nodes[Math.floor(Math.random() * nodes.length)];
        if (parent.conns >= 4) {
            parent = nodes[Math.floor(Math.random() * nodes.length)];
            if (parent.conns >= 4) return;
        }

        var angle = Math.random() * Math.PI * 2;
        var dist = 1.0 + Math.random() * 1.2;
        var newPos = new THREE.Vector3(
            parent.pos.x + Math.cos(angle) * dist,
            parent.pos.y + Math.sin(angle) * dist * 0.6 + (Math.random() - 0.5) * 0.5,
            parent.pos.z + (Math.random() - 0.5) * 0.8
        );
        newPos.x = Math.max(-SPREAD_X, Math.min(SPREAD_X, newPos.x));
        newPos.y = Math.max(-SPREAD_Y, Math.min(SPREAD_Y, newPos.y));
        newPos.z = Math.max(-SPREAD_Z, Math.min(SPREAD_Z, newPos.z));

        // Not too close
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].pos.distanceTo(newPos) < 0.5) return;
        }

        var child = addNode(newPos);
        if (!child) return;

        makeConnection(parent, child);

        // Auto-connect nearby
        for (var j = 0; j < nodes.length; j++) {
            var other = nodes[j];
            if (other === child || other === parent) continue;
            var d = other.pos.distanceTo(child.pos);
            if (d < CONNECT_DIST && other.conns < 4 && child.conns < 4 && Math.random() < 0.3) {
                makeConnection(child, other);
            }
        }
    }

    function makeConnection(a, b) {
        // Check not already connected
        for (var i = 0; i < connections.length; i++) {
            var c = connections[i];
            if ((c.a === a && c.b === b) || (c.a === b && c.b === a)) return;
        }

        var conn = new Connection(a, b);
        var mid = new THREE.Vector3().addVectors(a.pos, b.pos).multiplyScalar(0.5);
        mid.x += (Math.random() - 0.5) * 0.3;
        mid.y += (Math.random() - 0.5) * 0.3;
        mid.z += (Math.random() - 0.5) * 0.2;

        conn.curve = new THREE.QuadraticBezierCurve3(a.pos.clone(), mid, b.pos.clone());
        var pts = conn.curve.getPoints(24);
        conn.line = new THREE.Line(
            new THREE.BufferGeometry().setFromPoints(pts),
            new THREE.LineBasicMaterial({ color: GREEN, transparent: true, opacity: 0, blending: THREE.AdditiveBlending })
        );
        netGroup.add(conn.line);
        connections.push(conn);
        a.conns++;
        b.conns++;
    }

    function spawnPulse(conn, dir) {
        if (!dir) dir = Math.random() > 0.5 ? 1 : -1;
        var p = new Pulse(conn, dir);

        p.mesh = new THREE.Mesh(
            new THREE.SphereGeometry(0.03, 6, 6),
            new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.95 })
        );
        netGroup.add(p.mesh);

        p.glowMesh = new THREE.Mesh(
            new THREE.SphereGeometry(0.09, 6, 6),
            new THREE.MeshBasicMaterial({ color: GREEN, transparent: true, opacity: 0.4, blending: THREE.AdditiveBlending })
        );
        netGroup.add(p.glowMesh);

        pulses.push(p);
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

        var gCount = 250;
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
        scene.add(new THREE.Points(gGeo, new THREE.PointsMaterial({
            color: GREEN, size: 0.03, transparent: true, opacity: 0.3,
            blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
        })));
    }

    function animate() {
        requestAnimationFrame(animate);
        var time = Date.now() * 0.001;

        targetRotY += (mouseX * 0.2 - targetRotY) * 0.025;
        targetRotX += (mouseY * 0.12 - targetRotX) * 0.025;
        if (netGroup) {
            netGroup.rotation.y = targetRotY;
            netGroup.rotation.x = targetRotX;
        }

        // Grow
        if (!buildDone) {
            growTimer += 0.016;
            if (growTimer >= growInterval) { growTimer = 0; growNetwork(); }
        }

        // Spawn pulses
        var readyConns = connections.filter(function(c) { return c.progress >= 1; });
        if (readyConns.length > 3 && Math.random() < 0.025) {
            spawnPulse(readyConns[Math.floor(Math.random() * readyConns.length)]);
        }

        // Update nodes — float & breathe
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            n.radius += (n.targetRadius - n.radius) * 0.06;
            var s = n.radius;
            n.mesh.scale.set(s, s, s);
            n.glowMesh.scale.set(s * 3, s * 3, s * 3);
            n.outerGlow.scale.set(s * 7, s * 7, s * 7);

            var fy = Math.sin(time * n.floatSpeed + n.phase) * n.floatAmp;
            var fx = Math.cos(time * n.floatSpeed * 0.7 + n.phase) * n.floatAmp * 0.5;
            n.mesh.position.set(n.basePos.x + fx, n.basePos.y + fy, n.basePos.z);
            n.glowMesh.position.copy(n.mesh.position);
            n.outerGlow.position.copy(n.mesh.position);
            n.pos.copy(n.mesh.position);

            var gPulse = 0.25 + Math.sin(time * 1.2 + n.phase) * 0.1;
            n.glowMesh.material.opacity = gPulse;
            n.outerGlow.material.opacity = 0.04 + Math.sin(time * 0.8 + n.phase) * 0.02;
        }

        // Update connections
        for (var c = 0; c < connections.length; c++) {
            var conn = connections[c];
            if (conn.progress < 1) {
                conn.progress += conn.speed;
                if (conn.progress > 1) conn.progress = 1;
            }
            var tOp = 0.08 + conn.progress * 0.06;
            conn.opacity += (tOp - conn.opacity) * 0.04;
            conn.line.material.opacity = conn.opacity;

            if (conn.progress >= 1 && !conn.synapseFired) {
                conn.synapseFired = true;
                conn.line.material.opacity = 0.5;
                spawnPulse(conn);
            }
            if (conn.synapseFired && conn.line.material.opacity > tOp + 0.01) {
                conn.line.material.opacity -= 0.005;
            }

            // Update curve endpoints to follow floating nodes
            if (conn.curve) {
                conn.curve.v0.copy(conn.a.pos);
                conn.curve.v2.copy(conn.b.pos);
                var pts = conn.curve.getPoints(24);
                conn.line.geometry.setFromPoints(pts);
            }
        }

        // Update pulses
        for (var p = pulses.length - 1; p >= 0; p--) {
            var pulse = pulses[p];
            pulse.progress += pulse.speed;
            var t2 = Math.max(0, Math.min(1, pulse.dir > 0 ? pulse.progress : pulse.progress));

            if (pulse.conn.curve) {
                var pt = pulse.conn.curve.getPoint(Math.max(0, Math.min(1, t2)));
                pulse.mesh.position.copy(pt);
                pulse.glowMesh.position.copy(pt);
            }

            var fade = Math.sin(Math.max(0, Math.min(1, t2)) * Math.PI);
            pulse.mesh.material.opacity = fade * 0.95;
            pulse.glowMesh.material.opacity = fade * 0.45;
            pulse.conn.line.material.opacity = Math.max(pulse.conn.line.material.opacity, fade * 0.25);

            var done = (pulse.dir > 0 && pulse.progress > 1.05) || (pulse.dir < 0 && pulse.progress < -0.05);
            if (done) {
                // Chain
                if (Math.random() < 0.45) {
                    var endNode = pulse.dir > 0 ? pulse.conn.b : pulse.conn.a;
                    var nextC = connections.filter(function(cc) {
                        return cc !== pulse.conn && cc.progress >= 1 && (cc.a === endNode || cc.b === endNode);
                    });
                    if (nextC.length > 0) {
                        var nc = nextC[Math.floor(Math.random() * nextC.length)];
                        spawnPulse(nc, nc.a === endNode ? 1 : -1);
                    }
                }
                netGroup.remove(pulse.mesh);
                netGroup.remove(pulse.glowMesh);
                pulses.splice(p, 1);
            }
        }

        if (starsGroup) { starsGroup.rotation.y = time * 0.004; starsGroup.rotation.x = time * 0.002; }
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
