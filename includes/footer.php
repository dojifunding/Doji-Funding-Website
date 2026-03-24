<?php
/**
 * Doji Funding — Footer
 * 
 * Site footer with link columns, copyright, and SEO overlay container.
 * Also includes all JS script tags.
 */

$v = ASSET_VERSION;
?>

<!-- Disclaimer Banner (sticky bottom, dismissable) -->
<div class="disclaimer-banner" id="disclaimerBanner">
    <div class="disclaimer-content">
        <p class="disclaimer-text">
            All accounts provided by Doji Funding® operate in a simulated trading environment with virtual funds. 
            No real capital is at risk and all trading activity is conducted on demo accounts. 
            Performance-based rewards are derived from simulated results only. 
            For full details, please refer to our <a href="faq.php" class="disclaimer-link">FAQ</a> 
            and <a href="terms.php" class="disclaimer-link">Terms &amp; Conditions</a>.
        </p>
        <button class="disclaimer-btn" onclick="dismissDisclaimer()">I understand</button>
    </div>
</div>

<!-- Auth Modals (Login / Signup) -->
<?php require_once __DIR__ . '/modals.php'; ?>

<!-- SEO Overlay (populated by JS) -->
<div class="seo-overlay" id="seoOverlay"></div>

<!-- Footer -->
<footer>
    <div class="footer-grid">
        <div>
            <img class="footer-logo" src="<?= LOGO_FILE ?>" alt="<?= SITE_NAME ?> logo">
            <div class="footer-brand">DOJI <span class="green">FUNDING</span><sup class="tm">®</sup></div>
            <p class="footer-desc">
                The first fully customizable prop firm. Trade your way with transparent pricing based on real risk.
            </p>
            <div class="footer-socials">
                <a class="social-icon" href="https://x.com/DojiFunding" target="_blank" rel="noopener" aria-label="X (Twitter)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a class="social-icon" href="https://www.instagram.com/dojifunding/" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a class="social-icon" href="https://www.youtube.com/@DojiFunding" target="_blank" rel="noopener" aria-label="YouTube">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a class="social-icon" href="https://www.tiktok.com/@dojifunding" target="_blank" rel="noopener" aria-label="TikTok">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                </a>
                <a class="social-icon" href="https://www.linkedin.com/company/dojifunding" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <a class="social-icon discord" href="https://discord.gg/kNUqAqCppU" target="_blank" rel="noopener" aria-label="Discord">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <div class="footer-col-title">Challenges</div>
            <a style="text-decoration:none" class="footer-link" href="challenges.php">1 Step Challenge</a>
            <a style="text-decoration:none" class="footer-link" href="challenges.php">2 Step Challenge</a>
            <a style="text-decoration:none" class="footer-link" href="#">Instant Funding</a>
            <a style="text-decoration:none" class="footer-link" href="challenges.php">Pricing</a>
        </div>
        <div>
            <div class="footer-col-title">Resources</div>
            <a style="text-decoration:none" class="footer-link" href="faq.php">FAQ</a>
            <a style="text-decoration:none" class="footer-link" href="rules.php">Trading Rules</a>
            <a style="text-decoration:none" class="footer-link" href="symbols.php">Symbols List</a>
        </div>
        <div>
            <div class="footer-col-title">Company</div>
            <a style="text-decoration:none" class="footer-link" href="about.php">About Us</a>
            <a style="text-decoration:none" class="footer-link" href="contact.php">Contact</a>
            <a style="text-decoration:none" class="footer-link" href="affiliates.php">Affiliate Program</a>
            <a style="text-decoration:none" class="footer-link" href="terms.php">Terms &amp; Conditions</a>
            <a style="text-decoration:none" class="footer-link" href="privacy.php">Privacy Policy</a>
            <a style="text-decoration:none" class="footer-link" href="refund.php">Refund Policy</a>
        </div>
    </div>

    <!-- Legal Disclaimer -->
    <div class="footer-disclaimer">
        <div class="footer-disclaimer-title">Important Information &amp; Disclaimer</div>

        <div class="footer-disclaimer-section">
            <strong>Corporate Entity</strong>
            Doji Funding® is a trading name and brand operated by Volatys Dynamics LTD. All references to "Doji Funding", "the Company", "we", "us", or "our" on this website refer to Volatys Dynamics LTD and its subsidiaries. Volatys Dynamics LTD is responsible for the operation, management, and administration of all programs and services offered under the Doji Funding® brand.
            <br><br>
            <strong>Registered Address:</strong> Suite 4.3.02, Block 4, Eurotowers, Gibraltar GX11 1AA, Gibraltar.<br>
            <strong>Incorporation No.:</strong> 125095 &nbsp;|&nbsp; <strong>REID Number:</strong> GICO.125095-94
        </div>

        <div class="footer-disclaimer-section">
            <strong>Simulated Trading Environment</strong>
            All accounts provided by Doji Funding® are demo accounts operating exclusively in a simulated trading environment. No actual trades are executed on live financial markets. The services we offer are designed for educational and evaluation purposes only. Past simulated performance is not necessarily indicative of future results.
        </div>

        <button class="disclaimer-toggle" id="disclaimerToggle" onclick="toggleDisclaimerMore()">
            Read more <svg viewBox="0 0 10 6" width="10" height="6" style="vertical-align:middle;margin-left:4px"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
        </button>

        <div class="disclaimer-more" id="disclaimerMore" style="display:none">

        <div class="footer-disclaimer-section">
            <strong>No Investment Services</strong>
            Doji Funding® does not provide investment advice, does not solicit or recommend the purchase or sale of any financial instruments, securities, or funds, and does not act as a broker, custodian, or financial intermediary. All content published by Doji Funding® and its related entities is for general information only.
        </div>

        <div class="footer-disclaimer-section">
            <strong>Program Fees</strong>
            Participation in any of our programs is entirely voluntary. All fees paid are strictly service fees for access to simulated trading evaluations and related services. Program fees are not deposits, do not represent client funds, and should not be considered investments under any circumstances. These fees do not earn interest, returns, or profit sharing of any kind. All program fees are applied toward operational and administrative expenses, including technology infrastructure, platform development, risk management systems, customer support, and other business-related costs. Payment of program fees does not create any fiduciary duty, custodial relationship, or investment arrangement between participants and Volatys Dynamics LTD.
        </div>

        <div class="footer-disclaimer-section">
            <strong>Performance-Based Rewards</strong>
            Profit splits and payouts referenced throughout this website are performance-based rewards derived entirely from simulated trading results. They do not constitute returns on investment, dividends, or financial gains from real market activity. Nothing on this website constitutes an offer to buy or sell futures, options, CFDs, forex, stocks, or any other financial instruments.
        </div>

        <div class="footer-disclaimer-section">
            <strong>Our Mission</strong>
            Doji Funding® is committed to identifying and developing trading talent through structured evaluation programs and professional-grade simulated trading infrastructure. Our focus is on skill-building, discipline, and consistent performance — not speculation. By providing traders with realistic market conditions and clear performance benchmarks, we aim to discover and nurture real talent capable of managing live capital with confidence and responsibility.
        </div>

        <div class="footer-disclaimer-section">
            <strong>General Risk Warning</strong>
            Trading financial markets involves substantial risk of loss. Even in a simulated environment, strategies tested under leveraged conditions may produce outcomes that do not reflect real-world execution. You should carefully consider your objectives, level of experience, and risk tolerance before participating. The information on this site is not directed at residents in any country or jurisdiction where such distribution or use would be contrary to local laws or regulations.
        </div>

        <div class="footer-disclaimer-section">
            <strong>Intellectual Property</strong>
            Doji Funding® is a registered trademark of Volatys Dynamics LTD. All logos, branding, platform designs, and proprietary systems are the exclusive property of Volatys Dynamics LTD and its affiliates. Unauthorized reproduction or distribution is strictly prohibited.
        </div>

        <div class="footer-disclaimer-section">
            <strong>Doji Coins™</strong>
            Doji Coins™ are Doji Funding's proprietary loyalty rewards and have no cash value. They may be earned through competitions, achievements, and platform activity. Doji Coins™ can be redeemed for discounts on challenges and other platform benefits as specified by Doji Funding. Doji Coins™ do not constitute currency, securities, or any form of financial instrument. They cannot be transferred, sold, or exchanged for cash. Terms and conditions for Doji Coins™ usage may change at any time at the sole discretion of Volatys Dynamics LTD.
        </div>

        </div>
    </div>

    <!-- Payment Methods -->
    <div class="footer-payments">
        <div class="footer-payments-label">Accepted Payment Methods</div>
        <div class="footer-payments-grid">
            <!-- Cards -->
            <a class="payment-icon" href="faq.php#billing" title="Visa"><img src="assets/img/payments/visa.svg" alt="Visa" height="28"></a>
            <a class="payment-icon" href="faq.php#billing" title="Mastercard"><img src="assets/img/payments/mastercard.svg" alt="Mastercard" height="28"></a>
            <a class="payment-icon" href="faq.php#billing" title="Apple Pay"><img src="assets/img/payments/apple.svg" alt="Apple Pay" height="28"></a>
            <a class="payment-icon" href="faq.php#billing" title="Google Pay"><img src="assets/img/payments/google-pay.svg" alt="Google Pay" height="28"></a>
            <a class="payment-icon" href="faq.php#billing" title="Alipay"><img src="assets/img/payments/alipay.svg" alt="Alipay" height="28"></a>
            <a class="payment-icon" href="faq.php#billing" title="American Express"><img src="assets/img/payments/amex.svg" alt="American Express" height="28"></a>
            <span class="payment-more">+ more</span>
            <!-- Divider -->
            <div class="payment-divider"></div>
            <!-- Crypto -->
            <a class="payment-icon" href="faq.php#billing" title="Bitcoin (BTC)"><img src="assets/img/payments/bitcoin-btc-logo.svg" alt="Bitcoin" height="24"></a>
            <a class="payment-icon" href="faq.php#billing" title="Ethereum (ETH)"><img src="assets/img/payments/ethereum-eth-logo.svg" alt="Ethereum" height="24"></a>
            <a class="payment-icon" href="faq.php#billing" title="Tether (USDT)"><img src="assets/img/payments/tether-usdt-logo.svg" alt="Tether USDT" height="24"></a>
            <a class="payment-icon" href="faq.php#billing" title="USD Coin (USDC)"><img src="assets/img/payments/usd-coin-usdc-logo.svg" alt="USDC" height="24"></a>
            <a class="payment-icon" href="faq.php#billing" title="Litecoin (LTC)"><img src="assets/img/payments/litecoin-ltc-logo.svg" alt="Litecoin" height="24"></a>
            <a class="payment-icon" href="faq.php#billing" title="Solana (SOL)"><img src="assets/img/payments/solana-sol-logo.svg" alt="Solana" height="24"></a>
            <span class="payment-more">+ more</span>
        </div>
    </div>

    <div class="footer-bottom">
        <span class="footer-copy">&copy; <?= SITE_YEAR ?> <?= SITE_NAME ?>. A brand of Volatys Dynamics LTD. All rights reserved.</span>
        <span class="footer-seo mono" id="footerSeo"></span>
    </div>
