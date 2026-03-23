# Doji Funding

**Trade Your Way. Get Funded.** -- The first 100% customizable prop trading firm.

Doji Funding is a full-featured website for a proprietary trading firm, built with PHP, vanilla JavaScript, and CSS. It includes a challenge configurator, user dashboard, authentication system, KYC submission, affiliate program, and comprehensive informational pages.

## Tech Stack

| Layer     | Technology                |
|-----------|---------------------------|
| Backend   | PHP 7.4+                  |
| Frontend  | Vanilla JavaScript (ES6)  |
| Styling   | CSS3 (custom properties)  |
| Database  | MySQL                     |
| Fonts     | Google Fonts CDN          |
| Server    | Apache (mod_rewrite)      |

No build tools, no Node.js, no npm -- pure PHP and vanilla JS.

## Features

- **Challenge Configurator** -- interactive pricing calculator with sliders, toggles, and promo codes
- **User Authentication** -- registration, login, logout, and password management
- **User Dashboard** -- account overview with profile and password update
- **KYC Submission** -- identity verification flow
- **Affiliate Program** -- referral tracking page
- **Competitions** -- trading competition listings
- **SEO System** -- per-page meta tags, JSON-LD schema markup, and an on-page SEO debug overlay
- **FAQ System** -- category-based accordion with FAQPage schema
- **Informational Pages** -- About, Platforms, Symbols, Scaling, Rules, Privacy Policy, Terms, Refund Policy, Contact
- **Custom 404 Page** -- branded error page
- **Apache Optimization** -- clean URLs, GZIP compression, browser caching, security headers

## Project Structure

```
doji-funding/
├── index.php                   # Homepage entry point
├── challenges.php              # Challenge configurator
├── dashboard.php               # User dashboard
├── about.php                   # About page
├── affiliates.php              # Affiliate program
├── competitions.php            # Competitions
├── contact.php                 # Contact page
├── faq.php                     # FAQ
├── platforms.php               # Trading platforms
├── symbols.php                 # Tradeable symbols
├── scaling.php                 # Scaling plan
├── rules.php                   # Trading rules
├── privacy.php                 # Privacy policy
├── terms.php                   # Terms of service
├── refund.php                  # Refund policy
├── 404.php                     # Custom 404 page
├── .htaccess                   # Apache config, rewrites, security
├── .gitignore
│
├── config/                     # Server-side configuration
│   ├── app.php                 #   Global constants (site name, URL, stats)
│   ├── database.php            #   Database connection settings
│   ├── pricing.php             #   Pricing tables, promo codes, adjustments
│   ├── presets.php              #   Challenge presets
│   ├── faq.php                 #   FAQ categories and Q&A data
│   └── seo.php                 #   Per-page SEO metadata and schema
│
├── includes/                   # Shared PHP components
│   ├── header.php              #   HTML head, meta tags, CSS imports
│   ├── nav.php                 #   Sticky navigation bar
│   ├── footer.php              #   Footer, JS imports, config injection
│   ├── icons.php               #   SVG icon definitions
│   ├── modals.php              #   Modal dialog components
│   ├── auth.php                #   Authentication helpers
│   ├── community.php           #   Community section component
│   └── dashboard-data.php      #   Dashboard data helpers
│
├── pages/                      # Page-specific content templates
│   ├── home.php
│   ├── challenges.php
│   ├── dashboard.php
│   ├── faq.php
│   └── ...                     #   One template per page
│
├── api/                        # Backend API endpoints
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── submit-kyc.php
│   ├── update-password.php
│   └── update-profile.php
│
├── database/                   # Database schema and migrations
│   └── schema.sql
│
└── assets/                     # Frontend assets
    ├── css/                    #   Stylesheets (split by module)
    ├── js/                     #   JavaScript (split by module)
    └── img/                    #   Images and media
```

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with `mod_rewrite`, `mod_headers`, `mod_expires`, and `mod_deflate` enabled

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-org/doji-funding.git
   cd doji-funding
   ```

2. **Configure environment variables**

   Copy the example environment file and fill in your values:

   ```bash
   cp .env.example .env
   ```

   See the [Environment Variables](#environment-variables) section below.

3. **Create the database**

   ```bash
   mysql -u your_user -p your_database < database/schema.sql
   ```

   Apply any migrations in the `database/` directory in order.

4. **Point your web server to the project root**

   **Apache** -- set the `DocumentRoot` to the project directory, or place the files in your `htdocs/` folder. The `.htaccess` file handles URL rewriting, security headers, and caching automatically.

   **Nginx** -- configure equivalent rewrite rules to remove `.php` extensions and block access to `config/`, `includes/`, and `database/` directories.

5. **Visit your site** -- the homepage should load at your configured domain.

## Environment Variables

Create a `.env` file in the project root with the following values:

| Variable      | Description                        |
|---------------|------------------------------------|
| `DB_HOST`     | Database host (e.g., `localhost`)  |
| `DB_NAME`     | Database name                      |
| `DB_USER`     | Database username                  |
| `DB_PASS`     | Database password                  |
| `SITE_URL`    | Full site URL (e.g., `https://dojifunding.com`) |

## Architecture

### Data Flow

```
config/*.php (data)  ->  includes/footer.php (injects as JSON)  ->  window.DOJI_CONFIG  ->  assets/js/*.js
```

- **PHP** renders HTML and injects configuration data as `window.DOJI_CONFIG`
- **JavaScript** handles all client-side interactivity (configurator, FAQ, dashboard)
- **CSS** is split by module with shared variables defined in `:root`

### Page Assembly

Each entry-point PHP file follows this pattern:

```php
$currentPage = 'challenges';
require 'config/app.php';
require 'config/pricing.php';
require 'config/seo.php';
require 'includes/header.php';
require 'includes/nav.php';
require 'pages/challenges.php';
require 'includes/footer.php';
```

## License

All Rights Reserved. This is proprietary software. Unauthorized copying, modification, distribution, or use of this software, in whole or in part, is strictly prohibited without prior written permission from the copyright holder.
