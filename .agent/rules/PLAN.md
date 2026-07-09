# 🎯 PLAN.md — NovaElectronics Roadmap & Status
> *Last updated: 2026-07-07 | Status: Local Dev Complete & Verified*

---

## 📅 Roadmap & Status Checklist

### Phase 1: DRY Refactoring & English Translation (Completed)
- [x] Write refactoring script [refactor_theme.py](file:///r:/_Projects/Eurus_Workspace/dienmay_clone/scratch/refactor_theme.py) to clean 42+ subpages.
- [x] Inject `get_header()` and `get_footer()` dynamically into all templates, removing duplicate static code.
- [x] Translate vertical sidebar, horizontal navigation menu, top contact bar, and footer widgets to 100% English.
- [x] Scanned codebase and verified that the old `webdemo@gmail.com` email address is replaced with `info@dailysmartlife.com`.

### Phase 2: Dynamic Woo Commerce & Image Synchronization (Completed)
- [x] Setup unique high-quality crawled image paths (98 unique images) and update seeder inputs.
- [x] Fix database seeder media attachment registration query to force fresh registrations and avoid cache duplicate reuse.
- [x] Dynamic grids: Convert the 10 target categories on the homepage (`index.php`) to query WooCommerce products dynamically.
- [x] Dynamic slider: Convert the static "SALES PRODUCTS" slider in `index.php` to a dynamic `WP_Query` loop to eliminate 404 links.
- [x] Footer menus: Synced footer widget category links to the English WooCommerce paths and added the 10th category "Smart Lighting".

### Phase 3: Pretty Link Redirect & Tracking (Completed)
- [x] Setup local redirect routing matching `/go/{slug}` in `functions.php`.
- [x] Integrate click tracking logging to increment `_affiliate_clicks` postmeta on redirect trigger.
- [x] Verify redirection rules (HTTP 307) and backend click meta tracking end-to-end.

### Phase 4: Layout & Asset Hotfixes (Completed)
- [x] Restored layout integrity by correcting orphaned closing `</div>` tags, resolving the 1/3-width collapsing bug on VPS homepage sections.
- [x] Replaced 50+ hardcoded local upload media assets (sliders, category icons, customer avatars, backgrounds, play overlays) with CDN/Unsplash URLs.

---

## 🚀 VPS Deployment Steps

To release these changes to the live VPS:
1. Log in to the 1Panel terminal on the VPS.
2. Run `git pull origin main` in the web directory `/home/agvhrrhghosting/git_source/`.
3. Copy the updated child theme changes:
   - Copy folder `/git_source/src/wp-content/themes/dienmay8-clone/` -> overwrite `/public_html/wp-content/themes/dienmay8-clone/` (or copy `/git_source/src/wp-content/themes/dienmay8-clone/index.php` -> `/public_html/wp-content/themes/dienmay8-clone/index.php`).
4. Purge LiteSpeed Cache or 1Panel page caches to clear old templates.