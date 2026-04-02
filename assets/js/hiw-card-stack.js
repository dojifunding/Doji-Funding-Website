/**
 * Doji Funding — How It Works · Card Stack
 *
 * Mobile  (≤ 768 px):
 *   Each card gets a JS-injected scroll-space wrapper (.hiw-card-wrap).
 *   The wrappers give each card ~55 vh of personal scroll territory so
 *   the user sees each card "featured" before the next one stacks on top.
 *   Cards are position:sticky (all at the same top), with ascending z-index
 *   so later cards slide over earlier ones as you scroll.
 *   A RAF-throttled scroll listener scales down buried cards (scaleFactor 0.05)
 *   and adds .hiw-card--active when a card is the topmost visible one.
 *
 * Desktop (> 768 px):
 *   Staggered scroll-reveal (fade + slide) via IntersectionObserver.
 *   Hover group: hovered card lifts + glows, siblings dim + shrink.
 */
(function () {
  'use strict';

  var MOBILE_BP    = 768;
  var STICKY_TOP   = 72;     // px — matches CSS .hiw-card top value
  var SCALE_FACTOR = 0.05;
  var OFFSET_PX    = 8;

  // ── Helpers ───────────────────────────────────────────────────────────────

  function getCards() {
    return Array.from(document.querySelectorAll('.hiw-grid .hiw-card'));
  }

  function isSticky(card) {
    return card.getBoundingClientRect().top <= STICKY_TOP + 2;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MOBILE — sticky card stack
  // ═══════════════════════════════════════════════════════════════════════════

  function wrapCards(cards) {
    // Inject a .hiw-card-wrap scroll-spacer around every card.
    // CSS gives these wrappers padding-bottom: 55vh so the user can
    // "sit" on each card before the next one scrolls into position.
    cards.forEach(function (card, i) {
      var wrap = document.createElement('div');
      wrap.className = 'hiw-card-wrap';
      if (i === cards.length - 1) wrap.classList.add('hiw-card-wrap--last');
      card.parentNode.insertBefore(wrap, card);
      wrap.appendChild(card);
    });
  }

  function updateStack(cards) {
    var n = cards.length;

    // For every card, count how many LATER cards are already in sticky slot.
    // Those later cards have higher z-index and are visually on top.
    cards.forEach(function (card, i) {
      var cardsOnTop = 0;
      for (var j = i + 1; j < n; j++) {
        if (isSticky(cards[j])) cardsOnTop++;
      }

      if (cardsOnTop > 0) {
        var scale = Math.max(0.82, 1 - cardsOnTop * SCALE_FACTOR);
        var y     = -(cardsOnTop * OFFSET_PX);
        card.style.transform = 'scale(' + scale + ') translateY(' + y + 'px)';
        card.classList.add('hiw-card--stacked');
        card.classList.remove('hiw-card--active');
      } else {
        card.style.transform = '';
        card.classList.remove('hiw-card--stacked');
        // Active = this card is currently in its sticky slot and nothing is on top
        if (isSticky(card)) {
          card.classList.add('hiw-card--active');
        } else {
          card.classList.remove('hiw-card--active');
        }
      }
    });
  }

  function initMobile() {
    var cards = getCards();
    if (!cards.length) return;

    wrapCards(cards);

    var ticking = false;

    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        updateStack(cards);
        ticking = false;
      });
    }, { passive: true });

    updateStack(cards);
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // DESKTOP — staggered reveal + hover group
  // ═══════════════════════════════════════════════════════════════════════════

  function initDesktop() {
    var cards = getCards();
    var grid  = document.querySelector('.hiw-grid');
    if (!cards.length || !grid) return;

    var revealed = false;

    // Hidden initial state
    cards.forEach(function (card) {
      card.style.opacity   = '0';
      card.style.transform = 'translateY(28px)';
      card.style.transition =
        'opacity 0.55s ease, ' +
        'transform 0.55s cubic-bezier(0.16,1,0.3,1), ' +
        'box-shadow 0.35s ease, ' +
        'border-color 0.35s ease';
    });

    // Scroll reveal
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries, obs) {
        if (!entries[0].isIntersecting) return;
        revealed = true;
        cards.forEach(function (card, i) {
          setTimeout(function () {
            card.style.opacity   = '1';
            card.style.transform = 'translateY(0)';
          }, i * 110);
        });
        obs.disconnect();
      }, { threshold: 0.15 }).observe(grid);
    } else {
      revealed = true;
      cards.forEach(function (c) {
        c.style.opacity = '1'; c.style.transform = 'translateY(0)';
      });
    }

    // Hover group: lift focused card, dim + shrink siblings
    cards.forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        if (!revealed) return;
        cards.forEach(function (c) {
          if (c === card) {
            c.style.transform   = 'translateY(-7px) scale(1.025)';
            c.style.opacity     = '1';
            c.style.boxShadow   = '0 24px 52px rgba(16,185,129,0.13)';
            c.style.borderColor = 'rgba(16,185,129,0.22)';
            c.style.zIndex      = '2';
          } else {
            c.style.transform   = 'translateY(0) scale(0.97)';
            c.style.opacity     = '0.5';
            c.style.boxShadow   = '';
            c.style.borderColor = '';
            c.style.zIndex      = '';
          }
        });
      });

      card.addEventListener('mouseleave', function () {
        cards.forEach(function (c) {
          c.style.transform   = 'translateY(0) scale(1)';
          c.style.opacity     = '1';
          c.style.boxShadow   = '';
          c.style.borderColor = '';
          c.style.zIndex      = '';
        });
      });
    });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // Bootstrap
  // ═══════════════════════════════════════════════════════════════════════════

  function init() {
    if (!document.querySelector('.hiw-grid')) return;
    if (window.innerWidth <= MOBILE_BP) {
      initMobile();
    } else {
      initDesktop();
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
