# 🧱 CONTEXT.md — NovaElectronics Project Architecture
> *Last updated: 2026-07-07 | Env: Docker-compose (Local Dev) & 1Panel VPS (Production)*

---

## 🛠️ Core Tech Stack & Infrastructure

| Layer | Technology | Description |
|---|---|---|
| **CMS Core** | WordPress 6.x + WooCommerce | Serves as the database, product catalog, and backend manager. |
| **Theme** | Flatsome (Parent) + `dienmay8-clone` (Child) | Parent theme provides styles & frameworks. Child theme overrides templates, layout, and logic. |
| **Local Dev** | Docker Compose | Containers: `wordpress_app` (WordPress), `wordpress_db` (MariaDB), and `wp-cli` helper container. |
| **Production** | 1Panel VPS | Runs on VPS (target domain: `dailysmartlife.com`). Code deployed via `git pull` in 1Panel terminal. |
| **Affiliate Router** | Custom Pretty Link Rewrite | Redirects `/go/{product-slug}` links to Amazon affiliate URLs, logging click metadata. |

---

## 📁 Repository Directory Structure

```
dienmay_clone/
├── .agent/                      # AI Agent session persistence & instructions
│   ├── rules/                   # Project rules (CONTEXT.md, PLAN.md, ORCHESTRATOR.md)
│   ├── skills/                  # Domain-specific skill guides (anti_rationalization.md, etc.)
│   └── workflows/               # Session memory (session_memory.md)
├── docs/                        # Project requirements, task spec sheets, and Jira docs
├── src/                         # WordPress Root Directory (mapped into Docker container)
│   ├── wp-content/themes/
│   │   └── dienmay8-clone/      # Active Child Theme Folder
│   │       ├── header.php       # Dynamic vertical & horizontal menus in English
│   │       ├── footer.php       # Dynamic footer widget categories in English
│   │       ├── functions.php    # Pretty link rewrite rule, shortcode, and click counter logic
│   │       ├── index.php        # Dynamic homepage grids (10 target categories) & sales slider
│   │       ├── woocommerce/     # WooCommerce template overrides
│   │       │   └── single-product/layouts/product.php # Single product details template
│   │       └── templates/       # Crawled static pages (about, news, etc.)
│   └── seed_affiliate_products.php # PHP Seeder script for 50 affiliate products & unique images
└── requirements.md              # Original project requirements list
```

---

## 📏 System Rules & Guidelines

- **DRY Template Principle**: Subpage templates inside `templates/` must NOT copy header/footer HTML code. They must use the standard WordPress hooks `<?php get_header(); ?>` and `<?php get_footer(); ?>` to ensure unified layout synchronization.
- **English Localization**: All user-facing components, categories, titles, buttons, search bars, and placeholders must be 100% in English. The target email address is `info@dailysmartlife.com`.
- **Dynamic Content**: Do not hardcode product listings, prices, images, or links on the homepage or landing pages. Use WooCommerce WP_Query loops so changes in the WooCommerce backend reflect instantly.
- **Masked Affiliate Links**: Affiliate links must never point directly to Amazon on hover. They must point locally to `/go/{slug}` and log the click to `_affiliate_clicks` postmeta before performing a 307 redirect.
