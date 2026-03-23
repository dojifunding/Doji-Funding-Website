<?php
/**
 * Doji Funding — Navigation Bar
 * 
 * - Challenges dropdown (Instant Funding, 1 Step, 2 Step, Competitions)
 * - Language selector
 * - Dashboard / Start Challenge buttons
 * - Mobile hamburger menu
 */

$currentPage = $currentPage ?? 'home';
$loggedIn = isLoggedIn();
$user = getCurrentUser();

$languages = [
    'en' => ['flag' => '🇬🇧', 'label' => 'English'],
    'fr' => ['flag' => '🇫🇷', 'label' => 'Français'],
    'es' => ['flag' => '🇪🇸', 'label' => 'Español'],
    'de' => ['flag' => '🇩🇪', 'label' => 'Deutsch'],
    'it' => ['flag' => '🇮🇹', 'label' => 'Italiano'],
    'pt' => ['flag' => '🇧🇷', 'label' => 'Português'],
    'hi' => ['flag' => '🇮🇳', 'label' => 'हिन्दी'],
    'ar' => ['flag' => '🇸🇦', 'label' => 'العربية'],
    'zh' => ['flag' => '🇨🇳', 'label' => '中文'],
];
$currentLang = $_GET['lang'] ?? 'en';
if (!isset($languages[$currentLang])) $currentLang = 'en';
?>

<!-- Promo Top Banner -->
<div class="promo-banner" id="promoBanner">
    <div class="promo-banner-content">
        <span class="promo-banner-text">
            <span class="promo-badge">LAUNCH OFFER</span> Use code <strong class="promo-banner-code" onclick="copyPromoCode('WELCOME')">WELCOME</strong> and get <strong>20% OFF</strong> your first challenge and <strong>50% OFF</strong> for your first retry!
        </span>
        <button class="promo-banner-close" onclick="closePromoBanner()" aria-label="Close">&times;</button>
    </div>
</div>

