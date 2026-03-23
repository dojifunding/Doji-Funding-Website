/**
 * Doji Funding — Auth Module
 * 
 * Handles login/signup modals, AJAX form submission,
 * password visibility toggle, strength indicator, user dropdown.
 */

const AuthModal = (function() {
    'use strict';

    // ─── Open modal ───
    function open(type) {
        close(); // close any open
        const id = type === 'login' ? 'loginModal' : 'signupModal';
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
        // Focus first input after animation
        setTimeout(() => {
            const input = document.querySelector('#' + id + ' .form-input');
            if (input) input.focus();
        }, 300);
    }

    // ─── Close all modals ───
    function close() {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
        document.body.style.overflow = '';
        // Clear errors
        const le = document.getElementById('loginError');
        const se = document.getElementById('signupError');
        if (le) le.textContent = '';
        if (se) se.textContent = '';
    }

    // ─── Switch between login/signup ───
    function switchTo(type) {
        close();
        setTimeout(() => open(type), 150);
    }

    // ─── Toggle password visibility ───
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    }

    // ─── Password strength ───
    function checkStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        
        if (score <= 2) return { text: '⚠ Weak password', cls: 'weak' };
        if (score <= 3) return { text: '◐ Medium strength', cls: 'medium' };
        return { text: '✓ Strong password', cls: 'strong' };
    }

    // ─── Submit Login ───
    async function submitLogin(e) {
        e.preventDefault();
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const err = document.getElementById('loginError');
        
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline';
        err.textContent = '';

        try {
            const data = new FormData(form);
            const res = await fetch('api/login.php', {
                method: 'POST',
                body: data,
            });
            const json = await res.json();

            if (json.success) {
                close();
                // Reload to update nav state
                window.location.reload();
            } else {
                err.textContent = json.error || 'Login failed. Please try again.';
            }
        } catch (error) {
            err.textContent = 'Connection error. Please try again.';
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    }

    // ─── Submit Signup ───
    async function submitSignup(e) {
        e.preventDefault();
        const form = document.getElementById('signupForm');
        const btn = document.getElementById('signupBtn');
        const err = document.getElementById('signupError');

        // Client-side validation
        const password = form.password.value;
        
        if (password.length < 8) {
            err.textContent = 'Password must be at least 8 characters.';
            return;
        }
        if (!form.country.value) {
            err.textContent = 'Please select your country.';
            return;
        }

        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline';
        err.textContent = '';

        try {
            const data = new FormData(form);
            const res = await fetch('api/register.php', {
                method: 'POST',
                body: data,
            });
            const json = await res.json();

            if (json.success) {
                close();
                // Auto-login after signup — reload to update nav
                window.location.reload();
            } else {
                err.textContent = json.error || 'Registration failed. Please try again.';
            }
        } catch (error) {
            err.textContent = 'Connection error. Please try again.';
        } finally {
            btn.disabled = false;
            btn.querySelector('.btn-text').style.display = 'inline';
            btn.querySelector('.btn-loader').style.display = 'none';
        }
    }

    // ─── Logout ───
    async function logout() {
        try {
            await fetch('api/logout.php', { method: 'POST' });
        } catch (e) { /* ignore */ }
        window.location.href = 'index.php';
    }

    // ─── User dropdown toggle ───
    function toggleDropdown() {
        const dd = document.getElementById('userDropdown');
        if (dd) {
            const isVisible = dd.style.display === 'block';
            dd.style.display = isVisible ? 'none' : 'block';
        }
    }

    // ─── Google OAuth ───
    function googleAuth() {
        // Placeholder: In production, this would redirect to Google OAuth consent screen
        // For now, show a friendly message
        const loginErr = document.getElementById('loginError');
        const signupErr = document.getElementById('signupError');
        const msg = 'Google Sign-In will be available soon. Please use email registration for now.';
        if (loginErr && loginErr.closest('.modal-overlay').classList.contains('active')) {
            loginErr.textContent = msg;
            loginErr.style.display = 'block';
            loginErr.style.color = 'var(--orange)';
        } else if (signupErr) {
            signupErr.textContent = msg;
            signupErr.style.display = 'block';
            signupErr.style.color = 'var(--orange)';
        }
    }

    // ─── Country & Phone Code data ───
    const COUNTRIES = [
        {n:'Afghanistan',c:'AF',p:'+93'},{n:'Albania',c:'AL',p:'+355'},{n:'Algeria',c:'DZ',p:'+213'},
        {n:'Argentina',c:'AR',p:'+54'},{n:'Australia',c:'AU',p:'+61'},{n:'Austria',c:'AT',p:'+43'},
        {n:'Bangladesh',c:'BD',p:'+880'},{n:'Belgium',c:'BE',p:'+32'},{n:'Brazil',c:'BR',p:'+55'},
        {n:'Bulgaria',c:'BG',p:'+359'},{n:'Cameroon',c:'CM',p:'+237'},{n:'Canada',c:'CA',p:'+1'},
        {n:'Chile',c:'CL',p:'+56'},{n:'China',c:'CN',p:'+86'},{n:'Colombia',c:'CO',p:'+57'},
        {n:'Congo (DRC)',c:'CD',p:'+243'},{n:'Costa Rica',c:'CR',p:'+506'},{n:'Croatia',c:'HR',p:'+385'},
        {n:'Cuba',c:'CU',p:'+53'},{n:'Czech Republic',c:'CZ',p:'+420'},{n:'Denmark',c:'DK',p:'+45'},
        {n:'Dominican Republic',c:'DO',p:'+1'},{n:'Ecuador',c:'EC',p:'+593'},{n:'Egypt',c:'EG',p:'+20'},
        {n:'Estonia',c:'EE',p:'+372'},{n:'Ethiopia',c:'ET',p:'+251'},{n:'Finland',c:'FI',p:'+358'},
        {n:'France',c:'FR',p:'+33'},{n:'Germany',c:'DE',p:'+49'},{n:'Ghana',c:'GH',p:'+233'},
        {n:'Greece',c:'GR',p:'+30'},{n:'Guatemala',c:'GT',p:'+502'},{n:'Honduras',c:'HN',p:'+504'},
        {n:'Hong Kong',c:'HK',p:'+852'},{n:'Hungary',c:'HU',p:'+36'},{n:'Iceland',c:'IS',p:'+354'},
        {n:'India',c:'IN',p:'+91'},{n:'Indonesia',c:'ID',p:'+62'},{n:'Iran',c:'IR',p:'+98'},
        {n:'Iraq',c:'IQ',p:'+964'},{n:'Ireland',c:'IE',p:'+353'},{n:'Israel',c:'IL',p:'+972'},
        {n:'Italy',c:'IT',p:'+39'},{n:'Ivory Coast',c:'CI',p:'+225'},{n:'Jamaica',c:'JM',p:'+1'},
        {n:'Japan',c:'JP',p:'+81'},{n:'Jordan',c:'JO',p:'+962'},{n:'Kazakhstan',c:'KZ',p:'+7'},
        {n:'Kenya',c:'KE',p:'+254'},{n:'Kuwait',c:'KW',p:'+965'},{n:'Latvia',c:'LV',p:'+371'},
        {n:'Lebanon',c:'LB',p:'+961'},{n:'Libya',c:'LY',p:'+218'},{n:'Lithuania',c:'LT',p:'+370'},
        {n:'Luxembourg',c:'LU',p:'+352'},{n:'Malaysia',c:'MY',p:'+60'},{n:'Mexico',c:'MX',p:'+52'},
        {n:'Morocco',c:'MA',p:'+212'},{n:'Mozambique',c:'MZ',p:'+258'},{n:'Nepal',c:'NP',p:'+977'},
        {n:'Netherlands',c:'NL',p:'+31'},{n:'New Zealand',c:'NZ',p:'+64'},{n:'Nigeria',c:'NG',p:'+234'},
        {n:'Norway',c:'NO',p:'+47'},{n:'Oman',c:'OM',p:'+968'},{n:'Pakistan',c:'PK',p:'+92'},
        {n:'Panama',c:'PA',p:'+507'},{n:'Paraguay',c:'PY',p:'+595'},{n:'Peru',c:'PE',p:'+51'},
        {n:'Philippines',c:'PH',p:'+63'},{n:'Poland',c:'PL',p:'+48'},{n:'Portugal',c:'PT',p:'+351'},
        {n:'Qatar',c:'QA',p:'+974'},{n:'Romania',c:'RO',p:'+40'},{n:'Russia',c:'RU',p:'+7'},
        {n:'Saudi Arabia',c:'SA',p:'+966'},{n:'Senegal',c:'SN',p:'+221'},{n:'Serbia',c:'RS',p:'+381'},
        {n:'Singapore',c:'SG',p:'+65'},{n:'Slovakia',c:'SK',p:'+421'},{n:'Slovenia',c:'SI',p:'+386'},
        {n:'South Africa',c:'ZA',p:'+27'},{n:'South Korea',c:'KR',p:'+82'},{n:'Spain',c:'ES',p:'+34'},
        {n:'Sri Lanka',c:'LK',p:'+94'},{n:'Sweden',c:'SE',p:'+46'},{n:'Switzerland',c:'CH',p:'+41'},
        {n:'Taiwan',c:'TW',p:'+886'},{n:'Tanzania',c:'TZ',p:'+255'},{n:'Thailand',c:'TH',p:'+66'},
        {n:'Tunisia',c:'TN',p:'+216'},{n:'Turkey',c:'TR',p:'+90'},{n:'UAE',c:'AE',p:'+971'},
        {n:'Uganda',c:'UG',p:'+256'},{n:'Ukraine',c:'UA',p:'+380'},{n:'United Kingdom',c:'GB',p:'+44'},
        {n:'United States',c:'US',p:'+1'},{n:'Uruguay',c:'UY',p:'+598'},{n:'Venezuela',c:'VE',p:'+58'},
        {n:'Vietnam',c:'VN',p:'+84'},{n:'Zimbabwe',c:'ZW',p:'+263'}
    ];

    function populateCountries() {
        const sel = document.getElementById('signupCountry');
        const phoneCode = document.getElementById('phoneCode');
        if (!sel) return;

        // Populate country dropdown
        COUNTRIES.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.n;
            opt.textContent = c.n;
            sel.appendChild(opt);
        });

        // Populate phone codes (deduplicated)
        if (phoneCode) {
            phoneCode.innerHTML = '';
            const seen = new Set();
            COUNTRIES.forEach(c => {
                if (seen.has(c.p)) return;
                seen.add(c.p);
                const opt = document.createElement('option');
                opt.value = c.p;
                opt.textContent = c.p;
                phoneCode.appendChild(opt);
            });
        }

        // Auto-sync phone code when country changes
        sel.addEventListener('change', () => {
            const country = COUNTRIES.find(c => c.n === sel.value);
            if (country && phoneCode) {
                phoneCode.value = country.p;
            }
        });
    }

    // ─── Init ───
    function init() {
        // Populate country & phone dropdowns
        populateCountries();
        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) close();
            });
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                close();
                const dd = document.getElementById('userDropdown');
                if (dd) dd.style.display = 'none';
            }
        });

        // Password strength indicator
        const signupPw = document.getElementById('signupPassword');
        if (signupPw) {
            signupPw.addEventListener('input', () => {
                const el = document.getElementById('passwordStrength');
                if (!el) return;
                if (signupPw.value.length === 0) {
                    el.textContent = '';
                    el.className = 'form-hint';
                } else {
                    const s = checkStrength(signupPw.value);
                    el.textContent = s.text;
                    el.className = 'form-hint ' + s.cls;
                }
            });
        }

        // Close dropdown on outside click
        document.addEventListener('click', (e) => {
            const dd = document.getElementById('userDropdown');
            const btn = document.querySelector('.nav-user-btn');
            if (dd && btn && !dd.contains(e.target) && !btn.contains(e.target)) {
                dd.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        open,
        close,
        switchTo,
        togglePassword,
        submitLogin,
        submitSignup,
        logout,
        toggleDropdown,
        googleAuth,
    };
})();
