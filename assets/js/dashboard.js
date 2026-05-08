/**
 * Doji Funding — Dashboard Module
 * Tab navigation, form submissions, filters, and clipboard utils.
 */

/* ── Wallet tab pagination ── */
(function() {
    var PAGE_SIZE = 8;

    function initPagination(pagEl) {
        var listId = pagEl.getAttribute('data-list');
        var list   = document.getElementById(listId);
        if (!list) return;

        var rows  = list.querySelectorAll('.wlt-tx-row');
        var total = rows.length;
        if (total <= PAGE_SIZE) { pagEl.style.display = 'none'; return; }

        var page  = 0;
        var pages = Math.ceil(total / PAGE_SIZE);
        var info  = pagEl.querySelector('.wlt-pg-info');
        var prev  = pagEl.querySelector('[data-dir="-1"]');
        var next  = pagEl.querySelector('[data-dir="1"]');

        function render() {
            var start = page * PAGE_SIZE;
            var end   = start + PAGE_SIZE;
            rows.forEach(function(r, i) {
                r.style.display = (i >= start && i < end) ? '' : 'none';
            });
            if (info) info.textContent = 'PAGE ' + (page + 1) + ' / ' + pages;
            if (prev) prev.disabled = (page === 0);
            if (next) next.disabled = (page >= pages - 1);
        }

        prev.addEventListener('click', function() { if (page > 0) { page--; render(); } });
        next.addEventListener('click', function() { if (page < pages - 1) { page++; render(); } });
        render();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.wlt-pagination').forEach(initPagination);
    });
}());

const Dashboard = (function() {
    'use strict';

    // ─── Tab titles ───
    const TAB_TITLES = {
        overview:     'DASHBOARD',
        challenges:   'CHALLENGES',
        configurator: 'CONFIGURATOR',
        wallet:       'WALLET',
        payouts:      'PAYOUTS',
        statistics:   'STATISTICS',
        competitions: 'COMPETITIONS',
        leaderboard:  'LEADERBOARD',
        certificates: 'CERTIFICATES',
        calendar:     'CALENDAR',
        affiliate:    'AFFILIATE',
        testimonials: 'TESTIMONIALS',
        support:      'SUPPORT',
        settings:     'PROFILE',
    };

    // ─── Profile sub-section titles ───
    var SUBSECTION_TITLES = {
        profile:      'PROFILE',
        verification: 'ACCOUNT VERIFICATION',
        security:     'SECURITY',
        bank:         'BANK ACCOUNTS',
        cards:        'CREDIT CARDS',
        crypto:       'CRYPTO WALLETS',
        payments:     'PAYMENT HISTORY',
        discord:      'DISCORD',
        suggestions:  'FEATURE SUGGESTIONS',
        preferences:  'PREFERENCES',
    };

    // ─── Show a single profile sub-section ───
    function showProfileSection(section) {
        document.querySelectorAll('.dash-psection').forEach(function(el) {
            el.classList.remove('psec-active');
        });
        var target = document.getElementById('psec-' + section);
        if (target) target.classList.add('psec-active');

        var titleEl = document.getElementById('dashPageTitle');
        if (titleEl) titleEl.textContent = SUBSECTION_TITLES[section] || 'PROFILE';

        document.querySelectorAll('.dash-nav-sub-item').forEach(function(i) { i.classList.remove('active'); });
        var activeItem = document.querySelector('.dash-nav-sub-item[data-section="' + section + '"]');
        if (activeItem) activeItem.classList.add('active');
    }

    // ─── Reset: hide all profile sections, clear sub-item active state ───
    function resetProfileSections() {
        document.querySelectorAll('.dash-psection').forEach(function(el) {
            el.classList.remove('psec-active');
        });
        document.querySelectorAll('.dash-nav-sub-item').forEach(function(i) { i.classList.remove('active'); });
    }

    // ─── Tab switching ───
    function switchTab(tabName) {
        // Update content tabs
        document.querySelectorAll('.dash-tab').forEach(t => t.classList.remove('active'));
        const tab = document.getElementById('tab-' + tabName);
        if (tab) tab.classList.add('active');

        // Update sidebar nav
        document.querySelectorAll('.dash-nav-item').forEach(n => {
            n.classList.toggle('active', n.dataset.tab === tabName);
        });

        // Update mobile tabs
        document.querySelectorAll('.dash-mobile-tab').forEach(n => {
            n.classList.toggle('active', n.dataset.tab === tabName);
        });

        // Update topbar page title
        const titleEl = document.getElementById('dashPageTitle');
        if (titleEl) titleEl.textContent = TAB_TITLES[tabName] || tabName;

        // Show greeting only on overview tab
        var greetingEl = document.getElementById('dashGreeting');
        if (greetingEl) greetingEl.style.display = tabName === 'overview' ? '' : 'none';

        // Show leaderboard public-profile banner only on leaderboard tab
        var lbBanner = document.getElementById('dashPublicBanner');
        if (lbBanner) lbBanner.style.display = tabName === 'leaderboard' ? 'flex' : 'none';

        // Init testimonials tab when first opened
        if (tabName === 'testimonials' && typeof TestimonialsTab !== 'undefined') TestimonialsTab.init();

        // Update URL hash without scrolling
        history.replaceState(null, '', '#' + tabName);

        // Close profile sub-nav and reset sections when switching away
        if (tabName !== 'settings') {
            var profileGroup = document.getElementById('navGroupProfile');
            if (profileGroup) profileGroup.classList.remove('open');
            resetProfileSections();
        }
    }

    // ─── Challenge filters ───
    function filterChallenges(filter) {
        document.querySelectorAll('.dash-filter').forEach(f => {
            f.classList.toggle('active', f.dataset.filter === filter);
        });

        document.querySelectorAll('.dash-row[data-status]').forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.dataset.status === filter ? '' : 'none';
            }
        });
    }

    // ─── Profile form ───
    async function submitProfile(e) {
        e.preventDefault();
        const form = document.getElementById('profileForm');
        const msg = document.getElementById('profileMsg');
        msg.className = 'dash-form-msg';
        msg.textContent = '';

        try {
            const res = await fetch('api/update-profile.php', {
                method: 'POST',
                body: new FormData(form),
            });
            const json = await res.json();
            msg.textContent = json.message || json.error;
            msg.className = 'dash-form-msg ' + (json.success ? 'ok' : 'err');
        } catch (err) {
            msg.textContent = 'Connection error. Please try again.';
            msg.className = 'dash-form-msg err';
        }
    }

    // ─── Public profile toggle ───
    async function togglePublicProfile(forceOn) {
        const toggle   = document.getElementById('pubProfileToggle');
        const status   = document.getElementById('pubProfileStatus');
        const msg      = document.getElementById('pubProfileMsg');
        const csrf     = document.getElementById('publicProfileCsrf');
        const banner   = document.getElementById('dashPublicBanner');

        const currentlyOn = toggle ? toggle.classList.contains('is-on') : false;
        const nextOn = forceOn === true ? true : !currentlyOn;

        const fd = new FormData();
        fd.append('csrf', csrf ? csrf.value : '');
        fd.append('is_public', nextOn ? '1' : '0');

        try {
            const res  = await fetch('api/toggle-public-profile.php', { method: 'POST', body: fd });
            const json = await res.json();
            if (json.success) {
                if (toggle) {
                    toggle.classList.toggle('is-on', nextOn);
                    toggle.setAttribute('aria-pressed', String(nextOn));
                }
                if (status) {
                    status.textContent = nextOn ? 'PUBLIC' : 'PRIVATE';
                    status.className = 'dash-pub-status ' + (nextOn ? 'is-public' : '');
                }
                if (banner) banner.style.display = nextOn ? 'none' : '';
                if (msg) { msg.textContent = ''; }
            } else {
                if (msg) { msg.textContent = json.error || 'Error updating visibility.'; }
            }
        } catch {
            if (msg) { msg.textContent = 'Connection error. Please try again.'; }
        }
    }

    // ─── Password form ───
    async function submitPassword(e) {
        e.preventDefault();
        const form = document.getElementById('passwordForm');
        const msg = document.getElementById('passwordMsg');
        msg.className = 'dash-form-msg';
        msg.textContent = '';

        const newPw = form.new_password.value;
        const confirmPw = form.confirm_password.value;
        if (newPw !== confirmPw) {
            msg.textContent = 'Passwords do not match.';
            msg.className = 'dash-form-msg err';
            return;
        }
        if (newPw.length < 8) {
            msg.textContent = 'Password must be at least 8 characters.';
            msg.className = 'dash-form-msg err';
            return;
        }

        try {
            const res = await fetch('api/update-password.php', {
                method: 'POST',
                body: new FormData(form),
            });
            const json = await res.json();
            msg.textContent = json.message || json.error;
            msg.className = 'dash-form-msg ' + (json.success ? 'ok' : 'err');
            if (json.success) form.reset();
        } catch (err) {
            msg.textContent = 'Connection error. Please try again.';
            msg.className = 'dash-form-msg err';
        }
    }

    // ─── KYC form ───
    async function submitKyc(e) {
        e.preventDefault();
        const form = document.getElementById('kycForm');
        const msg = document.getElementById('kycMsg');
        msg.className = 'dash-form-msg';
        msg.textContent = '';

        const file = document.getElementById('kycFile').files[0];
        if (!file) {
            msg.textContent = 'Please select a file.';
            msg.className = 'dash-form-msg err';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            msg.textContent = 'File must be under 5MB.';
            msg.className = 'dash-form-msg err';
            return;
        }

        try {
            const res = await fetch('api/submit-kyc.php', {
                method: 'POST',
                body: new FormData(form),
            });
            const json = await res.json();
            msg.textContent = json.message || json.error;
            msg.className = 'dash-form-msg ' + (json.success ? 'ok' : 'err');
            if (json.success) {
                setTimeout(() => location.reload(), 1500);
            }
        } catch (err) {
            msg.textContent = 'Connection error. Please try again.';
            msg.className = 'dash-form-msg err';
        }
    }

    // ─── KYC document submit (multi-form) ───
    async function submitKycDoc(e, docType) {
        e.preventDefault();
        const form = e.target;
        const msg  = form.querySelector('.dash-doc-form-msg');
        const btn  = form.querySelector('button[type="submit"]');
        if (!msg || !btn) return;

        const file = form.querySelector('input[type="file"]').files[0];
        if (!file) { msg.textContent = 'Please select a file.'; msg.className = 'dash-doc-form-msg dash-form-msg err'; return; }
        if (file.size > 5 * 1024 * 1024) { msg.textContent = 'File must be under 5MB.'; msg.className = 'dash-doc-form-msg dash-form-msg err'; return; }

        btn.disabled = true;
        msg.textContent = 'Uploading…'; msg.className = 'dash-doc-form-msg dash-form-msg';

        try {
            const res  = await fetch('api/submit-kyc.php', { method: 'POST', body: new FormData(form) });
            const json = await res.json();
            msg.textContent  = json.message || json.error;
            msg.className    = 'dash-doc-form-msg dash-form-msg ' + (json.success ? 'ok' : 'err');
            if (json.success) setTimeout(() => location.reload(), 1500);
        } catch (err) {
            msg.textContent = 'Connection error. Please try again.';
            msg.className   = 'dash-doc-form-msg dash-form-msg err';
        } finally {
            btn.disabled = false;
        }
    }

    // ─── Copy referral code ───
    function copyReferral(code) {
        navigator.clipboard?.writeText(code).then(() => {
            const btn = document.querySelector('.dash-copy-btn');
            if (btn) {
                btn.textContent = '✓ Copied';
                btn.style.color = 'var(--green)';
                setTimeout(() => { btn.textContent = 'Copy'; btn.style.color = ''; }, 2000);
            }
        });
    }

    // ─── File upload label ───
    function updateUploadLabel() {
        const input = document.getElementById('kycFile');
        const label = document.getElementById('kycUploadLabel');
        if (!input || !label) return;
        input.addEventListener('change', () => {
            if (input.files.length > 0) {
                const name = input.files[0].name;
                label.querySelector('span').textContent = name;
                label.style.borderColor = 'var(--green)';
                label.style.color = 'var(--green)';
            }
        });
    }

    // ─── Theme management ───
    // SVG paths for sun and moon icons
    var SUN_PATH  = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
    var MOON_PATH = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';

    function updateThemeIcon(theme) {
        var icon = document.getElementById('dashThemeIcon');
        if (!icon) return;
        // In dark mode show sun (click → go light), in light mode show moon (click → go dark)
        icon.innerHTML = theme === 'dark' ? SUN_PATH : MOON_PATH;
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('doji-theme', theme);
        updateThemeIcon(theme);
    }

    function toggleTheme() {
        var current = localStorage.getItem('doji-theme') || 'dark';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    function setTheme(theme) { applyTheme(theme); }

    function initTheme() {
        var saved = localStorage.getItem('doji-theme') || 'dark';
        applyTheme(saved);
    }

    // ─── Time-aware greeting ───
    function initGreeting() {
        var titleEl = document.getElementById('dashPageTitle');
        if (!titleEl || !titleEl.parentNode) return;
        var h = new Date().getHours();
        var salut = h < 12 ? 'Good morning' : h < 18 ? 'Good afternoon' : 'Good evening';
        var usernameEl  = document.getElementById('dashUsername');
        var firstNameEl = document.getElementById('dashFirstName');
        var username    = usernameEl  ? usernameEl.textContent.trim()  : '';
        var first       = firstNameEl ? firstNameEl.textContent.trim() : '';
        var name        = username || first;
        var el = document.createElement('p');
        el.className = 'dash-greeting';
        el.id = 'dashGreeting';
        el.textContent = salut + (name ? ' ' + name : '') + ' !';
        titleEl.parentNode.insertBefore(el, titleEl.nextSibling);
    }

    // ─── Laser burn inside .dash-main then switch tab ───
    function burnThenSwitch(tabName, afterSwitch) {
        var main = document.querySelector('.dash-main');
        if (!main || typeof LaserBurn === 'undefined' || !LaserBurn.triggerInElement) {
            switchTab(tabName);
            if (afterSwitch) afterSwitch();
            return;
        }
        LaserBurn.triggerInElement(main, function() {
            switchTab(tabName);
            if (afterSwitch) afterSwitch();
        });
    }

    // ─── Console easter egg ───
    function initConsoleEgg() {
        if (typeof console === 'undefined') return;
        console.log('%c DOJI FUNDING ', 'background:#10B981;color:#000;font-family:monospace;font-size:14px;font-weight:700;padding:3px 8px;letter-spacing:0.08em;');
        console.log('%cYou opened the console. We like the curious ones.', 'color:#10B981;font-family:monospace;font-size:12px;');
        console.log('%cBuilding something serious. hello@dojifunding.com', 'color:#555;font-family:monospace;font-size:11px;');
    }

    // ─── Init ───
    function init() {
        // Sidebar nav clicks — laser burn inside .dash-main, then switch tab
        document.querySelectorAll('.dash-nav-item').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var group = btn.closest('.dash-nav-group');
                if (group && group.id === 'navGroupProfile') {
                    var isOpen = group.classList.toggle('open');
                    if (isOpen) {
                        burnThenSwitch('settings', function() { showProfileSection('profile'); });
                    } else {
                        resetProfileSections();
                    }
                } else {
                    burnThenSwitch(btn.dataset.tab);
                    closeSidebar();
                }
            });
        });

        // Sidebar drawer (mobile)
        function openSidebar() {
            var sidebar = document.querySelector('.dash-sidebar');
            var overlay = document.getElementById('dashSidebarOverlay');
            if (sidebar) sidebar.classList.add('open');
            if (overlay) overlay.classList.add('open');
        }
        function closeSidebar() {
            var sidebar = document.querySelector('.dash-sidebar');
            var overlay = document.getElementById('dashSidebarOverlay');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
        var hamburger = document.getElementById('dashHamburger');
        if (hamburger) hamburger.addEventListener('click', openSidebar);
        var sidebarOverlay = document.getElementById('dashSidebarOverlay');
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
        var mobileMore = document.getElementById('dashMobileMore');
        if (mobileMore) mobileMore.addEventListener('click', openSidebar);

        // Mobile tab clicks — use laser burn same as sidebar
        document.querySelectorAll('.dash-mobile-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!btn.dataset.tab) return; // "More" button handled separately
                burnThenSwitch(btn.dataset.tab);
            });
        });

        // Challenge filters
        document.querySelectorAll('.dash-filter').forEach(btn => {
            btn.addEventListener('click', () => filterChallenges(btn.dataset.filter));
        });

        // Form submissions
        const profileForm = document.getElementById('profileForm');
        if (profileForm) profileForm.addEventListener('submit', submitProfile);

        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) passwordForm.addEventListener('submit', submitPassword);

        const kycForm = document.getElementById('kycForm');
        if (kycForm) kycForm.addEventListener('submit', submitKyc);

        // Profile sub-nav item clicks
        document.querySelectorAll('.dash-nav-sub-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                switchTab('settings');
                showProfileSection(item.dataset.section);
                // Scroll content back to top
                var main = document.querySelector('.dash-main');
                if (main) main.scrollTop = 0;
            });
        });

        // File upload label
        updateUploadLabel();

        // Restore tab from hash
        const hash = location.hash.replace('#', '');
        if (['overview', 'challenges', 'wallet', 'payouts', 'settings'].includes(hash)) {
            switchTab(hash);
            if (hash === 'settings') {
                var profileGroup = document.getElementById('navGroupProfile');
                if (profileGroup) profileGroup.classList.add('open');
                showProfileSection('profile');
            }
        }

        // Init theme
        initTheme();

        // Language toggle (EN / FR) — UI only, persisted in localStorage
        (function () {
            var btn = document.getElementById('dashLangBtn');
            var txt = document.getElementById('dashLangTxt');
            if (!btn || !txt) return;
            var lang = localStorage.getItem('doji-lang') || 'EN';
            txt.textContent = lang;
            btn.addEventListener('click', function () {
                lang = lang === 'EN' ? 'FR' : 'EN';
                localStorage.setItem('doji-lang', lang);
                txt.textContent = lang;
            });
        }());

        // Delight
        initGreeting();
        initConsoleEgg();


        // ─── Promo banner height → CSS var (fixes overlap) ───
        (function() {
            var banner = document.getElementById('promoBanner');
            function updatePromoH() {
                var h = (banner && !banner.classList.contains('hidden'))
                    ? banner.getBoundingClientRect().height : 0;
                document.documentElement.style.setProperty('--promo-h', h + 'px');
            }
            updatePromoH();
            if (banner) {
                new MutationObserver(updatePromoH).observe(banner, { attributes: true, attributeFilter: ['class', 'style'] });
            }
            window.addEventListener('resize', updatePromoH);
        })();
    }

    document.addEventListener('DOMContentLoaded', init);

    return { switchTab, filterChallenges, copyReferral, setTheme, toggleTheme, submitKycDoc, showProfileSection, togglePublicProfile };
})();

