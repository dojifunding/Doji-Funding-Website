/**
 * Doji Funding — Split Reveal
 *
 * Masked text reveal animation triggered on scroll.
 * Vanilla JS reimplementation of motion-core's SplitReveal component.
 *
 * Modes  : "words" (default) | "chars"
 * Timing : words → 0.6s / 0.06s stagger
 *          chars → 0.4s / 0.008s stagger
 *
 * HTML usage (optional overrides via data attributes):
 *   <h2 data-sr-mode="words" data-sr-delay="0.1">…</h2>
 */
(function () {
  'use strict';

  var EASE = 'cubic-bezier(0.16, 1, 0.3, 1)'; // ≈ power3.out

  var MODE_CFG = {
    words: { duration: 0.6, stagger: 0.06  },
    chars: { duration: 0.4, stagger: 0.008 }
  };

  // Elements to target (same selector list as the old scramble.js)
  var SELECTORS = [
    '.section h2', '.page-title', '[data-split-reveal]',
    '.kh-card h3', '.hiw-card h3', '.ac-card-lg h3', '.ac-card-sm h3',
    '.dc-step-card h3', '.engage-card h3', '.rule-card h3',
    '.scale-card h3', '.platform-card h3', '.contact-card h3',
    '.aff-step h3', '.aff-tier h3', '.comp-card h3'
  ].join(', ');

  var SKIP = '.cfg-title, .promo-bar *, .footer-legal *, .doji-toast *, .modal *';

  var processed = new WeakSet();

  // ─── Text splitting ───────────────────────────────────────────────────────

  /**
   * Walk `node` recursively and wrap each word (or char) in a mask+inner pair.
   * Returns an array of the inner <span> elements that will animate.
   */
  function splitNode(node, mode, units) {
    var children = Array.from(node.childNodes);
    for (var i = 0; i < children.length; i++) {
      var child = children[i];

      if (child.nodeType === Node.TEXT_NODE) {
        var raw = child.textContent;
        if (!raw) continue;

        // Tokenise: keep whitespace runs as plain text, animate the rest
        var tokens = mode === 'chars'
          ? raw.split('')
          : raw.split(/(\s+)/);

        var frag = document.createDocumentFragment();
        for (var t = 0; t < tokens.length; t++) {
          var tok = tokens[t];
          if (!tok) continue;

          // Pure whitespace — preserve as-is
          if (/^\s+$/.test(tok)) {
            frag.appendChild(document.createTextNode(tok));
            continue;
          }

          // Overflow mask (inline, clips the sliding inner span)
          var mask = document.createElement('span');
          mask.style.cssText = 'display:inline-block;overflow:hidden;vertical-align:bottom;';

          // Inner element that actually moves
          var inner = document.createElement('span');
          inner.setAttribute('data-sr-unit', '');
          inner.style.display = 'inline-block';
          inner.textContent = tok;

          mask.appendChild(inner);
          frag.appendChild(mask);
          units.push(inner);
        }
        node.replaceChild(frag, child);

      } else if (child.nodeType === Node.ELEMENT_NODE) {
        // Skip decorative / utility elements
        if (child.classList.contains('seo-tag')) continue;
        if (child.hasAttribute('data-no-split'))  continue;
        splitNode(child, mode, units);
      }
    }
  }

  // ─── Per-element init ─────────────────────────────────────────────────────

  function prepare(el) {
    if (processed.has(el)) return;
    processed.add(el);

    var mode  = el.dataset.srMode  || 'words';
    var delay = parseFloat(el.dataset.srDelay || 0);
    var cfg   = MODE_CFG[mode] || MODE_CFG.words;

    var units = [];
    splitNode(el, mode, units);
    if (!units.length) return;

    // Set initial hidden position for each unit with staggered transition-delay
    units.forEach(function (unit, i) {
      var staggerDelay = delay + i * cfg.stagger;
      unit.style.transform  = 'translateY(110%)';
      unit.style.transition =
        'transform ' + cfg.duration + 's ' + EASE + ' ' + staggerDelay + 's';
    });

    el.setAttribute('data-sr-ready', '');
  }

  function reveal(el) {
    el.querySelectorAll('[data-sr-unit]').forEach(function (unit) {
      unit.style.transform = 'translateY(0)';
    });
    el.classList.add('split-reveal-done');
  }

  // ─── Scroll observation ───────────────────────────────────────────────────

  function initAll() {
    var targets = [];

    document.querySelectorAll(SELECTORS).forEach(function (el) {
      try {
        if (el.closest(SKIP) || el.matches(SKIP)) return;
      } catch (e) { return; }
      prepare(el);
      if (el.hasAttribute('data-sr-ready')) targets.push(el);
    });

    if (!targets.length) return;

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          // Small extra stagger for h3s inside cards (mirrors old scramble behaviour)
          var delay = entry.target.tagName === 'H3' ? 150 : 0;
          var el = entry.target;
          setTimeout(function () { reveal(el); }, delay);
          io.unobserve(el);
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

      targets.forEach(function (el) { io.observe(el); });
    } else {
      // Fallback: reveal everything immediately
      targets.forEach(reveal);
    }
  }

  // ─── Public API ───────────────────────────────────────────────────────────

  window.DojiSplitReveal = { init: initAll, reveal: reveal };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { setTimeout(initAll, 100); });
  } else {
    setTimeout(initAll, 100);
  }
})();
