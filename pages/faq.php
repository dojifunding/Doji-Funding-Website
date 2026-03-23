<?php
/**
 * Doji Funding — FAQ Page
 * 
 * Categorized FAQ with accordion.
 * Data comes from config/faq.php.
 * Interactivity in assets/js/faq.js.
 */

global $faqCategories;
?>

<!-- BREADCRUMB -->
<section style="padding:48px 32px 80px;background:var(--bg)">
    <div class="faq-container">

        <h1 class="page-title">
            Frequently Asked <span class="green">Questions</span>
            <span class="seo-tag">H1</span>
        </h1>
        <p class="page-subtitle">
            Everything you need to know about Doji Funding® challenges, rules, and payouts
        </p>

        <div class="seo-only" style="margin-bottom:24px;padding:12px;background:var(--green-dim);border-radius:8px;border:1px solid rgba(16,185,129,0.15);font-size:11px;color:var(--text2);line-height:1.6;text-align:center">
            <strong class="green">FAQPage Schema Active</strong> — 
            Each Q&amp;A pair generates a Question/Answer schema entity for Google rich results
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:0 auto 32px;max-width:120px;opacity:0.5">

        <!-- Category Tabs (built by JS from config data) -->
        <div class="faq-cats" id="faqCats"></div>

        <!-- FAQ Content (built by JS) -->
        <div id="faqContent"></div>

        <!-- Fallback: Server-rendered FAQ for SEO (hidden from visual, visible to crawlers) -->
        <noscript>
            <?php foreach ($faqCategories as $cat): ?>
            <div class="faq-category">
                <h2><?= $cat['icon'] ?> <?= htmlspecialchars($cat['title']) ?></h2>
                <?php foreach ($cat['questions'] as $qa): ?>
                <div class="faq-item open">
                    <h3><?= htmlspecialchars($qa['q']) ?></h3>
                    <p><?= htmlspecialchars($qa['a']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </noscript>

        <!-- SEO Strategy -->
        <div class="seo-only seo-strategy" style="margin-top:32px">
            <div class="seo-strategy-title">SEO CONTENT STRATEGY — FAQ PAGE</div>
            <div class="seo-strategy-grid">
                <div>
                    <div class="seo-strategy-label">Long-Tail Keywords Targeted</div>
                    "can I use EAs on prop firm", "prop firm daily loss rules", 
                    "how prop firm payout works", "best prop firm for beginners", 
                    "prop firm overnight holding"
                </div>
                <div>
                    <div class="seo-strategy-label">Blog Content Ideas</div>
                    → Static vs Trailing Drawdown Explained<br>
                    → How to Pass a Prop Firm Challenge in 5 Days<br>
                    → Best Trading Strategies for Funded Accounts<br>
                    → Prop Firm Scaling Plans Compared
                </div>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/community.php'; ?>
