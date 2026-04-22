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

    return { switchTab, filterChallenges, copyReferral, setTheme, toggleTheme, submitKycDoc, showProfileSection };
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
        if (!code || code === '—') return '—';
        try { return Array.from(code.toUpperCase().slice(0,2)).map(function(c){ return String.fromCodePoint(c.charCodeAt(0)+127397); }).join(''); }
        catch(e){ return code; }
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

    /* ── Render: podium ── */
    function _podium(lb) {
        if (!lb.length) return '<div class="comp-podium-empty">[ NO PARTICIPANTS YET ]</div>';
        function slot(p, cls, sz) {
            if (!p) return '<div class="comp-podium-slot '+cls+'"></div>';
            return '<div class="comp-podium-slot '+cls+'">'
                +_av(p.uid,p.name,sz)
                +'<div class="comp-podium-rank">#'+p.rank+'</div>'
                +'<div class="comp-podium-name">'+_esc((p.name||'').split(' ')[0])+'</div>'
                +'<div class="comp-podium-gain">'+_fmtG(p.gain)+'</div>'
                +'</div>';
        }
        return '<div class="comp-podium">'+slot(lb[1],'comp-podium-slot--2',36)+slot(lb[0],'comp-podium-slot--1',50)+slot(lb[2],'comp-podium-slot--3',36)+'</div>';
    }

    /* ── Render: leaderboard table ── */
    function _table(lb) {
        if (!lb.length) return '<div class="comp-lb-empty">[ COMPETITION NOT YET STARTED ]</div>';
        var rows = lb.map(function(p) {
            var isMe = p.me||p.isMe;
            var medal = p.rank<=3 ? ['','🥇','🥈','🥉'][p.rank]+' ' : '';
            return '<tr class="comp-lb-row'+(isMe?' comp-lb-row--me':'')+'">'
                +'<td class="comp-lb-td comp-lb-td--rank">'+medal+p.rank+'</td>'
                +'<td class="comp-lb-td comp-lb-td--name">'+_av(p.uid,p.name,26)
                +'<span class="comp-lb-name-txt">'+_esc(p.name)+(isMe?' <span class="comp-lb-me">YOU</span>':'')+'</span></td>'
                +'<td class="comp-lb-td">'+_flag(p.country)+'</td>'
                +'<td class="comp-lb-td">'+p.trades+'</td>'
                +'<td class="comp-lb-td">'+p.wr+'%</td>'
                +'<td class="comp-lb-td comp-lb-td--profit">'+_fmtP(p.profit)+'</td>'
                +'<td class="comp-lb-td comp-lb-td--gain">'+_fmtG(p.gain)+'</td>'
                +'<td class="comp-lb-td comp-lb-td--arrow"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></td>'
                +'</tr>';
        }).join('');
        return '<table class="comp-lb-table"><thead><tr>'
            +'<th>RANK</th><th>NAME</th><th>COUNTRY</th><th>TRADES</th><th>WIN RATIO</th><th>PROFIT</th><th>GAIN</th><th></th>'
            +'</tr></thead><tbody>'+rows+'</tbody></table>';
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
        var cdT    = _pd(cdEnd); var cdV = cdT ? _cd(cdT) : {d:'00',h:'00',m:'00',s:'00'};
        var cdLbl  = isLive ? 'ENDING IN' : (isUp ? 'STARTS IN' : 'ENDED');
        var stLbl  = isLive ? 'ONGOING' : comp.status.toUpperCase();
        var cdAttr = cdEnd ? ' data-comp-detail-end="'+_esc(cdEnd)+'"' : '';
        return '<div class="comp-detail">'
            // header
            +'<div class="comp-detail-hdr">'
            +'<button class="comp-detail-back" onclick="CompTab.closeView()"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg> BACK</button>'
            +'<div class="comp-detail-hdr-title">'+_esc(comp.edition+' '+comp.name)+'</div>'
            +'<span class="comp-status comp-status--'+comp.status+'"><span class="comp-status-dot comp-status-dot--'+comp.status+'"></span>'+stLbl+'</span>'
            +'</div>'
            // countdown blocks
            +'<div class="comp-detail-cd"><div class="comp-detail-cd-lbl">'+cdLbl+'</div>'
            +'<div class="comp-detail-cd-blocks"'+cdAttr+'>'
            +'<div class="comp-cd-block"><div class="comp-cd-val" id="cdD">'+cdV.d+'</div><div class="comp-cd-unit">DAY</div></div>'
            +'<div class="comp-cd-sep">:</div>'
            +'<div class="comp-cd-block"><div class="comp-cd-val" id="cdH">'+cdV.h+'</div><div class="comp-cd-unit">HR</div></div>'
            +'<div class="comp-cd-sep">:</div>'
            +'<div class="comp-cd-block"><div class="comp-cd-val" id="cdM">'+cdV.m+'</div><div class="comp-cd-unit">MIN</div></div>'
            +'<div class="comp-cd-sep">:</div>'
            +'<div class="comp-cd-block"><div class="comp-cd-val" id="cdS">'+cdV.s+'</div><div class="comp-cd-unit">SEC</div></div>'
            +'</div></div>'
            // body
            +'<div class="comp-detail-body">'
            +'<div class="comp-detail-main">'+_podium(lb)+_table(lb)+'</div>'
            +_sidebar(comp, lb)
            +'</div></div>';
    }

    /* ── Detail countdown tick ── */
    function _tickDetail() {
        var blocks = document.querySelector('.comp-detail-cd-blocks');
        if (!blocks) { _stopDetail(); return; }
        var t = _pd(blocks.getAttribute('data-comp-detail-end')); if (!t) return;
        var v = _cd(t);
        var q = function(id,val){ var el=document.getElementById(id); if(el)el.textContent=val; };
        q('cdD',v.d); q('cdH',v.h); q('cdM',v.m); q('cdS',v.s);
    }
    function _startDetail() { _stopDetail(); _tickDetail(); _detailTick = setInterval(_tickDetail,1000); }
    function _stopDetail()  { if(_detailTick){clearInterval(_detailTick);_detailTick=null;} }

    /* ── Grid countdown tick ── */
    function _tick() {
        if (_viewOpen) return;
        document.querySelectorAll('[data-comp-end]').forEach(function(el){
            var t=_pd(el.getAttribute('data-comp-end')); if(t) el.textContent=_cd(t).inline;
        });
    }
    function _startTick() { if(_gridTick)return; _tick(); _gridTick=setInterval(_tick,1000); }
    function _stopTick()  { if(_gridTick){clearInterval(_gridTick);_gridTick=null;} }

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
        var tab    = document.getElementById('tab-competitions');
        var detail = document.getElementById('compDetailView');
        if (!detail) { detail=document.createElement('div'); detail.id='compDetailView'; tab.appendChild(detail); }
        detail.innerHTML = _buildDetail(comp, lb);
        tab.querySelectorAll('.comp-hero,.comp-subtabs,#compGrid').forEach(function(el){el.hidden=true;});
        detail.hidden = false;
        _startDetail();
    }

    function closeView() {
        _viewOpen = false; _stopDetail();
        var tab    = document.getElementById('tab-competitions');
        var detail = document.getElementById('compDetailView');
        if (detail) detail.hidden = true;
        tab.querySelectorAll('.comp-hero,.comp-subtabs,#compGrid').forEach(function(el){el.hidden=false;});
    }

    function openPrizepool(id) { /* TBD */ }
    function openInfo(id)      { /* TBD */ }

    /* ── Init ── */
    function init() {
        document.querySelectorAll('.comp-subtab-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                document.querySelectorAll('.comp-subtab-btn').forEach(function(b){b.classList.remove('comp-subtab-btn--active');});
                btn.classList.add('comp-subtab-btn--active');
                _filter(btn.getAttribute('data-comp-filter'));
            });
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
    return { openView:openView, closeView:closeView, openPrizepool:openPrizepool, openInfo:openInfo };
}());
