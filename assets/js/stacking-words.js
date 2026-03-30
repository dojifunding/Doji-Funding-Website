/**
 * Doji Funding — Stacking Words
 *
 * Scroll-scrubbed text reveal: words travel in from the right,
 * tied to scroll position with staggered timing.
 * Vanilla JS reimplementation of motion-core's StackingWords component.
 *
 * Props mirrored:
 *   start   → element top reaches 90 % of viewport  ("top 90%")
 *   end     → element top reaches 30 % of viewport  ("top 30%")
 *   stagger → 0.21 (fraction of total scroll range per word offset)
 *   ease    → power3.out
 */
(function () {
  'use strict';

  // ─── Config ───────────────────────────────────────────────────────────────

  var START_VH   = 0.9;   // animation begins when el.top = 90 % of viewport
  var END_VH     = 0.3;   // animation ends   when el.top = 30 % of viewport
  var STAGGER    = 0.21;  // word-offset fraction within the scroll window
  var TRAVEL_PX  = 48;    // translateX start offset (px)

  // Same selector list as the previous scramble / split-reveal
  var SELECTORS = [
    '.section h2', '.page-title', '[data-stacking-words]',
    '.kh-card h3', '.hiw-card h3', '.ac-card-lg h3', '.ac-card-sm h3',
    '.dc-step-card h3', '.engage-card h3', '.rule-card h3',
    '.scale-card h3', '.platform-card h3', '.contact-card h3',
    '.aff-step h3', '.aff-tier h3', '.comp-card h3'
  ].join(', ');

  var SKIP = '.cfg-title, .promo-bar *, .footer-legal *, .doji-toast *, .modal *';

  // ─── Easing ───────────────────────────────────────────────────────────────

  function power3Out(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  function clamp01(v) {
    return v < 0 ? 0 : v > 1 ? 1 : v;
  }

  // ─── Text splitting ───────────────────────────────────────────────────────

  /**
   * Walk `node` recursively and wrap every word in a plain <span>.
   * Whitespace is preserved as text nodes. Skips .seo-tag and [data-no-split].
   */
  function splitNode(node, units) {
    var children = Array.from(node.childNodes);
    for (var i = 0; i < children.length; i++) {
      var child = children[i];

      if (child.nodeType === Node.TEXT_NODE) {
        var raw = child.textContent;
        if (!raw) continue;

        var tokens = raw.split(/(\s+)/);
        var frag   = document.createDocumentFragment();

        for (var t = 0; t < tokens.length; t++) {
          var tok = tokens[t];
          if (!tok) continue;

          if (/^\s+$/.test(tok)) {
            frag.appendChild(document.createTextNode(tok));
            continue;
          }

          var span = document.createElement('span');
          span.setAttribute('data-sw-word', '');
          span.style.display    = 'inline-block';
          span.style.willChange = 'transform, opacity';
          span.textContent      = tok;
          frag.appendChild(span);
          units.push(span);
        }
        node.replaceChild(frag, child);

      } else if (child.nodeType === Node.ELEMENT_NODE) {
        if (child.classList.contains('seo-tag'))       continue;
        if (child.hasAttribute('data-no-split'))       continue;
        splitNode(child, units);
      }
    }
  }

  // ─── Component state ──────────────────────────────────────────────────────

  var components  = [];   // { el, words, n, totalSlots }
  var processed   = new WeakSet();

  function initElement(el) {
    if (processed.has(el)) return;
    processed.add(el);

    var words = [];
    splitNode(el, words);
    if (!words.length) return;

    var n          = words.length;
    var totalSlots = 1 + STAGGER * (n - 1);

    // Initial state: invisible, shifted right
    words.forEach(function (w) {
      w.style.opacity   = '0';
      w.style.transform = 'translateX(' + TRAVEL_PX + 'px)';
    });

    components.push({ el: el, words: words, n: n, totalSlots: totalSlots });
  }

  // ─── Scroll update ────────────────────────────────────────────────────────

  function updateAll() {
    var viewH = window.innerHeight;

    for (var c = 0; c < components.length; c++) {
      var comp  = components[c];
      var elTop = comp.el.getBoundingClientRect().top;

      // progress: 0 → element top at START_VH × viewH from top
      //           1 → element top at END_VH   × viewH from top
      var range    = (START_VH - END_VH) * viewH;
      var progress = clamp01((viewH * START_VH - elTop) / range);

      var n          = comp.n;
      var totalSlots = comp.totalSlots;

      for (var i = 0; i < n; i++) {
        var wordStart = (i * STAGGER)       / totalSlots;
        var wordEnd   = (i * STAGGER + 1)   / totalSlots;
        var local     = clamp01((progress - wordStart) / (wordEnd - wordStart));
        var eased     = power3Out(local);

        var word           = comp.words[i];
        word.style.opacity = eased;
        word.style.transform =
          'translateX(' + (TRAVEL_PX * (1 - eased)) + 'px)';
      }
    }
  }

  // RAF-throttled scroll + resize handler
  var ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      updateAll();
      ticking = false;
    });
  }

  // ─── Bootstrap ────────────────────────────────────────────────────────────

  function initAll() {
    document.querySelectorAll(SELECTORS).forEach(function (el) {
      try {
        if (el.closest(SKIP) || el.matches(SKIP)) return;
      } catch (e) { return; }
      initElement(el);
    });

    if (!components.length) return;

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });

    // Run once for elements already in view
    updateAll();
  }

  window.DojiStackingWords = { init: initAll, update: updateAll };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { setTimeout(initAll, 100); });
  } else {
    setTimeout(initAll, 100);
  }
})();
