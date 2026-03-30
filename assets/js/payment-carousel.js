/**
 * Doji Funding — Payment Logo Carousel
 *
 * Multi-column display where each column independently cycles through
 * payment logos with vertical-motion + fade transitions.
 * Vanilla JS reimplementation of motion-core's LogoCarousel component.
 *
 * columnCount  : 6   (logos visible simultaneously)
 * cycleInterval: 2000 ms
 */
(function () {
  'use strict';

  var COLUMNS  = 6;
  var INTERVAL = 2000;   // ms between swaps per column
  var DURATION = 380;    // ms for the CSS transition

  var LOGOS = [
    { src: 'assets/img/payments/visa.svg',               alt: 'Visa'          },
    { src: 'assets/img/payments/mastercard.svg',         alt: 'Mastercard'    },
    { src: 'assets/img/payments/apple.svg',              alt: 'Apple Pay'     },
    { src: 'assets/img/payments/google-pay.svg',         alt: 'Google Pay'    },
    { src: 'assets/img/payments/alipay.svg',             alt: 'Alipay'        },
    { src: 'assets/img/payments/amex.svg',               alt: 'Amex'          },
    { src: 'assets/img/payments/bitcoin-btc-logo.svg',   alt: 'Bitcoin'       },
    { src: 'assets/img/payments/ethereum-eth-logo.svg',  alt: 'Ethereum'      },
    { src: 'assets/img/payments/tether-usdt-logo.svg',   alt: 'Tether USDT'   },
    { src: 'assets/img/payments/usd-coin-usdc-logo.svg', alt: 'USD Coin'      },
    { src: 'assets/img/payments/litecoin-ltc-logo.svg',  alt: 'Litecoin'      },
    { src: 'assets/img/payments/solana-sol-logo.svg',    alt: 'Solana'        },
  ];

  // ── DOM helpers ────────────────────────────────────────────────────────────

  function makeLogoEl(logo) {
    var a   = document.createElement('a');
    a.href  = 'faq.php#billing';
    a.title = logo.alt;
    a.className = 'pc-logo';

    var img = document.createElement('img');
    img.src    = logo.src;
    img.alt    = logo.alt;
    img.height = 28;

    a.appendChild(img);
    return a;
  }

  // ── Per-column swap ────────────────────────────────────────────────────────

  function cycleColumn(state) {
    // Next logo index for this column (step by COLUMNS so columns stay distinct)
    state.idx = (state.idx + COLUMNS) % LOGOS.length;

    var next = makeLogoEl(LOGOS[state.idx]);

    // Start below, invisible
    next.style.transform  = 'translateY(100%)';
    next.style.opacity    = '0';
    next.style.transition = 'none';
    state.col.appendChild(next);

    // Exit current
    var curr = state.current;
    curr.style.transition =
      'transform ' + DURATION + 'ms ease, opacity ' + DURATION + 'ms ease';
    curr.style.transform = 'translateY(-100%)';
    curr.style.opacity   = '0';

    // Enter next (double rAF to commit the "none" frame first)
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        next.style.transition =
          'transform ' + DURATION + 'ms cubic-bezier(0.16,1,0.3,1), ' +
          'opacity '   + DURATION + 'ms ease';
        next.style.transform = 'translateY(0)';
        next.style.opacity   = '1';
      });
    });

    // Clean up the exited element
    setTimeout(function () {
      if (curr.parentNode) curr.parentNode.removeChild(curr);
      state.current = next;
    }, DURATION + 50);
  }

  // ── Init ───────────────────────────────────────────────────────────────────

  function init() {
    var container = document.getElementById('paymentCarousel');
    if (!container) return;

    var states = [];

    for (var i = 0; i < COLUMNS; i++) {
      var col = document.createElement('div');
      col.className = 'pc-col';

      var first = makeLogoEl(LOGOS[i % LOGOS.length]);
      col.appendChild(first);
      container.appendChild(col);

      states.push({ col: col, idx: i % LOGOS.length, current: first });
    }

    // Stagger each column's cycle so they don't all swap simultaneously
    var stagger = Math.floor(INTERVAL / COLUMNS);

    states.forEach(function (state, i) {
      setTimeout(function () {
        setInterval(function () { cycleColumn(state); }, INTERVAL);
      }, i * stagger);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
