/**
 * Doji Funding — Main Application JS
 * 
 * Handles: SEO toggle, SEO overlay rendering.
 * Uses window.DOJI_CONFIG injected by PHP.
 */

(function() {
    'use strict';

    let seoOn = false;
    const config = window.DOJI_CONFIG || {};
    const currentPage = config.currentPage || 'home';
    const seoData = config.seo || {};

    // ─── Mobile Menu ───
    window.toggleMobileMenu = function() {
        const menu = document.getElementById('mobileMenu');
        const burger = document.getElementById('navHamburger');
        if (menu && burger) {
            menu.classList.toggle('open');
            burger.classList.toggle('open');
            document.body.classList.toggle('menu-open');
        }
    };

    // Close mobile menu on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const menu = document.getElementById('mobileMenu');
            const burger = document.getElementById('navHamburger');
            if (menu) menu.classList.remove('open');
            if (burger) burger.classList.remove('open');
            document.body.classList.remove('menu-open');
        } else {
            // Close any desktop dropdowns when going mobile
            document.querySelectorAll('.nav-dropdown.open').forEach(d => d.classList.remove('open'));
        }
    });

    // ─── Nav Dropdowns (Challenges, Language) ───
    window.toggleNavDrop = function(id) {
        // Don't toggle dropdowns on mobile — use hamburger menu instead
        if (window.innerWidth <= 768) return;

        const drop = document.getElementById(id);
        if (!drop) return;
        const isOpen = drop.classList.contains('open');

        // Close all dropdowns first
        document.querySelectorAll('.nav-dropdown.open').forEach(d => {
            d.classList.remove('open');
            const trigger = d.closest('.nav-dropdown-wrap, .lang-wrap')?.querySelector('.nav-dropdown-trigger, .lang-btn');
            if (trigger) trigger.classList.remove('open');
        });

        if (!isOpen) {
            drop.classList.add('open');
            const trigger = drop.closest('.nav-dropdown-wrap, .lang-wrap')?.querySelector('.nav-dropdown-trigger, .lang-btn');
            if (trigger) trigger.classList.add('open');
        }
    };

    // Close dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-dropdown-wrap') && !e.target.closest('.lang-wrap')) {
            document.querySelectorAll('.nav-dropdown.open').forEach(d => {
                d.classList.remove('open');
                const trigger = d.closest('.nav-dropdown-wrap, .lang-wrap')?.querySelector('.nav-dropdown-trigger, .lang-btn');
                if (trigger) trigger.classList.remove('open');
            });
        }
    });

    // ─── Mobile Init: close any stray dropdowns ───
    if (window.innerWidth <= 768) {
        document.querySelectorAll('.nav-dropdown, .lang-dropdown').forEach(d => d.classList.remove('open'));
    }

    // ─── Promo Top Banner ───
    window.closePromoBanner = function() {
        const banner = document.getElementById('promoBanner');
        if (banner) {
            banner.classList.add('hidden');
            document.body.classList.add('promo-closed');
            try { localStorage.setItem('doji_promo_banner', '1'); } catch(e) {}
        }
    };

    window.copyPromoCode = function(code) {
        navigator.clipboard?.writeText(code).then(() => {
            const el = document.querySelector('.promo-banner-code');
            if (el) {
                const orig = el.textContent;
                el.textContent = '✓ COPIED';
                el.classList.add('copied');
                setTimeout(() => { el.textContent = orig; el.classList.remove('copied'); }, 1500);
            }
            // Auto-fill promo input on challenges page if present
            const input = document.getElementById('promoInput');
            if (input) {
                input.value = code;
                if (typeof Configurator !== 'undefined') Configurator.applyPromo();
            }
        });
    };

    // Auto-hide promo banner if already closed
    try {
        if (localStorage.getItem('doji_promo_banner') === '1') {
            const b = document.getElementById('promoBanner');
            if (b) b.classList.add('hidden');
            document.body.classList.add('promo-closed');
        }
    } catch(e) {}

    // ─── Disclaimer Banner (reappears after 7 days) ───
    const DISCLAIMER_COOLDOWN = 7 * 24 * 60 * 60 * 1000; // 7 days in ms

    window.dismissDisclaimer = function() {
        const banner = document.getElementById('disclaimerBanner');
        if (banner) {
            banner.classList.add('hidden');
            try { localStorage.setItem('doji_disclaimer_ts', Date.now().toString()); } catch(e) {}
        }
    };

    // Auto-hide only if dismissed less than 24h ago
    try {
        const ts = localStorage.getItem('doji_disclaimer_ts');
        if (ts && (Date.now() - parseInt(ts)) < DISCLAIMER_COOLDOWN) {
            const b = document.getElementById('disclaimerBanner');
            if (b) b.classList.add('hidden');
        } else {
            // Expired or never set — remove old key so banner shows
            localStorage.removeItem('doji_disclaimer_ts');
        }
    } catch(e) {}

    // ─── SEO Toggle ───
    window.toggleSeo = function() {
        seoOn = !seoOn;
        document.body.classList.toggle('seo-on', seoOn);

        const btn = document.getElementById('seoToggle');
        btn.textContent = seoOn ? '◉ SEO ON' : '○ SEO OFF';
        btn.classList.toggle('on', seoOn);

        document.getElementById('seoOverlay').classList.toggle('visible', seoOn);

        const footerSeo = document.getElementById('footerSeo');
        if (footerSeo) footerSeo.textContent = seoOn ? 'sitemap.xml · robots.txt · hreflang tags ready' : '';

        if (seoOn) renderSeoOverlay();
    };

    // ─── SEO Overlay ───
    function renderSeoOverlay() {
        const d = seoData[currentPage];
        if (!d) return;

        const charOk = d.desc.length <= 155;
        const overlay = document.getElementById('seoOverlay');

        const fields = [
            ['&lt;title&gt;', d.title],
            ['meta description', d.desc],
            ['&lt;h1&gt;', d.h1],
            ['canonical', d.canonical],
            ['og:type', d.ogType],
            ['keywords', d.keywords],
        ];

        let html = `
            <div style="color:var(--green);font-weight:700;font-size:.85em;letter-spacing:.12em;margin-bottom:16px;text-transform:uppercase;border-bottom:1px solid var(--border2);padding-bottom:8px">
                SEO Metadata — ${currentPage.toUpperCase()}
            </div>`;

        fields.forEach(([label, value]) => {
            html += `<div style="margin-bottom:10px">
                <div style="color:var(--orange);font-weight:600;font-size:.9em;margin-bottom:2px">${label}</div>
                <div style="color:var(--text2);line-height:1.5;word-break:break-word">${value}</div>`;
            if (label === 'meta description') {
                html += `<div style="color:${charOk ? 'var(--green)' : 'var(--red)'};font-size:.85em;margin-top:2px">
                    ${d.desc.length} / 160 chars ${charOk ? '✓' : '⚠ too long'}
                </div>`;
            }
            html += '</div>';
        });

        // Schema
        let schemaFormatted;
        try { schemaFormatted = JSON.stringify(JSON.parse(d.schema), null, 2); }
        catch(e) { schemaFormatted = d.schema; }

        html += `<div style="margin-top:12px;border-top:1px solid var(--border2);padding-top:12px">
            <div style="color:var(--blue);font-weight:600;font-size:.9em;margin-bottom:6px">Schema JSON-LD</div>
            <pre style="color:var(--text3);font-size:.85em;line-height:1.5;white-space:pre-wrap;background:var(--bg2);padding:10px;border-radius:6px;border:1px solid var(--border)">${schemaFormatted}</pre>
        </div>`;

        // Checklist
        const checklist = [
            'Yoast/RankMath configured', 'Open Graph tags set', 'Schema markup injected',
            'Canonical URL defined', 'Breadcrumbs enabled', 'Internal linking',
            'Image alt tags', 'Core Web Vitals optimized', 'Mobile responsive', 'Sitemap.xml includes page',
        ];
        html += `<div style="margin-top:12px;border-top:1px solid var(--border2);padding-top:12px">
            <div style="color:var(--green);font-weight:600;font-size:.9em;margin-bottom:8px">WP SEO Checklist</div>`;
        checklist.forEach(item => {
            html += `<div style="color:var(--text2);margin-bottom:4px;display:flex;align-items:center;gap:6px">
                <span style="color:var(--green)">✓</span> ${item}
            </div>`;
        });
        html += '</div>';

        overlay.innerHTML = html;
    }
})();

