<?php
/**
 * Doji Funding — Header Include
 * 
 * HTML <head>, meta tags, CSS imports, and opening <body>.
 * Expects $currentPage to be set before inclusion.
 */

$currentPage = $currentPage ?? 'home';
require_once __DIR__ . '/icons.php';
$seo = getSeoData($currentPage);
$v = ASSET_VERSION;

// ── Security Headers ────────────────────────────────────
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-ancestors 'none';");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Theme: apply before CSS to avoid flash of wrong theme -->
    <script>(function(){var t=localStorage.getItem('doji-theme')||'dark';if(t==='system'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-theme',t);})();</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta -->
    <title><?= htmlspecialchars($seo['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo['desc']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seo['keywords']) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($seo['canonical']) ?>">
    
    <!-- Open Graph -->
    <meta property="og:type" content="<?= $seo['ogType'] ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seo['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo['desc']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($seo['canonical']) ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/img/hero-poster.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo['desc']) ?>">
    <meta name="twitter:image" content="<?= SITE_URL ?>/assets/img/hero-poster.jpg">
    
    <!-- Schema JSON-LD -->
    <script type="application/ld+json"><?= json_encode($seo['schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chivo+Mono:ital,wght@0,100..900;1,100..900&family=Inter:wght@400;500;600;700;800&family=Doto:wght@100..900&display=swap" rel="stylesheet">
    <link rel="preload" href="assets/fonts/Nippo-Variable.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- Stylesheets — global.css bundles main + effects + polish (3 → 1 request) -->
    <link rel="stylesheet" href="assets/css/global.css?v=<?= $v ?>">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= $v ?>">
    <?php if ($currentPage === 'challenges' || $currentPage === 'home' || $currentPage === 'dashboard'): ?>
    <link rel="stylesheet" href="assets/css/configurator.css?v=<?= $v ?>">
    <?php endif; ?>
    <?php if ($currentPage === 'faq'): ?>
    <link rel="stylesheet" href="assets/css/faq.css?v=<?= $v ?>">
    <?php endif; ?>
    <?php if ($currentPage === 'dashboard'): ?>
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?= $v ?>">
    <?php endif; ?>

    <!-- Hero canvas positioning -->
    <?php if (in_array($currentPage, ['home','challenges','affiliates','competitions'])): ?>
    <style>
    .hero-shape-canvas{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;opacity:.9;}
    </style>
    <?php endif; ?>

    <!-- Nothing OS Design System — loaded last, overrides everything -->
    <?php if ($currentPage !== 'dashboard'): ?>
    <link rel="stylesheet" href="assets/css/nothing-site.css?v=<?= $v ?>">
    <?php endif; ?>

    <!-- Animated Icons (CDN Web Components) -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <?= iconScripts() ?>
    <style>
    .skip-link {
        position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden;
        font-family: 'Chivo Mono', monospace; font-size: 12px; font-weight: 700;
        background: #10B981; color: #000; padding: 8px 16px; z-index: 99999;
        text-decoration: none; border-radius: 0 0 2px 2px;
    }
    .skip-link:focus { position: fixed; left: 50%; transform: translateX(-50%); top: 0; width: auto; height: auto; overflow: visible; }
    </style>
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
