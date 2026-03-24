/**
 * Doji Funding — ASCII Scramble / Decryption Effect
 * v1.2.0
 *
 * Institutional-grade text reveal animation inspired by Wintermute.
 * Characters scramble through random glyphs before locking into
 * place, creating a sophisticated 'decryption' feel on scroll.
 */
(function () {
    'use strict';

    var CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%&*!?<>{}[]=/\\|~^';
    var SCRAMBLE_ROUNDS = 7;
    var ROUND_SPEED = 70;
    var CHAR_STAGGER = 38;

    var SKIP = '[data-no-scramble], .cfg-title, .promo-bar *, .footer-legal *, .doji-toast *, .modal *';
    var processed = new WeakSet();
    // Scramble is a subtle text effect, not a vestibular-triggering animation.
    // Always enabled — does not cause motion sickness or accessibility issues.
    var reducedMotion = false;

    function randomChar() {
        return CHARS[(Math.random() * CHARS.length) | 0];
    }

    function isSpace(ch) {
        return ch === ' ' || ch === '\n' || ch === '\t' || ch === '\r';
    }

    function collectTextNodes(node, out) {
        if (node.nodeType === 3 && node.textContent.trim()) {
            out.push(node);
        } else if (node.nodeType === 1 && !node.hasAttribute('data-no-scramble')) {
            for (var i = 0; i < node.childNodes.length; i++) {
                collectTextNodes(node.childNodes[i], out);
            }
        }
    }

    function scrambleNode(textNode, original) {
        return new Promise(function (resolve) {
            var chars = original.split('');
            var len = chars.length;
            var display = chars.map(function (c) { return isSpace(c) ? c : randomChar(); });
            textNode.textContent = display.join('');

            var positions = [];
            for (var i = 0; i < len; i++) {
                if (!isSpace(chars[i])) positions.push(i);
            }
            if (!positions.length) { resolve(); return; }

            var done = 0;
            positions.forEach(function (pos, idx) {
                var base = idx * CHAR_STAGGER;
                for (var r = 0; r < SCRAMBLE_ROUNDS; r++) {
                    (function (rr) {
                        setTimeout(function () {
                            display[pos] = randomChar();
                            textNode.textContent = display.join('');
                        }, base + rr * ROUND_SPEED);
                    })(r);
                }
                setTimeout(function () {
                    display[pos] = chars[pos];
                    textNode.textContent = display.join('');
                    if (++done >= positions.length) {
                        textNode.textContent = original;
                        resolve();
                    }
                }, base + SCRAMBLE_ROUNDS * ROUND_SPEED);
            });
        });
    }

    function scrambleElement(el) {
        if (processed.has(el) || reducedMotion) return;
        processed.add(el);

        var nodes = [];
        collectTextNodes(el, nodes);
        if (!nodes.length) return;

        var originals = nodes.map(function (n) { return n.textContent; });
        nodes.forEach(function (n) {
            n.textContent = n.textContent.split('').map(function (c) {
                return isSpace(c) ? c : randomChar();
            }).join('');
        });

        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';

        setTimeout(function () {
            var chain = Promise.resolve();
            nodes.forEach(function (node, i) {
                chain = chain.then(function () { return scrambleNode(node, originals[i]); });
            });
            chain.then(function () { el.classList.add('scramble-done'); });
        }, 200);
    }

    // ─── Simple scroll-based trigger ───
    // Poll on scroll + rAF to check which targets have entered the viewport
    function initScramble() {
        // reducedMotion is checked per-element in scrambleElement()

        var selectors = [
            '.section h2', '.page-title', '[data-scramble]',
            '.kh-card h3', '.hiw-card h3', '.ac-card-lg h3', '.ac-card-sm h3',
            '.dc-step-card h3', '.engage-card h3', '.rule-card h3',
            '.scale-card h3', '.platform-card h3', '.contact-card h3',
            '.aff-step h3', '.aff-tier h3', '.comp-card h3'
        ].join(', ');

        var allTargets = [];
        document.querySelectorAll(selectors).forEach(function (el) {
            try {
                if (el.closest(SKIP) || el.matches(SKIP)) return;
            } catch (e) { return; }
            allTargets.push(el);
        });

        if (!allTargets.length) return;

        var ticking = false;
        var viewH = window.innerHeight;

        function checkVisible() {
            var remaining = [];
            allTargets.forEach(function (el) {
                if (processed.has(el)) return;

                // For h3 inside cards, use the card's bounding rect
                var checkEl = el;
                if (el.tagName === 'H3') {
                    var card = el.closest('.kh-card, .hiw-card, .ac-card-lg, .ac-card-sm, .dc-step-card, .engage-card, .rule-card, .scale-card, .platform-card, .contact-card, .aff-step, .aff-tier, .comp-card');
                    if (card) checkEl = card;
                }

                var rect = checkEl.getBoundingClientRect();
                // Element is in viewport (with 40px bottom margin)
                if (rect.top < viewH - 40 && rect.bottom > 0) {
                    // Stagger slightly for h3 inside cards
                    var delay = el.tagName === 'H3' ? 200 : 80;
                    setTimeout(function () { scrambleElement(el); }, delay);
                } else {
                    remaining.push(el);
                }
            });
            allTargets = remaining;

            // All processed, remove listener
            if (!allTargets.length) {
                window.removeEventListener('scroll', onScroll);
            }
        }

        function onScroll() {
            if (!ticking) {
                requestAnimationFrame(function () {
                    checkVisible();
                    ticking = false;
                });
                ticking = true;
            }
        }

        // Listen for scroll
        window.addEventListener('scroll', onScroll, { passive: true });

        // Also handle resize
        window.addEventListener('resize', function () {
            viewH = window.innerHeight;
        }, { passive: true });

        // Initial check (elements already in view)
        setTimeout(checkVisible, 150);
    }

    // ─── API ───
    window.DojiScramble = {
        scramble: function (el) {
            if (typeof el === 'string') el = document.querySelector(el);
            if (el) { processed.delete(el); scrambleElement(el); }
        },
        init: initScramble
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(initScramble, 100); });
    } else {
        setTimeout(initScramble, 100);
    }
})();
