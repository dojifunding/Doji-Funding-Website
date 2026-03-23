# Doji Funding — Website Frontend

> **Trade Your Way. Get Funded.** — First 100% customizable prop firm.

## 📁 Project Structure

```
doji-funding/
│
├── index.php                  # Entry: Homepage
├── challenges.php             # Entry: Challenges + Configurator
├── faq.php                    # Entry: FAQ
├── .htaccess                  # Apache rewrites, caching, security
├── .gitignore
├── README.md
│
├── config/                    # ⚙️  Server-side configuration (PHP)
│   ├── app.php                #   Global constants (site name, URL, stats)
│   ├── pricing.php            #   Pricing tables, promo codes, adjustment rules
│   ├── faq.php                #   FAQ categories & Q/A data
│   └── seo.php                #   Per-page SEO metadata & schema markup
│
├── includes/                  # 🧩 Shared PHP components
│   ├── header.php             #   <head>, meta tags, CSS imports, <body>
│   ├── nav.php                #   Sticky navigation bar
│   └── footer.php             #   Footer, SEO overlay container, JS imports
│
├── pages/                     # 📄 Page-specific content (PHP templates)
│   ├── home.php               #   Hero, trust bar, steps, cards, stats, CTA
│   ├── challenges.php         #   Configurator HTML structure
│   └── faq.php                #   FAQ categories & accordion structure
│
└── assets/                    # 🎨 Frontend assets
    ├── css/
    │   ├── main.css           #   Global: variables, reset, nav, footer, layout
    │   ├── configurator.css   #   Configurator: sliders, toggles, pricing
    │   └── faq.css            #   FAQ: categories, accordions
    │
    └── js/
        ├── app.js             #   Global: SEO toggle & overlay
        ├── configurator.js    #   Full configurator: pricing calc, UI, events
        └── faq.js             #   FAQ: category tabs, accordion toggle
```

## 🏗️ Architecture

### Data Flow

```
config/*.php (data)  →  includes/footer.php (injects as JSON)  →  window.DOJI_CONFIG  →  assets/js/*.js
```

- **PHP** renders the HTML structure and injects data as `window.DOJI_CONFIG`
- **JavaScript** handles all interactivity (configurator, FAQ, SEO overlay)
- **CSS** is split by module with shared variables in `:root`

### Page Assembly (each entry point)

```php
$currentPage = 'challenges';       // 1. Set context
require 'config/app.php';           // 2. Load configs
require 'config/pricing.php';
require 'config/faq.php';
require 'config/seo.php';
require 'includes/header.php';      // 3. HTML head + meta
require 'includes/nav.php';         // 4. Navigation
require 'pages/challenges.php';     // 5. Page content
require 'includes/footer.php';      // 6. Footer + JS
```

## 🚀 Deployment on InfinityFree

### Quick Deploy

1. Go to **InfinityFree Control Panel** → **Online File Manager**
2. Navigate to `htdocs/`
3. Upload the **entire project structure** (all files/folders)
4. Your site is live at your domain

### Via FTP (FileZilla)

1. Get FTP credentials from InfinityFree Control Panel → **FTP Details**
2. Connect with FileZilla: Host, Port 21, Username, Password
3. Upload everything to `/htdocs/`
4. Done

### URLs

| Page       | URL                              |
|------------|----------------------------------|
| Homepage   | `yourdomain.com/`                |
| Challenges | `yourdomain.com/challenges.php`  |
| FAQ        | `yourdomain.com/faq.php`         |

With `.htaccess` rewrite enabled:
- `yourdomain.com/challenges` (no .php needed)
- `yourdomain.com/faq`

## 🔧 Configuration Guide

### Update pricing (`config/pricing.php`)
- `$basePrices` — Base price per account size per challenge type
- `$promoCodes` — Active promo codes with type (percent/fixed) and value
- Adjustment rules are documented as comments in the file

### Update FAQ (`config/faq.php`)
- Add/remove categories or Q&A pairs in the `$faqCategories` array
- Changes appear on both the page and the FAQPage schema

### Update SEO (`config/seo.php`)
- Per-page: title, meta description, canonical, Open Graph, schema
- Schema is output as JSON-LD in the `<head>`

### Update site-wide settings (`config/app.php`)
- `SITE_NAME`, `SITE_URL`, trust metrics, account limits
- `ASSET_VERSION` — bump this to bust CSS/JS cache after updates

## 🔍 SEO Features

- **Toggle**: Click "SEO ON/OFF" in the nav to see:
  - Inline badges on H1, H2, H3 headings
  - Fixed overlay with full page metadata
  - Schema JSON-LD preview
  - WordPress SEO checklist
- **Real meta tags** in `<head>` per page (title, description, canonical, OG)
- **JSON-LD schema** per page (FinancialService, Product, FAQPage)
- **Noscript FAQ** for crawlers that don't execute JS

## 👥 Team Workflow

### Adding a new page

1. Create `config/seo.php` entry for the new page
2. Create `pages/newpage.php` with the content
3. Create `newpage.php` at root as entry point (copy pattern from `faq.php`)
4. Add link in `includes/nav.php`
5. Optionally add `assets/css/newpage.css` and `assets/js/newpage.js`

### CSS conventions

- All colors use CSS variables from `:root` in `main.css`
- Class naming: `.module-element` (e.g. `.cfg-panel`, `.faq-item`)
- Font stacks: `'DM Sans'` for UI, `'JetBrains Mono'` for data

### JS conventions

- Modules use IIFE pattern with public API via global objects
- Configurator exposes `Configurator.setTab()`, `.reset()`, `.applyPromo()`, etc.
- Config data read from `window.DOJI_CONFIG` (injected by PHP)

## 📋 Tech Stack

| Layer    | Technology           |
|----------|---------------------|
| Backend  | PHP 7.4+            |
| Frontend | Vanilla JS (ES6)    |
| Styling  | CSS3 (custom props)  |
| Fonts    | Google Fonts CDN     |
| Hosting  | InfinityFree (Apache)|

## 📝 Notes

- No Node.js, no build step, no npm — pure PHP + vanilla JS
- CSS/JS loaded conditionally per page (configurator CSS only on /challenges)
- All interactivity works without page reload
- Compatible with PHP 7.4+ (InfinityFree compatible)
- `.htaccess` blocks direct access to `/config/` and `/includes/`
