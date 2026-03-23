/**
 * Doji Funding — Dashboard Module
 * Tab navigation, form submissions, filters, and clipboard utils.
 */
const Dashboard = (function() {
    'use strict';

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

        // Update URL hash without scrolling
        history.replaceState(null, '', '#' + tabName);
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

    // ─── Init ───
    function init() {
        // Sidebar nav clicks
        document.querySelectorAll('.dash-nav-item').forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
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

        // File upload label
        updateUploadLabel();

        // Restore tab from hash
        const hash = location.hash.replace('#', '');
        if (['overview', 'challenges', 'payouts', 'settings'].includes(hash)) {
            switchTab(hash);
        }
    }

    document.addEventListener('DOMContentLoaded', init);

    return { switchTab, filterChallenges, copyReferral };
})();
