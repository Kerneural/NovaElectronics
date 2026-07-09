# 💾 SESSION MEMORY — NovaElectronics Project
> Last Checkpoint: 2026-07-07 | Status: Theme DRY refactoring, full English translation, dynamic product grids, and pretty link tracking fully implemented and verified.

---

## ⚡ Active Tasks Completed

- **DRY Layout Refactoring**: Stripped duplicate hardcoded headers/footers from all 42+ template files and injected `get_header()` / `get_footer()`.
- **English Localization**: Translated all navigation elements, search placeholders, categories, footer contacts, and email entries to English (`info@dailysmartlife.com`).
- **Dynamic WooCommerce Homepage Grids**: Replaced static homepage categories with query loops for the 10 target categories.
- **Dynamic SALES PRODUCTS Slider**: Converted the static 705-line HTML sales product slider to a dynamic WooCommerce `WP_Query` loop, resolving all 404 links.
- **Footer Category Sync**: Synced all desktop and mobile footer links to WooCommerce dynamic category paths.
- **Unique Product Images & Seeder Fix**: Solved the duplicate media attachment reuse issue in the database.
- **Pretty Links & Click Tracking**: Developed and tested `/go/{slug}` redirect route which increments `_affiliate_clicks` postmeta in the database before performing a 307 temporary redirect to Amazon search.
- **Full-Width Layout Recovery**: Fixed a major HTML nesting issue in `index.php` where two orphaned closing `</div>` tags were prematurely closing the `.section-content` wrapper of `section_873236097`. This restored the articles, projects, and review grids from restricted 1/3 and 2/3 widths back to their original full 1253px container.
- **Hardcoded Media Removal**: Replaced 50+ hardcoded local upload paths (`wp-content/uploads/2022/11/...`) inside `index.php` that broke on VPS (returning 404 errors) with high-quality dynamic Unsplash URLs. This includes the main 3-slide homepage banner slider, horizontal promo strip, category icons, video thumbnails, background textures, and play button overlay icons.
- **Customer Review Avatars**: Replaced hardcoded customer review images with dynamic avatars using the UI Avatars API.

---

## 🧠 Semantic Context Essence (Crucial Lessons Learned & Warnings)

### ⚠️ Pitfall 1: Database Seeder Image Attachment Cache
- **The Issue:** WooCommerce/WordPress deletes products upon `wp_delete_post()` but retains the attachments (images) in the database. If a seeded attachment slug already exists in `wp_posts` as an attachment, WordPress reuses the old ID and metadata, ignoring the updated physical file on disk. This results in the same placeholder image showing up everywhere despite unique paths.
- **The Fix:** In the seeder `seed_affiliate_products.php`, we must query all attachments matching target product slugs and delete them via `wp_delete_attachment($id, true)` before running product seeding.

### ⚠️ Pitfall 2: Naive HTML Tag Parsing in Python Scripts
- **The Issue:** Writing a python script that walks character-by-character to parse matching opening/closing tags (`<div` vs `</div`) to replace a container is highly error-prone on large templates. Differences in spacing (like `<div ` vs `<div class=`) or typos can cause the loop counter to go out of bounds, resulting in the script replacing everything to the end of the file and wiping out crucial page sections.
- **The Lesson:** Always restore files using `git checkout` if a script fails. When modifying large files, use exact line bounds (e.g. read lines into list and replace specific indices) or target specific unique string boundaries instead of character walking.

### ⚠️ Pitfall 3: PowerShell/Windows Cp1258 Console Encoding Crash
- **The Issue:** Running python scripts that print standard terminal output containing special Vietnamese accents (like `ê`, `ồ`, etc.) on Windows terminals set to Cp1258/Cp1252 will crash with a `UnicodeEncodeError`.
- **The Lesson:** Make scripts write results to a text file in `scratch/` instead of printing directly to standard console output, or force standard output to handle utf-8.

### ⚠️ Pitfall 4: Pretty Link Router Registration Hook
- **The Issue:** The `/go/` rewrite rule must be registered in the `template_redirect` hook. If query vars are not correctly registered, custom router redirects may conflict with standard page templates. Always verify redirection using curl or browser navigation.

### ⚠️ Pitfall 5: Flatsome Section Flex Collapsing
- **The Issue:** Flatsome sections (`.section` class) use `display: flex; flex-direction: row` by default. If a `.section` contains multiple `.section-content` divs or nested sections as direct siblings instead of nesting them inside a single `.section-content` container, they will be laid out as flex-items side-by-side, collapsing the content block width (e.g. to 1/3 width each).
- **The Lesson:** Keep all layout rows/columns strictly nested within the single primary `.section-content` wrapper of a section unless an explicit multi-column section design is intended.

---

## 🔜 Next Steps

- [ ] **Step 1: VPS Code Update**: SSH into VPS and pull the latest code in `/home/agvhrrhghosting/git_source`, then copy the updated child theme's `index.php` to `/home/agvhrrhghosting/public_html/wp-content/themes/dienmay8-clone/index.php`.
- [ ] **Step 2: Caching Purge**: Clear LiteSpeed Cache/1Panel page caches to ensure layout changes, CSS adjustments, and Unsplash URLs are refreshed.
- [ ] **Step 3: Affiliate Link Sync**: Once affiliate links are ready, update the product URLs or run the database seeder to match production needs.