// ─── Newsletter Subscribe (placeholder — connect to backend later) ───
function subscribeNewsletter() {
    var input = document.getElementById('newsletterEmail');
    var msg = document.getElementById('newsletterMsg');
    if (!input || !msg) return;

    var email = input.value.trim();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        msg.className = 'engage-nl-msg err';
        msg.textContent = 'Please enter a valid email address.';
        return;
    }

    // Simulate success (replace with real API call later)
    msg.className = 'engage-nl-msg ok';
    msg.textContent = '✓ You\'re subscribed! Check your inbox for a welcome email.';
    input.value = '';
    input.disabled = true;

    // Store locally for now
    try {
        var subs = JSON.parse(localStorage.getItem('doji_newsletter') || '[]');
        subs.push({ email: email, date: new Date().toISOString() });
        localStorage.setItem('doji_newsletter', JSON.stringify(subs));
    } catch(e) {}
}

function toggleDisclaimerMore() {
    var more = document.getElementById('disclaimerMore');
    var btn = document.getElementById('disclaimerToggle');
    if (!more || !btn) return;
    if (more.style.display === 'none') {
        more.style.display = 'block';
        btn.classList.add('open');
        btn.innerHTML = 'Read less <svg viewBox="0 0 10 6" width="10" height="6" style="vertical-align:middle;margin-left:4px"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>';
    } else {
        more.style.display = 'none';
        btn.classList.remove('open');
        btn.innerHTML = 'Read more <svg viewBox="0 0 10 6" width="10" height="6" style="vertical-align:middle;margin-left:4px"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>';
    }
}
