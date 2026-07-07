# 🛑 Anti-Rationalization Guardrails for NovaElectronics Child Theme
> *Adapted to enforce production-grade discipline on affiliate marketing WordPress sites.*

AI coding agents often fail due to **cognitive shortcutting (rationalization)**—skipping verification steps, using hardcoded static strings instead of dynamic database queries, leaving 404 links, or ignoring attachment state in seeds.

This document lists common excuses and the engineering standards you must follow instead.

---

## 💻 Code & Database Rationalizations

| Rationalization (The Agent's Excuse) | Reality (The Corrective Engineering Standard) |
| :--- | :--- |
| *"I will write/edit index.php using a python regex/replace script to search HTML tags character by character."* | **No. Extremely dangerous.** Naive tag counting on large files (like `index.php`) can easily fail due to minor syntax anomalies, wiping out large blocks of code. Always use precise line bounds or specific unique search anchors for string replacements. |
| *"The seeder has run, so all product details are correctly updated in the database."* | **Verify.** Always check if the products are successfully created. Verify their metadata, permalinks, and attachment images. Do not assume successful console execution means correct frontend display. |
| *"I will reuse the existing attachments in database when seeding to save time."* | **No.** Attachment reuse triggers cache issues in WordPress, resulting in duplicate product images on the homepage. Wiping existing matching attachments prior to seeding is mandatory to force a clean, unique image registration. |

---

## 🔗 Redirection & Local Link Rationalizations

| Rationalization (The Agent's Excuse) | Reality (The Corrective Engineering Standard) |
| :--- | :--- |
| *"I will hardcode category links in header.php/footer.php to local URLs because they don't change often."* | **No.** WordPress vertical menus, horizontal menus, and footer categories must point dynamically to WooCommerce category pages (`/product-category/{slug}/`). Hardcoding old static links causes 404s. |
| *"The redirection is implemented in functions.php, so pretty links are working."* | **Verify.** Navigate to a `/go/{slug}` URL using the browser via DevTools or curl to verify: (1) HTTP 307 temporary redirect to the affiliate destination, (2) check database postmeta to confirm `_affiliate_clicks` increases. |
| *"The old email address webdemo@gmail.com is fine to leave in some comments or scripts."* | **No.** The entire codebase must be purged of any old development emails, fully migrating to the correct production domain contact email `info@dailysmartlife.com`. |