/* ═══════════════════════════════════════════════════
   DashPresets — save/load named configurator presets
═══════════════════════════════════════════════════ */
const DashPresets = (function() {
    'use strict';

    var _presets = [];
    var _open    = false;

    /* ── Toggle panel open/close ── */
    function toggle(e) {
        var card = document.getElementById('modeMyPresets');
        if (!card) return;
        _open = !card.classList.contains('active');
        // Close other mode cards
        document.querySelectorAll('.mode-card').forEach(function(c) { c.classList.remove('active'); });
        if (_open) {
            card.classList.add('active');
            _renderList();
        }
    }

    /* ── Capture current Configurator state ── */
    function _captureConfig() {
        if (typeof Configurator === 'undefined') return null;
        // Access internal state via a lightweight approach: read slider values from DOM
        var S = {};
        var cfg = window.DOJI_CONFIG && window.DOJI_CONFIG.pricing;
        if (!cfg) return null;

        // Read from sliders + toggles in the DOM
        var sizeInput = document.querySelector('[data-slider-id="sizeIdx"] .slider-input');
        if (sizeInput) S.sizeIdx = parseInt(sizeInput.value);

        var tabOneActive = document.getElementById('tab-onestep') && document.getElementById('tab-onestep').classList.contains('active');
        S.tab = tabOneActive ? 'onestep' : 'twostep';

        ['target','target1','target2','daily','max','split','days','consistency'].forEach(function(id) {
            var el = document.querySelector('[data-slider-id="' + id + '"] .slider-input');
            if (el) S[id] = parseInt(el.value);
        });

        var getToggle = function(groupSel, defaultVal) {
            var active = document.querySelector(groupSel + ' .toggle-btn.active');
            return active ? active.getAttribute('onclick').match(/'([^']+)'\)/)?.[1] || defaultVal : defaultVal;
        };

        S.dailyType = getToggle('[data-slider-id="daily"] ~ div .toggle-group', 'intraday');
        S.maxType   = getToggle('[data-slider-id="max"] ~ div .toggle-group', 'intraday');

        var payActive = document.querySelector('.toggle-group .toggle-btn.active[onclick*="payout"]');
        if (payActive) {
            var m = payActive.getAttribute('onclick').match(/'([^']+)'\)/);
            S.payout = m ? m[1] : 'monthly';
        } else { S.payout = 'monthly'; }

        var onRow  = document.querySelector('.switch-row[onclick*="overnight"]');
        var owRow  = document.querySelector('.switch-row[onclick*="overweek"]');
        S.overnight = onRow  ? onRow.classList.contains('active')  : false;
        S.overweek  = owRow  ? owRow.classList.contains('active') : false;

        return S;
    }

    /* ── Save current config ── */
    function save() {
        var nameEl = document.getElementById('mpNameInput');
        var msgEl  = document.querySelector('.mp-msg') || _getOrCreateMsg();
        var name   = nameEl ? nameEl.value.trim() : '';
        if (!name) { _showMsg('err', 'Please enter a name.'); return; }

        var config = _captureConfig();
        if (!config) { _showMsg('err', 'Configurator not ready.'); return; }

        var csrf = (window.DOJI_CONFIG && window.DOJI_CONFIG.csrfToken) || '';
        var body = new FormData();
        body.append('csrf', csrf);
        body.append('name', name);
        body.append('config', JSON.stringify(config));

        fetch('api/presets.php', { method: 'POST', body: body })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    _presets.unshift(data.preset);
                    if (nameEl) nameEl.value = '';
                    _showMsg('ok', 'Saved!');
                    _renderList();
                } else {
                    _showMsg('err', data.error || 'Error saving.');
                }
            })
            .catch(function() { _showMsg('err', 'Connection error.'); });
    }

    /* ── Load a preset into Configurator ── */
    function load(config) {
        if (typeof Configurator === 'undefined' || !config) return;
        // Switch tab first if needed
        var needTab = config.tab || 'onestep';
        if (needTab !== (document.getElementById('tab-onestep').classList.contains('active') ? 'onestep' : 'twostep')) {
            Configurator.setTab(needTab);
        }
        // Apply config values via Configurator's public onSliderDone/onToggle/onCheck
        var sliders = ['sizeIdx','target','target1','target2','daily','max','split','days','consistency'];
        sliders.forEach(function(id) {
            if (config[id] !== undefined) {
                Configurator.onSliderDone(id, config[id]);
                var inp = document.querySelector('[data-slider-id="' + id + '"] .slider-input');
                if (inp) { inp.value = config[id]; }
            }
        });
        if (config.dailyType) Configurator.onToggle('dailyType', config.dailyType);
        if (config.maxType)   Configurator.onToggle('maxType',   config.maxType);
        if (config.payout)    Configurator.onToggle('payout',    config.payout);
        if (config.overnight !== undefined) Configurator.onCheck('overnight', !!config.overnight);
        if (config.overweek  !== undefined) Configurator.onCheck('overweek',  !!config.overweek);

        // Close panel
        var card = document.getElementById('modeMyPresets');
        if (card) card.classList.remove('active');
        _open = false;
    }

    /* ── Delete a preset ── */
    function remove(id) {
        var csrf = (window.DOJI_CONFIG && window.DOJI_CONFIG.csrfToken) || '';
        var body = new FormData();
        body.append('csrf', csrf);
        body.append('action', 'delete');
        body.append('id', id);

        fetch('api/presets.php', { method: 'POST', body: body })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    _presets = _presets.filter(function(p) { return p.id !== id; });
                    _renderList();
                }
            });
    }

    /* ── Render preset list ── */
    function _renderList() {
        var list = document.getElementById('mpList');
        if (!list) return;
        if (!_presets.length) {
            list.innerHTML = '<div class="mp-empty">No saved presets yet.</div>';
            return;
        }
        list.innerHTML = _presets.map(function(p) {
            var tabLabel = p.config && p.config.tab === 'twostep' ? '2-Step' : '1-Step';
            return '<div class="mp-item">' +
                '<span class="mp-item-name" title="' + _esc(p.name) + '">' + _esc(p.name) + '</span>' +
                '<span class="mp-item-tab">' + tabLabel + '</span>' +
                '<button class="mp-item-load" onclick="DashPresets.load(' + JSON.stringify(p.config).replace(/"/g, '&quot;') + ')">Load</button>' +
                '<button class="mp-item-del" onclick="DashPresets.remove(' + p.id + ')" title="Delete">✕</button>' +
                '</div>';
        }).join('');
    }

    function _showMsg(type, text) {
        var existing = document.getElementById('mpMsg');
        if (!existing) {
            existing = document.createElement('div');
            existing.id = 'mpMsg';
            existing.className = 'mp-msg';
            var list = document.getElementById('mpList');
            if (list && list.parentNode) list.parentNode.insertBefore(existing, list);
        }
        existing.className = 'mp-msg ' + type;
        existing.textContent = text;
        setTimeout(function() { if (existing) existing.textContent = ''; }, 3000);
    }

    function _getOrCreateMsg() {
        return document.getElementById('mpMsg');
    }

    function _esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Init: fetch presets from server ── */
    function init() {
        fetch('api/presets.php')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    _presets = data.presets || [];
                }
            })
            .catch(function() {});
    }

    document.addEventListener('DOMContentLoaded', init);

    return { toggle: toggle, save: save, load: load, remove: remove };
}());

