#!/bin/bash
# Bash script to initialize and set up the WordPress Hybrid Clone on teammate machine

set -e

echo "=== WordPress Hybrid Clone Setup ==="

# 1. Initialize .env file
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
else
    echo ".env file already exists. Skipping."
fi

# 2. Install python dependencies
echo "Installing python dependencies (beautifulsoup4, requests)..."
pip install beautifulsoup4 requests

# 3. Start Docker Compose
echo "Starting Docker containers..."
docker compose up -d

# 4. Extract Flatsome parent theme
echo "Setting up Flatsome parent theme..."
python setup_flatsome.py

# 5. Wait for WordPress container to become responsive
echo "Waiting for WordPress container to be ready..."
until $(curl --output /dev/null --silent --head --fail http://localhost:8080/wp-admin/install.php); do
    printf '.'
    sleep 3
done
echo ""
echo "WordPress is responsive!"

# 6. WordPress core installation
echo "Running WordPress installation via WP-CLI..."
docker compose run --rm wp-cli wp core install \
  --url="http://localhost:8080" \
  --title="Dienmay Clone" \
  --admin_user="admin" \
  --admin_password="adminpassword" \
  --admin_email="admin@example.com" \
  --skip-email

# 7. Download assets and subpages
echo "Downloading external assets..."
python download_assets.py

echo "Crawling subpages and generating static templates..."
python crawl_subpages.py

# 8. Setup theme and permalinks
echo "Activating child theme..."
docker compose run --rm wp-cli wp theme activate dienmay8-clone

echo "Configuring pretty permalinks..."
docker compose run --rm wp-cli wp rewrite structure '/%postname%/' --hard

# 9. WooCommerce Setup
echo "Installing and activating WooCommerce..."
docker compose run --rm wp-cli wp plugin install woocommerce --activate --force

# 10. Populate Database Products and link templates
echo "Creating dynamic products and linking forms..."
python setup_real_products.py

# 11. Enable COD and BACS payments
echo "Configuring default payment gateways..."
docker compose run --rm wp-cli wp wc payment_gateway update cod --enabled=true --user=admin
docker compose run --rm wp-cli wp wc payment_gateway update bacs --enabled=true --user=admin

# 12. Flush Rewrite Rules
echo "Flushing rewrite rules..."
docker compose run --rm wp-cli wp rewrite flush

echo "=============================================="
echo "Setup Completed Successfully!"
echo "You can now access:"
echo "- Website: http://localhost:8080/"
echo "- Admin Panel: http://localhost:8080/wp-admin/ (admin / adminpassword)"
echo "- Cart Page: http://localhost:8080/gio-hang/"
echo "- Accounts Page: http://localhost:8080/tai-khoan/"
echo "=============================================="
