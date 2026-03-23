/**
 * Doji Funding — FAQ Module
 * 
 * Renders FAQ categories and accordions from config data.
 * Uses window.DOJI_CONFIG.faq
 */

(function() {
    'use strict';

    const faqData = window.DOJI_CONFIG?.faq || [];
    let activeCat = 0;

    /* ═══ Custom SVG Icon Map ═══ */
    function getIcon(key) {
        const icons = {
            'info': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><circle cx="11" cy="11" r="9.5" stroke="#10B981" stroke-width="1.3"/><circle cx="11" cy="11" r="7" stroke="rgba(16,185,129,0.15)" stroke-width="0.8" stroke-dasharray="3 2"/><circle cx="11" cy="6.5" r="1.2" fill="#10B981"/><path d="M11 9.5v6" stroke="#10B981" stroke-width="1.6" stroke-linecap="round"/></svg>',

            'target': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><circle cx="11" cy="11" r="9.5" stroke="#10B981" stroke-width="1.2"/><circle cx="11" cy="11" r="6.5" stroke="#10B981" stroke-width="0.8" opacity="0.4"/><circle cx="11" cy="11" r="3.5" stroke="#10B981" stroke-width="0.8" opacity="0.6"/><circle cx="11" cy="11" r="1.5" fill="#10B981"/><path d="M11 1v3M11 18v3M1 11h3M18 11h3" stroke="#10B981" stroke-width="0.8" stroke-linecap="round" opacity="0.3"/></svg>',

            'rules': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><rect x="3" y="1.5" width="16" height="19" rx="2.5" stroke="#10B981" stroke-width="1.2"/><path d="M3 5.5h16" stroke="#10B981" stroke-width="1.2"/><circle cx="6" cy="9" r="0.7" fill="#10B981" opacity="0.6"/><circle cx="6" cy="12" r="0.7" fill="#10B981" opacity="0.6"/><circle cx="6" cy="15" r="0.7" fill="#10B981" opacity="0.6"/><path d="M8.5 9h8M8.5 12h6M8.5 15h4" stroke="#10B981" stroke-width="1" stroke-linecap="round" opacity="0.5"/></svg>',

            'wallet': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><rect x="1.5" y="5" width="19" height="14" rx="2.5" stroke="#10B981" stroke-width="1.2"/><path d="M1.5 8.5h19" stroke="#10B981" stroke-width="1.2"/><path d="M4 5V4a2 2 0 012-2h10a2 2 0 012 2v1" stroke="#10B981" stroke-width="1" opacity="0.4"/><rect x="14" y="11.5" width="4.5" height="3.5" rx="1.5" fill="rgba(16,185,129,0.12)" stroke="#10B981" stroke-width="0.8"/><circle cx="16.2" cy="13.3" r="0.8" fill="#10B981"/></svg>',

            'chart': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><path d="M2 19V3" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M2 19h18" stroke="#10B981" stroke-width="1.2" stroke-linecap="round"/><path d="M4,15 8,10 11,12 15,6 19,3 19,19 4,19Z" fill="rgba(16,185,129,0.06)"/><polyline points="4,15 8,10 11,12 15,6 19,3" stroke="#10B981" stroke-width="1.3" fill="none" stroke-linecap="round" stroke-linejoin="round"/><circle cx="19" cy="3" r="1.8" fill="#10B981" opacity="0.8"/><circle cx="8" cy="10" r="1.2" fill="#10B981" opacity="0.4"/><circle cx="15" cy="6" r="1.2" fill="#10B981" opacity="0.4"/></svg>',

            'trophy': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><path d="M6 3h10v5c0 4-2.2 6.5-5 7.5C8.2 14.5 6 12 6 8V3z" fill="rgba(16,185,129,0.06)" stroke="#10B981" stroke-width="1.2" stroke-linejoin="round"/><path d="M6 5.5c-2 0-3.5 1.5-3.5 3.5s1.5 3.5 3 3.5" stroke="#10B981" stroke-width="0.9" fill="none" stroke-linecap="round" opacity="0.5"/><path d="M16 5.5c2 0 3.5 1.5 3.5 3.5s-1.5 3.5-3 3.5" stroke="#10B981" stroke-width="0.9" fill="none" stroke-linecap="round" opacity="0.5"/><path d="M9.5 15.5v2h3v-2" stroke="#10B981" stroke-width="1"/><rect x="7.5" y="17.5" width="7" height="2.5" rx="1.2" fill="rgba(16,185,129,0.12)" stroke="#10B981" stroke-width="0.8"/><path d="M11 6.5v3M9.5 8h3" stroke="#10B981" stroke-width="0.8" stroke-linecap="round" opacity="0.4"/></svg>',

            'card': '<svg viewBox="0 0 22 22" fill="none" width="18" height="18"><rect x="1.5" y="4" width="19" height="14" rx="2.5" stroke="#10B981" stroke-width="1.2"/><path d="M1.5 8.5h19" stroke="#10B981" stroke-width="1.2"/><rect x="4" y="12" width="6" height="2.5" rx="0.8" fill="rgba(16,185,129,0.25)"/><rect x="4" y="15.5" width="3.5" height="1" rx="0.5" fill="rgba(16,185,129,0.15)"/><circle cx="16.5" cy="14" r="2.2" stroke="rgba(16,185,129,0.3)" stroke-width="0.8"/><circle cx="14.5" cy="14" r="2.2" stroke="rgba(16,185,129,0.3)" stroke-width="0.8"/></svg>'
        };

        if (key === 'doji-logo') {
            // Use the actual Doji Funding logo image
            return '<img src="assets/img/logo.png" alt="Doji Funding" width="18" height="18" class="faq-doji-logo" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'inline-block\'" /><svg viewBox="0 0 22 22" fill="none" width="18" height="18" style="display:none;vertical-align:middle"><rect x="2" y="2" width="18" height="18" rx="4" fill="rgba(16,185,129,0.1)" stroke="#10B981" stroke-width="1"/><rect x="9.5" y="3.5" width="3" height="15" rx="1.5" fill="#10B981" opacity="0.7"/><rect x="7" y="6.5" width="8" height="5" rx="1" fill="rgba(16,185,129,0.15)" stroke="#10B981" stroke-width="0.8"/></svg>';
        }

        return icons[key] || icons['info'];
    }

    function buildFaq() {
        // Category tabs
        const cats = document.getElementById('faqCats');
        if (cats) {
            cats.innerHTML = faqData.map((c, i) =>
                `<button class="faq-cat-btn${i === activeCat ? ' active' : ''}" onclick="setFaqCat(${i})"><span class="faq-cat-icon">${getIcon(c.icon)}</span>${c.title}</button>`
            ).join('');
        }

        // Content
        const content = document.getElementById('faqContent');
        if (content) {
            content.innerHTML = faqData.map((c, ci) =>
                `<div style="display:${ci === activeCat ? 'block' : 'none'}"${c.id ? ' id="' + c.id + '"' : ''}>
                    <h2 class="faq-section-title">
                        <span class="faq-section-icon">${getIcon(c.icon)}</span>${c.title}
                        ${ci === activeCat ? '<span class="seo-tag">H2</span>' : ''}
                    </h2>
                    ${c.questions.map((q, qi) => `
                        <div class="faq-item" id="faq-${ci}-${qi}">
                            <button class="faq-q" onclick="toggleFaq(${ci},${qi})">
                                <span>${q.q}${qi === 0 ? '<span class="seo-tag">H3 + Question Schema</span>' : ''}</span>
                                <svg class="faq-chevron" viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="faq-a">
                                ${q.a}
                                <span class="seo-tag">Answer Schema</span>
                            </div>
                        </div>
                    `).join('')}
                </div>`
            ).join('');
        }
    }

    // Global functions (called from onclick)
    window.setFaqCat = function(i) {
        activeCat = i;
        buildFaq();
    };

    window.toggleFaq = function(ci, qi) {
        document.getElementById('faq-' + ci + '-' + qi).classList.toggle('open');
    };

    // Hash-based navigation (e.g. faq.php#billing → Orders & Billing)
    const hashMap = {
        'general': 0, 'challenges': 1, 'rules': 2,
        'payouts': 3, 'scaling': 4, 'doji-coins': 5, 'loyalty': 5,
        'competitions': 6, 'competition': 6,
        'billing': 7, 'payments': 7,
    };
    let autoOpenQuestion = -1;

    function initFromHash() {
        const hash = window.location.hash.replace('#', '').toLowerCase();
        if (hash && hashMap[hash] !== undefined) {
            activeCat = hashMap[hash];
            autoOpenQuestion = 0; // auto-open first question
        }
    }

    // Init
    initFromHash();
    document.addEventListener('DOMContentLoaded', function() {
        buildFaq();
        if (autoOpenQuestion >= 0) {
            const el = document.getElementById('faq-' + activeCat + '-' + autoOpenQuestion);
            if (el) {
                el.classList.add('open');
                setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
            }
        }
    });
})();
