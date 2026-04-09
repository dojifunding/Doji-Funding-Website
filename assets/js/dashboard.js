/**
 * Doji Funding — Dashboard Module
 * Tab navigation, form submissions, filters, and clipboard utils.
 */
const Dashboard = (function() {
    'use strict';

    // ─── Tab titles ───
    const TAB_TITLES = {
        overview:     'DASHBOARD',
        challenges:   'CHALLENGES',
        configurator: 'CONFIGURATOR',
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
        if (['overview', 'challenges', 'payouts', 'settings'].includes(hash)) {
            switchTab(hash);
            if (hash === 'settings') {
                var profileGroup = document.getElementById('navGroupProfile');
                if (profileGroup) profileGroup.classList.add('open');
                showProfileSection('profile');
            }
        }

        // Init theme
        initTheme();

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