/* ═══════════════════════════════════════════════════
   ChallengeCredentials — Credentials card in Challenges tab
   Data is embedded by PHP as window.__credData / window.__credFirstId
═══════════════════════════════════════════════════ */
var ChallengeCredentials = (function() {
    'use strict';

    var _data          = {};
    var _currentId     = null;
    var _masterVisible = false;

    /* SVG icons */
    var EYE_OPEN  = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    var EYE_CLOSE = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

    function init() {
        _data = window.__credData || {};
        var firstId = window.__credFirstId || 0;
        if (firstId) {
            select(firstId);
            /* Highlight the first row in the table */
            var firstRow = document.querySelector('.dash-row[data-cred-id="' + firstId + '"]');
            if (firstRow) firstRow.classList.add('active-row');
        }
    }

    function select(id) {
        id = parseInt(id, 10);
        _currentId     = id;
        _masterVisible = false;

        var cred = _data[id];
        if (!cred) return;

        /* Update pills */
        document.querySelectorAll('.cred-pill').forEach(function(p) {
            p.classList.toggle('active', parseInt(p.dataset.credId, 10) === id);
        });

        /* Update mobile selects (both tabs) */
        document.querySelectorAll('.cred-mobile-sel').forEach(function(mSel) {
            mSel.value = id;
        });

        /* ── Credentials fields ── */
        _setText('credLogin',        cred.login || '—');
        _setText('credMasterPass',   cred.master_password   ? '••••••••' : 'N/A');
        _setText('credInvestorPass', cred.investor_password || 'N/A');
        _setText('credServer',       cred.server || '—');

        document.querySelectorAll('.cred-master-eye-btn').forEach(function(eyeBtn) {
            eyeBtn.style.display = cred.master_password ? '' : 'none';
            eyeBtn.innerHTML = EYE_OPEN;
        });
        _qsa('credMasterPass').forEach(function(masterEl) {
            masterEl.classList.toggle('cred-val-na', !cred.master_password);
        });

        /* ── KPI cards ── */
        _updateKpi(cred);
    }

    function _updateKpi(cred) {
        var profit  = cred.profit || 0;
        var size    = cred.account_size || 1;
        var target  = cred.profit_target || 10;
        var ddUsed  = cred.dd_used || 0;          // soft — capé à la limite daily
        var ddMax   = cred.daily_loss || 5;
        var mdUsed  = cred.md_used  != null ? cred.md_used : (cred.dd_used || 0); // hard — brut
        var mdMax   = cred.max_loss || 10;
        var mdProg  = cred.md_prog  != null ? cred.md_prog  : 0;
        var pnlPct  = cred.pnl_pct || 0;

        /* Account Balance */
        _setText('chKpiBalance', _fmtMoney(cred.balance || 0));
        var pnlEl = document.getElementById('chKpiPnl');
        if (pnlEl) {
            pnlEl.textContent = (profit >= 0 ? '▲ +' : '▼ ') + _fmtMoney(profit) + ' total P&L';
            pnlEl.className = 'ov-card-sub ' + (profit >= 0 ? 'green' : 'red');
        }

        /* Profit Target */
        var profEl = document.getElementById('chKpiProfitPct');
        if (profEl) {
            profEl.textContent = _fmtPct(pnlPct) + '/' + _fmtPct(target);
            profEl.className = 'ov-card-val ' + (profit >= 0 ? 'green' : 'red');
        }
        _setText('chKpiProfitConvert',
            _fmtMoney(profit) + ' / ' + _fmtMoney(size * target / 100));
        _setWidth('chKpiProfitBar', cred.prof_prog || 0);

        /* Daily Drawdown */
        var ddDanger = ddMax > 0 && ddUsed > ddMax * 0.7;
        var ddEl = document.getElementById('chKpiDdPct');
        if (ddEl) {
            ddEl.textContent = _fmtPct(ddUsed) + '/' + _fmtPct(ddMax);
            ddEl.className = 'ov-card-val ' + (ddDanger ? 'red' : 'warn');
        }
        _setText('chKpiDdConvert',
            _fmtMoney(size * ddUsed / 100) + ' / ' + _fmtMoney(size * ddMax / 100));
        var ddBar = document.getElementById('chKpiDdBar');
        if (ddBar) {
            ddBar.style.width = (cred.dd_prog || 0) + '%';
            ddBar.className = 'ov-bar-fill ' + (ddDanger ? 'ov-bar-fill-red' : 'ov-bar-fill-amber');
        }

        /* Max Drawdown */
        var mdDanger = mdMax > 0 && mdUsed > mdMax * 0.7;
        var mdEl = document.getElementById('chKpiMdPct');
        if (mdEl) {
            mdEl.textContent = _fmtPct(mdUsed) + '/' + _fmtPct(mdMax);
            mdEl.className = 'ov-card-val ' + (mdDanger ? 'red' : (mdUsed > mdMax * 0.4 ? 'warn' : ''));
        }
        _setText('chKpiMdConvert',
            _fmtMoney(size * mdUsed / 100) + ' / ' + _fmtMoney(size * mdMax / 100));
        var mdBar = document.getElementById('chKpiMdBar');
        if (mdBar) {
            mdBar.style.width = mdProg + '%';
            mdBar.className = 'ov-bar-fill ' + (mdDanger ? 'ov-bar-fill-red' : 'ov-bar-fill-amber');
        }

        /* Loss type labels */
        var _lossType = { intraday: 'TRAILING', eod: 'END OF DAY', static: 'STATIC' };
        _setText('chKpiDdType', _lossType[cred.daily_loss_type] || 'TRAILING');
        _setText('chKpiMdType', _lossType[cred.max_loss_type]   || 'TRAILING');

        /* Consistency gauge — funded accounts only */
        if (cred.is_funded) {
            _updateConsGauge(cred.cons_pct || 0, cred.cons_used || 0, cred.cons_rule || 30);
        } else {
            _updateConsGauge(0, 0, 0);
            var consStatusEl = document.getElementById('chKpiConsStatus');
            if (consStatusEl) {
                consStatusEl.textContent = 'FUNDED ONLY';
                consStatusEl.style.color = 'var(--text-dis)';
            }
        }

        /* Account Info */
        _setText('chKpiType', cred.type_label || '—');
        _setText('chKpiId',   cred.acct_ref   || cred.ch_id_fmt || '—');
        var resetBtn = document.getElementById('chKpiResetBtn');
        if (resetBtn) resetBtn.style.display = cred.is_eval ? '' : 'none';

        var payoutBtn = document.getElementById('chKpiPayoutBtn');
        if (payoutBtn) {
            if (cred.is_funded) {
                payoutBtn.style.display = '';
                payoutBtn.disabled = !cred.payout_eligible;
                payoutBtn.title = cred.payout_eligible
                    ? 'Request a payout'
                    : 'Not yet eligible — verify trading days, profit, and consistency rule';
            } else {
                payoutBtn.style.display = 'none';
                payoutBtn.disabled = false;
            }
        }
    }

    function selectFromRow(rowEl, id) {
        /* Highlight selected row */
        document.querySelectorAll('.dash-row').forEach(function(r) {
            r.classList.remove('active-row');
        });
        rowEl.classList.add('active-row');

        /* Update credentials + KPI cards */
        select(id);

        /* Scroll credentials card into view */
        var card = document.getElementById('credCard');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function copyChId(btnEl) {
        var el = document.getElementById('chKpiId');
        if (!el) return;
        navigator.clipboard.writeText(el.textContent.trim()).then(function() {
            if (!btnEl) return;
            btnEl.classList.add('copied');
            setTimeout(function() { btnEl.classList.remove('copied'); }, 1200);
        }).catch(function() {});
    }

    function goToPayouts() {
        var btn = document.querySelector('[data-tab="payouts"]');
        if (btn) btn.click();
    }

    function resetChallenge() {
        if (!confirm('Reset this challenge? This cannot be undone.'))  return;
        alert('Reset requested — support will process shortly.');
    }

    function deleteChallenge() {
        if (!confirm('Delete this challenge? This cannot be undone.')) return;
        alert('Deletion requested — support will process shortly.');
    }

    /* ── Format helpers ── */
    function _fmtMoney(v) {
        v = parseFloat(v) || 0;
        var abs = Math.abs(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return (v < 0 ? '-$' : '$') + abs;
    }
    function _fmtPct(v) {
        return (parseFloat(v) || 0).toFixed(1) + '%';
    }
    function _setWidth(id, pct) {
        var el = document.getElementById(id);
        if (el) el.style.width = pct + '%';
    }

    function toggleMaster() {
        var cred = _data[_currentId];
        if (!cred || !cred.master_password) return;
        _masterVisible = !_masterVisible;
        _setText('credMasterPass', _masterVisible ? cred.master_password : '••••••••');
        var icon = _masterVisible ? EYE_CLOSE : EYE_OPEN;
        document.querySelectorAll('.cred-master-eye-btn').forEach(function(btn) {
            btn.innerHTML = icon;
        });
    }

    function copy(field, btnEl) {
        var cred = _data[_currentId];
        if (!cred || !cred[field]) return;
        navigator.clipboard.writeText(cred[field]).then(function() {
            if (!btnEl) return;
            btnEl.classList.add('copied');
            setTimeout(function() { btnEl.classList.remove('copied'); }, 1200);
        }).catch(function() {});
    }

    function resetMaster() {
        if (!_currentId) return;
        if (!confirm('Reset the master password for this account?\nA new password will be generated and sent to your email.')) return;
        /* TODO: POST api/reset-password.php { challenge_id: _currentId } */
        alert('Password reset request submitted. Check your email.');
    }

    /* Find elements by id OR data-cred-field attribute (for multi-tab updates) */
    function _qsa(id) {
        var results = [];
        var byId = document.getElementById(id);
        if (byId) results.push(byId);
        document.querySelectorAll('[data-cred-field="' + id + '"]').forEach(function(el) {
            results.push(el);
        });
        return results;
    }

    function _setText(id, val) {
        _qsa(id).forEach(function(el) { el.textContent = val; });
    }

    /* Consistency arc-gauge — r=34, 270° sweep, circumference≈213.63 */
    /* Consistency arc-gauge — viewBox 100×100, r=34, 270° sweep
       circumference = 2π×34 ≈ 213.63 | sweep = 213.63×0.75 ≈ 160.22
       Centre : valeur actuelle (cons_used, sans %)
       Bas arc : limite choisie (cons_rule, ex: "/ 30%")
       NOTE: SVG presentation attributes need setAttribute, not el.style.prop  */
    function _updateConsGauge(consPct, consUsed, consRule) {
        var SWEEP = 160.22;
        var CIRC  = 213.63;
        var pct   = parseFloat(consPct) || 0;
        var used  = parseFloat(consUsed) || 0;
        var rule  = parseFloat(consRule) || 30;
        var fill  = pct > 0 ? Math.min(SWEEP * 1.04, SWEEP * pct / 100) : 0;

        var color = pct >= 100 ? '#D71921'
                  : pct >=  80 ? '#e86820'
                  : pct >=  60 ? '#D4A843'
                  : pct >    0 ? '#10B981'
                  : '#333';

        /* Arc */
        var arc = document.getElementById('chKpiConsArc');
        if (arc) {
            arc.setAttribute('stroke-dasharray', (pct > 0 ? fill.toFixed(2) : '0') + ' ' + CIRC);
            arc.setAttribute('stroke', color);
        }

        /* Centre — valeur actuelle sans % */
        var valEl = document.getElementById('chKpiConsPct');
        if (valEl) {
            valEl.textContent = pct > 0 ? Math.round(used) : '—';
            valEl.style.color = pct > 0 ? color : '';
        }

        /* Bas de l'arc — limite choisie */
        var limitEl = document.getElementById('chKpiConsLimit');
        if (limitEl) limitEl.textContent = pct > 0 ? '/' + Math.round(rule) + '%' : '—';

        /* Statut */
        var statusEl = document.getElementById('chKpiConsStatus');
        if (statusEl) {
            var label = pct <= 0   ? 'N/A'
                      : pct >= 100 ? 'TOO HIGH'
                      : pct >=  80 ? 'WARNING'
                      : 'OK';
            statusEl.textContent = label;
            statusEl.style.color = pct <= 0 ? 'var(--text-dis)' : color;
        }
    }

    document.addEventListener('DOMContentLoaded', init);

    return { select: select, selectFromRow: selectFromRow, toggleMaster: toggleMaster,
             copy: copy, resetMaster: resetMaster,
             copyChId: copyChId, resetChallenge: resetChallenge, deleteChallenge: deleteChallenge,
             goToPayouts: goToPayouts };
}());

/* ── Daily Drawdown Reset Countdown (GMT+3 midnight) ───────────────── */
(function () {
    var GMT3_OFFSET = 3 * 3600 * 1000; // ms

    function secsUntilMidnightGmt3() {
        var now = new Date();
        var nowGmt3 = new Date(now.getTime() + now.getTimezoneOffset() * 60000 + GMT3_OFFSET);
        var midnight = new Date(nowGmt3);
        midnight.setHours(24, 0, 0, 0);
        return Math.floor((midnight - nowGmt3) / 1000);
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function tick() {
        var secs = secsUntilMidnightGmt3();
        var h = Math.floor(secs / 3600);
        var m = Math.floor((secs % 3600) / 60);
        var s = secs % 60;
        var formatted = pad(h) + ':' + pad(m) + ':' + pad(s);
        var ov = document.getElementById('ovDdCountdown');
        var ch = document.getElementById('chDdCountdown');
        if (ov) ov.textContent = formatted;
        if (ch) ch.textContent = formatted;
    }

    document.addEventListener('DOMContentLoaded', function () {
        tick();
        setInterval(tick, 1000);
    });
}());

/* ── Payout Modal ── */
const PayoutModal = (function () {
    'use strict';

    var _method = 'rise';
    var _maxAmt = 0;

    function open() {
        var overlay = document.getElementById('payoutModal');
        if (!overlay) return;
        _showStep(1);
        var amtEl = document.getElementById('pytAmount');
        if (amtEl) { amtEl.value = ''; _maxAmt = parseFloat(amtEl.max) || 0; }
        var ack = document.getElementById('pytAck');
        if (ack) ack.checked = false;
        var firstBtn = document.querySelector('.pyt-method-btn');
        if (firstBtn) setMethod(firstBtn);
        validate();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        var overlay = document.getElementById('payoutModal');
        if (!overlay) return;
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function setMethod(btn) {
        document.querySelectorAll('.pyt-method-btn').forEach(function (b) {
            b.classList.remove('pyt-method-active');
        });
        if (btn) {
            btn.classList.add('pyt-method-active');
            _method = btn.getAttribute('data-method') || 'rise';
        }
    }

    function setMax() {
        var el = document.getElementById('pytAmount');
        if (el) { el.value = _maxAmt.toFixed(2); validate(); }
    }

    function validate() {
        var amtEl = document.getElementById('pytAmount');
        var ackEl = document.getElementById('pytAck');
        var errEl = document.getElementById('pytAmountErr');
        var btn   = document.getElementById('pytSubmitBtn');
        if (!amtEl || !ackEl || !btn) return;
        var val = parseFloat(amtEl.value);
        var amtOk = amtEl.value !== '' && !isNaN(val) && val > 0;
        if (amtOk && val > _maxAmt) {
            if (errEl) errEl.textContent = 'Amount exceeds available balance.';
            amtOk = false;
        } else {
            if (errEl) errEl.textContent = '';
        }
        btn.disabled = !(amtOk && ackEl.checked);
    }

    function submit() {
        var amtEl = document.getElementById('pytAmount');
        var val = parseFloat(amtEl ? amtEl.value : 0);
        if (!val || val <= 0) return;
        var ref = 'DOJ-' + new Date().getFullYear() + '-' + Math.random().toString(36).slice(2, 9).toUpperCase();
        var methodLabel = _method === 'confirmo' ? 'Confirmo (Crypto)' : 'Rise (rise.com)';
        var fmt = '$' + val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        document.getElementById('pytRecapAmt').textContent  = fmt;
        document.getElementById('pytRecapDest').textContent = methodLabel;
        document.getElementById('pytRecapRef').textContent  = ref;
        _showStep(2);
    }

    function _showStep(n) {
        var s1 = document.getElementById('pytStep1');
        var s2 = document.getElementById('pytStep2');
        if (s1) s1.style.display = n === 1 ? '' : 'none';
        if (s2) s2.style.display = n === 2 ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('payoutModal');
        if (!overlay) return;
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    });

    return { open: open, close: close, setMethod: setMethod, setMax: setMax, validate: validate, submit: submit };
}());

/* ── Profit Split Modal ── */
const ProfitSplitModal = (function () {
    'use strict';

    var _account  = null; // { id, ref, size, split }
    var _tier     = null; // { pct, cost }
    var _dcBal    = 0;

    function open() {
        var overlay = document.getElementById('profitSplitModal');
        if (!overlay) return;
        _dcBal   = parseInt(overlay.getAttribute('data-dc'), 10) || 0;
        _account = null;
        _tier    = null;
        document.querySelectorAll('.psl-acct-btn').forEach(function (b) { b.classList.remove('psl-acct-active'); });
        document.querySelectorAll('#pslTierRow .pyt-method-btn').forEach(function (b) { b.classList.remove('pyt-method-active'); });
        var ack = document.getElementById('pslAck');
        if (ack) ack.checked = false;
        _hideResult();
        _showStep(1);
        validate();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        var overlay = document.getElementById('profitSplitModal');
        if (!overlay) return;
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function setAccount(btn) {
        document.querySelectorAll('.psl-acct-btn').forEach(function (b) { b.classList.remove('psl-acct-active'); });
        btn.classList.add('psl-acct-active');
        _account = {
            id:    btn.getAttribute('data-id'),
            ref:   btn.getAttribute('data-ref'),
            size:  btn.getAttribute('data-size'),
            split: parseInt(btn.getAttribute('data-split'), 10) || 80
        };
        _updateResult();
        validate();
    }

    function setTier(btn) {
        document.querySelectorAll('#pslTierRow .pyt-method-btn').forEach(function (b) { b.classList.remove('pyt-method-active'); });
        btn.classList.add('pyt-method-active');
        _tier = {
            pct:  parseInt(btn.getAttribute('data-tier'), 10),
            cost: parseInt(btn.getAttribute('data-cost'), 10)
        };
        _updateResult();
        validate();
    }

    function _updateResult() {
        var line = document.getElementById('pslResultLine');
        if (!line) return;
        if (!_account || !_tier) { _hideResult(); return; }
        var newSplit = _account.split + _tier.pct;
        var remaining = _dcBal - _tier.cost;
        var lowDc = remaining < 0;
        line.style.display = '';
        line.innerHTML = 'New split: <strong>' + newSplit + '%</strong>'
            + ' &nbsp;·&nbsp; Cost: <strong>' + _tier.cost.toLocaleString() + ' DC</strong>'
            + ' &nbsp;·&nbsp; Remaining: <span class="' + (lowDc ? 'psl-err-dc' : '') + '">'
            + remaining.toLocaleString() + ' DC</span>';
    }

    function _hideResult() {
        var line = document.getElementById('pslResultLine');
        if (line) line.style.display = 'none';
    }

    function validate() {
        var btn = document.getElementById('pslSubmitBtn');
        var err = document.getElementById('pslErr');
        if (!btn) return;
        var ack = document.getElementById('pslAck');
        var hasAccount = !!_account;
        var hasTier    = !!_tier;
        var hasAck     = ack && ack.checked;
        var enoughDc   = _tier ? (_dcBal >= _tier.cost) : false;

        if (hasTier && !enoughDc) {
            if (err) err.textContent = 'Insufficient Doji Coins balance for this tier.';
        } else {
            if (err) err.textContent = '';
        }
        btn.disabled = !(hasAccount && hasTier && hasAck && enoughDc);
    }

    function submit() {
        if (!_account || !_tier) return;
        var ref      = 'PSL-' + new Date().getFullYear() + '-' + Math.random().toString(36).slice(2, 9).toUpperCase();
        var newSplit = _account.split + _tier.pct;
        document.getElementById('pslRecapAcct').textContent  = _account.ref;
        document.getElementById('pslRecapTier').textContent  = '+' + _tier.pct + '% Profit Split Upgrade';
        document.getElementById('pslRecapSplit').textContent = newSplit + '%';
        document.getElementById('pslRecapCost').textContent  = _tier.cost.toLocaleString() + ' DC';
        document.getElementById('pslRecapRef').textContent   = ref;
        _showStep(2);
    }

    function _showStep(n) {
        var s1 = document.getElementById('pslStep1');
        var s2 = document.getElementById('pslStep2');
        if (s1) s1.style.display = n === 1 ? '' : 'none';
        if (s2) s2.style.display = n === 2 ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('profitSplitModal');
        if (!overlay) return;
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    });

    return { open: open, close: close, setAccount: setAccount, setTier: setTier, validate: validate, submit: submit };
}());

/* ── Discount Coupon Modal ── */
const DiscountModal = (function () {
    'use strict';

    var _tier  = null;
    var _dcBal = 0;

    function open() {
        var overlay = document.getElementById('discountModal');
        if (!overlay) return;
        _dcBal = parseInt(overlay.getAttribute('data-dc'), 10) || 0;
        _tier  = null;
        document.querySelectorAll('.disc-tier-btn').forEach(function (b) { b.classList.remove('disc-tier-active'); });
        var ack = document.getElementById('discAck');
        if (ack) ack.checked = false;
        var rl = document.getElementById('discResultLine');
        if (rl) rl.style.display = 'none';
        var err = document.getElementById('discErr');
        if (err) err.textContent = '';
        _showStep(1);
        validate();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        var overlay = document.getElementById('discountModal');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function setTier(btn) {
        document.querySelectorAll('.disc-tier-btn').forEach(function (b) { b.classList.remove('disc-tier-active'); });
        btn.classList.add('disc-tier-active');
        _tier = { pct: parseInt(btn.getAttribute('data-pct'), 10), cost: parseInt(btn.getAttribute('data-cost'), 10) };
        var remaining = _dcBal - _tier.cost;
        var rl = document.getElementById('discResultLine');
        if (rl) {
            rl.style.display = '';
            rl.innerHTML = 'Cost: <strong>' + _tier.cost.toLocaleString() + ' DC</strong>'
                + ' &nbsp;·&nbsp; Remaining: <span class="' + (remaining < 0 ? 'psl-err-dc' : '') + '">'
                + remaining.toLocaleString() + ' DC</span>';
        }
        validate();
    }

    function validate() {
        var btn = document.getElementById('discSubmitBtn');
        var err = document.getElementById('discErr');
        if (!btn) return;
        var ack  = document.getElementById('discAck');
        var enoughDc = _tier ? (_dcBal >= _tier.cost) : true;
        if (_tier && !enoughDc) {
            if (err) err.textContent = 'Insufficient Doji Coins balance.';
        } else {
            if (err) err.textContent = '';
        }
        btn.disabled = !(_tier && ack && ack.checked && enoughDc);
    }

    function submit() {
        if (!_tier) return;
        var code = 'DC' + _tier.pct + '-' + Math.random().toString(36).slice(2, 9).toUpperCase();
        window.DojiActiveCoupon = { code: code, pct: _tier.pct, label: _tier.pct + '% OFF', cost: _tier.cost };
        document.getElementById('discRecapCode').textContent  = code;
        document.getElementById('discRecapPct').textContent   = _tier.pct + '% OFF';
        document.getElementById('discRecapCost').textContent  = _tier.cost.toLocaleString() + ' DC';
        _showStep(2);
    }

    function goCheckout() {
        close();
        if (typeof Dashboard !== 'undefined') Dashboard.switchTab('configurator');
    }

    function _showStep(n) {
        var s1 = document.getElementById('discStep1');
        var s2 = document.getElementById('discStep2');
        if (s1) s1.style.display = n === 1 ? '' : 'none';
        if (s2) s2.style.display = n === 2 ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('discountModal');
        if (!overlay) return;
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    });

    return { open: open, close: close, setTier: setTier, validate: validate, submit: submit, goCheckout: goCheckout };
}());

/* ── Purchase / Checkout Modal ── */
const PurchaseModal = (function () {
    'use strict';

    var _state   = null;
    var _payment = 'stripe';
    var _coupon  = null; // { pct, label } or { flat, label }

    function open(state) {
        _state   = state || {};
        _payment = 'stripe';
        _coupon  = null;
        var overlay = document.getElementById('purchaseModal');
        if (!overlay) return;
        _showStep(1);
        _populateSummary();
        _resetPaymentBtns();
        _checkAutoCoupon();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        var overlay = document.getElementById('purchaseModal');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function _populateSummary() {
        var typeLabel = _state.type === 'one_step' ? '1-STEP EVALUATION' : '2-STEP EVALUATION';
        var sizeFmt   = '$' + parseInt(_state.account_size || 0).toLocaleString();
        var opts = [];
        if (_state.overnight_holding) opts.push('Overnight Holding');
        if (_state.weekend_holding)   opts.push('Weekend Holding');

        _setText('coChallengeLbl', typeLabel + ' — ' + sizeFmt);
        _setText('coSumType', typeLabel);
        _setText('coSumSize', sizeFmt);

        var optsRow = document.getElementById('coSumOptionsRow');
        if (opts.length) {
            _setText('coSumOptions', opts.join(' + '));
            if (optsRow) optsRow.style.display = '';
        } else {
            if (optsRow) optsRow.style.display = 'none';
        }
        var sub = (_state.final_price || 0);
        _setText('coSumSubtotal', '$' + sub.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

        var inp = document.getElementById('coCouponInput');
        if (inp) inp.value = '';
        var msg = document.getElementById('coCouponMsg');
        if (msg) msg.innerHTML = '';
        var discRow = document.getElementById('coDiscountRow');
        if (discRow) discRow.style.display = 'none';
        _updateTotal();
    }

    function _checkAutoCoupon() {
        if (window.DojiActiveCoupon) {
            var inp = document.getElementById('coCouponInput');
            if (inp) inp.value = window.DojiActiveCoupon.code;
            applyCoupon();
        }
    }

    function applyCoupon() {
        var inp = document.getElementById('coCouponInput');
        var msg = document.getElementById('coCouponMsg');
        if (!inp || !msg) return;
        var code = inp.value.trim().toUpperCase();
        if (!code) { msg.innerHTML = '<span class="co-coupon-err">Please enter a code.</span>'; return; }

        // DC coupon format: DC5-XXXXXXX, DC10-XXXXXXX, DC15-XXXXXXX, DC20-XXXXXXX
        var dcMatch = code.match(/^DC(5|10|15|20)-[A-Z0-9]{7}$/);
        if (dcMatch) {
            _coupon = { pct: parseInt(dcMatch[1], 10), label: dcMatch[1] + '% OFF' };
            msg.innerHTML = '<span class="co-coupon-ok">✓ ' + _coupon.label + ' applied!</span>';
            _updateTotal(); return;
        }
        // Server promo codes
        var promoCodes = (window.DOJI_CONFIG && window.DOJI_CONFIG.pricing && window.DOJI_CONFIG.pricing.promoCodes) || {};
        if (promoCodes[code]) {
            var promo = promoCodes[code];
            _coupon = promo.type === 'percent'
                ? { pct: promo.value, label: promo.label }
                : { flat: promo.value, label: promo.label };
            msg.innerHTML = '<span class="co-coupon-ok">✓ ' + _coupon.label + ' applied!</span>';
            _updateTotal(); return;
        }
        _coupon = null;
        msg.innerHTML = '<span class="co-coupon-err">Invalid coupon code.</span>';
        _updateTotal();
    }

    function _updateTotal() {
        var base = parseFloat((_state && _state.final_price) || 0);
        var discount = 0;
        if (_coupon) {
            if (_coupon.pct)  discount = base * _coupon.pct / 100;
            else if (_coupon.flat) discount = _coupon.flat;
        }
        var total = Math.max(base - discount, 0);
        var discRow = document.getElementById('coDiscountRow');
        var discVal = document.getElementById('coDiscountVal');
        var totalEl = document.getElementById('coTotal');
        if (_coupon && discount > 0) {
            if (discRow) discRow.style.display = '';
            if (discVal) discVal.textContent = '-$' + discount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        } else {
            if (discRow) discRow.style.display = 'none';
        }
        if (totalEl) totalEl.textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function setPayment(btn) {
        document.querySelectorAll('#coStep1 .pyt-method-btn').forEach(function (b) { b.classList.remove('pyt-method-active'); });
        btn.classList.add('pyt-method-active');
        _payment = btn.getAttribute('data-pay') || 'stripe';
    }

    function _resetPaymentBtns() {
        document.querySelectorAll('#coStep1 .pyt-method-btn').forEach(function (b) { b.classList.remove('pyt-method-active'); });
        var first = document.querySelector('#coStep1 .pyt-method-btn');
        if (first) { first.classList.add('pyt-method-active'); _payment = first.getAttribute('data-pay') || 'stripe'; }
    }

    function confirm() {
        if (!_state) return;
        var base = parseFloat(_state.final_price || 0);
        var discount = 0;
        if (_coupon) {
            if (_coupon.pct)  discount = base * _coupon.pct / 100;
            else if (_coupon.flat) discount = _coupon.flat;
        }
        var total = Math.max(base - discount, 0);
        var fmt = function (n) { return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); };
        var ref = 'CHG-' + new Date().getFullYear() + '-' + Math.random().toString(36).slice(2, 9).toUpperCase();
        _setText('coRecapChallenge', _state.type === 'one_step' ? '1-STEP EVALUATION' : '2-STEP EVALUATION');
        _setText('coRecapSize', '$' + parseInt(_state.account_size || 0).toLocaleString());
        _setText('coRecapTotal', fmt(total));
        _setText('coRecapPayment', _payment === 'confirmo' ? 'Confirmo (Crypto)' : 'Stripe (Card)');
        _setText('coRecapRef', ref);
        window.DojiActiveCoupon = null;
        _showStep(2);
    }

    function _showStep(n) {
        var s1 = document.getElementById('coStep1');
        var s2 = document.getElementById('coStep2');
        if (s1) s1.style.display = n === 1 ? '' : 'none';
        if (s2) s2.style.display = n === 2 ? '' : 'none';
    }

    function _setText(id, txt) {
        var el = document.getElementById(id);
        if (el) el.textContent = txt;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('purchaseModal');
        if (!overlay) return;
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    });

    return { open: open, close: close, setPayment: setPayment, applyCoupon: applyCoupon, confirm: confirm };
}());

/* ── Payouts tab pagination ── */
(function () {
    var PAGE_SIZE = 5;

    document.addEventListener('DOMContentLoaded', function () {
        var tbody = document.getElementById('pyoTableBody');
        var prev  = document.getElementById('pyoPrev');
        var next  = document.getElementById('pyoNext');
        var info  = document.getElementById('pyoPagInfo');
        if (!tbody || !prev || !next) return;

        var rows  = tbody.querySelectorAll('.pyo-row');
        var total = rows.length;
        if (total <= PAGE_SIZE) return;

        var page  = 0;
        var pages = Math.ceil(total / PAGE_SIZE);

        function render() {
            var start = page * PAGE_SIZE;
            var end   = start + PAGE_SIZE;
            rows.forEach(function (r, i) {
                r.style.display = (i >= start && i < end) ? '' : 'none';
            });
            if (info) info.textContent = 'PAGE ' + (page + 1) + ' / ' + pages;
            prev.disabled = (page === 0);
            next.disabled = (page >= pages - 1);
        }

        prev.addEventListener('click', function () { if (page > 0) { page--; render(); } });
        next.addEventListener('click', function () { if (page < pages - 1) { page++; render(); } });
        render();
    });
}());

/* ── PayoutDetailModal ─────────────────────────────────── */
const PayoutDetailModal = (function () {
    'use strict';

    var _data = null;

    function open(btn) {
        var raw = btn.getAttribute('data-payout');
        if (!raw) return;
        try { _data = JSON.parse(raw); } catch (e) { return; }
        _fill();
        var overlay = document.getElementById('payoutDetailModal');
        if (overlay) overlay.classList.add('active');
    }

    function close() {
        var overlay = document.getElementById('payoutDetailModal');
        if (overlay) overlay.classList.remove('active');
        _data = null;
    }

    function _fill() {
        if (!_data) return;
        _setText('pydNum', '#' + _data.num);
        _setText('pydSource', _data.source);
        _setText('pydAmount', _data.amount + ' ' + (_data.currency || ''));
        _setText('pydMethod', (_data.currency || '') + ' · ' + (_data.provider || ''));
        _setText('pydRequested', _data.requested);

        var procRow = document.getElementById('pydProcessedRow');
        if (_data.processed) {
            _setText('pydProcessed', _data.processed);
            if (procRow) procRow.style.display = '';
        } else {
            if (procRow) procRow.style.display = 'none';
        }

        var certBtn = document.getElementById('pydCertBtn');
        if (certBtn) certBtn.style.display = _data.status === 'completed' ? '' : 'none';

        var vprog = document.getElementById('pydVprog');
        if (vprog) vprog.innerHTML = _buildVprog(_data.status);
    }

    function _buildVprog(status) {
        var steps = [
            { label: 'IN REVIEW',       sub: 'Payout request being processed' },
            { label: 'ACTION REQUIRED', sub: 'Document verification needed' },
            { label: 'COMPLETED',       sub: 'Funds transferred to your account' },
        ];

        var activeIdx = 0;
        if (status === 'action_required') activeIdx = 1;
        else if (status === 'completed')  activeIdx = 2;

        var html = '';
        steps.forEach(function (step, i) {
            var done   = i < activeIdx;
            var active = i === activeIdx;
            var isLast = i === steps.length - 1;

            var dotMod   = done ? '--done' : active ? '--active' : '--empty';
            var titleMod = done ? '--done' : active ? '--active' : '';
            var lineMod  = done ? ' pyd-vstep-vline--done' : '';

            html += '<div class="pyd-vstep">';
            html += '<div class="pyd-vstep-left">';
            html += '<div class="pyd-vstep-dot pyd-vstep-dot' + dotMod + '"></div>';
            if (!isLast) html += '<div class="pyd-vstep-vline' + lineMod + '"></div>';
            html += '</div>';
            html += '<div class="pyd-vstep-right">';
            html += '<div class="pyd-vstep-title' + (titleMod ? ' pyd-vstep-title' + titleMod : '') + '">' + step.label + '</div>';
            html += '<div class="pyd-vstep-sub">' + step.sub + '</div>';
            html += '</div>';
            html += '</div>';
        });
        return html;
    }

    function downloadCert() {
        if (!_data || _data.status !== 'completed') return;
        var lines = [
            '═══════════════════════════════════════',
            '         DOJI FUNDING',
            '         PAYOUT CERTIFICATE',
            '═══════════════════════════════════════',
            '',
            'PAYOUT  : #' + _data.num,
            'SOURCE  : ' + _data.source,
            'AMOUNT  : ' + _data.amount + ' ' + (_data.currency || ''),
            'METHOD  : ' + (_data.currency || '') + ' · ' + (_data.provider || ''),
            'REQ.    : ' + _data.requested,
            'PROC.   : ' + (_data.processed || '—'),
            'STATUS  : COMPLETED',
            '',
            '═══════════════════════════════════════',
            'This certificate confirms that the above',
            'payout has been successfully processed.',
            '═══════════════════════════════════════',
        ];
        _dlText(lines.join('\n'), 'doji-certificate-payout-' + _data.num + '.txt');
    }

    function download(data) {
        if (!data) return;
        var lines = [
            'DOJI FUNDING — PAYOUT DETAILS',
            '══════════════════════════════',
            '',
            'PAYOUT    : #' + data.num,
            'SOURCE    : ' + data.source,
            'AMOUNT    : ' + data.amount + ' ' + (data.currency || ''),
            'METHOD    : ' + (data.currency || '') + ' · ' + (data.provider || ''),
            'STATUS    : ' + data.status.toUpperCase().replace('_', ' '),
            'REQUESTED : ' + data.requested,
            'PROCESSED : ' + (data.processed || 'N/A'),
            '',
            '══════════════════════════════',
        ];
        _dlText(lines.join('\n'), 'doji-payout-' + data.num + '.txt');
    }

    function _dlText(content, filename) {
        var blob = new Blob([content], { type: 'text/plain' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href     = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function _setText(id, txt) {
        var el = document.getElementById(id);
        if (el) el.textContent = txt;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('payoutDetailModal');
        if (!overlay) return;
        overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
    });

    return { open: open, close: close, downloadCert: downloadCert, download: download };
}());

/* ── Competitions Tab ── */
window.CompTab = (function () {
    /* ── Gradient palette for avatars (12 pairs) ── */
    var GRADS = [
        ['#10B981','#0EA5E9'],['#8B5CF6','#EC4899'],['#F59E0B','#EF4444'],
        ['#06B6D4','#6366F1'],['#10B981','#84CC16'],['#F97316','#FBBF24'],
        ['#6366F1','#A855F7'],['#EF4444','#F97316'],['#0EA5E9','#10B981'],
        ['#EC4899','#8B5CF6'],['#14B8A6','#3B82F6'],['#A855F7','#06B6D4'],
    ];
    var RULES = {
        monthly:      ['10% Max Overall Loss','5% Max Daily Loss','EA execution is prohibited','Minimum 3 trading days','No overnight holding on Friday'],
        championship: ['10% Max Overall Loss','4% Max Daily Loss','EA execution is prohibited','Minimum 5 trading days','News trading prohibited'],
    };

    /* ── State ── */
    var _gridTick   = null;
    var _detailTick = null;
    var _podiumRaf  = null;
    var _detailLb   = null;
    var _viewOpen   = false;

    /* ── Low-level helpers ── */
    function _pd(str)  { return str ? new Date(str.replace(' ', 'T')) : null; }
    function _esc(s)   { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function _grad(uid){ return GRADS[Math.abs(uid|0) % GRADS.length]; }
    function _ini(name){
        var p = (name||'').trim().split(/\s+/);
        return p.length >= 2 ? (p[0][0]+p[1][0]).toUpperCase() : (name||'X').slice(0,2).toUpperCase();
    }
    function _flag(code) {
        if (!code || code === '—') return '<span style="color:var(--text-dis)">—</span>';
        var iso = code.toLowerCase().slice(0, 2);
        return '<img class="lb-flag-img" src="assets/img/flags/' + iso + '.svg" alt="' + iso.toUpperCase() + '" loading="lazy" onerror="this.replaceWith(document.createTextNode(this.alt))">';
    }
    function _fmtP(n)  { return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function _fmtG(n)  { var v=Number(n); return (v>=0?'+':'')+v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})+'%'; }

    function _cd(target) {
        var diff = Math.floor((target - new Date()) / 1000);
        if (diff <= 0) return {d:'00',h:'00',m:'00',s:'00',inline:'00:00:00'};
        var d=Math.floor(diff/86400); diff-=d*86400;
        var h=Math.floor(diff/3600);  diff-=h*3600;
        var m=Math.floor(diff/60);    diff-=m*60;
        var p=function(n){return('0'+n).slice(-2);};
        return {d:p(d),h:p(h),m:p(m),s:p(diff),
                inline: d>0 ? d+'D '+p(h)+':'+p(m)+':'+p(diff) : p(h)+':'+p(m)+':'+p(diff)};
    }

    function _av(uid, name, sz) {
        var g = _grad(uid);
        return '<span class="comp-av" style="width:'+sz+'px;height:'+sz+'px;background:linear-gradient(135deg,'+g[0]+','+g[1]+');font-size:'+Math.round(sz*.38)+'px">'+_esc(_ini(name))+'</span>';
    }

    function _getComp(id) {
        var a = window.DojiCompData||[]; for(var i=0;i<a.length;i++){if(a[i].id==id)return a[i];} return null;
    }
    function _getLb(id) { var l=window.DojiCompLeaderboards||{}; return l[id]||[]; }

    /* ── Leaderboard-style helpers (reused by _table) ── */
    function _lbAv(uid, name, sz) {
        var g = _grad(uid);
        return '<span class="lb-av" style="width:'+sz+'px;height:'+sz+'px;background:linear-gradient(135deg,'+g[0]+','+g[1]+');font-size:'+Math.round(sz*.38)+'px">'+_esc(_ini(name))+'</span>';
    }
    function _fmtM(n) {
        return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    }
    function _fmtHoldC(mins) {
        mins = parseInt(mins, 10);
        if (mins < 60) return mins+'m';
        var h = Math.floor(mins/60), m = mins%60;
        if (h < 24) return h+'h'+(m > 0 ? ' '+m+'m' : '');
        var d = Math.floor(h/24), rh = h%24;
        return d+'d'+(rh > 0 ? ' '+rh+'h' : '');
    }
    function _wrC(wr)  { if (wr >= 65) return 'lb-wr--hi'; if (wr >= 50) return 'lb-wr--mid'; return 'lb-wr--lo'; }
    function _rrC(rr)  { if (rr >= 2.0) return 'lb-rr--hi'; if (rr >= 1.5) return 'lb-rr--mid'; return 'lb-rr--lo'; }
    function _rkC(rank){ if (rank===1) return 'lb-rk--1'; if (rank===2) return 'lb-rk--2'; if (rank===3) return 'lb-rk--3'; if (rank<=10) return 'lb-rk--top'; return ''; }

    /* ── Isometric voxel podium (canvas) — same style as HIW cards ── */
    function _initPodiumCanvas() {
        var canvas = document.getElementById('compPodiumCanvas');
        if (!canvas) return;
        var lb = _detailLb || [];

        /* ── Responsive sizing from right-panel container ── */
        var rightEl = canvas.closest ? canvas.closest('.comp-det-right') : null;
        var cW = rightEl ? (rightEl.clientWidth  - 32) : 440;
        var cH = rightEl ? (rightEl.clientHeight - 60) : 360; // subtract title+gap
        var W = Math.max(260, Math.min(480, cW));
        var H = Math.max(260, Math.min(460, cH || Math.round(W * 0.84)));
        var ratio = W / 460; // scale factor (base design = 460px wide)

        var sc  = 18 * ratio;          // isometric scale
        var ds  = 3.5 * ratio;         // dot size
        var avR = Math.round(26 * ratio); // avatar radius
        var avOff = Math.round(28 * ratio); // offset above platform top

        var dpr = Math.min(window.devicePixelRatio||1,2);
        canvas.style.width=W+'px'; canvas.style.height=H+'px';
        canvas.width=W*dpr; canvas.height=H*dpr;
        var ctx=canvas.getContext('2d'); ctx.scale(dpr,dpr);

        var cx=W/2, cy=H*0.65, angle=0.45;

        /* 3 stepped platforms: 2nd left · 1st center (tallest) · 3rd right */
        var plats=[
            {bx:-6.5, h:3, col:'#C0C0C0', player:lb[1]||null},
            {bx:0,    h:5, col:'#FFD700', player:lb[0]||null},
            {bx:6.5,  h:2, col:'#CD7F32', player:lb[2]||null}
        ];
        var PW=4, PD=4;

        /* Surface voxels for each platform */
        var allDots=[];
        plats.forEach(function(p){
            for(var x=0;x<=PW;x++) for(var y=0;y<=p.h;y++) for(var z=0;z<=PD;z++){
                if(x===0||x===PW||y===0||y===p.h||z===0||z===PD)
                    allDots.push({wx:(x-PW/2)+p.bx, wy:y, wz:z-PD/2, c:p.col});
            }
        });

        function proj(wx,wy,wz,a){
            var rx=wx*Math.cos(a)+wz*Math.sin(a), rz=-wx*Math.sin(a)+wz*Math.cos(a);
            return {sx:(rx-rz)*sc*0.866, sy:(rx+rz)*sc*0.5-wy*sc, depth:rx+rz-wy};
        }

        function drawAvatar(ax,ay,player){
            if(!player) return;
            var g=_grad(player.uid);
            var gr=ctx.createLinearGradient(ax-avR,ay-avR,ax+avR,ay+avR);
            gr.addColorStop(0,g[0]); gr.addColorStop(1,g[1]);
            ctx.beginPath(); ctx.arc(ax,ay,avR,0,Math.PI*2);
            ctx.fillStyle=gr; ctx.fill();
            ctx.strokeStyle='rgba(255,255,255,0.22)'; ctx.lineWidth=1.5; ctx.stroke();
            /* initials */
            ctx.fillStyle='#fff';
            ctx.font='bold '+Math.round(14*ratio)+'px monospace';
            ctx.textAlign='center'; ctx.textBaseline='middle';
            ctx.fillText(_ini(player.name),ax,ay);
            /* first name */
            ctx.fillStyle='#E8E8E8';
            ctx.font='600 '+Math.round(11*ratio)+'px monospace';
            ctx.textBaseline='top';
            ctx.fillText((player.name||'').split(' ')[0].slice(0,9).toUpperCase(),ax,ay+avR+6);
            /* gain */
            ctx.fillStyle='#10B981';
            ctx.font='700 '+Math.round(11*ratio)+'px monospace';
            ctx.fillText(_fmtG(player.gain),ax,ay+avR+6+Math.round(15*ratio));
        }

        function draw(){
            ctx.clearRect(0,0,W,H);
            var pd=allDots.map(function(d){
                var p=proj(d.wx,d.wy,d.wz,angle);
                return {sx:p.sx,sy:p.sy,depth:p.depth,c:d.c};
            });
            pd.sort(function(a,b){return a.depth-b.depth;});
            pd.forEach(function(p){
                ctx.shadowColor=p.c; ctx.shadowBlur=10;
                ctx.fillStyle=p.c;
                ctx.fillRect(cx+p.sx-ds/2,cy+p.sy-ds/2,ds,ds);
            });
            ctx.shadowBlur=0;
            plats.forEach(function(p){
                var tp=proj(p.bx,p.h,0,angle);
                drawAvatar(cx+tp.sx, cy+tp.sy-avOff, p.player);
            });
        }

        function tick(){ angle+=0.003; draw(); _podiumRaf=requestAnimationFrame(tick); }
        if(_podiumRaf){cancelAnimationFrame(_podiumRaf);_podiumRaf=null;}
        tick();
    }

    /* ── Render: podium ── */
    function _podium(lb) {
        if (!lb.length) return '<div class="comp-podium-empty">[ NO PARTICIPANTS YET ]</div>';
        return '<div class="comp-podium"><canvas class="comp-podium-canvas" id="compPodiumCanvas"></canvas></div>';
    }

    /* ── Render: leaderboard table (same 12-column layout as main LB) ── */
    function _table(lb) {
        if (!lb.length) return '<div class="comp-lb-empty">[ COMPETITION NOT YET STARTED ]</div>';
        var rows = lb.map(function(p) {
            var isMe = p.me || p.isMe;
            var wr   = Number(p.winRate || p.wr || 0);
            var pct  = Number(p.profitPct || p.gain || 0);
            var rr   = Number(p.avgRR || 0);
            return '<tr class="lb-row'+(isMe?' lb-row--me':'')+'">'
                +'<td class="lb-td lb-td-rank"><span class="lb-rk '+_rkC(p.rank)+'">'+p.rank+'</span></td>'
                +'<td class="lb-td lb-td-trader">'+_lbAv(p.uid,p.name,26)
                    +'<span class="lb-name">'+_esc(p.name)+(isMe?' <span class="lb-me-tag">YOU</span>':'')+'</span></td>'
                +'<td class="lb-td lb-td-flag">'+_flag(p.country)+'</td>'
                +'<td class="lb-td lb-td-r lb-profit">+'+_fmtM(p.profit)+'</td>'
                +'<td class="lb-td lb-td-r"><span class="lb-pct-badge">+'+pct.toFixed(2)+'%</span></td>'
                +'<td class="lb-td lb-td-r"><span class="lb-wr '+_wrC(wr)+'">'+wr+'%</span></td>'
                +'<td class="lb-td lb-td-pair"><span class="lb-asset-badge">'+_esc(p.pair||'—')+'</span></td>'
                +'<td class="lb-td lb-td-r lb-win">+'+_fmtM(p.avgWin||0)+'</td>'
                +'<td class="lb-td lb-td-r lb-loss">−'+_fmtM(p.avgLoss||0)+'</td>'
                +'<td class="lb-td lb-td-r lb-hold">'+_fmtHoldC(p.avgHold||0)+'</td>'
                +'<td class="lb-td lb-td-r"><span class="lb-rr '+_rrC(rr)+'">'+rr.toFixed(2)+'</span></td>'
                +'<td class="lb-td lb-td-r lb-trades">'+p.trades+'</td>'
                +'</tr>';
        }).join('');
        return '<div class="lb-scroll"><table class="lb-table"><thead><tr>'
            +'<th class="lb-th lb-th-rank">RANK</th>'
            +'<th class="lb-th lb-th-trader">TRADER</th>'
            +'<th class="lb-th">COUNTRY</th>'
            +'<th class="lb-th lb-th-r">PROFIT</th>'
            +'<th class="lb-th lb-th-r">PROFIT %</th>'
            +'<th class="lb-th lb-th-r">WIN RATE</th>'
            +'<th class="lb-th">ASSET</th>'
            +'<th class="lb-th lb-th-r">AVG. WIN</th>'
            +'<th class="lb-th lb-th-r">AVG. LOSS</th>'
            +'<th class="lb-th lb-th-r">AVG. HOLD</th>'
            +'<th class="lb-th lb-th-r">AVG. R:R</th>'
            +'<th class="lb-th lb-th-r">TRADES</th>'
            +'</tr></thead><tbody>'+rows+'</tbody></table></div>';
    }

    /* ── Render: right sidebar ── */
    function _sidebar(comp, lb) {
        var myEntry = null; lb.forEach(function(p){if(p.me||p.isMe)myEntry=p;});
        var rules   = (RULES[comp.category]||RULES.monthly).map(function(r){
            return '<div class="comp-side-rule"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>'+_esc(r)+'</div>';
        }).join('');
        var myRank = myEntry
            ? '<div class="comp-myrank"><div class="comp-myrank-lbl">MY CURRENT RANK</div><div class="comp-myrank-val">#'+myEntry.rank+'</div><div class="comp-myrank-sub">OF '+lb.length+'</div></div>'
            : '<div class="comp-myrank comp-myrank--none"><div class="comp-myrank-lbl">MY RANK</div><div class="comp-myrank-val">—</div><div class="comp-myrank-sub">NOT JOINED</div></div>';
        var entryTxt = comp.type==='free' ? '<span class="comp-entry--free">FREE</span>' : '$'+Number(comp.entry).toFixed(2);
        return '<div class="comp-detail-side">'
            +myRank
            +'<div class="comp-side-info">'
            +'<div class="comp-side-row"><span class="comp-side-lbl">STARTS</span><span class="comp-side-val">'+_esc((comp.starts||'').slice(0,10))+'</span></div>'
            +'<div class="comp-side-row"><span class="comp-side-lbl">ENDS</span><span class="comp-side-val">'+_esc((comp.ends||'').slice(0,10))+'</span></div>'
            +'<div class="comp-side-row"><span class="comp-side-lbl">ENTRY</span><span class="comp-side-val">'+entryTxt+'</span></div>'
            +'<div class="comp-side-row"><span class="comp-side-lbl">PARTICIPANTS</span><span class="comp-side-val">'+Number(comp.participants).toLocaleString()+'</span></div>'
            +'<div class="comp-side-row"><span class="comp-side-lbl">ORGANIZER</span><span class="comp-side-val">'+_esc(comp.organizer)+'</span></div>'
            +'<div class="comp-side-row"><span class="comp-side-lbl">PLATFORM</span><span class="comp-side-val">'+_esc(comp.platform)+'</span></div>'
            +'</div>'
            +'<div class="comp-side-rules"><div class="comp-side-rules-title">TRADING RULES</div>'+rules+'</div>'
            +'</div>';
    }

    /* ── Build full detail view HTML ── */
    function _buildDetail(comp, lb) {
        var isLive = comp.status==='live', isUp = comp.status==='upcoming';
        var cdEnd  = isLive ? comp.ends : (isUp ? comp.starts : '');
        var cdLbl  = isLive ? 'ENDING IN' : (isUp ? 'STARTS IN' : 'ENDED');
        var stLbl  = isLive ? 'ONGOING' : comp.status.toUpperCase();
        var cdT    = _pd(cdEnd);
        var cdVals = cdT ? _fcVals(cdT) : null;
        var dStr   = cdVals && cdVals.d > 0 ? String(cdVals.d) : '';
        var fcHTML = cdVals && cdEnd
            ? '<div class="flip-clock flip-clock--'+comp.status+' comp-det-fc" data-fc-target="'+_esc(cdEnd)+'" data-fc-dlen="'+dStr.length+'">'
              +_fcBuildHTML(cdVals)+'</div>'
            : '';
        var isJoined = false; lb.forEach(function(p){if(p.me||p.isMe)isJoined=true;});
        var isFree   = comp.type==='free';
        var entryTxt = isFree ? 'FREE' : '$'+Number(comp.entry).toFixed(2);
        var prizeVal = comp.prize_pool ? Number(comp.prize_pool) : 0;
        var prizeStr = prizeVal ? '$'+prizeVal.toLocaleString('en-US') : 'TBD';
        var startsStr= (comp.starts||'').slice(0,10);
        var endsStr  = (comp.ends  ||'').slice(0,10);
        return '<div class="comp-detail">'
            +'<div class="comp-detail-hdr">'
            +'<button class="comp-detail-back" onclick="CompTab.closeView()"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg> BACK</button>'
            +'<div class="comp-detail-hdr-title">'+_esc(comp.name)+'</div>'
            +'<span class="comp-status comp-status--'+comp.status+'"><span class="comp-status-dot comp-status-dot--'+comp.status+'"></span>'+stLbl+'</span>'
            +'</div>'
            +'<div class="comp-det-layout">'
            +'<div class="comp-det-left">'
            +'<div class="comp-det-edition">'+_esc(comp.edition)+'</div>'
            +'<div class="comp-det-name">'+_esc(comp.name)+'</div>'
            +'<div class="comp-det-prize-banner">'
            +'<div class="comp-det-prize-lbl">PRIZE POOL</div>'
            +'<div class="comp-det-prize-val">'+prizeStr+'</div>'
            +'</div>'
            +'<div class="comp-det-dates">'
            +'<div class="comp-det-date"><div class="comp-det-date-lbl">STARTS</div><div class="comp-det-date-val">'+_esc(startsStr)+'</div></div>'
            +'<div class="comp-det-date"><div class="comp-det-date-lbl">ENDS</div><div class="comp-det-date-val">'+_esc(endsStr)+'</div></div>'
            +'</div>'
            +'<div class="comp-det-info">'
            +'<div class="comp-det-info-block"><div class="comp-det-info-lbl">PARTICIPANTS</div><div class="comp-det-info-val">'+Number(comp.participants).toLocaleString()+'</div></div>'
            +'<div class="comp-det-info-block"><div class="comp-det-info-lbl">ENTRY</div><div class="comp-det-info-val'+(isFree?' comp-det-info-val--free':'')+'">'  +entryTxt+'</div></div>'
            +'<div class="comp-det-info-block"><div class="comp-det-info-lbl">PLATFORM</div><div class="comp-det-info-val">'+_esc(comp.platform)+'</div></div>'
            +'<div class="comp-det-info-block"><div class="comp-det-info-lbl">ORGANIZER</div><div class="comp-det-info-val">'+_esc(comp.organizer)+'</div></div>'
            +'</div>'
            +'<div class="comp-det-actions">'
            +'<button class="comp-det-ghost-btn" onclick="CompTab.openPrizepool('+comp.id+')">PRIZES</button>'
            +'<button class="comp-det-ghost-btn" onclick="CompTab.openInfo('+comp.id+')">RULES</button>'
            +'</div>'
            +(isJoined
                ? '<div class="comp-det-joined"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> JOINED</div>'
                : '<button class="comp-det-join-btn">JOIN COMPETITION</button>')
            +(fcHTML ? '<div class="comp-det-cd"><div class="comp-det-cd-lbl">'+cdLbl+'</div>'+fcHTML+'</div>' : '')
            +'</div>'
            +'<div class="comp-det-right">'
            +'<div class="comp-det-lb-title">TOURNAMENT LEADERBOARD</div>'
            +_podium(lb)
            +'</div>'
            +'</div>'
            +'<div class="comp-det-rankings">'
            +'<div class="comp-det-rankings-title">RANKINGS</div>'
            +_table(lb)
            +'</div>'
            +'</div>';
    }

    /* ── Detail countdown tick ── */
    function _tickDetail() {
        var clock = document.querySelector('#compDetailView .flip-clock[data-fc-target]');
        if (!clock) { _stopDetail(); return; }
        var target = _pd(clock.getAttribute('data-fc-target')); if (!target) return;
        var vals = _fcVals(target); if (!vals) return;
        var dStr = vals.d > 0 ? String(vals.d) : '';
        var prevDLen = parseInt(clock.getAttribute('data-fc-dlen')||'0', 10);
        if (dStr.length !== prevDLen) {
            clock.innerHTML = _fcBuildHTML(vals);
            clock.setAttribute('data-fc-dlen', String(dStr.length)); return;
        }
        var us = {}; clock.querySelectorAll('.flip-unit').forEach(function(u){ us[u.getAttribute('data-fc-unit')] = u; });
        if (dStr && us['D']) _fcUpdateUnit(us['D'], dStr);
        if (us['H']) _fcUpdateUnit(us['H'], vals.h);
        if (us['M']) _fcUpdateUnit(us['M'], vals.m);
        if (us['S']) _fcUpdateUnit(us['S'], vals.s);
    }
    function _startDetail() { _stopDetail(); _tickDetail(); _detailTick = setInterval(_tickDetail,1000); requestAnimationFrame(_initPodiumCanvas); }
    function _stopDetail()  {
        if (_detailTick) { clearInterval(_detailTick); _detailTick = null; }
        if (_podiumRaf)  { cancelAnimationFrame(_podiumRaf); _podiumRaf = null; }
    }

    /* ── Flip Clock ── */
    function _fcVals(target) {
        var diff=Math.floor((target-new Date())/1000); if(diff<=0) return null;
        var d=Math.floor(diff/86400); diff-=d*86400;
        var h=Math.floor(diff/3600);  diff-=h*3600;
        var m=Math.floor(diff/60);    diff-=m*60;
        var p2=function(n){return('0'+n).slice(-2);};
        return {d:d,h:p2(h),m:p2(m),s:p2(diff)};
    }
    function _fcCardHTML(digit) {
        return '<div class="flip-card" data-fc-val="'+digit+'">'
            +'<div class="flip-top-half"><div class="flip-inner">'+digit+'</div></div>'
            +'<div class="flip-bot-half"><div class="flip-inner">'+digit+'</div></div>'
            +'</div>';
    }
    function _fcUnitHTML(val,lbl,key) {
        var c=''; for(var i=0;i<val.length;i++) c+=_fcCardHTML(val[i]);
        return '<div class="flip-unit" data-fc-unit="'+key+'"><div class="flip-unit-panels">'+c+'</div>'
            +'<div class="flip-unit-lbl">'+lbl+'</div></div>';
    }
    function _fcBuildHTML(vals) {
        var dStr=vals.d>0?String(vals.d):'', p=[];
        if(dStr){p.push(_fcUnitHTML(dStr,'DAYS','D'));p.push('<span class="flip-sep">:</span>');}
        p.push(_fcUnitHTML(vals.h,'HRS','H')); p.push('<span class="flip-sep">:</span>');
        p.push(_fcUnitHTML(vals.m,'MIN','M')); p.push('<span class="flip-sep">:</span>');
        p.push(_fcUnitHTML(vals.s,'SEC','S'));
        return p.join('');
    }
    function _fcFlip(card,nv) {
        var ov=card.getAttribute('data-fc-val'); if(ov===nv) return;
        card.setAttribute('data-fc-val',nv);
        var bi=card.querySelector('.flip-bot-half .flip-inner'); if(bi) bi.textContent=nv;
        var tf=document.createElement('div'); tf.className='flip-top-flap';
        tf.innerHTML='<div class="flip-inner">'+ov+'</div>';
        var bf=document.createElement('div'); bf.className='flip-bot-flap';
        bf.innerHTML='<div class="flip-inner">'+nv+'</div>';
        card.appendChild(tf); card.appendChild(bf);
        setTimeout(function(){
            var ti=card.querySelector('.flip-top-half .flip-inner'); if(ti) ti.textContent=nv;
            if(card.contains(tf)) card.removeChild(tf);
            if(card.contains(bf)) card.removeChild(bf);
        },420);
    }
    function _fcUpdateUnit(unit,val) {
        var cards=unit.querySelectorAll('.flip-card');
        for(var i=0;i<cards.length&&i<val.length;i++) _fcFlip(cards[i],val[i]);
    }
    function _fcNewEnd() {
        /* Demo helper: generate an end-date string 30 days from now */
        var t=new Date(Date.now()+30*86400*1000);
        var p=function(n){return('0'+n).slice(-2);};
        return t.getFullYear()+'-'+p(t.getMonth()+1)+'-'+p(t.getDate())+' '+p(t.getHours())+':'+p(t.getMinutes())+':'+p(t.getSeconds());
    }
    function _fcTick() {
        if(_viewOpen) return;
        document.querySelectorAll('.flip-clock[data-fc-target]').forEach(function(clock){
            var target=_pd(clock.getAttribute('data-fc-target')); if(!target) return;
            var vals=_fcVals(target);
            if(!vals) {
                /* Demo: auto-restart with a new 30-day window */
                var newStr=_fcNewEnd();
                clock.setAttribute('data-fc-target',newStr);
                var wrap=clock.closest('[data-flip-end]');
                if(wrap) wrap.setAttribute('data-flip-end',newStr);
                vals=_fcVals(_pd(newStr)); if(!vals) return;
                clock.innerHTML=_fcBuildHTML(vals);
                clock.setAttribute('data-fc-dlen',String(vals.d>0?String(vals.d).length:0));
                return;
            }
            var dStr=vals.d>0?String(vals.d):'';
            var prevDLen=parseInt(clock.getAttribute('data-fc-dlen')||'0',10);
            if(dStr.length!==prevDLen){
                clock.innerHTML=_fcBuildHTML(vals);
                clock.setAttribute('data-fc-dlen',String(dStr.length)); return;
            }
            var us={}; clock.querySelectorAll('.flip-unit').forEach(function(u){us[u.getAttribute('data-fc-unit')]=u;});
            if(dStr&&us['D']) _fcUpdateUnit(us['D'],dStr);
            if(us['H']) _fcUpdateUnit(us['H'],vals.h);
            if(us['M']) _fcUpdateUnit(us['M'],vals.m);
            if(us['S']) _fcUpdateUnit(us['S'],vals.s);
        });
    }
    function _fcInit() {
        document.querySelectorAll('[data-flip-end]').forEach(function(wrap){
            if(wrap.querySelector('.flip-clock')) return;
            var endStr=wrap.getAttribute('data-flip-end'), status=wrap.getAttribute('data-flip-status')||'live';
            if(!endStr) return;
            var target=_pd(endStr); if(!target) return;
            var vals=_fcVals(target); if(!vals) return;
            var dStr=vals.d>0?String(vals.d):'';
            var clock=document.createElement('div');
            clock.className='flip-clock flip-clock--'+status;
            clock.setAttribute('data-fc-target',endStr);
            clock.setAttribute('data-fc-dlen',String(dStr.length));
            clock.innerHTML=_fcBuildHTML(vals);
            var cdEl=wrap.querySelector('.comp-block-cd');
            if(cdEl) wrap.replaceChild(clock,cdEl); else wrap.insertBefore(clock,wrap.firstChild);
        });
    }

    /* ── Grid tick ── */
    function _startTick() { if(_gridTick)return; _fcInit(); _fcTick(); _gridTick=setInterval(_fcTick,1000); }
    function _stopTick()  { if(_gridTick){clearInterval(_gridTick);_gridTick=null;} }

    /* ── Drawer toggle ── */
    function toggleDrawer(id) {
        var el=document.getElementById(id); if(!el) return;
        var body=el.querySelector('.comp-drawer-body');
        var open=el.classList.contains('comp-drawer--open');
        el.classList.toggle('comp-drawer--open',!open);
        if(body) body.hidden=open;
    }

    /* ── Sub-tab filter ── */
    function _filter(f) {
        document.querySelectorAll('#compGrid .comp-card').forEach(function(card){
            var cat=card.getAttribute('data-comp-category'), j=card.getAttribute('data-comp-joined')==='1';
            card.hidden = !(f==='all' || (f==='joined'&&j) || (f==='championship'&&cat==='championship'));
        });
    }

    /* ── Public API ── */
    function openView(id) {
        var comp=_getComp(id); if(!comp)return;
        var lb=_getLb(id);
        _viewOpen = true;
        _detailLb = lb;
        var tab    = document.getElementById('tab-competitions');
        var detail = document.getElementById('compDetailView');
        if (!detail) { detail=document.createElement('div'); detail.id='compDetailView'; tab.appendChild(detail); }
        detail.innerHTML = _buildDetail(comp, lb);
        var blocks = document.getElementById('compBlocks');
        var drawers = document.getElementById('compDrawers');
        if (blocks) blocks.hidden = true;
        if (drawers) drawers.hidden = true;
        detail.hidden = false;
        _startDetail();
    }

    function closeView() {
        _viewOpen = false; _stopDetail();
        var detail = document.getElementById('compDetailView');
        if (detail) detail.hidden = true;
        var blocks = document.getElementById('compBlocks');
        var drawers = document.getElementById('compDrawers');
        if (blocks) blocks.hidden = false;
        if (drawers) drawers.hidden = false;
    }

    function openPrizepool(id) { /* TBD */ }
    function openInfo(id)      { /* TBD */ }

    /* ── Init ── */
    function init() {
        _fcInit();
        document.querySelectorAll('.comp-subtab-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelectorAll('.comp-subtab-btn').forEach(function(b){b.classList.remove('comp-subtab-btn--active');});
                btn.classList.add('comp-subtab-btn--active');
                _filter(btn.getAttribute('data-comp-filter'));
            });
        });
        /* Re-sync clocks when browser tab regains focus */
        document.addEventListener('visibilitychange', function(){
            if(!document.hidden && !_viewOpen) _fcTick();
        });
        var tabEl = document.getElementById('tab-competitions');
        if (tabEl) {
            new MutationObserver(function(){
                tabEl.classList.contains('active') ? _startTick() : _stopTick();
            }).observe(tabEl,{attributes:true,attributeFilter:['class']});
            if (tabEl.classList.contains('active')) _startTick();
        }
    }

    document.addEventListener('DOMContentLoaded', init);
    return { openView:openView, closeView:closeView, openPrizepool:openPrizepool, openInfo:openInfo, toggleDrawer:toggleDrawer };
}());

/* ══════════════════════════════════════════════════════════
   LEADERBOARD TAB
══════════════════════════════════════════════════════════ */
const LeaderboardTab = (function() {
    'use strict';

    var GRADS = [
        ['#10B981','#0EA5E9'],['#8B5CF6','#EC4899'],['#F59E0B','#EF4444'],
        ['#06B6D4','#6366F1'],['#10B981','#84CC16'],['#F97316','#FBBF24'],
        ['#6366F1','#A855F7'],['#EF4444','#F97316'],['#0EA5E9','#10B981'],
        ['#EC4899','#8B5CF6'],['#14B8A6','#3B82F6'],['#A855F7','#06B6D4'],
    ];

    var LB_DATA = [];
    var _loaded  = false;

    /* Demo traders — shown until real public users exist */
    var _DEMO = [
        {rank:1,  uid:201, name:'Marcus T.',    country:'US', size:50000,  profit:17450, profitPct:34.90, winRate:74, pair:'XAU/USD', avgWin:312.50, avgLoss:145.20, avgHold:92,  avgRR:2.15, trades:67,  fundedDays:342, totalPayout:24800, highestPayout:8200,  payoutCount:12},
        {rank:2,  uid:202, name:'Yuki N.',      country:'JP', size:25000,  profit:8225,  profitPct:32.90, winRate:71, pair:'USD/JPY', avgWin:198.40, avgLoss:102.30, avgHold:48,  avgRR:1.94, trades:52,  fundedDays:280, totalPayout:11600, highestPayout:4800,  payoutCount:10},
        {rank:3,  uid:203, name:'Anya K.',      country:'DE', size:100000, profit:31200, profitPct:31.20, winRate:68, pair:'EUR/USD', avgWin:489.60, avgLoss:198.40, avgHold:135, avgRR:2.47, trades:88,  fundedDays:415, totalPayout:42500, highestPayout:18900, payoutCount:9},
        {rank:4,  uid:204, name:'James O.',     country:'GB', size:50000,  profit:15100, profitPct:30.20, winRate:72, pair:'GBP/USD', avgWin:285.30, avgLoss:119.80, avgHold:78,  avgRR:2.38, trades:74,  fundedDays:356, totalPayout:21400, highestPayout:7100,  payoutCount:11},
        {rank:5,  uid:205, name:'Sofia R.',     country:'BR', size:10000,  profit:2910,  profitPct:29.10, winRate:66, pair:'EUR/USD', avgWin:84.20,  avgLoss:42.50,  avgHold:64,  avgRR:1.98, trades:48,  fundedDays:187, totalPayout:4100,  highestPayout:1650,  payoutCount:7},
        {rank:6,  uid:206, name:'Luca M.',      country:'IT', size:25000,  profit:7175,  profitPct:28.70, winRate:69, pair:'NAS100',  avgWin:176.40, avgLoss:88.30,  avgHold:210, avgRR:1.99, trades:56,  fundedDays:256, totalPayout:10200, highestPayout:4100,  payoutCount:9},
        {rank:7,  uid:207, name:'Elena V.',     country:'RU', size:200000, profit:55200, profitPct:27.60, winRate:65, pair:'GBP/JPY', avgWin:922.50, avgLoss:398.60, avgHold:180, avgRR:2.31, trades:93,  fundedDays:521, totalPayout:78400, highestPayout:26500, payoutCount:14},
        {rank:8,  uid:208, name:'Ahmed F.',     country:'AE', size:50000,  profit:13650, profitPct:27.30, winRate:67, pair:'XAU/USD', avgWin:278.40, avgLoss:130.20, avgHold:55,  avgRR:2.14, trades:71,  fundedDays:312, totalPayout:19200, highestPayout:6400,  payoutCount:10},
        {rank:9,  uid:209, name:'Clara S.',     country:'SE', size:10000,  profit:2690,  profitPct:26.90, winRate:70, pair:'USD/JPY', avgWin:79.80,  avgLoss:38.40,  avgHold:36,  avgRR:2.08, trades:43,  fundedDays:145, totalPayout:3800,  highestPayout:1500,  payoutCount:6},
        {rank:10, uid:210, name:'Noah B.',      country:'CA', size:100000, profit:26500, profitPct:26.50, winRate:63, pair:'NAS100',  avgWin:464.20, avgLoss:215.80, avgHold:285, avgRR:2.15, trades:79,  fundedDays:389, totalPayout:37600, highestPayout:12500, payoutCount:8},
        {rank:11, uid:211, name:'Hana M.',      country:'JP', size:25000,  profit:6525,  profitPct:26.10, winRate:68, pair:'EUR/JPY', avgWin:168.30, avgLoss:84.90,  avgHold:72,  avgRR:1.98, trades:58,  fundedDays:234, totalPayout:9200,  highestPayout:3800,  payoutCount:8},
        {rank:12, uid:212, name:'Pierre D.',    country:'FR', size:50000,  profit:12800, profitPct:25.60, winRate:64, pair:'EUR/USD', avgWin:259.80, avgLoss:128.50, avgHold:96,  avgRR:2.02, trades:72,  fundedDays:278, totalPayout:18100, highestPayout:5900,  payoutCount:10},
        {rank:13, uid:213, name:'Tom H.',       country:'AU', size:50000,  profit:12450, profitPct:24.90, winRate:67, pair:'AUD/USD', avgWin:244.60, avgLoss:116.80, avgHold:83,  avgRR:2.09, trades:69,  fundedDays:265, totalPayout:17500, highestPayout:5600,  payoutCount:9},
        {rank:14, uid:214, name:'Zara A.',      country:'ZA', size:5000,   profit:1235,  profitPct:24.70, winRate:65, pair:'XAU/USD', avgWin:42.80,  avgLoss:21.40,  avgHold:44,  avgRR:2.00, trades:38,  fundedDays:198, totalPayout:1740,  highestPayout:720,   payoutCount:7},
        {rank:15, uid:215, name:'Felix W.',     country:'DE', size:100000, profit:24300, profitPct:24.30, winRate:62, pair:'DAX40',   avgWin:435.60, avgLoss:204.20, avgHold:198, avgRR:2.13, trades:82,  fundedDays:362, totalPayout:34200, highestPayout:11400, payoutCount:8},
        {rank:16, uid:216, name:'Mei L.',       country:'SG', size:25000,  profit:5975,  profitPct:23.90, winRate:66, pair:'XAU/USD', avgWin:152.40, avgLoss:78.20,  avgHold:58,  avgRR:1.95, trades:54,  fundedDays:218, totalPayout:8400,  highestPayout:3400,  payoutCount:7},
        {rank:17, uid:217, name:'Ryu T.',       country:'JP', size:10000,  profit:2360,  profitPct:23.60, winRate:63, pair:'USD/JPY', avgWin:71.50,  avgLoss:36.80,  avgHold:42,  avgRR:1.94, trades:46,  fundedDays:156, totalPayout:3300,  highestPayout:1350,  payoutCount:6},
        {rank:18, uid:218, name:'Isabel C.',    country:'ES', size:50000,  profit:11700, profitPct:23.40, winRate:69, pair:'EUR/USD', avgWin:232.10, avgLoss:108.40, avgHold:76,  avgRR:2.14, trades:68,  fundedDays:245, totalPayout:16400, highestPayout:5200,  payoutCount:9},
        {rank:19, uid:219, name:'Owen P.',      country:'GB', size:200000, profit:46400, profitPct:23.20, winRate:64, pair:'GBP/USD', avgWin:798.60, avgLoss:368.40, avgHold:165, avgRR:2.17, trades:90,  fundedDays:684, totalPayout:65800, highestPayout:22400, payoutCount:12},
        {rank:20, uid:220, name:'Katya M.',     country:'PL', size:25000,  profit:5750,  profitPct:23.00, winRate:71, pair:'EUR/USD', avgWin:141.80, avgLoss:69.20,  avgHold:52,  avgRR:2.05, trades:57,  fundedDays:202, totalPayout:8100,  highestPayout:3200,  payoutCount:7},
        {rank:21, uid:221, name:'Sam B.',       country:'US', size:5000,   profit:1140,  profitPct:22.80, winRate:67, pair:'NAS100',  avgWin:38.60,  avgLoss:19.20,  avgHold:178, avgRR:2.01, trades:41,  fundedDays:178, totalPayout:1600,  highestPayout:680,   payoutCount:6},
        {rank:22, uid:222, name:'Lin X.',       country:'CN', size:100000, profit:22700, profitPct:22.70, winRate:61, pair:'XAU/USD', avgWin:412.80, avgLoss:198.60, avgHold:120, avgRR:2.08, trades:81,  fundedDays:334, totalPayout:32100, highestPayout:10800, payoutCount:8},
        {rank:23, uid:223, name:'Priya S.',     country:'IN', size:20000,  profit:4480,  profitPct:22.40, winRate:65, pair:'USD/JPY', avgWin:134.40, avgLoss:69.60,  avgHold:38,  avgRR:1.93, trades:44,  fundedDays:142, totalPayout:5600,  highestPayout:2100,  payoutCount:6},
        {rank:24, uid:224, name:'Nils J.',      country:'SE', size:50000,  profit:11100, profitPct:22.20, winRate:68, pair:'EUR/USD', avgWin:219.80, avgLoss:104.30, avgHold:68,  avgRR:2.11, trades:70,  fundedDays:224, totalPayout:15600, highestPayout:4900,  payoutCount:14},
        {rank:25, uid:225, name:'Kenji Y.',     country:'JP', size:30000,  profit:6600,  profitPct:22.00, winRate:66, pair:'XAU/USD', avgWin:162.50, avgLoss:80.20,  avgHold:60,  avgRR:2.03, trades:56,  fundedDays:198, totalPayout:8300,  highestPayout:3100,  payoutCount:7},
        {rank:26, uid:226, name:'Ana G.',       country:'MX', size:15000,  profit:3270,  profitPct:21.80, winRate:64, pair:'EUR/USD', avgWin:111.60, avgLoss:56.70,  avgHold:50,  avgRR:1.97, trades:39,  fundedDays:165, totalPayout:4100,  highestPayout:1650,  payoutCount:6},
        {rank:27, uid:227, name:'Tobias F.',    country:'CH', size:90000,  profit:19440, profitPct:21.60, winRate:60, pair:'USD/CHF', avgWin:358.60, avgLoss:176.60, avgHold:148, avgRR:2.03, trades:78,  fundedDays:312, totalPayout:27500, highestPayout:9200,  payoutCount:9},
        {rank:28, uid:228, name:'Maria E.',     country:'PT', size:20000,  profit:4300,  profitPct:21.50, winRate:67, pair:'EUR/USD', avgWin:129.60, avgLoss:64.80,  avgHold:45,  avgRR:2.00, trades:45,  fundedDays:134, totalPayout:5400,  highestPayout:2050,  payoutCount:5},
        {rank:29, uid:229, name:'Chris L.',     country:'AU', size:60000,  profit:12840, profitPct:21.40, winRate:63, pair:'AUD/JPY', avgWin:257.80, avgLoss:129.80, avgHold:88,  avgRR:1.98, trades:67,  fundedDays:256, totalPayout:16100, highestPayout:5200,  payoutCount:8},
        {rank:30, uid:230, name:'Valentina R.', country:'IT', size:30000,  profit:6390,  profitPct:21.30, winRate:70, pair:'EUR/USD', avgWin:153.10, avgLoss:73.40,  avgHold:55,  avgRR:2.08, trades:58,  fundedDays:189, totalPayout:7900,  highestPayout:2900,  payoutCount:7},
        {rank:31, uid:231, name:'Ethan W.',     country:'US', size:200000, profit:42000, profitPct:21.00, winRate:62, pair:'NAS100',  avgWin:742.80, avgLoss:354.60, avgHold:302, avgRR:2.09, trades:86,  fundedDays:456, totalPayout:59400, highestPayout:20200, payoutCount:11},
        {rank:32, uid:232, name:'Aiko T.',      country:'JP', size:45000,  profit:9405,  profitPct:20.90, winRate:65, pair:'USD/JPY', avgWin:280.80, avgLoss:143.10, avgHold:40,  avgRR:1.96, trades:47,  fundedDays:178, totalPayout:11800, highestPayout:3900,  payoutCount:7},
        {rank:33, uid:233, name:'David K.',     country:'IL', size:60000,  profit:12480, profitPct:20.80, winRate:61, pair:'USD/JPY', avgWin:255.10, avgLoss:130.10, avgHold:92,  avgRR:1.96, trades:69,  fundedDays:234, totalPayout:15600, highestPayout:5000,  payoutCount:8},
        {rank:34, uid:234, name:'Sophie B.',    country:'FR', size:35000,  profit:7245,  profitPct:20.70, winRate:68, pair:'EUR/GBP', avgWin:174.70, avgLoss:86.20,  avgHold:62,  avgRR:2.02, trades:57,  fundedDays:198, totalPayout:9100,  highestPayout:3200,  payoutCount:7},
        {rank:35, uid:235, name:'Ivan P.',      country:'RU', size:90000,  profit:18360, profitPct:20.40, winRate:60, pair:'USD/JPY', avgWin:344.50, avgLoss:173.30, avgHold:136, avgRR:1.99, trades:76,  fundedDays:289, totalPayout:25800, highestPayout:8600,  payoutCount:8},
        {rank:36, uid:236, name:'Mia H.',       country:'NO', size:15000,  profit:3045,  profitPct:20.30, winRate:66, pair:'EUR/USD', avgWin:104.40, avgLoss:53.40,  avgHold:46,  avgRR:1.96, trades:38,  fundedDays:145, totalPayout:3800,  highestPayout:1450,  payoutCount:5},
        {rank:37, uid:237, name:'Oliver C.',    country:'CA', size:70000,  profit:14140, profitPct:20.20, winRate:64, pair:'USD/CAD', avgWin:286.40, avgLoss:145.90, avgHold:79,  avgRR:1.96, trades:68,  fundedDays:212, totalPayout:17800, highestPayout:5800,  payoutCount:7},
        {rank:38, uid:238, name:'Fatima A.',    country:'MA', size:10000,  profit:2010,  profitPct:20.10, winRate:67, pair:'EUR/USD', avgWin:60.20,  avgLoss:30.60,  avgHold:43,  avgRR:1.97, trades:46,  fundedDays:134, totalPayout:2800,  highestPayout:1100,  payoutCount:5},
        {rank:39, uid:239, name:'Ravi M.',      country:'IN', size:35000,  profit:6965,  profitPct:19.90, winRate:63, pair:'GBP/JPY', avgWin:170.20, avgLoss:87.40,  avgHold:58,  avgRR:1.95, trades:55,  fundedDays:167, totalPayout:8700,  highestPayout:3000,  payoutCount:6},
        {rank:40, uid:240, name:'Emma L.',      country:'SE', size:70000,  profit:13720, profitPct:19.60, winRate:62, pair:'EUR/USD', avgWin:277.80, avgLoss:143.60, avgHold:85,  avgRR:1.93, trades:66,  fundedDays:198, totalPayout:17200, highestPayout:5500,  payoutCount:7},
        {rank:41, uid:241, name:'Darius B.',    country:'RO', size:125000, profit:24000, profitPct:19.20, winRate:59, pair:'EUR/USD', avgWin:453.50, avgLoss:235.50, avgHold:128, avgRR:1.93, trades:74,  fundedDays:278, totalPayout:30400, highestPayout:10200, payoutCount:8},
        {rank:42, uid:242, name:'Alicia F.',    country:'ES', size:40000,  profit:7560,  profitPct:18.90, winRate:65, pair:'EUR/USD', avgWin:183.00, avgLoss:93.80,  avgHold:60,  avgRR:1.95, trades:57,  fundedDays:167, totalPayout:9500,  highestPayout:3200,  payoutCount:6},
        {rank:43, uid:243, name:'Max S.',       country:'DE', size:80000,  profit:15040, profitPct:18.80, winRate:61, pair:'DAX40',   avgWin:308.50, avgLoss:160.60, avgHold:178, avgRR:1.92, trades:65,  fundedDays:234, totalPayout:18900, highestPayout:6200,  payoutCount:7},
        {rank:44, uid:244, name:'Li W.',        country:'CN', size:5000,   profit:935,   profitPct:18.70, winRate:64, pair:'XAU/USD', avgWin:32.40,  avgLoss:16.80,  avgHold:48,  avgRR:1.93, trades:36,  fundedDays:156, totalPayout:1320,  highestPayout:560,   payoutCount:5},
        {rank:45, uid:245, name:'Björn E.',     country:'SE', size:45000,  profit:8370,  profitPct:18.60, winRate:63, pair:'EUR/USD', avgWin:253.80, avgLoss:131.40, avgHold:44,  avgRR:1.93, trades:44,  fundedDays:178, totalPayout:10500, highestPayout:3600,  payoutCount:6},
        {rank:46, uid:246, name:'Carmen V.',    country:'CO', size:40000,  profit:7360,  profitPct:18.40, winRate:62, pair:'EUR/USD', avgWin:178.90, avgLoss:93.10,  avgHold:56,  avgRR:1.92, trades:56,  fundedDays:156, totalPayout:9200,  highestPayout:3100,  payoutCount:6},
        {rank:47, uid:247, name:'Josh K.',      country:'AU', size:175000, profit:31850, profitPct:18.20, winRate:60, pair:'AUD/USD', avgWin:573.00, avgLoss:299.80, avgHold:182, avgRR:1.91, trades:82,  fundedDays:312, totalPayout:44900, highestPayout:15200, payoutCount:22},
        {rank:48, uid:248, name:'Natalia P.',   country:'BR', size:80000,  profit:14480, profitPct:18.10, winRate:61, pair:'EUR/USD', avgWin:295.00, avgLoss:154.90, avgHold:78,  avgRR:1.90, trades:64,  fundedDays:218, totalPayout:18200, highestPayout:5900,  payoutCount:7},
        {rank:49, uid:249, name:'Hugo M.',      country:'FR', size:150000, profit:27000, profitPct:18.00, winRate:58, pair:'EUR/USD', avgWin:513.90, avgLoss:274.20, avgHold:125, avgRR:1.87, trades:72,  fundedDays:289, totalPayout:34100, highestPayout:11400, payoutCount:8},
        {rank:50, uid:250, name:'Léa F.',       country:'FR', size:50000,  profit:8850,  profitPct:17.70, winRate:60, pair:'EUR/GBP', avgWin:198.60, avgLoss:105.30, avgHold:72,  avgRR:1.88, trades:63,  fundedDays:168, totalPayout:11100, highestPayout:3800,  payoutCount:6},
    ];

    var _currentFilter = 'all';
    var _currentTier   = 'all';

    /* ── Tier configuration ── */
    var _TIER_CFG = {
        legend:   { label:'LEGEND',   color:'#EC4899', bg:'rgba(236,72,153,0.10)',  min:30 },
        masters:  { label:'MASTERS',  color:'#F97316', bg:'rgba(249,115,22,0.10)',  min:27 },
        diamond:  { label:'DIAMOND',  color:'#06B6D4', bg:'rgba(6,182,212,0.10)',   min:24 },
        platinum: { label:'PLATINUM', color:'#8B5CF6', bg:'rgba(139,92,246,0.10)',  min:21 },
        gold:     { label:'GOLD',     color:'#D4A843', bg:'rgba(212,168,67,0.10)',  min:19 },
        silver:   { label:'SILVER',   color:'#9CA3AF', bg:'rgba(156,163,175,0.10)',min:17 },
        bronze:   { label:'BRONZE',   color:'#CD7F32', bg:'rgba(205,127,50,0.10)',  min:0  },
    };

    function _tierKey(pct) {
        if (pct >= 30) return 'legend';
        if (pct >= 27) return 'masters';
        if (pct >= 24) return 'diamond';
        if (pct >= 21) return 'platinum';
        if (pct >= 19) return 'gold';
        if (pct >= 17) return 'silver';
        return 'bronze';
    }

    function _tierBadge(key) {
        var t = _TIER_CFG[key];
        if (!t) return '';
        return '<span class="lb-tier-badge lb-tier--'+key+'" style="color:'+t.color+';background:'+t.bg+';border-color:'+t.color+'">'+t.label+'</span>';
    }

    function _rng(seed) {
        var s = seed >>> 0;
        return function() { s = (Math.imul(1664525, s) + 1013904223) >>> 0; return s / 0xFFFFFFFF; };
    }

    function _lbScore(p) {
        var rand = _rng(p.uid * 31337);
        var wr   = Math.min(20, Math.round(p.winRate / 5));
        var pf   = Math.min(20, Math.round((p.avgRR || (1.5 + rand() * 1.0)) * 8));
        var rr   = Math.min(20, Math.round(((p.avgRR || 1.5) - 1.0) * 20));
        var vol  = Math.min(20, Math.round(12 + rand() * 8));
        var cons = Math.min(20, Math.round(10 + rand() * 10));
        return wr + Math.min(20, pf) + Math.min(20, rr) + vol + cons;
    }

    /* ── Fetch real leaderboard data from API ── */
    function _fetchData() {
        var body  = document.getElementById('lbBody');
        var empty = document.getElementById('lbEmpty');
        if (body)  body.innerHTML = '<tr><td colspan="14" class="lb-loading">[ LOADING... ]</td></tr>';
        if (empty) empty.style.display = 'none';

        fetch('api/leaderboard.php')
            .then(function(r) { return r.json(); })
            .then(function(json) {
                if (json.success && Array.isArray(json.data) && json.data.length) {
                    LB_DATA = json.data;
                    if (typeof TestimonialsTab !== 'undefined') TestimonialsTab.init();
                }
                filter(_currentFilter);
            })
            .catch(function() {
                filter(_currentFilter);
            });
    }

    /* ── Helpers ── */
    function _esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function _flag(code) {
        if (!code || code === '—') return '<span class="lb-flag-empty">—</span>';
        var iso = code.toLowerCase().slice(0, 2);
        return '<img class="lb-flag-img" src="assets/img/flags/' + iso + '.svg" alt="' + iso.toUpperCase() + '" loading="lazy" onerror="this.replaceWith(document.createTextNode(this.alt))">';
    }

    function _grad(uid) { return GRADS[Math.abs(uid|0) % GRADS.length]; }

    function _ini(name) {
        var p = (name||'').trim().split(/\s+/);
        return p.length >= 2 ? (p[0][0]+p[p.length-1][0]).toUpperCase() : (name||'X').slice(0,2).toUpperCase();
    }

    function _av(uid, name, sz) {
        var g = _grad(uid);
        return '<span class="lb-av" style="width:'+sz+'px;height:'+sz+'px;background:linear-gradient(135deg,'+g[0]+','+g[1]+');font-size:'+Math.round(sz*.38)+'px">'+_esc(_ini(name))+'</span>';
    }

    function _fmtMoney(n) {
        return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    }

    function _fmtHold(mins) {
        mins = parseInt(mins, 10);
        if (mins < 60) return mins+'m';
        var h = Math.floor(mins/60), m = mins%60;
        if (h < 24) return h+'h'+(m > 0 ? ' '+m+'m' : '');
        var d = Math.floor(h/24), rh = h%24;
        return d+'d'+(rh > 0 ? ' '+rh+'h' : '');
    }

    function _rankCls(rank) {
        if (rank === 1) return 'lb-rk--1';
        if (rank === 2) return 'lb-rk--2';
        if (rank === 3) return 'lb-rk--3';
        if (rank <= 10) return 'lb-rk--top';
        return '';
    }

    function _wrCls(wr) {
        if (wr >= 65) return 'lb-wr--hi';
        if (wr >= 50) return 'lb-wr--mid';
        return 'lb-wr--lo';
    }

    function _rrCls(rr) {
        if (rr >= 2.0) return 'lb-rr--hi';
        if (rr >= 1.5) return 'lb-rr--mid';
        return 'lb-rr--lo';
    }

    /* ── Stats strip helpers ── */
    function _fmtK(n) {
        if (n >= 1000) {
            var k = n / 1000;
            return '$' + (k % 1 === 0 ? k.toFixed(0) : k.toFixed(1)) + 'K';
        }
        return '$' + n;
    }

    function _fmtDays(d) {
        if (d >= 365) {
            var y = Math.floor(d / 365), m = Math.floor((d % 365) / 30);
            return y + 'y' + (m > 0 ? ' ' + m + 'mo' : '');
        }
        if (d >= 30) return Math.floor(d / 30) + 'mo';
        return d + 'd';
    }

    /* ── Stats strip ── */
    function _updateStats(data) {
        var elTP   = document.getElementById('lbStatTotalPayout');
        var elTPn  = document.getElementById('lbStatTotalPayoutName');
        var elDur  = document.getElementById('lbStatDuration');
        var elDurn = document.getElementById('lbStatDurationName');
        var elHP   = document.getElementById('lbStatHighPayout');
        var elHPn  = document.getElementById('lbStatHighPayoutName');
        var elPC   = document.getElementById('lbStatPayoutCount');
        var elPCn  = document.getElementById('lbStatPayoutCountName');
        if (!elTP) return;

        if (!data.length) {
            [elTP, elTPn, elDur, elDurn, elHP, elHPn, elPC, elPCn].forEach(function(el) { if (el) el.innerHTML = '—'; });
            return;
        }

        var bestTP  = data.reduce(function(b, p) { return p.totalPayout   > b.totalPayout   ? p : b; });
        var bestDur = data.reduce(function(b, p) { return p.fundedDays    > b.fundedDays    ? p : b; });
        var bestHP  = data.reduce(function(b, p) { return p.highestPayout > b.highestPayout ? p : b; });
        var bestPC  = data.reduce(function(b, p) { return p.payoutCount   > b.payoutCount   ? p : b; });

        function _nameFlagHtml(p) {
            return '<span class="lb-stat-sub-name">'+_esc(p.name)+'</span>'+_flag(p.country);
        }

        if (elTP)   elTP.textContent  = _fmtK(bestTP.totalPayout);
        if (elTPn)  elTPn.innerHTML   = _nameFlagHtml(bestTP);
        if (elDur)  elDur.textContent = _fmtDays(bestDur.fundedDays);
        if (elDurn) elDurn.innerHTML  = _nameFlagHtml(bestDur);
        if (elHP)   elHP.textContent  = _fmtK(bestHP.highestPayout);
        if (elHPn)  elHPn.innerHTML   = _nameFlagHtml(bestHP);
        if (elPC)   elPC.textContent  = bestPC.payoutCount;
        if (elPCn)  elPCn.innerHTML   = _nameFlagHtml(bestPC);
    }

    /* ── Build table rows ── */
    function _buildRows(data) {
        if (!data.length) return '';
        return data.map(function(p, i) {
            var rank  = (_currentFilter === 'all' && _currentTier === 'all') ? p.rank : (i + 1);
            var isMe  = !!p.me;
            var tk    = _tierKey(p.profitPct);
            var score = _lbScore(p);
            var tc    = _TIER_CFG[tk].color;
            return '<tr class="lb-row lb-row--clickable'+(isMe ? ' lb-row--me' : '')+'" onclick="TraderProfile.open('+p.uid+')">'
                +'<td class="lb-td lb-td-rank"><span class="lb-rk '+_rankCls(rank)+'">'+rank+'</span></td>'
                +'<td class="lb-td lb-td-trader">'+_av(p.uid, p.name, 26)
                    +'<span class="lb-name">'+_esc(p.name)+(isMe ? ' <span class="lb-me-tag">YOU</span>' : '')+'</span></td>'
                +'<td class="lb-td lb-td-flag">'+_flag(p.country)+'</td>'
                +'<td class="lb-td lb-td-tier">'+_tierBadge(tk)+'</td>'
                +'<td class="lb-td lb-td-r lb-td-score"><span class="lb-score lb-score--'+tk+'" style="color:'+tc+'">'+score+'</span></td>'
                +'<td class="lb-td lb-td-r lb-profit">+'+_fmtMoney(p.profit)+'</td>'
                +'<td class="lb-td lb-td-r"><span class="lb-pct-badge">+'+p.profitPct.toFixed(2)+'%</span></td>'
                +'<td class="lb-td lb-td-r"><span class="lb-wr '+_wrCls(p.winRate)+'">'+p.winRate+'%</span></td>'
                +'<td class="lb-td lb-td-pair"><span class="lb-asset-badge">'+_esc(p.pair)+'</span></td>'
                +'<td class="lb-td lb-td-r lb-win">'+(p.avgWin  ? '+'+_fmtMoney(p.avgWin)  : '<span class="lb-dash">—</span>')+'</td>'
                +'<td class="lb-td lb-td-r lb-loss">'+(p.avgLoss ? '−'+_fmtMoney(p.avgLoss) : '<span class="lb-dash">—</span>')+'</td>'
                +'<td class="lb-td lb-td-r lb-hold">'+(p.avgHold ? _fmtHold(p.avgHold)       : '<span class="lb-dash">—</span>')+'</td>'
                +'<td class="lb-td lb-td-r"><span class="lb-rr '+_rrCls(p.avgRR)+'">'+(p.avgRR ? p.avgRR.toFixed(2) : '—')+'</span></td>'
                +'<td class="lb-td lb-td-r lb-trades">'+p.trades+'</td>'
                +'</tr>';
        }).join('');
    }

    /* ── Filter & render ── */
    function filter(size) {
        _currentFilter = size;

        document.querySelectorAll('#lbSizePills .lb-pill').forEach(function(btn) {
            var s = btn.dataset.size;
            btn.classList.toggle('active', s === 'all' ? size === 'all' : parseInt(s) === size);
        });

        var data = LB_DATA;
        if (size !== 'all') data = data.filter(function(p) { return p.size === size; });
        if (_currentTier !== 'all') data = data.filter(function(p) { return _tierKey(p.profitPct) === _currentTier; });

        var body   = document.getElementById('lbBody');
        var empty  = document.getElementById('lbEmpty');
        var subHdr = document.getElementById('lbHdrSub');
        if (!body) return;

        if (subHdr) {
            if (size === 'all') {
                var total = LB_DATA.length;
                subHdr.textContent = (total ? 'TOP '+total : 'FUNDED ACCOUNTS') + ' · RANKED BY PROFIT % · UPDATED DAILY';
            } else {
                var sizeLabel = size >= 1000 ? '$'+(size/1000)+'K' : '$'+size;
                subHdr.textContent = 'TOP '+data.length+' · '+sizeLabel+' ACCOUNTS · RANKED BY PROFIT %';
            }
        }

        if (!data.length) {
            body.innerHTML = '';
            if (empty) {
                empty.textContent = (!LB_DATA.length && _loaded)
                    ? '[ NO PUBLIC TRADERS YET ]'
                    : '[ NO TRADERS FOR THIS ACCOUNT SIZE ]';
                empty.style.display = '';
            }
        } else {
            if (empty) empty.style.display = 'none';
            body.innerHTML = _buildRows(data);
        }

        _updateStats(data);

        /* My bar — visible only when the user's size is not the active filter */
        var myEntry = null;
        for (var i = 0; i < LB_DATA.length; i++) { if (LB_DATA[i].me) { myEntry = LB_DATA[i]; break; } }
        var bar = document.getElementById('lbMyBar');
        if (!bar || !myEntry) return;

        if (size === 'all' || myEntry.size === size) {
            bar.style.display = 'none';
        } else {
            bar.style.display = '';
            var sLbl = myEntry.size >= 1000 ? '$'+(myEntry.size/1000)+'K' : '$'+myEntry.size;
            bar.innerHTML = '<div class="lb-bar-inner">'
                +_av(0, myEntry.name, 22)
                +'<div class="lb-bar-name">'+_esc(myEntry.name)+'</div>'
                +'<div class="lb-bar-sep">·</div>'
                +'<div class="lb-bar-stat"><span class="lb-bar-lbl">GLOBAL RANK</span><span class="lb-bar-val">#'+myEntry.rank+'</span></div>'
                +'<div class="lb-bar-sep">·</div>'
                +'<div class="lb-bar-stat"><span class="lb-bar-lbl">ACCOUNT</span><span class="lb-bar-val">'+sLbl+'</span></div>'
                +'<div class="lb-bar-sep">·</div>'
                +'<div class="lb-bar-stat"><span class="lb-bar-lbl">PROFIT %</span><span class="lb-bar-val lb-profit">+'+myEntry.profitPct.toFixed(2)+'%</span></div>'
                +'<div class="lb-bar-sep">·</div>'
                +'<div class="lb-bar-note">NOT IN THIS SIZE FILTER</div>'
                +'</div>';
        }
    }

    function filterTier(tier) {
        _currentTier = tier;
        document.querySelectorAll('#lbTierPills .lb-tier-pill').forEach(function(btn) {
            btn.classList.toggle('active', btn.dataset.tier === tier);
        });
        filter(_currentFilter);
    }

    function getData(uid) {
        for (var i = 0; i < LB_DATA.length; i++) { if (LB_DATA[i].uid === uid) return LB_DATA[i]; }
        return null;
    }

    /* ── Init ── */
    function init() {
        // Always seed data so TestimonialsTab can render immediately
        LB_DATA = _DEMO;
        _loaded = true;

        if (!document.getElementById('tab-leaderboard')) return;

        var sizePills = document.getElementById('lbSizePills');
        if (sizePills) {
            sizePills.addEventListener('click', function(e) {
                var btn = e.target.closest('.lb-pill');
                if (!btn) return;
                var s = btn.dataset.size;
                filter(s === 'all' ? 'all' : parseInt(s, 10));
            });
        }

        var tierPills = document.getElementById('lbTierPills');
        if (tierPills) {
            tierPills.addEventListener('click', function(e) {
                var btn = e.target.closest('.lb-tier-pill');
                if (!btn) return;
                filterTier(btn.dataset.tier);
            });
        }

        _fetchData();
    }

    document.addEventListener('DOMContentLoaded', init);
    return { filter: filter, filterTier: filterTier, getData: getData, getAll: function() { return LB_DATA; } };
}());

/* ══════════════════════════════════════════════════════════
   TRADER PROFILE OVERLAY
══════════════════════════════════════════════════════════ */
window.TraderProfile = (function() {
    'use strict';

    var _TIER_CFG = {
        legend:   { label:'LEGEND',   color:'#EC4899' },
        masters:  { label:'MASTERS',  color:'#F97316' },
        diamond:  { label:'DIAMOND',  color:'#06B6D4' },
        platinum: { label:'PLATINUM', color:'#8B5CF6' },
        gold:     { label:'GOLD',     color:'#D4A843' },
        silver:   { label:'SILVER',   color:'#9CA3AF' },
        bronze:   { label:'BRONZE',   color:'#CD7F32' },
    };
    var GRADS = [
        ['#10B981','#0EA5E9'],['#8B5CF6','#EC4899'],['#F59E0B','#EF4444'],
        ['#06B6D4','#6366F1'],['#10B981','#84CC16'],['#F97316','#FBBF24'],
        ['#6366F1','#A855F7'],['#EF4444','#F97316'],['#0EA5E9','#10B981'],
        ['#EC4899','#8B5CF6'],['#14B8A6','#3B82F6'],['#A855F7','#06B6D4'],
    ];

    function _rng(seed) {
        var s = seed >>> 0;
        return function() { s = (Math.imul(1664525, s) + 1013904223) >>> 0; return s / 0xFFFFFFFF; };
    }

    function _tierKey(pct) {
        if (pct >= 30) return 'legend';
        if (pct >= 27) return 'masters';
        if (pct >= 24) return 'diamond';
        if (pct >= 21) return 'platinum';
        if (pct >= 19) return 'gold';
        if (pct >= 17) return 'silver';
        return 'bronze';
    }

    function _ini(name) {
        var p = (name||'').trim().split(/\s+/);
        return p.length >= 2 ? (p[0][0]+p[p.length-1][0]).toUpperCase() : (name||'X').slice(0,2).toUpperCase();
    }

    function _esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function _fmtMoney(n) {
        return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    }
    function _fmtK(n) {
        if (n >= 1000) { var k=n/1000; return '$'+(k%1===0?k.toFixed(0):k.toFixed(1))+'K'; }
        return '$'+n;
    }
    function _fmtDays(d) {
        if (d >= 365) { var y=Math.floor(d/365),m=Math.floor((d%365)/30); return y+'y'+(m>0?' '+m+'mo':''); }
        if (d >= 30)  return Math.floor(d/30)+'mo';
        return d+'d';
    }

    function _composite(p, rand) {
        var wr   = Math.min(20, Math.round(p.winRate / 5));
        var pf   = Math.min(20, Math.round((p.avgRR || (1.5 + rand()*1.0)) * 8));
        var rr   = Math.min(20, Math.round(((p.avgRR || 1.5) - 1.0) * 20));
        var vol  = Math.min(20, Math.round(12 + rand()*8));
        var cons = Math.min(20, Math.round(10 + rand()*10));
        return { wr:wr, pf:Math.min(20,pf), rr:Math.min(20,rr), vol:vol, cons:cons,
                 total: wr+Math.min(20,pf)+Math.min(20,rr)+vol+cons };
    }

    function _equitySvg(p, rand) {
        var W = 620, H = 130, pts = p.trades > 0 ? Math.min(80, p.trades) : 40;
        var vals = [0];
        for (var i = 1; i <= pts; i++) {
            var last = vals[vals.length - 1];
            vals.push(last + (rand() - 0.38) * 0.8);
        }
        var mn = Math.min.apply(null, vals), mx = Math.max.apply(null, vals);
        var range = mx - mn || 1;
        var PAD = { t: 14, b: 18, l: 8, r: 8 };
        function sY(v) { return PAD.t + (1 - (v - mn) / range) * (H - PAD.t - PAD.b); }
        function sX(i) { return PAD.l + (i / pts) * (W - PAD.l - PAD.r); }

        var path = 'M' + sX(0) + ' ' + sY(vals[0]);
        for (var j = 1; j <= pts; j++) path += ' L' + sX(j) + ' ' + sY(vals[j]);
        var floor  = H - PAD.b;
        var area   = path + ' L' + sX(pts) + ' ' + floor + ' L' + sX(0) + ' ' + floor + ' Z';
        var endVal = vals[vals.length - 1];
        var col    = endVal >= 0 ? '#10B981' : '#EF4444';
        var colRgb = endVal >= 0 ? '16,185,129' : '239,68,68';
        var lx = sX(pts), ly = sY(vals[vals.length - 1]);

        /* unique IDs per trader */
        var gFill = 'gf' + p.uid, gGlow = 'gg' + p.uid, gBlur = 'gb' + p.uid;

        /* horizontal grid lines */
        var grid = '';
        for (var g = 1; g <= 3; g++) {
            var gy = PAD.t + (g / 4) * (H - PAD.t - PAD.b);
            grid += '<line x1="' + PAD.l + '" y1="' + gy + '" x2="' + (W - PAD.r) + '" y2="' + gy + '" stroke="rgba(255,255,255,0.045)" stroke-width="1"/>';
        }

        return '<svg class="tp-equity-svg" viewBox="0 0 ' + W + ' ' + H + '" xmlns="http://www.w3.org/2000/svg" style="--eq-col:' + col + ';--eq-rgb:' + colRgb + '">'
            + '<defs>'
            /* area fill gradient */
            + '<linearGradient id="' + gFill + '" x1="0" y1="0" x2="0" y2="1">'
            + '<stop offset="0%"   stop-color="' + col + '" stop-opacity="0.38"/>'
            + '<stop offset="60%"  stop-color="' + col + '" stop-opacity="0.10"/>'
            + '<stop offset="100%" stop-color="' + col + '" stop-opacity="0"/>'
            + '</linearGradient>'
            /* line glow filter */
            + '<filter id="' + gGlow + '" x="-10%" y="-60%" width="120%" height="220%">'
            + '<feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur"/>'
            + '<feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>'
            + '</filter>'
            /* soft blur for glow halo */
            + '<filter id="' + gBlur + '" x="-20%" y="-80%" width="140%" height="260%">'
            + '<feGaussianBlur in="SourceGraphic" stdDeviation="7"/>'
            + '</filter>'
            + '</defs>'
            /* grid */
            + grid
            /* floor line */
            + '<line x1="' + PAD.l + '" y1="' + floor + '" x2="' + (W - PAD.r) + '" y2="' + floor + '" stroke="rgba(' + colRgb + ',0.18)" stroke-width="1"/>'
            /* area */
            + '<path d="' + area + '" fill="url(#' + gFill + ')"/>'
            /* halo (blurred wide stroke) */
            + '<path d="' + path + '" fill="none" stroke="' + col + '" stroke-width="6" stroke-linejoin="round" filter="url(#' + gBlur + ')" opacity="0.28"/>'
            /* main line with glow filter */
            + '<path d="' + path + '" fill="none" stroke="' + col + '" stroke-width="2" stroke-linejoin="round" filter="url(#' + gGlow + ')"/>'
            /* end-point rings */
            + '<circle cx="' + lx + '" cy="' + ly + '" r="9"  fill="rgba(' + colRgb + ',0.10)"/>'
            + '<circle cx="' + lx + '" cy="' + ly + '" r="5"  fill="rgba(' + colRgb + ',0.25)"/>'
            + '<circle cx="' + lx + '" cy="' + ly + '" r="2.5" fill="' + col + '"/>'
            + '<circle cx="' + lx + '" cy="' + ly + '" r="1"   fill="#fff" opacity="0.9"/>'
            + '</svg>';
    }

    function _calendarHtml(p, rand) {
        var MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var YLBLS  = ['Mon','','Wed','','Fri','',''];
        var WEEKS  = 52;
        var CELL   = 12; /* px per cell + gap, for month label positioning */

        /* Generate states — spread trades across recent weeks */
        var totalCells = WEEKS * 7;
        var tradeDens  = Math.min(0.72, Math.max(0.15, p.trades / 150));
        var states = [];
        for (var i = 0; i < totalCells; i++) {
            var r = rand();
            var inRange = i > totalCells * (1 - p.fundedDays / 365);
            if (inRange && r > (1 - tradeDens)) {
                if (r > 0.88)       states.push('loss');
                else if (r > 0.78)  states.push('be');
                else                states.push('win');
            } else {
                states.push('none');
            }
        }

        /* Month labels — one per month-change along the week axis */
        var refDate = new Date(2026, 3, 28); /* fixed anchor for determinism */
        var startDate = new Date(refDate);
        startDate.setDate(refDate.getDate() - WEEKS * 7);
        var monthLabels = [], lastM = -1;
        for (var w = 0; w < WEEKS; w++) {
            var d = new Date(startDate);
            d.setDate(startDate.getDate() + w * 7);
            var m = d.getMonth();
            if (m !== lastM) { monthLabels.push({w: w, lbl: MONTHS[m]}); lastM = m; }
        }

        /* Build HTML */
        var html = '<div class="tp-cal-graph">';

        /* Month label row */
        html += '<div class="tp-cal-header"><div class="tp-cal-ylabel-spacer"></div><div class="tp-cal-months-row">';
        for (var i = 0; i < monthLabels.length; i++) {
            html += '<span class="tp-cal-mlbl" style="left:'+(monthLabels[i].w * CELL)+'px">'+monthLabels[i].lbl+'</span>';
        }
        html += '</div></div>';

        /* Body: y-labels + week grid */
        html += '<div class="tp-cal-body"><div class="tp-cal-ylabels">';
        for (var d = 0; d < 7; d++) html += '<div class="tp-cal-ylabel">'+YLBLS[d]+'</div>';
        html += '</div><div class="tp-cal-weeks">';
        for (var w = 0; w < WEEKS; w++) {
            html += '<div class="tp-cal-wcol">';
            for (var d = 0; d < 7; d++) {
                html += '<div class="tp-cal-cell tp-cal-cell--'+states[w*7+d]+'"></div>';
            }
            html += '</div>';
        }
        html += '</div></div>';

        /* Legend */
        html += '<div class="tp-cal-legend">'
            +'<span class="tp-cal-leg"><span class="tp-cal-leg-dot tp-cal-c--none"></span>NO TRADE</span>'
            +'<span class="tp-cal-leg"><span class="tp-cal-leg-dot tp-cal-c--win"></span>WIN</span>'
            +'<span class="tp-cal-leg"><span class="tp-cal-leg-dot tp-cal-c--loss"></span>LOSS</span>'
            +'<span class="tp-cal-leg"><span class="tp-cal-leg-dot tp-cal-c--be"></span>BREAKEVEN</span>'
            +'</div>';

        return html + '</div>';
    }

    var _TROPHY_DEFS = {
        payday: {
            name: 'PAYDAY',
            hint: '5+ PAYOUTS',
            color: '#F59E0B',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="5.5" stroke-dasharray="1.5 2.2"/><line x1="12" y1="15.5" x2="12" y2="9"/><polyline points="9.5,11.5 12,9 14.5,11.5"/></svg>'
        },
        vault: {
            name: 'VAULT',
            hint: '10+ PAYOUTS',
            color: '#10B981',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" width="28" height="28"><rect x="3.5" y="4.5" width="17" height="15" rx="1.5"/><circle cx="11" cy="12" r="4"/><circle cx="11" cy="12" r="1.2"/><line x1="11" y1="8" x2="11" y2="9.7"/><line x1="13.5" y1="9.2" x2="12.5" y2="10.6"/><line x1="14.5" y1="12" x2="16.5" y2="12"/><circle cx="5.5" cy="6.5" r=".9" fill="currentColor" stroke="none"/><circle cx="5.5" cy="18.5" r=".9" fill="currentColor" stroke="none"/><circle cx="19" cy="6.5" r=".9" fill="currentColor" stroke="none"/><circle cx="19" cy="18.5" r=".9" fill="currentColor" stroke="none"/></svg>'
        },
        ironclad: {
            name: 'IRONCLAD',
            hint: '6 MONTHS',
            color: '#06B6D4',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><line x1="7.5" y1="3" x2="16.5" y2="3"/><line x1="7.5" y1="21" x2="16.5" y2="21"/><path d="M7.5 3C7.5 3 7.5 10 12 12C7.5 14 7.5 21 7.5 21"/><path d="M16.5 3C16.5 3 16.5 10 12 12C16.5 14 16.5 21 16.5 21"/><line x1="9.8" y1="12" x2="14.2" y2="12" stroke-dasharray="1.2 1.5"/></svg>'
        },
        veteran: {
            name: 'VETERAN',
            hint: '1 YEAR',
            color: '#8B5CF6',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><polygon points="12,2 20.5,7 20.5,17 12,22 3.5,17 3.5,7"/><line x1="3.5" y1="7" x2="20.5" y2="7"/><line x1="3.5" y1="17" x2="20.5" y2="17"/><line x1="12" y1="2" x2="12" y2="22"/><line x1="3.5" y1="7" x2="12" y2="12"/><line x1="20.5" y1="7" x2="12" y2="12"/><line x1="3.5" y1="17" x2="12" y2="12"/><line x1="20.5" y1="17" x2="12" y2="12"/></svg>'
        },
        sniper: {
            name: 'SNIPER',
            hint: '70%+ WIN RATE',
            color: '#EF4444',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" width="28" height="28"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5.5"/><circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/><line x1="2" y1="12" x2="6.5" y2="12"/><line x1="17.5" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="6.5"/><line x1="12" y1="17.5" x2="12" y2="22"/></svg>'
        },
        bullrun: {
            name: 'BULL RUN',
            hint: '25%+ PROFIT',
            color: '#84CC16',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><line x1="2" y1="22" x2="22" y2="22"/><line x1="2" y1="22" x2="2" y2="4"/><rect x="3" y="16" width="4" height="6" fill="currentColor" stroke="currentColor" opacity=".3" rx=".5"/><rect x="9" y="12" width="4" height="10" fill="currentColor" stroke="currentColor" opacity=".3" rx=".5"/><rect x="15" y="7" width="4" height="15" fill="currentColor" stroke="currentColor" opacity=".3" rx=".5"/><polyline points="3,15 9.5,11 15.5,6 21,3"/><polyline points="17.5,3 21,3 21,6.5"/></svg>'
        },
        centurion: {
            name: 'CENTURION',
            hint: '100 TRADES',
            color: '#F97316',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" width="28" height="28"><path d="M13.5 2L5 14h6l-1.5 8L19 10h-6z"/><line x1="3" y1="10.5" x2="6.5" y2="10.5" stroke-dasharray="1 1.2"/><line x1="17.5" y1="13.5" x2="21" y2="13.5" stroke-dasharray="1 1.2"/></svg>'
        },
    };

    function _trophies(p) {
        var list = [];
        if (p.payoutCount >= 5)  list.push(_TROPHY_DEFS.payday);
        if (p.payoutCount >= 10) list.push(_TROPHY_DEFS.vault);
        if (p.fundedDays >= 180) list.push(_TROPHY_DEFS.ironclad);
        if (p.fundedDays >= 365) list.push(_TROPHY_DEFS.veteran);
        if (p.winRate >= 70)     list.push(_TROPHY_DEFS.sniper);
        if (p.profitPct >= 25)   list.push(_TROPHY_DEFS.bullrun);
        if (p.trades >= 100)     list.push(_TROPHY_DEFS.centurion);
        return list;
    }

    function _render(p) {
        var rand = _rng(p.uid * 31337);
        var tk   = _tierKey(p.profitPct);
        var tc   = _TIER_CFG[tk];
        var grad = GRADS[Math.abs(p.uid|0) % GRADS.length];
        var comp = _composite(p, rand);
        var scorePct = (comp.total / 100) * 100;
        var trophyList = _trophies(p);
        var handle = '@'+_esc((p.name||'').replace(/\s+/g,'').replace(/\./g,'').toLowerCase()+'_'+p.uid);

        var memberSince = (function() {
            var d = new Date();
            d.setDate(d.getDate() - p.fundedDays);
            return d.toLocaleDateString('en-US',{month:'short', year:'numeric'});
        }());

        var metricColor = function(score) {
            if (score >= 16) return '#10B981';
            if (score >= 10) return '#D4A843';
            return '#D71921';
        };

        return ''
            /* ── HEADER ── */
            +'<div class="tp-hdr">'
                +'<div class="tp-av-wrap">'
                    +'<div class="tp-av" style="background:linear-gradient(135deg,'+grad[0]+','+grad[1]+')">'+_esc(_ini(p.name))+'</div>'
                +'</div>'
                +'<div class="tp-hdr-info">'
                    +'<div class="tp-hdr-top">'
                        +'<span class="tp-name">'+_esc(p.name)+'</span>'
                        +'<span class="tp-season-tier-badge lb-tier--'+tk+'" style="color:'+tc.color+';border-color:'+tc.color+';background:rgba(0,0,0,0.2)">'+tc.label+'</span>'
                    +'</div>'
                    +'<div class="tp-handle">'+handle+'</div>'
                    +'<div class="tp-hdr-meta">'
                        +'<div class="tp-hdr-stat"><span class="tp-hdr-stat-lbl">MEMBER SINCE</span><span class="tp-hdr-stat-val">'+memberSince+'</span></div>'
                        +'<div class="tp-hdr-stat"><span class="tp-hdr-stat-lbl">ACCOUNT SIZE</span><span class="tp-hdr-stat-val">'+_fmtK(p.size)+'</span></div>'
                        +'<div class="tp-hdr-stat"><span class="tp-hdr-stat-lbl">TOTAL PROFIT</span><span class="tp-hdr-stat-val tp-profit">+'+_fmtMoney(p.profit)+'</span></div>'
                        +'<div class="tp-hdr-stat"><span class="tp-hdr-stat-lbl">FUNDED</span><span class="tp-hdr-stat-val">'+_fmtDays(p.fundedDays)+'</span></div>'
                    +'</div>'
                +'</div>'
            +'</div>'

            /* ── SEASON 0 CARD ── */
            +'<div class="tp-section-lbl">SEASON 0 — PERFORMANCE RATING</div>'
            +'<div class="tp-season-card">'
                +'<div class="tp-season-top">'
                    +'<div class="tp-season-tier-big">'
                        +'<span class="tp-season-tier-badge" style="color:'+tc.color+';border-color:'+tc.color+';background:rgba(0,0,0,0.25);font-size:9px;padding:5px 12px">'+tc.label+'</span>'
                        +'<span class="tp-season-tier-lbl">CURRENT TIER</span>'
                    +'</div>'
                    +'<div class="tp-comp-wrap">'
                        +'<div class="tp-comp-header">'
                            +'<span class="tp-comp-title">COMPOSITE SCORE</span>'
                            +'<span class="tp-comp-score">'+comp.total+'</span>'
                        +'</div>'
                        +'<div class="tp-comp-track"><div class="tp-comp-fill" style="width:'+scorePct+'%;background:'+tc.color+'"></div></div>'
                        +'<div class="tp-metrics-row">'
                            +['WR','PF','R:R','VOL','CONS'].map(function(lbl,i) {
                                var scores = [comp.wr, comp.pf, comp.rr, comp.vol, comp.cons];
                                var s = scores[i];
                                var pct = (s/20)*100;
                                var mc = metricColor(s);
                                return '<div class="tp-metric">'
                                    +'<span class="tp-metric-lbl">'+lbl+'</span>'
                                    +'<div class="tp-metric-track"><div class="tp-metric-fill" style="width:'+pct+'%;background:'+mc+'"></div></div>'
                                    +'<span class="tp-metric-val">'+s+'/20</span>'
                                    +'</div>';
                            }).join('')
                        +'</div>'
                    +'</div>'
                +'</div>'
            +'</div>'

            /* ── STATS ROW ── */
            +'<div class="tp-stats-row">'
                +'<div class="tp-stat-box"><div class="tp-stat-lbl">WIN RATE</div><div class="tp-stat-val tp-green">'+p.winRate+'%</div></div>'
                +'<div class="tp-stat-box"><div class="tp-stat-lbl">TOTAL TRADES</div><div class="tp-stat-val">'+p.trades+'</div></div>'
                +'<div class="tp-stat-box"><div class="tp-stat-lbl">TOTAL PAYOUT</div><div class="tp-stat-val tp-green">'+_fmtK(p.totalPayout)+'</div></div>'
                +'<div class="tp-stat-box"><div class="tp-stat-lbl">BEST PAYOUT</div><div class="tp-stat-val">'+_fmtK(p.highestPayout)+'</div></div>'
            +'</div>'

            /* ── EQUITY CURVE ── */
            +'<div class="tp-section-lbl">EQUITY CURVE</div>'
            +'<div class="tp-equity-wrap">'+_equitySvg(p, rand)+'</div>'

            /* ── TRADING CALENDAR ── */
            +'<div class="tp-section-lbl">TRADING ACTIVITY</div>'
            +'<div class="tp-cal-wrap">'+_calendarHtml(p, rand)+'</div>'

            /* ── TROPHY CASE ── */
            +'<div class="tp-section-lbl">TROPHY CASE</div>'
            +'<div class="tp-trophy-wrap">'
                +(trophyList.length
                    ? '<div class="tp-trophies">'+trophyList.map(function(t){
                        return '<div class="tp-trophy" style="--tc:'+t.color+'">'
                            +'<div class="tp-trophy-icon" style="color:'+t.color+'">'+t.svg+'</div>'
                            +'<div class="tp-trophy-name">'+t.name+'</div>'
                            +'<div class="tp-trophy-lbl">'+t.hint+'</div>'
                            +'</div>';
                      }).join('')+'</div>'
                    : '<div class="tp-trophy-empty">[ NO TROPHIES YET ]</div>'
                )
            +'</div>';
    }

    function open(uid) {
        var p = LeaderboardTab.getData(uid);
        if (!p) return;
        var body = document.getElementById('tpBody');
        var overlay = document.getElementById('tpOverlay');
        if (!body || !overlay) return;
        body.innerHTML = _render(p);
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function close(e) {
        if (e && e.target !== document.getElementById('tpOverlay')) return;
        var overlay = document.getElementById('tpOverlay');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { close(null); }
    });

    return { open: open, close: close };
}());

/* ══════════════════════════════════════════════════════════
   TESTIMONIALS TAB
══════════════════════════════════════════════════════════ */
window.TestimonialsTab = (function() {
    'use strict';

    var _sort = 'amount';
    var _retry = 0;
    var _gridWired = false;

    var _TIER_CFG = {
        legend:   { label:'LEGEND',   color:'#EC4899', rank:7 },
        masters:  { label:'MASTERS',  color:'#F97316', rank:6 },
        diamond:  { label:'DIAMOND',  color:'#06B6D4', rank:5 },
        platinum: { label:'PLATINUM', color:'#8B5CF6', rank:4 },
        gold:     { label:'GOLD',     color:'#D4A843', rank:3 },
        silver:   { label:'SILVER',   color:'#9CA3AF', rank:2 },
        bronze:   { label:'BRONZE',   color:'#CD7F32', rank:1 }
    };

    var _GRADS = [
        ['#10B981','#0EA5E9'],['#8B5CF6','#EC4899'],['#F59E0B','#EF4444'],
        ['#06B6D4','#6366F1'],['#10B981','#84CC16'],['#F97316','#FBBF24'],
        ['#6366F1','#A855F7'],['#EF4444','#F97316'],['#0EA5E9','#10B981'],
        ['#EC4899','#8B5CF6'],['#14B8A6','#3B82F6'],['#A855F7','#06B6D4']
    ];

    function _tierKey(pct) {
        if (pct >= 30) return 'legend';
        if (pct >= 27) return 'masters';
        if (pct >= 24) return 'diamond';
        if (pct >= 21) return 'platinum';
        if (pct >= 19) return 'gold';
        if (pct >= 17) return 'silver';
        return 'bronze';
    }

    function _rng(seed) {
        var s = seed >>> 0;
        return function() { s = (Math.imul(1664525, s) + 1013904223) >>> 0; return s / 0xFFFFFFFF; };
    }

    function _ini(name) {
        var p = (name||'').trim().split(/\s+/);
        return p.length >= 2 ? (p[0][0]+p[p.length-1][0]).toUpperCase() : (name||'X').slice(0,2).toUpperCase();
    }

    function _esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function _fmtMoney(n) {
        return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    }

    function _fmtK(n) {
        if (n >= 1000) { var k=n/1000; return '$'+(k%1===0?k.toFixed(0):k.toFixed(1))+'K'; }
        return '$'+n;
    }

    function _renderStats(traders) {
        var strip = document.getElementById('tmStatsStrip');
        if (!strip) return;
        var total     = traders.length;
        var totalPaid = traders.reduce(function(s,t){ return s+(t.totalPayout||0); }, 0);
        var highest   = traders.reduce(function(m,t){ return Math.max(m, t.highestPayout||0); }, 0);
        var avg       = total > 0 ? totalPaid/total : 0;
        var avgPt     = total > 0 ? Math.round(traders.reduce(function(s,t){ return s + 4 + (Math.abs(t.uid|0) % 20); }, 0) / total) : 0;
        var stats = [
            { label:'FUNDED TRADERS',  val:total,              fmt:'n',   color:'#10B981' },
            { label:'TOTAL PAID OUT',  val:totalPaid,          fmt:'$',   color:'#8B5CF6' },
            { label:'HIGHEST PAYOUT',  val:highest,            fmt:'$',   color:'#D4A843' },
            { label:'AVG PAYOUT',      val:avg,                fmt:'$',   color:'#0EA5E9' },
            { label:'AVG PAYOUT TIME', val:'< '+avgPt+'h',     fmt:'raw', color:'#F97316' }
        ];
        strip.innerHTML = stats.map(function(s) {
            var d = s.fmt === '$' ? _fmtMoney(s.val) : s.val;
            return '<div class="tm-stat" style="--ts-c:'+s.color+'">'
                +'<div class="tm-stat-val">'+d+'</div>'
                +'<div class="tm-stat-lbl">'+s.label+'</div>'
                +'</div>';
        }).join('');
    }

    function _sorted(traders) {
        var arr = traders.slice();
        if (_sort === 'amount') {
            arr.sort(function(a,b){ return (b.totalPayout||0)-(a.totalPayout||0); });
        } else if (_sort === 'tier') {
            arr.sort(function(a,b){
                return _TIER_CFG[_tierKey(b.profitPct||0)].rank - _TIER_CFG[_tierKey(a.profitPct||0)].rank;
            });
        }
        return arr;
    }

    function _card(p) {
        var tier = _tierKey(p.profitPct||0);
        var cfg  = _TIER_CFG[tier];
        var g    = _GRADS[Math.abs(p.uid|0) % _GRADS.length];
        var pt   = '< ' + (4 + (Math.abs(p.uid|0) % 20)) + 'h';
        var flag = p.country
            ? '<img class="tm-card-flag" src="assets/img/flags/'+p.country.toLowerCase()+'.svg" onerror="this.style.display=\'none\'">'
            : '';
        return '<div class="tm-card" style="--tm-c:'+cfg.color+'" data-uid="'+_esc(p.uid||'')+'">'
            +'<div class="tm-card-top">'
            +'<div class="tm-card-av" style="background:linear-gradient(135deg,'+g[0]+','+g[1]+')">'+_esc(_ini(p.name))+'</div>'
            +'<div class="tm-card-info">'
            +'<div class="tm-card-name">'+_esc(p.name||'TRADER')+'</div>'
            +'<div class="tm-card-meta">'+flag
            +'<span class="tm-card-size">'+_fmtK(p.size||0)+'</span>'
            +'<span class="tm-tier-bdg" style="color:'+cfg.color+';border-color:'+cfg.color+'40">'+cfg.label+'</span>'
            +'</div>'
            +'</div>'
            +'<div class="tm-card-total">'+_fmtMoney(p.totalPayout||0)+'</div>'
            +'</div>'
            +'<div class="tm-card-foot">'
            +'<div class="tm-card-fs"><span class="tm-fs-lbl">PAYOUTS</span><span class="tm-fs-val">'+(p.payoutCount||1)+'</span></div>'
            +'<div class="tm-card-fs"><span class="tm-fs-lbl">WIN RATE</span><span class="tm-fs-val">'+(p.winRate||0)+'%</span></div>'
            +'<div class="tm-card-fs"><span class="tm-fs-lbl">PROFIT</span><span class="tm-fs-val tm-profit">+'+(p.profitPct||0)+'%</span></div>'
            +'<div class="tm-card-fs"><span class="tm-fs-lbl">PAYOUT TIME</span><span class="tm-fs-val">'+pt+'</span></div>'
            +'</div>'
            +'</div>';
    }

    function _renderGrid(traders) {
        var grid = document.getElementById('tmGrid');
        if (!grid) return;
        if (!traders.length) {
            grid.innerHTML = '<div class="tm-empty">[ NO FUNDED TRADERS YET ]</div>';
            return;
        }
        grid.innerHTML = _sorted(traders).map(_card).join('');
        if (!_gridWired) {
            _gridWired = true;
            grid.addEventListener('click', function(e) {
                var c = e.target.closest('.tm-card');
                if (c && c.dataset.uid && window.TraderProfile) TraderProfile.open(c.dataset.uid);
            });
        }
    }

    function _setSort(type) {
        _sort = type;
        document.querySelectorAll('#tmSortPills .tm-pill').forEach(function(b) {
            b.classList.toggle('active', b.dataset.sort === type);
        });
        var data = typeof LeaderboardTab !== 'undefined' ? LeaderboardTab.getAll() : [];
        _renderGrid(data);
    }

    function init() {
        var data = typeof LeaderboardTab !== 'undefined' ? LeaderboardTab.getAll() : [];
        if (!data || !data.length) {
            if (_retry < 10) { _retry++; setTimeout(init, 400); }
            return;
        }
        _retry = 0;
        _renderStats(data);
        _renderGrid(data);
        var pills = document.getElementById('tmSortPills');
        if (pills && !pills._wired) {
            pills._wired = true;
            pills.addEventListener('click', function(e) {
                var btn = e.target.closest('.tm-pill');
                if (btn) _setSort(btn.dataset.sort);
            });
        }
    }

    return { init: init };
}());

/* ── Certificates tab ── */
window.CertTab = (function() {
    'use strict';

    var ACCENTS = {
        eval:     '#10B981',
        funded:   '#0EA5E9',
        comp:     '#F59E0B',
        payout:   '#8B5CF6',
        lifetime: '#10B981'
    };

    var _flash = null;

    function _showFlash(msg) {
        if (_flash) { clearTimeout(_flash._t); _flash.remove(); }
        var el = document.createElement('div');
        el.className = 'cert-flash';
        el.textContent = msg;
        document.body.appendChild(el);
        requestAnimationFrame(function() { el.classList.add('cert-flash--in'); });
        _flash = el;
        _flash._t = setTimeout(function() {
            el.classList.remove('cert-flash--in');
            setTimeout(function() { el.remove(); if (_flash === el) _flash = null; }, 300);
        }, 2200);
    }

    function share(type, id, title, text) {
        var fullText = 'Doji Funding · ' + title + ' — ' + text;
        if (navigator.share) {
            navigator.share({ title: 'Doji Funding — ' + title, text: fullText })
                .catch(function() {});
        } else if (navigator.clipboard) {
            navigator.clipboard.writeText(fullText).then(function() {
                _showFlash('[ COPIED TO CLIPBOARD ]');
            }).catch(function() {
                _showFlash('[ SHARE NOT SUPPORTED ]');
            });
        } else {
            _showFlash('[ SHARE NOT SUPPORTED ]');
        }
    }

    function download(type, id, title, sub) {
        var accent = ACCENTS[type] || '#10B981';
        var now    = new Date().toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        var certNo = type.toUpperCase() + '-' + String(id).padStart(4, '0') + '-' + new Date().getFullYear();

        var html = '<!DOCTYPE html><html lang="en"><head>'
            + '<meta charset="UTF-8"><title>Certificate — ' + title + '</title>'
            + '<style>'
            + '*{margin:0;padding:0;box-sizing:border-box}'
            + 'body{background:#fff;font-family:"Courier New",monospace;color:#111;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:32px}'
            + '.cert{width:640px;border:2px solid #111;padding:48px 52px;position:relative}'
            + '.cert-corner{position:absolute;width:20px;height:20px;border-color:'+accent+'}'
            + '.cert-corner.tl{top:-2px;left:-2px;border-top:3px solid;border-left:3px solid}'
            + '.cert-corner.br{bottom:-2px;right:-2px;border-bottom:3px solid;border-right:3px solid}'
            + '.cert-logo{font-size:11px;font-weight:700;letter-spacing:.18em;color:#888;margin-bottom:40px}'
            + '.cert-label{font-size:9px;font-weight:700;letter-spacing:.18em;color:'+accent+';border:1px solid;border-color:'+accent+'40;background:'+accent+'0d;display:inline-block;padding:3px 10px;margin-bottom:28px}'
            + '.cert-title{font-size:36px;font-weight:700;letter-spacing:-.01em;color:'+accent+';line-height:1;margin-bottom:10px}'
            + '.cert-sub{font-size:13px;letter-spacing:.1em;color:#555;margin-bottom:36px}'
            + '.cert-divider{height:1px;background:#eee;margin-bottom:24px}'
            + '.cert-meta{font-size:10px;letter-spacing:.1em;color:#888;line-height:1.8}'
            + '.cert-no{font-size:9px;letter-spacing:.08em;color:#bbb;margin-top:36px}'
            + '@media print{body{padding:0}@page{margin:0;size:A4 landscape}}'
            + '</style></head><body>'
            + '<div class="cert">'
            + '<div class="cert-corner tl"></div><div class="cert-corner br"></div>'
            + '<div class="cert-logo">DOJI FUNDING</div>'
            + '<div class="cert-label">' + type.toUpperCase() + ' CERTIFICATE</div>'
            + '<div class="cert-title">' + _esc(title) + '</div>'
            + '<div class="cert-sub">' + _esc(sub) + '</div>'
            + '<div class="cert-divider"></div>'
            + '<div class="cert-meta">'
            + 'ISSUED BY &nbsp; DOJI FUNDING<br>'
            + 'ISSUED ON &nbsp; ' + now + '<br>'
            + '</div>'
            + '<div class="cert-no">CERTIFICATE No. ' + certNo + '</div>'
            + '</div>'
            + '<script>window.onload=function(){window.print();}<\/script>'
            + '</body></html>';

        var w = window.open('', '_blank');
        if (!w) { _showFlash('[ ALLOW POPUPS TO DOWNLOAD ]'); return; }
        w.document.write(html);
        w.document.close();
    }

    function _esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    return { share: share, download: download };
}());


/* ──────────────────────────────────────────────────────
   AffDash — Affiliate tab utilities
────────────────────────────────────────────────────── */
var AffDash = (function() {

    function copyText(elementId, btn) {
        var el = document.getElementById(elementId);
        if (!el) return;
        var text = el.textContent.trim();
        if (text.indexOf('dojifunding.com') !== -1 && text.indexOf('http') === -1) {
            text = 'https://' + text;
        }
        navigator.clipboard.writeText(text).then(function() {
            if (btn) {
                var orig = btn.textContent;
                btn.textContent = 'COPIED';
                btn.classList.add('copied');
                setTimeout(function() {
                    btn.textContent = orig;
                    btn.classList.remove('copied');
                }, 1800);
            }
        }).catch(function() {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            if (btn) {
                var orig = btn.textContent;
                btn.textContent = 'COPIED';
                btn.classList.add('copied');
                setTimeout(function() {
                    btn.textContent = orig;
                    btn.classList.remove('copied');
                }, 1800);
            }
        });
    }

    return { copyText: copyText };
}());

/* ═══════════════════════════════════════════════
   SUPPORT TAB
   ═══════════════════════════════════════════════ */
var SupportTab = (function() {
    'use strict';

    var _bound = false;

    function _setFeedback(el, msg, cls) {
        el.textContent = msg;
        el.className = 'sp-feedback' + (cls ? ' ' + cls : '');
    }

    function _bindForm(formId, feedbackId) {
        var form = document.getElementById(formId);
        var fb   = document.getElementById(feedbackId);
        if (!form || !fb) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn      = form.querySelector('.sp-btn');
            var origText = btn ? btn.textContent : '';

            _setFeedback(fb, '', '');
            if (btn) { btn.disabled = true; btn.textContent = 'SENDING...'; }

            var data = new FormData(form);
            data.append('csrf', (window.DOJI_CONFIG && window.DOJI_CONFIG.csrfToken) || '');

            fetch('api/support.php', { method: 'POST', body: data })
                .then(function(r) { return r.json(); })
                .then(function(json) {
                    if (json.success) {
                        _setFeedback(fb, "[SENT — WE'LL BE IN TOUCH SHORTLY]", 'ok');
                        form.reset();
                    } else {
                        _setFeedback(fb, '[ERROR: ' + (json.error || 'PLEASE TRY AGAIN') + ']', 'err');
                    }
                })
                .catch(function() {
                    _setFeedback(fb, '[NETWORK ERROR — PLEASE RETRY]', 'err');
                })
                .finally(function() {
                    if (btn) { btn.disabled = false; btn.textContent = origText; }
                });
        });
    }

    function openChat() {
        if (typeof Tawk_API !== 'undefined' && Tawk_API.toggle) { Tawk_API.toggle(); return; }
        if (typeof $crisp !== 'undefined') { $crisp.push(['do', 'chat:open']); return; }
        if (typeof Intercom !== 'undefined') { Intercom('show'); return; }
        window.location.href = 'mailto:hello@dojifunding.com?subject=Live+Chat+Request';
    }

    function init() {
        if (_bound) return;
        if (!document.getElementById('tab-support')) return;
        _bindForm('spContactForm', 'spContactFeedback');
        _bindForm('spBugForm',     'spBugFeedback');
        _bound = true;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return { init: init, openChat: openChat };
}());

/* ═══════════════════════════════════════════════
   WALLET ACHIEVEMENTS — category filter
   ═══════════════════════════════════════════════ */
var WalletAch = (function() {
    'use strict';
    var _bound = false;

    function init() {
        if (_bound) return;
        var cats = document.querySelectorAll('.wlt-ach-cat');
        if (!cats.length) return;
        cats.forEach(function(btn) {
            btn.addEventListener('click', function() {
                cats.forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                var cat = this.dataset.cat;
                document.querySelectorAll('.wlt-ach-card').forEach(function(card) {
                    card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
                });
            });
        });
        _bound = true;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return { init: init };
}());
