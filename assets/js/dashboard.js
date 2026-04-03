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

    // ─── Init ───
    function init() {
        // Sidebar nav clicks
        document.querySelectorAll('.dash-nav-item').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var group = btn.closest('.dash-nav-group');
                if (group && group.id === 'navGroupProfile') {
                    var isOpen = group.classList.toggle('open');
                    if (isOpen) {
                        switchTab('settings');
                        showProfileSection('profile');
                    } else {
                        resetProfileSections();
                    }
                } else {
                    switchTab(btn.dataset.tab);
                }
            });
        });

        // Mobile tab clicks
        document.querySelectorAll('.dash-mobile-tab').forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
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

    return { switchTab, filterChallenges, copyReferral, setTheme, toggleTheme, submitKycDoc };
})();