<nav class="nav">
    <div class="nav-inner">
    <div class="nav-left">
        <a class="nav-logo" href="index.php">
            <img class="logo-icon" src="<?= LOGO_FILE ?>" alt="<?= SITE_NAME ?> logo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="logo-icon-fallback" style="display:none">D</div>
            <span class="logo-text">DOJI <span class="green">FUNDING</span><sup class="tm">®</sup></span>
        </a>
    </div>
    <div class="nav-center" id="navLinks">
            <a style="text-decoration:none" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">Home</a>

            <!-- Challenges Dropdown -->
            <div class="nav-dropdown-wrap" id="challengesDropWrap">
                <button class="nav-link nav-dropdown-trigger <?= in_array($currentPage, ['challenges','competitions']) ? 'active' : '' ?>" onclick="toggleNavDrop('challengesDrop')">
                    Challenges <svg class="nav-drop-arrow" viewBox="0 0 10 6"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                </button>
                <div class="nav-dropdown" id="challengesDrop">
                    <a style="text-decoration:none" class="nav-dropdown-item disabled" href="#">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M10 2l1.5 5H17l-4 3.5 1.5 5L10 12l-4.5 3.5 1.5-5L3 7h5.5z" fill="rgba(255,159,26,0.15)" stroke="#ff9f1a" stroke-width="1.2" stroke-linejoin="round"/><path d="M10 7v6M7 10h6" stroke="#ff9f1a" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">Instant Funding</span>
                            <span class="nav-dropdown-sub">Coming Soon</span>
                        </span>
                    </a>
                    <a style="text-decoration:none" class="nav-dropdown-item" href="challenges.php?t=1">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><circle cx="10" cy="10" r="8" stroke="#10B981" stroke-width="1.2"/><circle cx="10" cy="10" r="5" stroke="#10B981" stroke-width="0.8" opacity="0.4"/><circle cx="10" cy="10" r="2" fill="#10B981"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">1 Step Challenge</span>
                            <span class="nav-dropdown-sub">Fast Track</span>
                        </span>
                    </a>
                    <a style="text-decoration:none" class="nav-dropdown-item" href="challenges.php?t=2">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="3" y="11" width="4" height="6" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="1"/><rect x="8" y="7" width="4" height="10" rx="1" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="1"/><rect x="13" y="3" width="4" height="14" rx="1" fill="rgba(16,185,129,0.25)" stroke="#10B981" stroke-width="1"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">2 Step Challenge</span>
                            <span class="nav-dropdown-sub">Classic</span>
                        </span>
                    </a>
                    <div class="nav-dropdown-sep"></div>
                    <a style="text-decoration:none" class="nav-dropdown-item" href="competitions.php">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M6 4h8v4c0 3-1.8 5-4 5.5C7.8 13 6 11 6 8V4z" fill="rgba(16,185,129,0.1)" stroke="#10B981" stroke-width="1.2" stroke-linejoin="round"/><path d="M6 6c-1.5 0-2.5 1-2.5 2.5S5 11 6 11M14 6c1.5 0 2.5 1 2.5 2.5S15 11 14 11" stroke="#10B981" stroke-width="0.8" fill="none" opacity="0.5"/><rect x="7" y="15" width="6" height="2" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="0.8"/><path d="M9 13.5v1.5M11 13.5v1.5" stroke="#10B981" stroke-width="0.8"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">Competitions</span>
                            <span class="nav-dropdown-sub">Free & Paid</span>
                        </span>
                    </a>
                </div>
            </div>

            <a style="text-decoration:none" class="nav-link <?= $currentPage === 'affiliates' ? 'active' : '' ?>" href="affiliates.php">Affiliate</a>

            <!-- Trading Dropdown -->
            <div class="nav-dropdown-wrap" id="tradingDropWrap">
                <button class="nav-link nav-dropdown-trigger <?= in_array($currentPage, ['platforms','symbols']) ? 'active' : '' ?>" onclick="toggleNavDrop('tradingDrop')">
                    Trading <svg class="nav-drop-arrow" viewBox="0 0 10 6"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                </button>
                <div class="nav-dropdown" id="tradingDrop">
                    <a style="text-decoration:none" class="nav-dropdown-item" href="platforms.php">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="3" width="16" height="11" rx="2" stroke="#10B981" stroke-width="1.2"/><path d="M7 17h6M10 14v3" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M6 7h3M6 9h5" stroke="#10B981" stroke-width="0.8" stroke-linecap="round" opacity="0.4"/><circle cx="14" cy="8" r="2" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="0.8"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">Platforms</span>
                            <span class="nav-dropdown-sub">MT5 & cTrader</span>
                        </span>
                    </a>
                    <a style="text-decoration:none" class="nav-dropdown-item" href="symbols.php">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M3 16V4" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M3 16h14" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M6 12V8M9 13V6M12 11V9M15 10V5" stroke="#10B981" stroke-width="2" stroke-linecap="round" opacity="0.6"/><polyline points="6,8 9,6 12,9 15,5" stroke="#10B981" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">Symbols</span>
                            <span class="nav-dropdown-sub">150+ Instruments</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- About Dropdown -->
            <div class="nav-dropdown-wrap" id="aboutDropWrap">
                <button class="nav-link nav-dropdown-trigger <?= in_array($currentPage, ['about','contact']) ? 'active' : '' ?>" onclick="toggleNavDrop('aboutDrop')">
                    About <svg class="nav-drop-arrow" viewBox="0 0 10 6"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                </button>
                <div class="nav-dropdown" id="aboutDrop">
                    <a style="text-decoration:none" class="nav-dropdown-item" href="about.php">
                        <span class="nav-dropdown-icon"><img src="<?= LOGO_FILE ?>" alt="Doji" style="width:18px;height:18px;border-radius:4px;object-fit:contain"></span>
                        <span>
                            <span class="nav-dropdown-label">About Us</span>
                            <span class="nav-dropdown-sub">Our Mission</span>
                        </span>
                    </a>
                    <a style="text-decoration:none" class="nav-dropdown-item" href="contact.php">
                        <span class="nav-dropdown-icon"><svg viewBox="0 0 20 20" fill="none" width="18" height="18"><rect x="2" y="4" width="16" height="12" rx="2" stroke="#10B981" stroke-width="1.2"/><path d="M2 6l8 5 8-5" stroke="#10B981" stroke-width="1.2" stroke-linejoin="round"/></svg></span>
                        <span>
                            <span class="nav-dropdown-label">Contact</span>
                            <span class="nav-dropdown-sub">Get in Touch</span>
                        </span>
                    </a>
                </div>
            </div>

            <a style="text-decoration:none" class="nav-link <?= $currentPage === 'faq' ? 'active' : '' ?>" href="faq.php">FAQ</a>
        </div>
    <div class="nav-right">

        <?php if ($loggedIn): ?>
            <a class="nav-dashboard" href="dashboard.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><rect x="1" y="1" width="6" height="6" rx="1" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="1"/><rect x="9" y="1" width="6" height="3" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="1"/><rect x="1" y="9" width="6" height="6" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="1"/><rect x="9" y="6" width="6" height="9" rx="1" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="1"/></svg> Dashboard</a>
            <button class="nav-user-btn" onclick="AuthModal.toggleDropdown()">
                <span class="nav-avatar"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></span>
                <?= htmlspecialchars($user['first_name']) ?>
            </button>
        <?php else: ?>
            <button class="nav-dashboard" onclick="AuthModal.open('login')">Dashboard</button>
            <button class="btn-primary nav-cta" onclick="AuthModal.open('signup')">Challenge</button>
        <?php endif; ?>

        <!-- Language Selector -->
        <div class="lang-wrap" id="langWrap">
            <button class="lang-btn" onclick="toggleNavDrop('langDrop')" aria-label="Language">
                <span class="lang-flag"><?= $languages[$currentLang]['flag'] ?></span>
                <svg class="nav-drop-arrow" viewBox="0 0 10 6"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
            </button>
            <div class="nav-dropdown lang-dropdown" id="langDrop">
                <?php foreach ($languages as $code => $lang): ?>
                <a style="text-decoration:none" class="lang-option <?= $code === $currentLang ? 'active' : '' ?>" href="?lang=<?= $code ?>">
                    <span class="lang-flag"><?= $lang['flag'] ?></span>
                    <span><?= $lang['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Hamburger (mobile only) -->
        <button class="nav-hamburger" id="navHamburger" onclick="toggleMobileMenu()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    </div>
</nav>

<!-- Mobile menu overlay -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-links">
        <a style="text-decoration:none" class="mobile-menu-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">Home</a>

        <!-- Challenges sub-links -->
        <div class="mobile-menu-section">Challenges</div>
        <a style="text-decoration:none" class="mobile-menu-link sub disabled" href="#"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><path d="M8 1.5l1.2 4H14l-3.5 3 1.2 4L8 9.5l-3.7 3 1.2-4L2 5.5h4.8z" fill="rgba(255,159,26,0.2)" stroke="#ff9f1a" stroke-width="1" stroke-linejoin="round"/></svg> Instant Funding <span class="mobile-soon">Soon</span></a>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'challenges' ? 'active' : '' ?>" href="challenges.php?t=1"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><circle cx="8" cy="8" r="6" stroke="#10B981" stroke-width="1"/><circle cx="8" cy="8" r="3.5" stroke="#10B981" stroke-width="0.7" opacity="0.4"/><circle cx="8" cy="8" r="1.5" fill="#10B981"/></svg> 1 Step Challenge</a>
        <a style="text-decoration:none" class="mobile-menu-link sub" href="challenges.php?t=2"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><rect x="2" y="9" width="3" height="5" rx="0.8" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="0.8"/><rect x="6.5" y="6" width="3" height="8" rx="0.8" fill="rgba(16,185,129,0.25)" stroke="#10B981" stroke-width="0.8"/><rect x="11" y="3" width="3" height="11" rx="0.8" fill="rgba(16,185,129,0.3)" stroke="#10B981" stroke-width="0.8"/></svg> 2 Step Challenge</a>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'competitions' ? 'active' : '' ?>" href="competitions.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><path d="M5 3h6v3c0 2.5-1.5 4-3 4.5-1.5-.5-3-2-3-4.5V3z" fill="rgba(16,185,129,0.1)" stroke="#10B981" stroke-width="1" stroke-linejoin="round"/><rect x="5.5" y="12" width="5" height="1.5" rx="0.75" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="0.6"/></svg> Competitions</a>

        <!-- About sub-links -->
        <div class="mobile-menu-section">About</div>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'about' ? 'active' : '' ?>" href="about.php"><img src="<?= LOGO_FILE ?>" alt="Doji" style="width:16px;height:16px;border-radius:3px;object-fit:contain;vertical-align:-2px"> About Us</a>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'contact' ? 'active' : '' ?>" href="contact.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><rect x="1.5" y="3" width="13" height="10" rx="1.5" stroke="#10B981" stroke-width="1"/><path d="M1.5 5l6.5 4 6.5-4" stroke="#10B981" stroke-width="1" stroke-linejoin="round"/></svg> Contact</a>

        <!-- Trading sub-links -->
        <div class="mobile-menu-section">Trading</div>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'platforms' ? 'active' : '' ?>" href="platforms.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><rect x="1.5" y="2.5" width="13" height="9" rx="1.5" stroke="#10B981" stroke-width="1"/><path d="M6 14h4M8 11.5v2.5" stroke="#10B981" stroke-width="1" stroke-linecap="round"/></svg> Platforms</a>
        <a style="text-decoration:none" class="mobile-menu-link sub <?= $currentPage === 'symbols' ? 'active' : '' ?>" href="symbols.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><path d="M2 13V3" stroke="#10B981" stroke-width="1" stroke-linecap="round"/><path d="M2 13h12" stroke="#10B981" stroke-width="1" stroke-linecap="round"/><rect x="4" y="8" width="2" height="5" rx="0.5" fill="rgba(16,185,129,0.3)"/><rect x="7" y="5" width="2" height="8" rx="0.5" fill="rgba(16,185,129,0.3)"/><rect x="10" y="7" width="2" height="6" rx="0.5" fill="rgba(16,185,129,0.3)"/></svg> Symbols</a>

        <a style="text-decoration:none" class="mobile-menu-link <?= $currentPage === 'faq' ? 'active' : '' ?>" href="faq.php">FAQ</a>
        <a style="text-decoration:none" class="mobile-menu-link <?= $currentPage === 'affiliates' ? 'active' : '' ?>" href="affiliates.php">Affiliate</a>
    </div>

    <!-- Mobile Language -->
    <div class="mobile-menu-section">Language</div>
    <div class="mobile-lang-grid">
        <?php foreach ($languages as $code => $lang): ?>
        <a class="mobile-lang-item <?= $code === $currentLang ? 'active' : '' ?>" href="?lang=<?= $code ?>">
            <?= $lang['flag'] ?> <?= $lang['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="mobile-menu-actions">
        <?php if ($loggedIn): ?>
            <a class="mobile-menu-btn" href="dashboard.php"><svg viewBox="0 0 16 16" fill="none" width="14" height="14" style="vertical-align:-2px"><rect x="1" y="1" width="6" height="6" rx="1" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="1"/><rect x="9" y="1" width="6" height="3" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="1"/><rect x="1" y="9" width="6" height="6" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="1"/><rect x="9" y="6" width="6" height="9" rx="1" fill="rgba(16,185,129,0.2)" stroke="#10B981" stroke-width="1"/></svg> Dashboard</a>
        <?php else: ?>
            <button class="mobile-menu-btn" onclick="AuthModal.open('login');toggleMobileMenu()">Dashboard</button>
            <button class="mobile-menu-btn primary" onclick="AuthModal.open('signup');toggleMobileMenu()">Challenge</button>
        <?php endif; ?>
    </div>
</div>
