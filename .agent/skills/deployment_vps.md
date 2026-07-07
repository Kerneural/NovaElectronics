# Production VPS Deployment Guide
> *Last updated: 2026-07-07 | NovaElectronics*

This guide details the steps to synchronize local theme modifications to the production 1Panel VPS environment.

---

## 🛠️ Step-by-Step Sync Steps

### 1. Push to GitHub
Commit and push all changes from the local development branch to GitHub:
```bash
git add .
git commit -m "feat: complete dynamic woo integration and pretty redirects"
git push origin main
```

### 2. Pull on VPS
Log in to the VPS terminal (or 1Panel SSH Terminal) and pull the latest changes:
```bash
cd /home/agvhrrhghosting/git_source/
git pull origin main
```

### 3. Deploy Theme to Web Directory
Copy the modified theme files to the public WordPress themes folder:
```bash
cp -r /home/agvhrrhghosting/git_source/src/wp-content/themes/dienmay8-clone/* /home/agvhrrhghosting/public_html/wp-content/themes/dienmay8-clone/
```

### 4. Run Affiliate Database Seeder
To refresh the database dynamic products and unique image registrations:
```bash
wp eval-file /var/www/html/seed_affiliate_products.php --allow-root
```
*(Alternatively, navigate to `https://dailysmartlife.com/seed_affiliate_products.php` in the browser, verify execution, and delete the script file immediately for security).*

### 5. Clear Caches
Purge the LiteSpeed Cache or proxy server outputs on the VPS.
