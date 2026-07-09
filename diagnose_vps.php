<?php
// diagnose_vps.php
// Put this in public_html/ and run via browser or terminal: php diagnose_vps.php

require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== NOVAELECTRONICS VPS DIAGNOSTIC REPORT ===\n\n";

// 1. Check WooCommerce Currency
$currency = get_option('woocommerce_currency');
echo "1. WooCommerce Currency: {$currency} (Should be USD to display $)\n";
if ($currency !== 'USD') {
    echo "   [ACTION REQUIRED] Go to WP Admin > WooCommerce > Settings > General and change Currency to 'United States (US) Dollar ($)'.\n\n";
} else {
    echo "   [OK] Currency is set to USD.\n\n";
}

// 2. Check upload directory path
$upload_dir_info = wp_upload_dir();
echo "2. WordPress Upload Directory Settings:\n";
echo "   Base Path: " . $upload_dir_info['basedir'] . "\n";
echo "   Base URL: " . $upload_dir_info['baseurl'] . "\n";
echo "   Target Subfolder Path: " . $upload_dir_info['basedir'] . "/2022/11\n";

$target_dir = $upload_dir_info['basedir'] . "/2022/11";
if (!is_dir($target_dir)) {
    echo "   [WARNING] Target subfolder does not exist: {$target_dir}\n";
} else {
    echo "   [OK] Target subfolder exists.\n";
}
echo "\n";

// 3. Check physical image files on disk
echo "3. Checking physical image files in uploads/2022/11/:\n";
$slugs = [
    "asus-rog-zephyrus-g14", "apple-macbook-air-m2", "dell-xps-13-ultrabook", "samsung-32-inch-gaming-monitor",
    "logitech-mx-master-3s-mouse", "ring-video-doorbell-wired", "google-nest-learning-thermostat",
    "eufy-security-solocam-s40", "august-wi-fi-smart-lock", "tp-link-tapo-smart-plug",
    "lg-front-load-smart-washer", "dyson-v15-detect-vacuum", "honeywell-hepa-air-purifier",
    "honeywell-uberheat-ceramic-heater", "dreame-l10s-ultra-robot-vacuum", "instant-pot-duo-plus",
    "keurig-k-elite-coffee-maker", "ninja-professional-plus-blender", "cosori-pro-ii-air-fryer",
    "kitchenaid-artisan-stand-mixer", "anker-737-power-bank", "belkin-3-in-1-wireless-charger",
    "jackery-portable-power-station-240", "nekteck-60w-usb-c-charger", "baseus-65w-gan3-pro-station",
    "sony-playstation-5-console", "nintendo-switch-oled-model", "xbox-series-x-console",
    "steelseries-arctis-nova-pro", "meta-quest-3-vr-headset", "rexing-v1-dash-cam",
    "noco-boost-plus-gb40", "astroai-digital-tire-inflator", "garmin-drivesmart-65-gps",
    "garmin-instinct-2-smartwatch", "oral-b-io-series-9", "waterpik-aquarius-water-flosser",
    "philips-norelco-oneblade-pro", "theragun-prime-massage-gun", "braun-series-9-pro-shaver",
    "epson-ecotank-et-2800", "brother-p-touch-ptd210", "canon-canoscan-lide-300",
    "texas-instruments-ti-84-plus", "hp-12c-financial-calculator", "philips-hue-starter-kit",
    "nanoleaf-shapes-hexagons", "govee-rgbic-led-strip-lights", "lifx-color-a19-bulb",
    "kasa-smart-light-switch"
];

$missing_files = 0;
foreach ($slugs as $slug) {
    $file_path = $target_dir . "/" . $slug . ".jpg";
    if (!file_exists($file_path)) {
        echo "   [MISSING] File not found: wp-content/uploads/2022/11/{$slug}.jpg\n";
        $missing_files++;
    }
}
if ($missing_files == 0) {
    echo "   [OK] All 50 image files are physically present on disk.\n";
} else {
    echo "   [ACTION REQUIRED] Missing {$missing_files} image files. Make sure the python download scripts ran inside the correct directory where 'wp-content' is located.\n";
}
echo "\n";

// 4. Query WordPress products and their attachments
echo "4. Checking Product Image registrations in Database:\n";
$products = get_posts(array(
    'post_type' => 'product',
    'numberposts' => 10,
    'post_status' => 'publish'
));

if (empty($products)) {
    echo "   [WARNING] No products found in WooCommerce database. Run the seeder!\n";
} else {
    echo "   Checking first 10 products:\n";
    foreach ($products as $p) {
        $p_id = $p->ID;
        $title = $p->post_title;
        $thumbnail_id = get_post_thumbnail_id($p_id);
        
        if (!$thumbnail_id) {
            echo "   [NO IMAGE ID] Product '{$title}' (ID: {$p_id}) has no thumbnail ID registered.\n";
        } else {
            $image_url = wp_get_attachment_image_url($thumbnail_id, 'full');
            $attached_file = get_post_meta($thumbnail_id, '_wp_attached_file', true);
            echo "   [OK] Product '{$title}' (ID: {$p_id}) -> Image ID: {$thumbnail_id}\n";
            echo "        Attached File Meta: '{$attached_file}'\n";
            echo "        Image URL: '{$image_url}'\n";
        }
    }
}
echo "\n=== END OF REPORT ===\n";
