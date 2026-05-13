/**
 * Doji Funding — Onboarding Flow
 * Drives the welcome modal (5 steps) and the overview checklist.
 */
const Onboarding = (function () {

    var currentStep = 0;
    var TOTAL_STEPS = 5;

    function init() {
        // Mark modal as seen server-side
        post('modal_seen');
        showStep(0);

        var overlay = document.getElementById('onbOverlay');
        if (overlay) {
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.25s ease';
            requestAnimationFrame(function () {
                overlay.style.opacity = '1';
            });
        }
    }

    function showStep(n) {
        currentStep = n;

        document.querySelectorAll('.onb-step-panel').forEach(function (p, i) {
            p.style.display = i === n ? '' : 'none';
        });

        document.querySelectorAll('.onb-dot').forEach(function (d, i) {
            d.classList.toggle('active', i === n);
            d.classList.toggle('done',   i < n);
        });

        var label = document.getElementById('onbStepLabel');
        if (label) label.textContent = 'ÉTAPE ' + (n + 1) + ' / ' + TOTAL_STEPS;

        var back = document.getElementById('onbBack');
        if (back) back.style.visibility = n === 0 ? 'hidden' : 'visible';

        var next = document.getElementById('onbNext');
        if (next) next.textContent = n === TOTAL_STEPS - 1 ? 'ACCÉDER AU DASHBOARD' : 'SUIVANT →';
    }

    function next() {
        if (currentStep < TOTAL_STEPS - 1) {
            showStep(currentStep + 1);
        } else {
            close();
        }
    }

    function back() {
        if (currentStep > 0) showStep(currentStep - 1);
    }

    function skip() {
        close();
    }

    function close() {
        var overlay = document.getElementById('onbOverlay');
        if (!overlay) return;
        overlay.style.opacity = '0';
        setTimeout(function () { overlay.style.display = 'none'; }, 250);
    }

    function dismissChecklist() {
        post('dismiss');
        var cl = document.getElementById('onbChecklist');
        if (!cl) return;
        cl.style.transition = 'opacity 0.25s ease';
        cl.style.opacity = '0';
        setTimeout(function () { cl.style.display = 'none'; }, 250);
    }

    function post(action) {
        var body = 'action=' + encodeURIComponent(action);
        if (window.DOJI_CONFIG && window.DOJI_CONFIG.csrfToken) {
            body += '&csrf=' + encodeURIComponent(window.DOJI_CONFIG.csrfToken);
        }
        fetch('api/onboarding.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body,
        }).catch(function () {});
    }

    return { init: init, next: next, back: back, skip: skip, dismissChecklist: dismissChecklist };
})();
