/**
 * Weight Wave — Variable font weight animation on hover
 * Vanilla JS reimplementation of motion-core's WeightWave component.
 * Requires a variable font with a 'wght' axis (e.g. Nippo-Variable).
 *
 * Usage (HTML):
 *   <h1 data-weight-wave
 *       data-ww-base-weight="300"
 *       data-ww-hover-weight="800"
 *       data-ww-radius="5">
 *     Your text here
 *   </h1>
 */
(function () {
  'use strict';

  var DEFAULTS = {
    baseWeight:         300,
    hoverWeight:        800,
    influenceRadius:    5,
    falloffPower:       1.5,
    transitionDuration: '0.55s',
    transitionEase:     'cubic-bezier(0.16, 1, 0.3, 1)',
  };

  /**
   * Walk the DOM tree of `node`, replacing text node characters with
   * individual <span data-ww> elements. Skips `.seo-tag` subtrees.
   */
  function splitIntoChars(node, chars) {
    var children = Array.from(node.childNodes);
    for (var i = 0; i < children.length; i++) {
      var child = children[i];

      if (child.nodeType === Node.TEXT_NODE) {
        var text = child.textContent;
        if (!text) continue;
        var frag = document.createDocumentFragment();
        for (var c = 0; c < text.length; c++) {
          var ch = text[c];
          // Preserve whitespace / line-breaks as plain text nodes
          if (ch === ' ' || ch === '\n' || ch === '\t' || ch === '\r') {
            frag.appendChild(document.createTextNode(ch));
          } else {
            var span = document.createElement('span');
            span.setAttribute('data-ww', '');
            span.textContent = ch;
            chars.push(span);
            frag.appendChild(span);
          }
        }
        node.replaceChild(frag, child);

      } else if (child.nodeType === Node.ELEMENT_NODE) {
        // Skip decorative / SEO-only elements and text-loop targets
        if (child.classList.contains('seo-tag')) continue;
        if (child.hasAttribute('data-no-ww')) continue;
        splitIntoChars(child, chars);
      }
    }
  }

  /**
   * Initialise Weight Wave on a single element.
   * @param {HTMLElement} el
   * @param {object}      options
   */
  function initWeightWave(el, options) {
    var cfg = Object.assign({}, DEFAULTS, options || {});
    var transition =
      'font-variation-settings ' +
      cfg.transitionDuration + ' ' +
      cfg.transitionEase;

    // Switch to the variable font
    el.style.fontFamily = "'Nippo', sans-serif";

    // Split text into per-character spans
    var chars = [];
    splitIntoChars(el, chars);

    if (!chars.length) return;

    // Prime every char at base weight
    chars.forEach(function (span) {
      span.style.transition            = transition;
      span.style.fontVariationSettings = "'wght' " + cfg.baseWeight;
    });

    // Mousemove — find closest char, spread weight with falloff
    el.addEventListener('mousemove', function (e) {
      var mx = e.clientX;
      var my = e.clientY;

      // Identify the character closest to the cursor
      var closestIdx  = 0;
      var closestDist = Infinity;
      chars.forEach(function (span, i) {
        var r  = span.getBoundingClientRect();
        var cx = r.left + r.width  / 2;
        var cy = r.top  + r.height / 2;
        var d  = Math.hypot(mx - cx, my - cy);
        if (d < closestDist) {
          closestDist = d;
          closestIdx  = i;
        }
      });

      // Animate each char proportionally to its distance from the hot char
      chars.forEach(function (span, i) {
        var dist   = Math.abs(i - closestIdx);
        var t      = Math.max(0, 1 - Math.pow(dist / cfg.influenceRadius, cfg.falloffPower));
        var weight = Math.round(cfg.baseWeight + (cfg.hoverWeight - cfg.baseWeight) * t);
        span.style.fontVariationSettings = "'wght' " + weight;
      });
    });

    // Mouseleave — return to base weight
    el.addEventListener('mouseleave', function () {
      chars.forEach(function (span) {
        span.style.fontVariationSettings = "'wght' " + cfg.baseWeight;
      });
    });
  }

  // Auto-init all [data-weight-wave] elements when the DOM is ready
  function autoInit() {
    document.querySelectorAll('[data-weight-wave]').forEach(function (el) {
      var opts = {};
      if (el.dataset.wwBaseWeight)  opts.baseWeight        = Number(el.dataset.wwBaseWeight);
      if (el.dataset.wwHoverWeight) opts.hoverWeight       = Number(el.dataset.wwHoverWeight);
      if (el.dataset.wwRadius)      opts.influenceRadius   = Number(el.dataset.wwRadius);
      if (el.dataset.wwFalloff)     opts.falloffPower      = Number(el.dataset.wwFalloff);
      initWeightWave(el, opts);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }

  window.WeightWave = { init: initWeightWave };
})();
