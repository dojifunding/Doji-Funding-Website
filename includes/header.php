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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seo['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seo['desc']) ?>">
    
    <!-- Schema JSON-LD -->
    <script type="application/ld+json"><?= json_encode($seo['schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chivo+Mono:ital,wght@0,100..900;1,100..900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="assets/fonts/Nippo-Variable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/Array-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/Array-BoldWide.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/main.css?v=<?= $v ?>">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= $v ?>">
    <?php if ($currentPage === 'challenges' || $currentPage === 'home'): ?>
    <link rel="stylesheet" href="assets/css/configurator.css?v=<?= $v ?>">
    <?php endif; ?>
    <?php if ($currentPage === 'faq'): ?>
    <link rel="stylesheet" href="assets/css/faq.css?v=<?= $v ?>">
    <?php endif; ?>
    <?php if ($currentPage === 'dashboard'): ?>
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?= $v ?>">
    <?php endif; ?>
    
    <!-- Visual Effects Layer -->
    <link rel="stylesheet" href="assets/css/effects.css?v=<?= $v ?>">

    <!-- Polish & UX Enhancements -->
    <link rel="stylesheet" href="assets/css/polish.css?v=<?= $v ?>">

    <!-- Animated Icons (CDN Web Components) -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <?= iconScripts() ?>
</head>
<body>
