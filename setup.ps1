# PowerShell script to initialize and set up the WordPress Hybrid Clone on teammate machine

Write-Host "=== WordPress Hybrid Clone Setup ===" -ForegroundColor Green

# 1. Initialize .env file
if (-not (Test-Path .env)) {
    Write-Host "Creating .env file from .env.example..."
    Copy-Item .env.example .env
} else {
    Write-Host ".env file already exists. Skipping."
}

# 2. Install python dependencies
Write-Host "Installing python dependencies (beautifulsoup4, requests)..."
pip install beautifulsoup4 requests

# 3. Start Docker Compose
Write-Host "Starting Docker containers..."
docker compose up -d

# 4. Extract Flatsome parent theme
Write-Host "Setting up Flatsome parent theme..."
python setup_flatsome.py

# 5. Wait for WordPress container to become responsive
Write-Host "Waiting for WordPress container to be ready..."
do {
    Write-Host -NoNewline "."
    Start-Sleep -Seconds 3
    $check = curl.exe -s -o NUL -w "%{http_code}" http://localhost:8080/wp-admin/install.php
} while ($check -ne "200")

Write-Host ""
Write-Host "WordPress is responsive!" -ForegroundColor Green

# 6. WordPress core installation
Write-Host "Running WordPress installation via WP-CLI..."
docker compose run --rm wp-cli wp core install --url="http://localhost:8080" --title="Dienmay Clone" --admin_user="admin" --admin_password="adminpassword" --admin_email="admin@example.com" --skip-email

# 7. Download assets and subpages
Write-Host "Downloading external assets..."
python download_assets.py

Write-Host "Crawling subpages and generating static templates..."
python crawl_subpages.py

# 8. Setup theme and permalinks
Write-Host "Activating child theme..."
docker compose run --rm wp-cli wp theme activate dienmay8-clone

Write-Host "Configuring pretty permalinks..."
docker compose run --rm wp-cli wp rewrite structure '/%postname%/' --hard

# 9. WooCommerce Setup
Write-Host "Installing and activating WooCommerce..."
docker compose run --rm wp-cli wp plugin install woocommerce --activate --force

# 10. Populate Database Products and link templates
Write-Host "Creating dynamic products and linking forms..."
python setup_real_products.py

# 11. Enable COD and BACS payments
Write-Host "Configuring default payment gateways..."
docker compose run --rm wp-cli wp wc payment_gateway update cod --enabled=true --user=admin
docker compose run --rm wp-cli wp wc payment_gateway update bacs --enabled=true --user=admin

# 12. Flush Rewrite Rules
Write-Host "Flushing rewrite rules..."
docker compose run --rm wp-cli wp rewrite flush

Write-Host "==============================================" -ForegroundColor Green
Write-Host "Setup Completed Successfully!" -ForegroundColor Green
Write-Host "You can now access:"
Write-Host "- Website: http://localhost:8080/"
Write-Host "- Admin Panel: http://localhost:8080/wp-admin/ (admin / adminpassword)"
Write-Host "- Cart Page: http://localhost:8080/gio-hang/"
Write-Host "- Accounts Page: http://localhost:8080/tai-khoan/"
Write-Host "==============================================" -ForegroundColor Green
