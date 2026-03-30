/**
 * Doji Funding — Text Loop
 *
 * Cycles through words with blurred vertical transitions.
 * Vanilla JS reimplementation of motion-core's TextLoop component.
 *
 * Targets:  #textLoopWord  (footer headline)
 *           #heroLoopWord  (hero H1, home page only)
 *
 * Words cycle: Way → Freedom → Setup → Rules → Edge → Terms → Style → Pace
 * Interval: 2200ms  |  Transition: 450ms blur + Y
 */
(function () {
  'use strict';

  var WORDS    = ['Way', 'Freedom', 'Setup', 'Rules', 'Edge', 'Terms', 'Style', 'Pace'];
  var INTERVAL = 2200;
  var DURATION = 450;

  /**
   * Attach the loop animation to a single element.
   * @param {HTMLElement} el
   */
  function attachLoop(el) {
    var idx = 0; // 'Way' is already rendered

    function transition() {
      // Exit: blur + slide up
      el.style.transition =
        'opacity '   + DURATION + 'ms ease, ' +
        'filter '    + DURATION + 'ms ease, ' +
        'transform ' + DURATION + 'ms cubic-bezier(0.4, 0, 0.2, 1)';
      el.style.opacity   = '0';
      el.style.filter    = 'blur(8px)';
      el.style.transform = 'translateY(-18px)';

      setTimeout(function () {
        idx = (idx + 1) % WORDS.length;
        el.textContent = WORDS[idx];

        // Reset to "enter from below" — no transition for the reposition
        el.style.transition = 'none';
        el.style.opacity    = '0';
        el.style.filter     = 'blur(8px)';
        el.style.transform  = 'translateY(18px)';

        // Double rAF so the browser commits the "none" frame before re-animating
        requestAnimationFrame(function () {
          requestAnimationFrame(function () {
            el.style.transition =
              'opacity '   + DURATION + 'ms ease, ' +
              'filter '    + DURATION + 'ms ease, ' +
              'transform ' + DURATION + 'ms cubic-bezier(0.16, 1, 0.3, 1)';
            el.style.opacity   = '1';
            el.style.filter    = 'blur(0px)';
            el.style.transform = 'translateY(0)';
          });
        });
      }, DURATION);
    }

    setInterval(transition, INTERVAL);
  }

  function init() {
    // Footer headline loop (all pages)
    var footer = document.getElementById('textLoopWord');
    if (footer) attachLoop(footer);

    // Hero H1 loop (home page only)
    var hero = document.getElementById('heroLoopWord');
    if (hero) attachLoop(hero);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
