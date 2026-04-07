/**
 * Doji Funding — Hero Canvas v4
 *
 * Nothing-style dot-grid: white dots, all visible.
 * Mouse hover anywhere in the hero (incl. over shape): emerald scatter.
 * Spacetime warp ring around shape centre.
 * Full great-circle meridians on sphere.
 * defer-safe: readyState check.
 */
(function () {
  'use strict';

  // ── Config ────────────────────────────────────────────────
  var GRID       = 22;
  var DOT_R      = 1.5;
  var M_RAD      = 115;
  var M_FORCE    = 10;
  var SPRING     = 0.07;
  var FRIC       = 0.82;
  var SP_PUSH    = 58;
  var SP_RAD     = 148;
  var S_SCALE    = 120;   // base scale; overridden per-canvas via data-scale
  var A_ROT      = 0.004;
  var ROT_LERP   = 0.055;
  var M_ROT_MAX  = 0.55;

  // ── Grid Dot ─────────────────────────────────────────────
  function Dot(ox, oy) {
    this.ox    = ox; this.oy    = oy;
    this.x     = ox; this.y     = oy;
    this.vx    = 0;  this.vy    = 0;
    this.green = 0;  // 0=white → 1=emerald; fades after mouse leaves
  }

  Dot.prototype.tick = function (mx, my, mOn, scx, scy) {
    // Spacetime ring warp
    var sdx = this.ox - scx, sdy = this.oy - scy;
    var sd  = Math.sqrt(sdx * sdx + sdy * sdy) || 1;
    var n   = sd / SP_RAD;
    var amp = SP_PUSH * n * Math.exp(-n * n * 0.7);
    var tx  = this.ox + (sdx / sd) * amp;
    var ty  = this.oy + (sdy / sd) * amp;

    this.vx += (tx - this.x) * SPRING;
    this.vy += (ty - this.y) * SPRING;

    // Mouse scatter
    if (mOn) {
      var dx = this.x - mx, dy = this.y - my;
      var d2 = dx * dx + dy * dy;
      if (d2 < M_RAD * M_RAD) {
        var d = Math.sqrt(d2) || 1;
        var f = (M_RAD - d) / M_RAD;
        this.vx   += (dx / d) * f * M_FORCE;
        this.vy   += (dy / d) * f * M_FORCE;
        this.green = 1;
      }
    }

    this.vx *= FRIC; this.vy *= FRIC;
    this.x  += this.vx; this.y  += this.vy;

    if (this.green > 0) {
      this.green *= 0.96;
      if (this.green < 0.02) this.green = 0;
    }
  };

  Dot.prototype.draw = function (ctx) {
    var px = this.x, py = this.y;
    if ((px - this.ox) * (px - this.ox) + (py - this.oy) * (py - this.oy) < 0.3) {
      px = this.ox; py = this.oy;
    }
    ctx.beginPath();
    ctx.arc(px, py, DOT_R, 0, Math.PI * 2);
    if (this.green > 0.02) {
      ctx.fillStyle = 'rgba(16,185,129,' + Math.min(0.95, 0.25 + this.green * 0.70).toFixed(3) + ')';
    } else {
      ctx.fillStyle = 'rgba(255,255,255,0.18)';
    }
    ctx.fill();
  };

  // ── Shape builders ────────────────────────────────────────

  function edgePts(pa, pb, n) {
    var pts = [];
    for (var i = 0; i <= n; i++) {
      var t = i / n;
      pts.push([
        pa[0] + (pb[0] - pa[0]) * t,
        pa[1] + (pb[1] - pa[1]) * t,
        pa[2] + (pb[2] - pa[2]) * t
      ]);
    }
    return pts;
  }

  // Sphere: latitude rings + FULL great-circle meridians (θ: 0 → 2π)
  function buildSphere() {
    var pts = [];

    // Latitude rings every 15°, point count proportional to cos(lat)
    for (var latD = -75; latD <= 75; latD += 15) {
      var lat = latD * Math.PI / 180;
      var r   = Math.cos(lat), yv = Math.sin(lat);
      var N   = Math.max(12, Math.round(r * 52));
      for (var i = 0; i < N; i++) {
        var a = (i / N) * Math.PI * 2;
        pts.push([r * Math.cos(a), yv, r * Math.sin(a)]);
      }
    }

    // Full great-circle meridians every 20°
    // Parametric: x=cos(θ)·cos(lon), y=sin(θ), z=cos(θ)·sin(lon)
    // θ from 0 → 2π traces the complete vertical circle (both hemispheres)
    for (var lonD = 0; lonD < 180; lonD += 20) {
      var lon = lonD * Math.PI / 180;
      var M   = 52;
      for (var j = 0; j <= M; j++) {
        var th = (j / M) * Math.PI * 2;
        pts.push([Math.cos(th) * Math.cos(lon), Math.sin(th), Math.cos(th) * Math.sin(lon)]);
      }
    }

    return pts;
  }

  // Perfect cube — 12 edges
  function buildCube() {
    var C = [[-1,-1,-1],[1,-1,-1],[1,1,-1],[-1,1,-1],[-1,-1,1],[1,-1,1],[1,1,1],[-1,1,1]];
    var E = [[0,1],[1,2],[2,3],[3,0],[4,5],[5,6],[6,7],[7,4],[0,4],[1,5],[2,6],[3,7]];
    var pts = [];
    E.forEach(function (e) { pts = pts.concat(edgePts(C[e[0]], C[e[1]], 44)); });
    return pts;
  }

  // Clear 4-sided pyramid — apex up, base down
  function buildPyramid() {
    var apex = [0, -1.5, 0];
    var base = [[-1, 0.8,-1],[1, 0.8,-1],[1, 0.8,1],[-1, 0.8,1]];
    var pts  = [];
    [[0,1],[1,2],[2,3],[3,0]].forEach(function (e) {
      pts = pts.concat(edgePts(base[e[0]], base[e[1]], 44));
    });
    base.forEach(function (p) { pts = pts.concat(edgePts(p, apex, 44)); });
    pts = pts.concat(edgePts(base[0], base[2], 44));
    pts = pts.concat(edgePts(base[1], base[3], 44));
    return pts;
  }

  // Classic brilliant-cut diamond (see reference image):
  //   table   — flat octagonal top         (y = -1.20, small radius)
  //   girdle  — widest horizontal ring     (y =  0.10)
  //   culet   — single point at bottom     (y =  1.65)
  // Positive Y = down on canvas, negative Y = up on canvas.
  function buildDiamond() {
    var n       = 8;
    var tableR  = 0.44;   // table polygon radius (~44% of girdle)
    var girdleR = 1.0;
    var tableY  = -0.33;  // table sits at top (negative = up) — crown = 1/3 original
    var girdleY =  0.10;  // girdle slightly below centre
    var culetY  =  1.65;  // culet at bottom (positive = down)

    var table  = [];
    var girdle = [];
    var i;

    for (i = 0; i < n; i++) {
      var ag = (i / n) * Math.PI * 2;
      // Table vertices rotated 22.5° (π/n) so they sit between girdle vertices,
      // matching the kite-facet layout of a brilliant cut.
      var at = ag + Math.PI / n;
      girdle.push([girdleR * Math.cos(ag), girdleY, girdleR * Math.sin(ag)]);
      table.push( [tableR  * Math.cos(at), tableY,  tableR  * Math.sin(at)]);
    }

    var culet = [0, culetY, 0];
    var pts   = [];

    // ── Table (flat octagon) ──────────────────────────────
    for (i = 0; i < n; i++) {
      pts = pts.concat(edgePts(table[i], table[(i + 1) % n], 14));
    }
    // Table diagonals (all 4 cross-lines — asterisk pattern on the flat top)
    for (i = 0; i < n / 2; i++) {
      pts = pts.concat(edgePts(table[i], table[i + n / 2], 20));
    }

    // ── Crown (table → girdle) ────────────────────────────
    // Each table vertex fans out to its 2 nearest girdle vertices
    for (i = 0; i < n; i++) {
      pts = pts.concat(edgePts(table[i], girdle[i],           30));
      pts = pts.concat(edgePts(table[i], girdle[(i + 1) % n], 30));
    }

    // ── Girdle ring ───────────────────────────────────────
    for (i = 0; i < n; i++) {
      pts = pts.concat(edgePts(girdle[i], girdle[(i + 1) % n], 14));
    }

    // ── Pavilion (girdle → culet) ─────────────────────────
    for (i = 0; i < n; i++) {
      pts = pts.concat(edgePts(girdle[i], culet, 32));
    }
    // Pavilion cross lines (opposite girdle points through centre)
    for (i = 0; i < n / 2; i++) {
      pts = pts.concat(edgePts(girdle[i], girdle[i + n / 2], 24));
    }

    return pts;
  }

  // ── 3D rotation ───────────────────────────────────────────
  function rY(p, a) {
    var c = Math.cos(a), s = Math.sin(a);
    return [c * p[0] + s * p[2], p[1], -s * p[0] + c * p[2]];
  }
  function rX(p, a) {
    var c = Math.cos(a), s = Math.sin(a);
    return [p[0], c * p[1] - s * p[2], s * p[1] + c * p[2]];
  }

  // ── HeroCanvas ────────────────────────────────────────────
  function HeroCanvas(canvas) {
    this.canvas    = canvas;
    this.ctx       = canvas.getContext('2d');
    this.shape     = canvas.dataset.shape  || 'sphere';
    this.cxDefault = parseFloat(canvas.dataset.cx    || '0.5');
    this.cxRatio   = this.cxDefault;
    this.cyRatio   = parseFloat(canvas.dataset.cy    || '0.5');
    this.sScale    = S_SCALE * parseFloat(canvas.dataset.scale || '1');
    this.pts3d     = this._buildShape();
    this.dots      = [];
    this.rotY      = 0; this.rotX  = 0;
    this.tgtY      = 0; this.tgtX  = 0;
    this.autoY     = 0;
    this.mouseOver = false;
    this.mx        = 0; this.my    = 0;
    this.raf       = null;
    this.visible   = true;
    this._resize();
    this._bind();
    this._observe();
    this._tick();
  }

  HeroCanvas.prototype._buildShape = function () {
    switch (this.shape) {
      case 'cube':    return buildCube();
      case 'pyramid': return buildPyramid();
      case 'diamond': return buildDiamond();
      default:        return buildSphere();
    }
  };

  HeroCanvas.prototype._buildGrid = function () {
    var cols = Math.ceil(this.W / GRID) + 1;
    var rows = Math.ceil(this.H / GRID) + 1;
    this.dots = [];
    for (var r = 0; r < rows; r++) {
      for (var c = 0; c < cols; c++) {
        this.dots.push(new Dot(c * GRID, r * GRID));
      }
    }
  };

  HeroCanvas.prototype._resize = function () {
    var p = this.canvas.parentElement;
    this.W = p.offsetWidth  || window.innerWidth;
    this.H = p.offsetHeight || 480;
    this.canvas.width  = this.W;
    this.canvas.height = this.H;
    // Centre shape on mobile (single-column layout)
    this.cxRatio = this.W < 768 ? 0.5 : this.cxDefault;
    this._buildGrid();
  };

  HeroCanvas.prototype._bind = function () {
    var self = this;
    var hero = this.canvas.closest('section') || this.canvas.parentElement;

    // Track on window so mouse over shape particles (which sit on top) is captured too
    window.addEventListener('mousemove', function (e) {
      var r = hero.getBoundingClientRect();
      var x = e.clientX - r.left;
      var y = e.clientY - r.top;
      if (x >= 0 && x <= r.width && y >= 0 && y <= r.height) {
        self.mx = x; self.my = y; self.mouseOver = true;
      } else {
        self.mouseOver = false;
      }
    }, { passive: true });

    window.addEventListener('resize', function () { self._resize(); }, { passive: true });
  };

  HeroCanvas.prototype._observe = function () {
    if (!('IntersectionObserver' in window)) return;
    var self = this;
    new IntersectionObserver(function (entries) {
      self.visible = entries[0].isIntersecting;
      if (self.visible && !self.raf) self._tick();
    }, { threshold: 0.05 }).observe(this.canvas);
  };

  HeroCanvas.prototype._tick = function () {
    if (!this.visible) { this.raf = null; return; }
    var self = this;
    this.raf = requestAnimationFrame(function () { self._tick(); });
    this._draw();
  };

  HeroCanvas.prototype._draw = function () {
    this.autoY += A_ROT;
    if (this.mouseOver) {
      this.tgtY = ((this.mx / this.W) - 0.5) * M_ROT_MAX * 2;
      this.tgtX = ((this.my / this.H) - 0.5) * M_ROT_MAX;
    } else {
      this.tgtY  = this.autoY;
      this.tgtX += (0 - this.tgtX) * 0.03;
    }
    this.rotY += (this.tgtY - this.rotY) * ROT_LERP;
    this.rotX += (this.tgtX - this.rotX) * ROT_LERP;

    var ctx = this.ctx;
    var W = this.W, H = this.H;
    var cx  = W * this.cxRatio;
    var cy  = H * this.cyRatio;
    var sc  = this.sScale;
    var fov = sc * 3;

    ctx.clearRect(0, 0, W, H);

    // Grid
    for (var i = 0; i < this.dots.length; i++) {
      this.dots[i].tick(this.mx, this.my, this.mouseOver, cx, cy);
      this.dots[i].draw(ctx);
    }

    // Shape
    var proj = [];
    for (var j = 0; j < this.pts3d.length; j++) {
      var p = this.pts3d[j];
      var q = rY(p, this.rotY);
      q = rX(q, this.rotX);
      var qz = q[2] * sc + sc * 2.5;
      if (qz <= 0) continue;
      var s = fov / qz;
      proj.push({
        x: cx + q[0] * sc * s,
        y: cy + q[1] * sc * s,
        z: q[2],
        a: 0.28 + (q[2] + 1) * 0.36
      });
    }
    proj.sort(function (a, b) { return a.z - b.z; });

    for (var k = 0; k < proj.length; k++) {
      var pt = proj[k];
      var r  = 1.1 + (pt.z + 1) * 0.55;
      ctx.beginPath();
      ctx.arc(pt.x, pt.y, Math.max(0.7, r), 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(16,185,129,' + Math.min(1, pt.a).toFixed(3) + ')';
      ctx.fill();
    }
  };

  // ── Bootstrap ─────────────────────────────────────────────
  function init() {
    document.querySelectorAll('.hero-shape-canvas').forEach(function (el) {
      new HeroCanvas(el);
    });
  }

  if (document.readyState !== 'loading') { init(); }
  else { document.addEventListener('DOMContentLoaded', init); }

})();
