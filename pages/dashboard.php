<?php
/**
 * Doji Funding — Dashboard Page
 * Layout: fixed sidebar + fixed topbar + scrollable content (Phidias-style)
 */
?>
<!-- Nothing OS — Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=Space+Grotesk:wght@300;400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
/* Nothing Style — supprime grain/bruit/scanlines */
body::before  { opacity: 0 !important; animation: none !important; }
.noise        { display: none !important; }
.scanlines    { display: none !important; }
</style>
<?php

$user = getCurrentUser();
$kycLabels  = ['none' => 'Not Submitted', 'pending' => 'Under Review', 'approved' => 'Verified', 'rejected' => 'Rejected'];
$kycStatus  = $profile['kyc_status'] ?? 'none';
$kycClass   = ['none' => 'kyc-none', 'pending' => 'kyc-pending', 'approved' => 'kyc-approved', 'rejected' => 'kyc-rejected'];
$initials   = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
?>

<div class="dash">

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside class="dash-sidebar">

        <!-- Logo -->
        <div class="dash-sidebar-logo">
            <a href="index.php" class="dash-logo-link">
                <img src="<?= LOGO_FILE ?>" alt="Doji" class="dash-logo-img" onerror="this.style.display='none'">
                <span class="dash-logo-brand">DOJI <span class="green">FUNDING</span></span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="dash-nav">
            <button class="dash-nav-item active" data-tab="overview">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>Dashboard</span>
            </button>
            <button class="dash-nav-item" data-tab="challenges">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                <span>Challenges</span>
                <?php if (($overview['active_challenges'] ?? 0) > 0): ?>
                <span class="dash-nav-badge"><?= $overview['active_challenges'] ?></span>
                <?php endif; ?>
            </button>
            <button class="dash-nav-item" data-tab="configurator">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41M20 12h2M2 12h2M12 20v2M12 2v2"/></svg>
                <span>Configurator</span>
            </button>
            <button class="dash-nav-item" data-tab="payouts">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
                <span>Payouts</span>
            </button>

            <button class="dash-nav-item" data-tab="statistics">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                <span>Statistics</span>
            </button>
            <button class="dash-nav-item" data-tab="competitions">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M6 9H3.5a1.5 1.5 0 010-3H6"/><path d="M18 9h2.5a1.5 1.5 0 000-3H18"/><path d="M6 6h12v5a6 6 0 01-12 0V6z"/><path d="M12 17v4"/><path d="M8 21h8"/></svg>
                <span>Competitions</span>
            </button>
            <button class="dash-nav-item" data-tab="leaderboard">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><rect x="2" y="14" width="6" height="8" rx="1"/><rect x="9" y="9" width="6" height="13" rx="1"/><rect x="16" y="11" width="6" height="11" rx="1"/></svg>
                <span>Leaderboard</span>
            </button>
            <button class="dash-nav-item" data-tab="certificates">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                <span>Certificates</span>
            </button>
            <button class="dash-nav-item" data-tab="calendar">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Calendar</span>
            </button>
            <button class="dash-nav-item" data-tab="affiliate">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                <span>Affiliate</span>
            </button>
            <button class="dash-nav-item" data-tab="testimonials">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Testimonials</span>
            </button>
            <button class="dash-nav-item" data-tab="support">
                <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>Support</span>
            </button>

            <div class="dash-nav-group" id="navGroupProfile">
                <button class="dash-nav-item" data-tab="settings" id="navProfile">
                    <svg class="dash-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profile</span>
                    <svg class="dash-nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="dash-nav-sub" id="navSubProfile">
                    <a class="dash-nav-sub-item" data-section="profile" href="#">Profile</a>
                    <a class="dash-nav-sub-item" data-section="verification" href="#">Account Verification</a>
                    <a class="dash-nav-sub-item" data-section="security" href="#">Security</a>
                    <a class="dash-nav-sub-item" data-section="bank" href="#">Bank Accounts</a>
                    <a class="dash-nav-sub-item" data-section="cards" href="#">Credit Cards</a>
                    <a class="dash-nav-sub-item" data-section="crypto" href="#">Crypto Wallets</a>
                    <a class="dash-nav-sub-item" data-section="payments" href="#">Payment History</a>
                    <a class="dash-nav-sub-item" data-section="discord" href="#">Discord</a>
                    <a class="dash-nav-sub-item" data-section="suggestions" href="#">Feature Suggestions</a>
                    <a class="dash-nav-sub-item" data-section="preferences" href="#">Preferences</a>
                </div>
            </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="dash-sidebar-foot">
            <button class="dash-theme-switch" id="dashThemeSwitch" onclick="Dashboard.toggleTheme()" title="Toggle theme">
                <svg id="dashThemeIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></svg>
            </button>
            <a href="https://discord.gg/kNUqAqCppU" target="_blank" rel="noopener" class="dash-discord-btn" title="Join Doji Funding Discord">
                <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M20.317 4.492c-1.53-.69-3.17-1.2-4.885-1.49a.075.075 0 00-.079.036c-.21.369-.444.85-.608 1.23a18.566 18.566 0 00-5.487 0 12.36 12.36 0 00-.617-1.23A.077.077 0 008.562 3c-1.714.29-3.354.8-4.885 1.491a.07.07 0 00-.032.027C.533 9.093-.32 13.555.099 17.961a.08.08 0 00.031.055 20.03 20.03 0 005.993 2.98.078.078 0 00.084-.026 13.83 13.83 0 001.226-1.963.074.074 0 00-.041-.104 13.201 13.201 0 01-1.872-.878.075.075 0 01-.008-.125c.126-.093.252-.19.372-.287a.075.075 0 01.078-.01c3.927 1.764 8.18 1.764 12.061 0a.075.075 0 01.079.009c.12.098.245.195.372.288a.075.075 0 01-.006.125c-.598.344-1.22.635-1.873.877a.075.075 0 00-.041.105c.36.687.772 1.341 1.225 1.962a.077.077 0 00.084.028 19.963 19.963 0 006.002-2.981.076.076 0 00.032-.054c.5-5.094-.838-9.52-3.549-13.442a.06.06 0 00-.031-.028zM8.02 15.278c-1.182 0-2.157-1.069-2.157-2.38 0-1.312.956-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.956 2.38-2.157 2.38zm7.975 0c-1.183 0-2.157-1.069-2.157-2.38 0-1.312.955-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.946 2.38-2.157 2.38z"/></svg>
                <span>Discord</span>
            </a>
            <a href="index.php" class="dash-back-link">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                dojifunding.com
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>

    </aside>

    <!-- ═══════════════ MAIN WRAP ═══════════════ -->
    <div class="dash-main-wrap">

        <!-- ─── TOPBAR ─── -->
        <header class="dash-topbar">

            <!-- LEFT — user + logout + new challenge -->
            <div class="dash-topbar-left">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="dash-topbar-username"><?= htmlspecialchars(strtolower($user['first_name'] . $user['last_name'])) ?></span>
                <button class="dash-topbar-logout" onclick="AuthModal.logout()">Log out</button>
                <button class="dash-topbar-new-challenge" onclick="Dashboard.switchTab('configurator')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Challenge
                </button>
                <button class="dash-topbar-support" onclick="Dashboard.switchTab('support')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Support
                </button>
            </div>

            <!-- RIGHT — market session cards -->
            <div class="dash-topbar-right">
                <div class="dash-sessions" id="dashSessions">

                    <div class="dash-sc" id="sc-sydney" data-utc-open="22" data-utc-close="7" data-zone="Australia/Sydney" data-hours="22:00–07:00 UTC">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">Sydney</span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-status">
                            <span class="dash-sc-state-dot"></span>
                            <span class="dash-sc-state-txt"></span>
                        </div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours">22:00 – 07:00 local</div>
                    </div>

                    <div class="dash-sc" id="sc-tokyo" data-utc-open="0" data-utc-close="9" data-zone="Asia/Tokyo" data-hours="09:00–18:00 UTC">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">Tokyo</span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-status">
                            <span class="dash-sc-state-dot"></span>
                            <span class="dash-sc-state-txt"></span>
                        </div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours">09:00 – 18:00 local</div>
                    </div>

                    <div class="dash-sc" id="sc-london" data-utc-open="8" data-utc-close="17" data-zone="Europe/London" data-hours="08:00–17:00 UTC">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">London</span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-status">
                            <span class="dash-sc-state-dot"></span>
                            <span class="dash-sc-state-txt"></span>
                        </div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours">08:00 – 17:00 local</div>
                    </div>

                    <div class="dash-sc" id="sc-newyork" data-utc-open="13" data-utc-close="22" data-zone="America/New_York" data-hours="08:00–17:00 local">
                        <div class="dash-sc-top">
                            <span class="dash-sc-dot"></span>
                            <span class="dash-sc-city">New York</span>
                        </div>
                        <div class="dash-sc-time"></div>
                        <div class="dash-sc-timelabel">Local Time</div>
                        <div class="dash-sc-status">
                            <span class="dash-sc-state-dot"></span>
                            <span class="dash-sc-state-txt"></span>
                        </div>
                        <div class="dash-sc-countdown"></div>
                        <div class="dash-sc-hours">08:00 – 17:00 local</div>
                    </div>

                    <div class="dash-sc dash-sc-local" id="sc-local">
                        <div class="dash-sc-top">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span class="dash-sc-city" id="scLocalCity">My Time</span>
                        </div>
                        <div class="dash-sc-time" id="scLocalTime">—</div>
                        <div class="dash-sc-timelabel">Your Time Zone</div>
                        <div class="dash-sc-tz-name" id="scLocalTz"></div>
                        <div class="dash-sc-hours" id="scLocalTzLabel" style="cursor:pointer;color:var(--accent);opacity:0.7" onclick="Dashboard.switchTab('settings')">Set in Profile ›</div>
                    </div>

                </div>
            </div>

        </header>

        <!-- Market session clock script -->
        <script>
        (function() {
            /* ── Country → IANA timezone map ── */
            var COUNTRY_TZ = {
                'AF':'Asia/Kabul','AL':'Europe/Tirane','DZ':'Africa/Algiers','AR':'America/Argentina/Buenos_Aires',
                'AU':'Australia/Sydney','AT':'Europe/Vienna','BE':'Europe/Brussels','BR':'America/Sao_Paulo',
                'BG':'Europe/Sofia','CA':'America/Toronto','CL':'America/Santiago','CN':'Asia/Shanghai',
                'CO':'America/Bogota','HR':'Europe/Zagreb','CZ':'Europe/Prague','DK':'Europe/Copenhagen',
                'EG':'Africa/Cairo','FI':'Europe/Helsinki','FR':'Europe/Paris','DE':'Europe/Berlin',
                'GH':'Africa/Accra','GR':'Europe/Athens','HK':'Asia/Hong_Kong','HU':'Europe/Budapest',
                'IN':'Asia/Kolkata','ID':'Asia/Jakarta','IE':'Europe/Dublin','IL':'Asia/Jerusalem',
                'IT':'Europe/Rome','JP':'Asia/Tokyo','JO':'Asia/Amman','KE':'Africa/Nairobi',
                'KW':'Asia/Kuwait','LB':'Asia/Beirut','MY':'Asia/Kuala_Lumpur','MX':'America/Mexico_City',
                'MA':'Africa/Casablanca','NL':'Europe/Amsterdam','NZ':'Pacific/Auckland','NG':'Africa/Lagos',
                'NO':'Europe/Oslo','PK':'Asia/Karachi','PE':'America/Lima','PH':'Asia/Manila',
                'PL':'Europe/Warsaw','PT':'Europe/Lisbon','QA':'Asia/Qatar','RO':'Europe/Bucharest',
                'RU':'Europe/Moscow','SA':'Asia/Riyadh','SG':'Asia/Singapore','ZA':'Africa/Johannesburg',
                'KR':'Asia/Seoul','ES':'Europe/Madrid','SE':'Europe/Stockholm','CH':'Europe/Zurich',
                'TW':'Asia/Taipei','TH':'Asia/Bangkok','TN':'Africa/Tunis','TR':'Europe/Istanbul',
                'UA':'Europe/Kiev','AE':'Asia/Dubai','GB':'Europe/London','US':'America/New_York',
                'VN':'Asia/Ho_Chi_Minh','VE':'America/Caracas','PK':'Asia/Karachi','BD':'Asia/Dhaka',
                'LK':'Asia/Colombo','NP':'Asia/Kathmandu','MM':'Asia/Rangoon','KZ':'Asia/Almaty',
                'UZ':'Asia/Tashkent','YE':'Asia/Aden','IQ':'Asia/Baghdad','IR':'Asia/Tehran',
                'BH':'Asia/Bahrain','OM':'Asia/Muscat','AO':'Africa/Luanda','CI':'Africa/Abidjan',
                'TZ':'Africa/Dar_es_Salaam','UG':'Africa/Kampala','ZM':'Africa/Lusaka','ZW':'Africa/Harare',
                'LY':'Africa/Tripoli','SD':'Africa/Khartoum','ET':'Africa/Addis_Ababa',
                'FJ':'Pacific/Fiji','GU':'Pacific/Guam','HI':'Pacific/Honolulu'
            };

            /* ── Read/write user timezone from localStorage ── */
            function getUserTz() {
                return localStorage.getItem('doji_tz') || Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
            }

            function setUserTz(tz) {
                localStorage.setItem('doji_tz', tz);
                updateLocalCard();
            }

            /* ── Auto-set timezone from country code (2-letter ISO) ── */
            window.DojiTz = {
                setFromCountry: function(code) {
                    var tz = COUNTRY_TZ[code.toUpperCase()];
                    if (tz) { setUserTz(tz); return tz; }
                    return null;
                },
                set: setUserTz,
                get: getUserTz
            };

            /* ── Pre-fill timezone select if on profile page ── */
            function prefillTzSelect() {
                var sel = document.getElementById('profileTimezone');
                if (!sel) return;
                var saved = getUserTz();
                for (var i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === saved) { sel.selectedIndex = i; break; }
                }
                sel.addEventListener('change', function() {
                    if (this.value) setUserTz(this.value);
                });
            }
            prefillTzSelect();

            /* ── Local card update ── */
            function updateLocalCard() {
                var tz    = getUserTz();
                var label = tz.split('/').pop().replace(/_/g,' ');
                var city  = document.getElementById('scLocalCity');
                var tzEl  = document.getElementById('scLocalTz');
                var hint  = document.getElementById('scLocalTzLabel');
                if (city) city.textContent = label;
                if (tzEl) tzEl.textContent = tz;
                if (hint) {
                    var hasTz = !!localStorage.getItem('doji_tz');
                    hint.textContent = hasTz ? tz : 'Set in Profile ›';
                    hint.style.color = hasTz ? 'var(--text-dis)' : 'var(--accent)';
                }
            }
            updateLocalCard();

            /* ── Clock helpers ── */
            function fmtCountdown(secs) {
                var h = Math.floor(secs/3600), m = Math.floor((secs%3600)/60);
                return (h > 0 ? h+'h ' : '') + m+'m';
            }

            function isOpen(utcH, utcM, openH, closeH) {
                var cur = utcH*60 + utcM, o = openH*60, c = closeH*60;
                if (o < c) return cur >= o && cur < c;
                return cur >= o || cur < c;
            }

            function secsTo(utcH, utcM, utcS, targetH, forward) {
                var cur  = utcH*3600 + utcM*60 + utcS;
                var diff = targetH*3600 - cur;
                if (forward && diff <= 0) diff += 86400;
                if (!forward && diff >= 0) diff -= 86400;
                return Math.abs(diff);
            }

            function fmt(now, zone) {
                return new Intl.DateTimeFormat('en-GB', {
                    timeZone: zone, hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false
                }).format(now);
            }

            /* ── Main tick ── */
            function tick() {
                var now  = new Date();
                var utcH = now.getUTCHours(), utcM = now.getUTCMinutes(), utcS = now.getUTCSeconds();

                /* Market session cards */
                document.querySelectorAll('.dash-sc:not(.dash-sc-local)').forEach(function(card) {
                    var zone   = card.dataset.zone;
                    var openH  = parseInt(card.dataset.utcOpen);
                    var closeH = parseInt(card.dataset.utcClose);
                    var open   = isOpen(utcH, utcM, openH, closeH);

                    card.querySelector('.dash-sc-time').textContent = fmt(now, zone);

                    var stateTxt  = card.querySelector('.dash-sc-state-txt');
                    var stateDot  = card.querySelector('.dash-sc-state-dot');
                    var countdown = card.querySelector('.dash-sc-countdown');

                    if (open) {
                        card.classList.add('open'); card.classList.remove('closed');
                        stateDot.className = 'dash-sc-state-dot open';
                        stateTxt.textContent = 'OPEN';
                        countdown.textContent = 'Closes in ' + fmtCountdown(secsTo(utcH, utcM, utcS, closeH, true));
                    } else {
                        card.classList.remove('open'); card.classList.add('closed');
                        stateDot.className = 'dash-sc-state-dot closed';
                        stateTxt.textContent = 'CLOSED';
                        countdown.textContent = 'Opens in ' + fmtCountdown(secsTo(utcH, utcM, utcS, openH, true));
                    }
                });

                /* Local card */
                var localTimeEl = document.getElementById('scLocalTime');
                if (localTimeEl) {
                    try { localTimeEl.textContent = fmt(now, getUserTz()); }
                    catch(e) { localTimeEl.textContent = '—'; }
                }
            }

            tick();
            setInterval(tick, 1000);
        })();
        </script>

        <!-- Auto-map country → timezone on signup form -->
        <script>
        (function() {
            /* Hook into signup country select (if modal exists) */
            function hookSignup() {
                var sel = document.getElementById('signupCountry');
                if (!sel) return;
                sel.addEventListener('change', function() {
                    if (this.value && window.DojiTz) {
                        var tz = window.DojiTz.setFromCountry(this.value);
                        /* Also pre-fill profile timezone select if open */
                        var pSel = document.getElementById('profileTimezone');
                        if (pSel && tz) {
                            for (var i=0; i<pSel.options.length; i++) {
                                if (pSel.options[i].value === tz) { pSel.selectedIndex=i; break; }
                            }
                        }
                    }
                });
            }
            /* Wait for modal to be in DOM */
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hookSignup);
            } else {
                hookSignup();
            }
        })();
        </script>


        <main class="dash-main">

            <div class="dash-page-head">
                <h1 class="dash-page-title" id="dashPageTitle">DASHBOARD</h1>
            </div>

            <!-- ══ TAB: OVERVIEW ══ -->
            <div class="dash-tab active" id="tab-overview">

                <div class="dash-kpis">
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
                        <div class="dash-kpi-label">Active Challenges</div>
                        <div class="dash-kpi-val"><?= $overview['active_challenges'] ?></div>
                        <div class="dash-kpi-sub">of <?= $overview['total_challenges'] ?> total</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                        <div class="dash-kpi-label">Funded Accounts</div>
                        <div class="dash-kpi-val green"><?= $overview['funded_accounts'] ?></div>
                        <div class="dash-kpi-sub"><?= $overview['funded_accounts'] > 0 ? 'Congratulations!' : 'Complete a challenge' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg></div>
                        <div class="dash-kpi-label">Total Payouts</div>
                        <div class="dash-kpi-val"><?= formatMoney($overview['total_payout_amount']) ?></div>
                        <div class="dash-kpi-sub"><?= $overview['total_payouts'] ?> payout<?= $overview['total_payouts'] !== 1 ? 's' : '' ?></div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div class="dash-kpi-label">Win Rate</div>
                        <div class="dash-kpi-val"><?= $overview['win_rate'] ?>%</div>
                        <div class="dash-kpi-sub">across all challenges</div>
                    </div>
                </div>

                <?php if (!empty($overview['active_list'])): ?>
                <div class="dash-section">
                    <h2 class="dash-section-title">Active Challenges</h2>
                    <div class="dash-active-grid">
                        <?php foreach ($overview['active_list'] as $ch):
                            $target      = $ch['profit_target_1'];
                            $pnlPct      = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                            $progressPct = $target > 0 ? min(100, max(0, ($pnlPct / $target) * 100)) : 0;
                            $ddUsed      = $ch['account_size'] > 0 && $ch['peak_balance'] > 0
                                ? max(0, (($ch['peak_balance'] - $ch['current_balance']) / $ch['account_size']) * 100) : 0;
                        ?>
                        <div class="dash-challenge-card">
                            <div class="dash-cc-header">
                                <div class="dash-cc-type"><?= $ch['type'] === 'one_step' ? '1-STEP' : '2-STEP' ?><?= $ch['phase'] > 1 ? ' · P' . $ch['phase'] : '' ?></div>
                                <?= challengeStatusBadge($ch['status']) ?>
                            </div>
                            <div class="dash-cc-size"><?= formatMoneyShort($ch['account_size']) ?></div>
                            <div class="dash-cc-platform"><?= strtoupper($ch['platform']) ?></div>
                            <div class="dash-cc-progress">
                                <div class="dash-cc-progress-header">
                                    <span>Profit Target</span>
                                    <span class="<?= $pnlPct >= 0 ? 'green' : 'red' ?>"><?= number_format($pnlPct, 2) ?>% / <?= number_format($target, 0) ?>%</span>
                                </div>
                                <div class="dash-cc-bar"><div class="dash-cc-bar-fill" style="width:<?= $progressPct ?>%"></div></div>
                            </div>
                            <div class="dash-cc-metrics">
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">Balance</span><span class="dash-cc-metric-val"><?= formatMoney($ch['current_balance']) ?></span></div>
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">DD Used</span><span class="dash-cc-metric-val <?= $ddUsed > ($ch['max_loss'] * 0.7) ? 'red' : '' ?>"><?= number_format($ddUsed, 2) ?>%</span></div>
                                <div class="dash-cc-metric"><span class="dash-cc-metric-label">Days</span><span class="dash-cc-metric-val"><?= $ch['trading_days'] ?>/<?= $ch['min_trading_days'] ?></span></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h3>No Active Challenges</h3>
                    <p>Start your first challenge and begin your trading journey</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: MY CHALLENGES ══ -->
            <div class="dash-tab" id="tab-challenges">
                <div class="dash-tab-actions">
                    <p class="dash-tab-count"><?= count($challenges) ?> challenge<?= count($challenges) !== 1 ? 's' : '' ?></p>
                    <a href="challenges.php" class="dash-action-btn">+ New Challenge</a>
                </div>
                <div class="dash-filters">
                    <button class="dash-filter active" data-filter="all">All</button>
                    <button class="dash-filter" data-filter="active">Active</button>
                    <button class="dash-filter" data-filter="funded">Funded</button>
                    <button class="dash-filter" data-filter="passed">Passed</button>
                    <button class="dash-filter" data-filter="failed">Failed</button>
                </div>
                <?php if (!empty($challenges)): ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Challenge</th><th>Account</th><th>Progress</th><th>Balance</th><th>P&amp;L</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($challenges as $ch):
                                $pnlPct = $ch['account_size'] > 0 ? ($ch['total_profit'] / $ch['account_size']) * 100 : 0;
                                $target = $ch['profit_target_1'];
                            ?>
                            <tr class="dash-row" data-status="<?= $ch['status'] ?>">
                                <td><div class="dash-cell-type"><?= $ch['type'] === 'one_step' ? '1-Step' : '2-Step' ?></div><div class="dash-cell-sub"><?= strtoupper($ch['platform']) ?><?= $ch['phase'] > 1 ? ' · Phase ' . $ch['phase'] : '' ?></div></td>
                                <td class="mono"><?= formatMoneyShort($ch['account_size']) ?></td>
                                <td><div class="dash-mini-bar"><div class="dash-mini-bar-fill" style="width:<?= min(100, max(0, ($pnlPct / $target) * 100)) ?>%"></div></div><span class="dash-cell-sub"><?= number_format($pnlPct, 1) ?>% / <?= $target ?>%</span></td>
                                <td class="mono"><?= formatMoney($ch['current_balance']) ?></td>
                                <td class="mono <?= $ch['total_profit'] >= 0 ? 'green' : 'red' ?>"><?= $ch['total_profit'] >= 0 ? '+' : '' ?><?= formatMoney($ch['total_profit']) ?></td>
                                <td><?= challengeStatusBadge($ch['status']) ?></td>
                                <td class="dash-cell-sub"><?= timeAgo($ch['purchased_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    <h3>No Challenges Yet</h3>
                    <p>Purchase your first challenge to start trading</p>
                    <a href="challenges.php" class="dash-action-btn">Browse Challenges</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: PAYOUTS ══ -->
            <div class="dash-tab" id="tab-payouts">
                <div class="dash-tab-actions">
                    <p class="dash-tab-count">Total earned: <strong class="green"><?= formatMoney($overview['total_payout_amount']) ?></strong></p>
                </div>
                <?php if (!empty($payouts)): ?>
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead><tr><th>Payout</th><th>Challenge</th><th>Amount</th><th>Method</th><th>Status</th><th>Requested</th></tr></thead>
                        <tbody>
                            <?php foreach ($payouts as $po):
                                $methodLabels = ['crypto_btc'=>'BTC','crypto_eth'=>'ETH','crypto_usdt'=>'USDT','bank_transfer'=>'Bank','wise'=>'Wise','paypal'=>'PayPal'];
                            ?>
                            <tr>
                                <td class="mono">#<?= $po['id'] ?></td>
                                <td><span class="dash-cell-type"><?= $po['challenge_type'] === 'one_step' ? '1-Step' : '2-Step' ?></span> <span class="dash-cell-sub"><?= formatMoneyShort($po['account_size']) ?></span></td>
                                <td class="mono green">+<?= formatMoney($po['amount']) ?></td>
                                <td><?= $methodLabels[$po['method']] ?? '—' ?></td>
                                <td><?= payoutStatusBadge($po['status']) ?></td>
                                <td class="dash-cell-sub"><?= timeAgo($po['requested_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dash-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
                    <h3>No Payouts Yet</h3>
                    <p>Complete a funded challenge to request your first payout</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: PROFILE ══ -->
            <div class="dash-tab" id="tab-settings">
                <div class="dash-profile-layout">

                    <!-- ── Left: User card ── -->
                    <div class="dash-profile-left">
                        <div class="dash-user-card">
                            <div class="dash-user-card-av"><?= $initials ?></div>
                            <div class="dash-user-card-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                            <div class="dash-user-card-email"><?= htmlspecialchars($profile['email'] ?? '') ?></div>
                            <div class="dash-user-card-badge <?= $kycClass[$kycStatus] ?>">
                                <?php if ($kycStatus === 'approved'): ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified Trader
                                <?php elseif ($kycStatus === 'pending'): ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Under Review
                                <?php else: ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Not Verified
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($user['created_at'])): ?>
                            <div class="dash-user-card-since">Member since <?= date('M Y', strtotime($user['created_at'])) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Coins widget -->
                        <div class="dash-coins-card">
                            <div class="dash-coins-card-icon">🪙</div>
                            <div class="dash-coins-card-val"><?= number_format($overview['doji_coins'] ?? 0) ?></div>
                            <div class="dash-coins-card-label">Doji Coins</div>
                        </div>
                    </div>

                    <!-- ── Right: Sections ── -->
                    <div class="dash-profile-right">

                        <!-- Section: Personal Information -->
                        <div class="dash-psection" id="psec-profile">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Personal Information
                            </div>
                            <div class="dash-psection-body">
                                <form id="profileForm" class="dash-form">
                                    <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>First Name <span class="req">*</span></label>
                                            <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" class="dash-input" required>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Last Name <span class="req">*</span></label>
                                            <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" class="dash-input" required>
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Username</label>
                                            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" class="dash-input" placeholder="your_pseudo" pattern="[a-zA-Z0-9_]{3,30}">
                                            <span class="dash-form-hint">Letters, numbers, underscores — min 3 characters</span>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Email</label>
                                            <input type="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" class="dash-input" disabled>
                                            <span class="dash-form-hint">Contact support to change email</span>
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Phone</label>
                                            <input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" class="dash-input" placeholder="+1 234 567 890">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Address</label>
                                            <input type="text" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" class="dash-input" placeholder="Street address">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>City</label>
                                            <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>" class="dash-input">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Postal Code</label>
                                            <input type="text" name="zipcode" value="<?= htmlspecialchars($profile['zipcode'] ?? '') ?>" class="dash-input">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Country</label>
                                            <input type="text" name="country" value="<?= htmlspecialchars($profile['country'] ?? '') ?>" class="dash-input">
                                        </div>
                                        <div class="dash-form-group">
                                            <label>Region / State</label>
                                            <input type="text" name="region" value="<?= htmlspecialchars($profile['region'] ?? '') ?>" class="dash-input">
                                        </div>
                                    </div>
                                    <div class="dash-form-row">
                                        <div class="dash-form-group" style="grid-column:1/-1">
                                            <label>Preferred Time Zone</label>
                                            <select name="timezone" id="profileTimezone" class="dash-input">
                                                <option value="">— Select your time zone —</option>
                                                <optgroup label="UTC">
                                                    <option value="UTC">UTC — Coordinated Universal Time</option>
                                                </optgroup>
                                                <optgroup label="Americas">
                                                    <option value="America/Anchorage">America/Anchorage — AKST/AKDT</option>
                                                    <option value="America/Los_Angeles">America/Los_Angeles — PST/PDT</option>
                                                    <option value="America/Denver">America/Denver — MST/MDT</option>
                                                    <option value="America/Phoenix">America/Phoenix — MST</option>
                                                    <option value="America/Chicago">America/Chicago — CST/CDT</option>
                                                    <option value="America/New_York">America/New_York — EST/EDT</option>
                                                    <option value="America/Halifax">America/Halifax — AST/ADT</option>
                                                    <option value="America/St_Johns">America/St_Johns — NST/NDT</option>
                                                    <option value="America/Sao_Paulo">America/Sao_Paulo — BRT</option>
                                                    <option value="America/Argentina/Buenos_Aires">America/Buenos_Aires — ART</option>
                                                    <option value="America/Santiago">America/Santiago — CLT</option>
                                                    <option value="America/Bogota">America/Bogota — COT</option>
                                                    <option value="America/Lima">America/Lima — PET</option>
                                                    <option value="America/Caracas">America/Caracas — VET</option>
                                                    <option value="America/Mexico_City">America/Mexico_City — CST/CDT</option>
                                                    <option value="America/Toronto">America/Toronto — EST/EDT</option>
                                                    <option value="America/Vancouver">America/Vancouver — PST/PDT</option>
                                                </optgroup>
                                                <optgroup label="Europe">
                                                    <option value="Europe/London">Europe/London — GMT/BST</option>
                                                    <option value="Europe/Lisbon">Europe/Lisbon — WET/WEST</option>
                                                    <option value="Europe/Paris">Europe/Paris — CET/CEST</option>
                                                    <option value="Europe/Berlin">Europe/Berlin — CET/CEST</option>
                                                    <option value="Europe/Madrid">Europe/Madrid — CET/CEST</option>
                                                    <option value="Europe/Rome">Europe/Rome — CET/CEST</option>
                                                    <option value="Europe/Amsterdam">Europe/Amsterdam — CET/CEST</option>
                                                    <option value="Europe/Brussels">Europe/Brussels — CET/CEST</option>
                                                    <option value="Europe/Zurich">Europe/Zurich — CET/CEST</option>
                                                    <option value="Europe/Stockholm">Europe/Stockholm — CET/CEST</option>
                                                    <option value="Europe/Oslo">Europe/Oslo — CET/CEST</option>
                                                    <option value="Europe/Copenhagen">Europe/Copenhagen — CET/CEST</option>
                                                    <option value="Europe/Helsinki">Europe/Helsinki — EET/EEST</option>
                                                    <option value="Europe/Warsaw">Europe/Warsaw — CET/CEST</option>
                                                    <option value="Europe/Prague">Europe/Prague — CET/CEST</option>
                                                    <option value="Europe/Budapest">Europe/Budapest — CET/CEST</option>
                                                    <option value="Europe/Athens">Europe/Athens — EET/EEST</option>
                                                    <option value="Europe/Bucharest">Europe/Bucharest — EET/EEST</option>
                                                    <option value="Europe/Kiev">Europe/Kiev — EET/EEST</option>
                                                    <option value="Europe/Moscow">Europe/Moscow — MSK</option>
                                                    <option value="Europe/Istanbul">Europe/Istanbul — TRT</option>
                                                </optgroup>
                                                <optgroup label="Africa">
                                                    <option value="Africa/Casablanca">Africa/Casablanca — WET</option>
                                                    <option value="Africa/Lagos">Africa/Lagos — WAT</option>
                                                    <option value="Africa/Cairo">Africa/Cairo — EET</option>
                                                    <option value="Africa/Nairobi">Africa/Nairobi — EAT</option>
                                                    <option value="Africa/Johannesburg">Africa/Johannesburg — SAST</option>
                                                </optgroup>
                                                <optgroup label="Middle East">
                                                    <option value="Asia/Dubai">Asia/Dubai — GST</option>
                                                    <option value="Asia/Riyadh">Asia/Riyadh — AST</option>
                                                    <option value="Asia/Qatar">Asia/Qatar — AST</option>
                                                    <option value="Asia/Kuwait">Asia/Kuwait — AST</option>
                                                    <option value="Asia/Bahrain">Asia/Bahrain — AST</option>
                                                    <option value="Asia/Tehran">Asia/Tehran — IRST</option>
                                                    <option value="Asia/Beirut">Asia/Beirut — EET/EEST</option>
                                                    <option value="Asia/Jerusalem">Asia/Jerusalem — IST/IDT</option>
                                                </optgroup>
                                                <optgroup label="Asia">
                                                    <option value="Asia/Karachi">Asia/Karachi — PKT</option>
                                                    <option value="Asia/Kolkata">Asia/Kolkata — IST</option>
                                                    <option value="Asia/Colombo">Asia/Colombo — IST</option>
                                                    <option value="Asia/Dhaka">Asia/Dhaka — BST</option>
                                                    <option value="Asia/Kathmandu">Asia/Kathmandu — NPT</option>
                                                    <option value="Asia/Almaty">Asia/Almaty — ALMT</option>
                                                    <option value="Asia/Tashkent">Asia/Tashkent — UZT</option>
                                                    <option value="Asia/Rangoon">Asia/Rangoon — MMT</option>
                                                    <option value="Asia/Bangkok">Asia/Bangkok — ICT</option>
                                                    <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh — ICT</option>
                                                    <option value="Asia/Jakarta">Asia/Jakarta — WIB</option>
                                                    <option value="Asia/Shanghai">Asia/Shanghai — CST</option>
                                                    <option value="Asia/Hong_Kong">Asia/Hong_Kong — HKT</option>
                                                    <option value="Asia/Singapore">Asia/Singapore — SGT</option>
                                                    <option value="Asia/Taipei">Asia/Taipei — CST</option>
                                                    <option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur — MYT</option>
                                                    <option value="Asia/Manila">Asia/Manila — PST</option>
                                                    <option value="Asia/Seoul">Asia/Seoul — KST</option>
                                                    <option value="Asia/Tokyo">Asia/Tokyo — JST</option>
                                                    <option value="Asia/Yakutsk">Asia/Yakutsk — YAKT</option>
                                                    <option value="Asia/Vladivostok">Asia/Vladivostok — VLAT</option>
                                                </optgroup>
                                                <optgroup label="Pacific &amp; Oceania">
                                                    <option value="Australia/Perth">Australia/Perth — AWST</option>
                                                    <option value="Australia/Darwin">Australia/Darwin — ACST</option>
                                                    <option value="Australia/Adelaide">Australia/Adelaide — ACST/ACDT</option>
                                                    <option value="Australia/Brisbane">Australia/Brisbane — AEST</option>
                                                    <option value="Australia/Sydney">Australia/Sydney — AEST/AEDT</option>
                                                    <option value="Australia/Melbourne">Australia/Melbourne — AEST/AEDT</option>
                                                    <option value="Pacific/Auckland">Pacific/Auckland — NZST/NZDT</option>
                                                    <option value="Pacific/Fiji">Pacific/Fiji — FJT</option>
                                                    <option value="Pacific/Honolulu">Pacific/Honolulu — HST</option>
                                                    <option value="Pacific/Guam">Pacific/Guam — ChST</option>
                                                </optgroup>
                                            </select>
                                            <span class="dash-form-hint">Used for your local clock in the topbar. Auto-detected from your country if not set.</span>
                                        </div>
                                    </div>
                                    <div id="profileMsg" class="dash-form-msg"></div>
                                    <button type="submit" class="dash-btn">Save Changes</button>
                                </form>
                            </div>
                        </div>

                        <!-- Section: Security -->
                        <div class="dash-psection" id="psec-security">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                Security
                            </div>
                            <div class="dash-psection-body">

                                <!-- 2FA Banner (emphasized) -->
                                <div class="dash-2fa-banner">
                                    <div class="dash-2fa-left">
                                        <div class="dash-2fa-icon">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                                        </div>
                                        <div>
                                            <div class="dash-2fa-title">Two-Factor Authentication (2FA)</div>
                                            <div class="dash-2fa-desc">Protect your account with an authenticator app. We strongly recommend enabling 2FA — it is your first line of defense against unauthorized access.</div>
                                        </div>
                                    </div>
                                    <div class="dash-2fa-right">
                                        <span class="dash-2fa-status">Not enabled</span>
                                        <button class="dash-2fa-btn" onclick="alert('2FA setup coming soon. We will notify you by email when available.')">Enable 2FA</button>
                                    </div>
                                </div>

                                <!-- Change Password -->
                                <div class="dash-subsection-title">Change Password</div>
                                <form id="passwordForm" class="dash-form">
                                    <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                    <div class="dash-form-row">
                                        <div class="dash-form-group">
                                            <label>Current Password</label>
                                            <input type="password" name="current_password" class="dash-input" required>
                                        </div>
                                        <div class="dash-form-group">
                                            <label>New Password</label>
                                            <input type="password" name="new_password" class="dash-input" required minlength="8" placeholder="Min. 8 characters">
                                        </div>
                                    </div>
                                    <div class="dash-form-group" style="max-width:50%">
                                        <label>Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="dash-input" required minlength="8">
                                    </div>
                                    <div id="passwordMsg" class="dash-form-msg"></div>
                                    <button type="submit" class="dash-btn">Update Password</button>
                                </form>
                            </div>
                        </div>

                        <!-- Section: KYC Documents -->
                        <div class="dash-psection" id="psec-verification">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                My Documents
                                <span class="dash-psection-badge <?= $kycClass[$kycStatus] ?>"><?= $kycLabels[$kycStatus] ?></span>
                            </div>
                            <div class="dash-psection-body">

                                <div class="dash-docs-grid">

                                    <!-- ID Document — Front -->
                                    <div class="dash-doc-card">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">ID Document (Front)</div>
                                                <div class="dash-doc-card-sub">Front side of document</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Accepted documents:</div>
                                            <ul class="dash-doc-list">
                                                <li>National ID card</li>
                                                <li>Passport</li>
                                                <li>Residence permit</li>
                                            </ul>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'id_front')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="id_front">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <!-- ID Document — Back -->
                                    <div class="dash-doc-card">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">ID Document (Back)</div>
                                                <div class="dash-doc-card-sub">Back side of document</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Required for:</div>
                                            <ul class="dash-doc-list">
                                                <li>National ID card</li>
                                                <li>Residence permit</li>
                                            </ul>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'id_back')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="id_back">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Proof of Address -->
                                    <div class="dash-doc-card dash-doc-card-full">
                                        <div class="dash-doc-card-head">
                                            <div>
                                                <div class="dash-doc-card-title">Proof of Address</div>
                                                <div class="dash-doc-card-sub">Less than 90 days old</div>
                                            </div>
                                            <?php if ($kycStatus === 'approved'): ?>
                                            <span class="dash-doc-verified"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Verified</span>
                                            <?php elseif ($kycStatus === 'pending'): ?>
                                            <span class="dash-doc-pending">Under Review</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dash-doc-accepted">
                                            <div class="dash-doc-accepted-title">Accepted documents:</div>
                                            <ul class="dash-doc-list">
                                                <li>Water / electricity / gas bill</li>
                                                <li>Bank statement</li>
                                            </ul>
                                            <div class="dash-doc-rejected-item">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                Internet/phone bills not accepted
                                            </div>
                                        </div>
                                        <?php if ($kycStatus !== 'approved' && $kycStatus !== 'pending'): ?>
                                        <form class="dash-doc-form" enctype="multipart/form-data" onsubmit="Dashboard.submitKycDoc(event, 'proof_address')">
                                            <input type="hidden" name="csrf" value="<?= generateCsrf() ?>">
                                            <input type="hidden" name="doc_type" value="proof_address">
                                            <div class="dash-upload">
                                                <input type="file" name="kyc_document" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="dash-upload-label">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                                    <span>Click to upload or drag &amp; drop</span>
                                                    <span class="dash-upload-sub">JPG, PNG, PDF — Max 5MB</span>
                                                </div>
                                            </div>
                                            <div class="dash-doc-form-msg dash-form-msg"></div>
                                            <button type="submit" class="dash-btn dash-btn-sm">Submit</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>

                                </div><!-- .dash-docs-grid -->
                            </div>
                        </div>

                        <!-- Section: Referral -->
                        <div class="dash-psection">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                Referral Program
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Share your referral code and earn Doji Coins for every friend who purchases a challenge.</p>
                                <?php if (!empty($profile['referral_code'])): ?>
                                <div class="dash-referral-code">
                                    <span class="mono"><?= htmlspecialchars($profile['referral_code']) ?></span>
                                    <button class="dash-copy-btn" onclick="Dashboard.copyReferral('<?= htmlspecialchars($profile['referral_code']) ?>')">Copy</button>
                                </div>
                                <?php else: ?>
                                <p class="dash-form-hint">Your referral code will be generated after your first challenge purchase.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Section: Bank Accounts -->
                        <div class="dash-psection" id="psec-bank">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="1"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                                Bank Accounts
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Connect your bank account to receive payouts via bank transfer. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Credit Cards -->
                        <div class="dash-psection" id="psec-cards">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Credit Cards
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Manage your saved credit and debit cards for purchases. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Crypto Wallets -->
                        <div class="dash-psection" id="psec-crypto">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.5 9.5c0-1.1.9-2 2-2h1a2 2 0 010 4h-3a2 2 0 010 4h1a2 2 0 002-2M12 7v10"/></svg>
                                Crypto Wallets
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Add your crypto wallet addresses (BTC, ETH, USDT) to receive payout withdrawals. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Payment History -->
                        <div class="dash-psection" id="psec-payments">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
                                Payment History
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">View a full history of your purchases and transactions. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Discord -->
                        <div class="dash-psection" id="psec-discord">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                Discord
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Connect your Discord account to access the Doji Funding community server and receive role-based access. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Feature Suggestions -->
                        <div class="dash-psection" id="psec-suggestions">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Feature Suggestions
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Submit ideas and vote on features you'd like to see in Doji Funding. Help us shape the platform. This feature will be available shortly.</p>
                            </div>
                        </div>

                        <!-- Section: Preferences -->
                        <div class="dash-psection" id="psec-preferences">
                            <div class="dash-psection-head">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                                Preferences
                                <span class="dash-psection-badge" style="background:rgba(255,255,255,0.06);color:var(--dash-text3);margin-left:auto">Coming Soon</span>
                            </div>
                            <div class="dash-psection-body">
                                <p class="dash-psection-desc">Customize your dashboard experience — notifications, language, display settings, and more. This feature will be available shortly.</p>
                            </div>
                        </div>

                    </div><!-- .dash-profile-right -->
                </div><!-- .dash-profile-layout -->
            </div>

            <!-- ══ TAB: CONFIGURATOR ══ -->
            <div class="dash-tab" id="tab-configurator">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41M20 12h2M2 12h2M12 20v2M12 2v2"/></svg>
                    <div class="dash-cs-title">CONFIGURATOR</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Configure and purchase your challenge directly from the dashboard — account size, rules, profit split, and more.</div>
                </div>
            </div>

            <!-- ══ TAB: STATISTICS ══ -->
            <div class="dash-tab" id="tab-statistics">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                    <div class="dash-cs-title">STATISTICS</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Advanced performance analytics, trade history, drawdown charts, win rate, and risk score for all your funded accounts.</div>
                </div>
            </div>

            <!-- ══ TAB: COMPETITIONS ══ -->
            <div class="dash-tab" id="tab-competitions">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><path d="M6 9H3.5a1.5 1.5 0 010-3H6"/><path d="M18 9h2.5a1.5 1.5 0 000-3H18"/><path d="M6 6h12v5a6 6 0 01-12 0V6z"/><path d="M12 17v4"/><path d="M8 21h8"/></svg>
                    <div class="dash-cs-title">COMPETITIONS</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Monthly trading competitions with real cash prizes. Compete against traders worldwide and climb the global ranking.</div>
                </div>
            </div>

            <!-- ══ TAB: LEADERBOARD ══ -->
            <div class="dash-tab" id="tab-leaderboard">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><rect x="2" y="14" width="6" height="8" rx="1"/><rect x="9" y="9" width="6" height="13" rx="1"/><rect x="16" y="11" width="6" height="11" rx="1"/></svg>
                    <div class="dash-cs-title">LEADERBOARD</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Global trader rankings by profit, consistency score, and payout volume. Updated in real time.</div>
                </div>
            </div>

            <!-- ══ TAB: CERTIFICATES ══ -->
            <div class="dash-tab" id="tab-certificates">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    <div class="dash-cs-title">CERTIFICATES</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Download your official funded trader certificates, challenge completion diplomas, and milestone badges.</div>
                </div>
            </div>

            <!-- ══ TAB: CALENDAR ══ -->
            <div class="dash-tab" id="tab-calendar">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <div class="dash-cs-title">CALENDAR</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Economic events, payout schedule, competition dates, and your personal trading milestones — all in one view.</div>
                </div>
            </div>

            <!-- ══ TAB: AFFILIATE ══ -->
            <div class="dash-tab" id="tab-affiliate">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <div class="dash-cs-title">AFFILIATE</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Your referral link, commission tracking, conversion stats, and payout requests — all from your affiliate dashboard.</div>
                </div>
            </div>

            <!-- ══ TAB: TESTIMONIALS ══ -->
            <div class="dash-tab" id="tab-testimonials">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <div class="dash-cs-title">TESTIMONIALS</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Share your trading journey and read success stories from the Doji Funding community. Your review matters.</div>
                </div>
            </div>

            <!-- ══ TAB: SUPPORT ══ -->
            <div class="dash-tab" id="tab-support">
                <div class="dash-coming-soon">
                    <svg class="dash-cs-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="40" height="40" opacity=".2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <div class="dash-cs-title">SUPPORT</div>
                    <div class="dash-cs-status">[ COMING SOON ]</div>
                    <div class="dash-cs-desc">Live chat, ticket system, and FAQ — get help from the Doji Funding team directly from your dashboard.</div>
                </div>
            </div>

        </main>
    </div>

    <!-- Mobile tab bar -->
    <div class="dash-mobile-tabs">
        <button class="dash-mobile-tab active" data-tab="overview">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span>Overview</span>
        </button>
        <button class="dash-mobile-tab" data-tab="challenges">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            <span>Challenges</span>
        </button>
        <button class="dash-mobile-tab" data-tab="payouts">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6"/></svg>
            <span>Payouts</span>
        </button>
        <button class="dash-mobile-tab" data-tab="settings">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profile</span>
        </button>
    </div>

</div>