</footer>

<!-- JavaScript -->
<script>
    // Inject server-side data for JS modules
    window.DOJI_CONFIG = {
        currentPage: '<?= $currentPage ?>',
        isLoggedIn: <?= isLoggedIn() ? 'true' : 'false' ?>,
        pricing: <?= getPricingJson() ?>,
        faq: <?= getFaqJson() ?>,
        seo: <?= getSeoJson() ?>,
        <?php if ($currentPage === 'challenges' && function_exists('getPresetsJson')): ?>
        presets: <?= getPresetsJson() ?>,
        <?php endif; ?>
    };
</script>
<script src="assets/js/app.js?v=<?= $v ?>"></script>
<script src="assets/js/auth.js?v=<?= $v ?>"></script>
<?php if ($currentPage === 'challenges' || $currentPage === 'home'): ?>
<script src="assets/js/configurator.js?v=<?= $v ?>"></script>
<?php endif; ?>
<?php if ($currentPage === 'home'): ?>
<script src="assets/js/globe.js?v=<?= $v ?>"></script>
<?php endif; ?>
<?php if ($currentPage === 'affiliates'): ?>
<script src="assets/js/circuit.js?v=<?= $v ?>"></script>
<?php endif; ?>
<?php if ($currentPage === 'challenges' || $currentPage === 'competitions'): ?>
<script src="assets/js/wave.js?v=<?= $v ?>"></script>
<?php endif; ?>
<?php if ($currentPage === 'faq'): ?>
<script src="assets/js/faq.js?v=<?= $v ?>"></script>
<?php endif; ?>
<?php if ($currentPage === 'dashboard'): ?>
<script src="assets/js/dashboard.js?v=<?= $v ?>"></script>
<?php endif; ?>

<!-- Visual Effects Engine -->
<script src="assets/js/effects.js?v=<?= $v ?>"></script>

<!-- Polish & UX Enhancements -->
<script src="assets/js/polish.js?v=<?= $v ?>"></script>

<!-- ASCII Scramble / Decryption Effect -->
<script src="assets/js/scramble.js?v=<?= $v ?>"></script>

<!-- Interactive Pixel Particle Footer -->
<script src="assets/js/particle-footer.js?v=<?= $v ?>"></script>

</body>
</html>
