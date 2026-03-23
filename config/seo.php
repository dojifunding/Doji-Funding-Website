<?php
/**
 * Doji Funding — SEO Metadata
 * 
 * Per-page SEO data: title, meta description, schema markup, etc.
 * Used by header.php for meta tags and by JS for SEO overlay.
 */

$seoMeta = [
    'home' => [
        'title'     => 'Doji Funding® | #1 Customizable Prop Firm — Trade Your Way',
        'desc'      => 'Get funded up to $100K with fully customizable trading challenges. Choose your profit target, drawdown, and payout split. MetaTrader 5 & cTrader. Start from $39.',
        'h1'        => 'Trade Your Way. Get Funded.',
        'canonical'  => SITE_URL . '/',
        'ogType'    => 'website',
        'keywords'  => 'prop firm, funded trading, trading challenge, prop trading, forex funding, customizable prop firm',
        'schema'    => [
            '@context'    => 'https://schema.org',
            '@type'       => 'FinancialService',
            'name'        => SITE_NAME,
            'description' => 'Customizable proprietary trading firm',
            'url'         => SITE_URL,
            'areaServed'  => 'Worldwide',
            'serviceType' => 'Proprietary Trading Evaluation',
        ],
    ],
    'challenges' => [
        'title'     => 'Trading Challenges & Pricing | Doji Funding® — From $39',
        'desc'      => 'Compare 1-Step and 2-Step trading challenges. Customize profit targets (5-15%), drawdown limits, profit split up to 90%. Accounts $5K-$100K. Transparent pricing.',
        'h1'        => 'Choose Your Challenge',
        'canonical'  => SITE_URL . '/challenges/',
        'ogType'    => 'product',
        'keywords'  => 'trading challenge pricing, 1 step challenge, 2 step challenge, prop firm evaluation, funded account',
        'schema'    => [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => 'Doji Funding® Trading Challenge',
            'description' => 'Customizable trading evaluation',
            'brand'    => ['@type' => 'Brand', 'name' => SITE_NAME],
            'offers'   => [
                '@type'         => 'AggregateOffer',
                'lowPrice'      => '39',
                'highPrice'     => '499',
                'priceCurrency' => 'USD',
            ],
        ],
    ],
    'faq' => [
        'title'     => 'FAQ — Doji Funding® | Rules, Payouts, Trading Guidelines',
        'desc'      => 'Find answers about Doji Funding® challenges, trading rules, payouts, profit splits, platforms, and account management. Updated ' . SITE_YEAR . '.',
        'h1'        => 'Frequently Asked Questions',
        'canonical'  => SITE_URL . '/faq/',
        'ogType'    => 'website',
        'keywords'  => 'prop firm FAQ, trading rules, payout frequency, drawdown rules, funded account FAQ',
        'schema'    => [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => [], // Populated dynamically from FAQ data
        ],
    ],
    'about' => [
        'title'     => 'About Doji Funding® | Our Mission & Vision',
        'desc'      => 'Learn about Doji Funding®, the first fully customizable proprietary trading firm. Built by traders, for traders. Transparent pricing, flexible challenges.',
        'h1'        => 'About Doji Funding®',
        'canonical'  => SITE_URL . '/about/',
        'ogType'    => 'website',
        'keywords'  => 'about doji funding, prop firm company, prop trading firm, funded trading company',
        'schema'    => [
            '@context'    => 'https://schema.org',
            '@type'       => 'Organization',
            'name'        => SITE_NAME,
            'description' => 'First fully customizable proprietary trading firm',
            'url'         => SITE_URL,
        ],
    ],
    'competitions' => [
        'title'     => 'Trading Competitions | Doji Funding® — Free & Paid',
        'desc'      => 'Compete against traders worldwide in free and paid trading competitions. Win cash prizes and funded accounts with Doji Funding®.',
        'h1'        => 'Trading Competitions',
        'canonical'  => SITE_URL . '/competitions/',
        'ogType'    => 'website',
        'keywords'  => 'trading competition, prop firm competition, free trading contest, funded account competition',
        'schema'    => [
            '@context'    => 'https://schema.org',
            '@type'       => 'Event',
            'name'        => 'Doji Funding® Trading Competitions',
            'description' => 'Free and paid trading competitions with cash prizes',
            'organizer'   => ['@type' => 'Organization', 'name' => SITE_NAME],
        ],
    ],
    'platforms' => [
        'title'     => 'Trading Platforms | Doji Funding® — MetaTrader 5 & cTrader',
        'desc'      => 'Trade on MetaTrader 5 and cTrader with Doji Funding®. Advanced charting, fast execution, and full EA support. Available on desktop, web, and mobile.',
        'h1'        => 'Trading Platforms',
        'canonical'  => SITE_URL . '/platforms',
        'ogType'    => 'website',
        'keywords'  => 'MetaTrader 5, cTrader, trading platform, prop firm platform, MT5 prop firm, forex platform',
        'schema'    => [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => 'Trading Platforms — Doji Funding®',
            'description' => 'MetaTrader 5 and cTrader trading platforms',
        ],
    ],
    'symbols' => [
        'title'     => 'Trading Instruments & Symbols | Doji Funding® — 150+ Markets',
        'desc'      => 'Trade 150+ instruments including Forex, Indices, Commodities, Crypto, and Stocks with Doji Funding®. Competitive spreads and deep liquidity.',
        'h1'        => 'Trading Instruments',
        'canonical'  => SITE_URL . '/symbols',
        'ogType'    => 'website',
        'keywords'  => 'trading instruments, forex pairs, indices trading, commodities, crypto trading, prop firm symbols',
        'schema'    => [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            'name'        => 'Trading Instruments — Doji Funding®',
            'description' => '150+ tradeable instruments across multiple asset classes',
        ],
    ],
    'rules' => [
        'title'     => 'Trading Rules | Doji Funding® — Clear & Transparent',
        'desc'      => 'Complete trading rules for all Doji Funding® challenges. Drawdown calculation, consistency rules, news trading, EAs, prohibited strategies. No hidden conditions.',
        'h1'        => 'Trading Rules',
        'canonical'  => SITE_URL . '/rules',
        'ogType'    => 'website',
        'keywords'  => 'prop firm rules, trading rules, drawdown rules, consistency rule, prop firm conditions',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Trading Rules — Doji Funding®'],
    ],
    'scaling' => [
        'title'     => 'Scaling Plan | Doji Funding® — Grow Up to 10×',
        'desc'      => 'Scale your funded account up to 10× with Doji Funding®. Consistent performance unlocks higher capital. Detailed scaling tiers and trader levels.',
        'h1'        => 'Scaling Plan',
        'canonical'  => SITE_URL . '/scaling',
        'ogType'    => 'website',
        'keywords'  => 'prop firm scaling, account scaling, funded account growth, prop firm levels',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Scaling Plan — Doji Funding®'],
    ],
    'terms' => [
        'title'     => 'Terms of Service | Doji Funding® by Volatys Dynamics LTD',
        'desc'      => 'Terms of Service for Doji Funding® evaluation programs. Read our complete terms covering eligibility, fees, payouts, and trading conduct.',
        'h1'        => 'Terms of Service',
        'canonical'  => SITE_URL . '/terms',
        'ogType'    => 'website',
        'keywords'  => 'terms of service, prop firm terms, trading terms',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Terms of Service — Doji Funding®'],
    ],
    'privacy' => [
        'title'     => 'Privacy Policy | Doji Funding® by Volatys Dynamics LTD',
        'desc'      => 'Privacy Policy for Doji Funding®. Learn how we collect, use, and protect your personal data. GDPR compliant.',
        'h1'        => 'Privacy Policy',
        'canonical'  => SITE_URL . '/privacy',
        'ogType'    => 'website',
        'keywords'  => 'privacy policy, data protection, prop firm privacy',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Privacy Policy — Doji Funding®'],
    ],
    'refund' => [
        'title'     => 'Refund Policy | Doji Funding® — Fair & Transparent',
        'desc'      => 'Refund Policy for Doji Funding® challenges. Fee refund on successful completion, 14-day cooling-off period, and clear refund conditions.',
        'h1'        => 'Refund Policy',
        'canonical'  => SITE_URL . '/refund',
        'ogType'    => 'website',
        'keywords'  => 'refund policy, prop firm refund, challenge refund',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Refund Policy — Doji Funding®'],
    ],
    'affiliates' => [
        'title'     => 'Affiliate Program | Doji Funding® — Earn 15-25% Commission',
        'desc'      => 'Join the Doji Funding® affiliate program. Earn 15-25% commission on every referral. Weekly payouts, real-time tracking, and branded materials.',
        'h1'        => 'Affiliate Program',
        'canonical'  => SITE_URL . '/affiliates',
        'ogType'    => 'website',
        'keywords'  => 'prop firm affiliate, trading affiliate program, forex affiliate, referral program',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Affiliate Program — Doji Funding®'],
    ],
    'contact' => [
        'title'     => 'Contact Us | Doji Funding® — Support & Help',
        'desc'      => 'Contact Doji Funding® support team. Email, contact form, and social media channels. Response within 24 hours.',
        'h1'        => 'Contact Us',
        'canonical'  => SITE_URL . '/contact',
        'ogType'    => 'website',
        'keywords'  => 'contact prop firm, trading support, customer service',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Contact — Doji Funding®'],
    ],
    'dashboard' => [
        'title'     => 'Dashboard | Doji Funding® — Manage Your Challenges',
        'desc'      => 'View your active challenges, trading stats, payouts, and account settings. Your personal Doji Funding dashboard.',
        'h1'        => 'Dashboard',
        'canonical'  => SITE_URL . '/dashboard',
        'ogType'    => 'website',
        'keywords'  => 'trading dashboard, prop firm dashboard, challenge overview',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => 'Dashboard — Doji Funding®'],
    ],
    '404' => [
        'title'     => 'Page Not Found | Doji Funding®',
        'desc'      => 'The page you requested could not be found. Browse our trading challenges or return to the homepage.',
        'h1'        => 'Page Not Found',
        'canonical'  => SITE_URL . '/404',
        'ogType'    => 'website',
        'keywords'  => '404, page not found, doji funding',
        'schema'    => ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => '404 — Page Not Found — Doji Funding®'],
    ],
];

/**
 * Get current page SEO data
 */
function getSeoData($page = 'home') {
    global $seoMeta;
    return $seoMeta[$page] ?? $seoMeta['home'];
}

/**
 * Export all SEO data as JSON for JS overlay
 */
function getSeoJson() {
    global $seoMeta;
    $output = [];
    foreach ($seoMeta as $page => $data) {
        $output[$page] = [
            'title'     => $data['title'],
            'desc'      => $data['desc'],
            'h1'        => $data['h1'],
            'canonical'  => $data['canonical'],
            'ogType'    => $data['ogType'],
            'keywords'  => $data['keywords'],
            'schema'    => json_encode($data['schema'], JSON_PRETTY_PRINT),
        ];
    }
    return json_encode($output);
}
