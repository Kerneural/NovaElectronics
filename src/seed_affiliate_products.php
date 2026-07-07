<?php
// seed_affiliate_products.php
// Evaluated by WP-CLI or direct browser access
require_once __DIR__ . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Disable terms caching to prevent issues
wp_defer_term_counting(true);
wp_defer_comment_counting(true);

// 1. Delete all products
echo "Deleting old products...\n";
$products = get_posts(array(
    'post_type' => 'product',
    'numberposts' => -1,
    'post_status' => 'any',
    'fields' => 'ids'
));
foreach ($products as $p_id) {
    wp_delete_post($p_id, true);
}
echo "Old products deleted.\n";

// 2. Delete all product categories except 'uncategorized'
echo "Deleting old product categories...\n";
$terms = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false
));
foreach ($terms as $term) {
    if ($term->slug !== 'uncategorized') {
        wp_delete_term($term->term_id, 'product_cat');
    }
}
echo "Old categories deleted.\n";

// 3. Create target categories
$categories = [
    ["name" => "Electronics & Computer", "slug" => "electronics-computer"],
    ["name" => "Smart Home & Security", "slug" => "smart-home-security"],
    ["name" => "Home Appliances", "slug" => "home-appliances"],
    ["name" => "Kitchen & Dining", "slug" => "kitchen-dining"],
    ["name" => "Power & Charging", "slug" => "power-charging"],
    ["name" => "Gaming & Entertainment", "slug" => "gaming-entertainment"],
    ["name" => "Automotive & Outdoor", "slug" => "automotive-outdoor"],
    ["name" => "Health & Personal Care", "slug" => "health-personal-care"],
    ["name" => "Office Electronics", "slug" => "office-electronics"],
    ["name" => "Smart Lighting & Ambient", "slug" => "smart-lighting"]
];

$cat_ids = [];
echo "Creating target categories...\n";
foreach ($categories as $cat) {
    $term = wp_insert_term($cat["name"], 'product_cat', array('slug' => $cat["slug"]));
    if (!is_wp_error($term)) {
        $cat_ids[$cat["slug"]] = $term['term_id'];
    } else {
        $existing = get_term_by('slug', $cat["slug"], 'product_cat');
        if ($existing) {
            $cat_ids[$cat["slug"]] = $existing->term_id;
        }
    }
}
echo "Categories created.\n";

// 4. Seed 50 products with local Unsplash images
$product_specs = [
    "electronics-computer" => [
        ["ASUS ROG Zephyrus G14 Gaming Laptop", "asus-rog-zephyrus-g14", 1499, "Check Latest Price"],
        ["Apple MacBook Air M2 Laptop", "apple-macbook-air-m2", 999, "Buy on Amazon"],
        ["Dell XPS 13 Ultrabook", "dell-xps-13-ultrabook", 1199, "Check Latest Price"],
        ["Samsung 32-Inch Curved Gaming Monitor", "samsung-32-inch-gaming-monitor", 349, "Buy on Amazon"],
        ["Logitech MX Master 3S Wireless Mouse", "logitech-mx-master-3s-mouse", 99, "Check Latest Price"]
    ],
    "smart-home-security" => [
        ["Ring Video Doorbell Wired", "ring-video-doorbell-wired", 64, "Buy on Amazon"],
        ["Google Nest Learning Thermostat", "google-nest-learning-thermostat", 249, "Check Latest Price"],
        ["Eufy Security SoloCam S40", "eufy-security-solocam-s40", 199, "Buy on Amazon"],
        ["August Wi-Fi Smart Lock", "august-wi-fi-smart-lock", 229, "Check Latest Price"],
        ["TP-Link Tapo Smart Plug", "tp-link-tapo-smart-plug", 19, "Buy on Amazon"]
    ],
    "home-appliances" => [
        ["LG Front Load Smart Washer", "lg-front-load-smart-washer", 899, "Check Latest Price"],
        ["Dyson V15 Detect Cordless Vacuum", "dyson-v15-detect-vacuum", 749, "Buy on Amazon"],
        ["Honeywell HEPA Air Purifier", "honeywell-hepa-air-purifier", 229, "Check Latest Price"],
        ["Honeywell UberHeat Ceramic Heater", "honeywell-uberheat-ceramic-heater", 39, "Buy on Amazon"],
        ["Dreame L10s Ultra Robot Vacuum", "dreame-l10s-ultra-robot-vacuum", 999, "Check Latest Price"]
    ],
    "kitchen-dining" => [
        ["Instant Pot Duo Plus 9-in-1", "instant-pot-duo-plus", 129, "Buy on Amazon"],
        ["Keurig K-Elite Single Serve Coffee Maker", "keurig-k-elite-coffee-maker", 189, "Check Latest Price"],
        ["Ninja Professional Plus Blender", "ninja-professional-plus-blender", 119, "Buy on Amazon"],
        ["Cosori Pro II Air Fryer 5.8QT", "cosori-pro-ii-air-fryer", 119, "Check Latest Price"],
        ["KitchenAid Artisan Series Stand Mixer", "kitchenaid-artisan-stand-mixer", 449, "Buy on Amazon"]
    ],
    "power-charging" => [
        ["Anker 737 Power Bank 24K", "anker-737-power-bank", 149, "Check Latest Price"],
        ["Belkin 3-in-1 Wireless Charger Stand", "belkin-3-in-1-wireless-charger", 149, "Buy on Amazon"],
        ["Jackery Portable Power Station 240", "jackery-portable-power-station-240", 199, "Check Latest Price"],
        ["Nekteck 60W USB-C Wall Charger", "nekteck-60w-usb-c-charger", 25, "Buy on Amazon"],
        ["Baseus 65W GaN3 Pro Charging Station", "baseus-65w-gan3-pro-station", 49, "Check Latest Price"]
    ],
    "gaming-entertainment" => [
        ["Sony PlayStation 5 Console", "sony-playstation-5-console", 499, "Check Latest Price"],
        ["Nintendo Switch OLED Model", "nintendo-switch-oled-model", 349, "Buy on Amazon"],
        ["Xbox Series X Console", "xbox-series-x-console", 499, "Check Latest Price"],
        ["SteelSeries Arctis Nova Pro Wireless", "steelseries-arctis-nova-pro", 349, "Buy on Amazon"],
        ["Meta Quest 3 VR Headset", "meta-quest-3-vr-headset", 499, "Check Latest Price"]
    ],
    "automotive-outdoor" => [
        ["Rexing V1 Dash Cam 4K", "rexing-v1-dash-cam", 99, "Buy on Amazon"],
        ["NOCO Boost Plus GB40 Jump Starter", "noco-boost-plus-gb40", 99, "Check Latest Price"],
        ["AstroAI Digital Tire Inflator", "astroai-digital-tire-inflator", 29, "Buy on Amazon"],
        ["Garmin DriveSmart 65 GPS", "garmin-drivesmart-65-gps", 169, "Check Latest Price"],
        ["Garmin Instinct 2 Outdoor Smartwatch", "garmin-instinct-2-smartwatch", 299, "Buy on Amazon"]
    ],
    "health-personal-care" => [
        ["Oral-B iO Series 9 Electric Toothbrush", "oral-b-io-series-9", 249, "Check Latest Price"],
        ["Waterpik Aquarius Water Flosser", "waterpik-aquarius-water-flosser", 99, "Buy on Amazon"],
        ["Philips Norelco OneBlade Pro", "philips-norelco-oneblade-pro", 79, "Check Latest Price"],
        ["Theragun Prime Quiet Massage Gun", "theragun-prime-massage-gun", 299, "Check Latest Price"],
        ["Braun Series 9 Pro Electric Shaver", "braun-series-9-pro-shaver", 299, "Check Latest Price"]
    ],
    "office-electronics" => [
        ["Epson EcoTank ET-2800 Printer", "epson-ecotank-et-2800", 199, "Buy on Amazon"],
        ["Brother P-touch PTD210 Label Maker", "brother-p-touch-ptd210", 39, "Check Latest Price"],
        ["Canon CanoScan Lide 300 Scanner", "canon-canoscan-lide-300", 69, "Buy on Amazon"],
        ["Texas Instruments TI-84 Plus CE", "texas-instruments-ti-84-plus", 129, "Check Latest Price"],
        ["HP 12C Financial Calculator", "hp-12c-financial-calculator", 69, "Buy on Amazon"]
    ],
    "smart-lighting" => [
        ["Philips Hue White & Color Ambiance Kit", "philips-hue-starter-kit", 189, "Buy on Amazon"],
        ["Nanoleaf Shapes Hexagons Smarter Kit", "nanoleaf-shapes-hexagons", 199, "Check Latest Price"],
        ["Govee RGBIC LED Strip Lights 32.8ft", "govee-rgbic-led-strip-lights", 35, "Buy on Amazon"],
        ["LIFX Color A19 1100 Lumens", "lifx-color-a19-bulb", 49, "Check Latest Price"],
        ["Kasa Smart Light Switch HS200", "kasa-smart-light-switch", 19, "Buy on Amazon"]
    ]
];

echo "Seeding products...\n";
$success_count = 0;
foreach ($product_specs as $cat_slug => $specs) {
    $cat_id = isset($cat_ids[$cat_slug]) ? $cat_ids[$cat_slug] : null;
    if (!$cat_id) continue;

    foreach ($specs as $spec) {
        $title = $spec[0];
        $slug = $spec[1];
        $price = $spec[2];
        $btn_text = $spec[3];
        $aff_url = "https://www.amazon.com/s?k=" . urlencode($title);

        // Create product using WooCommerce Product class
        $product = new WC_Product_External();
        $product->set_name($title);
        $product->set_slug($slug);
        $product->set_regular_price($price);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        
        // External product properties
        $product->set_product_url($aff_url);
        $product->set_button_text($btn_text);
        
        $product_id = $product->save();

        if ($product_id) {
            // Set category
            wp_set_object_terms($product_id, array($cat_id), 'product_cat');
            
            // Set metadata
            update_post_meta($product_id, '_affiliate_clicks', 0);
            
            // Look for the local image file inside container
            $local_file = "/var/www/html/wp-content/uploads/2022/11/" . $slug . ".jpg";
            if (file_exists($local_file)) {
                // Check if attachment with this name already exists
                $existing_attach = get_posts(array(
                    'post_type' => 'attachment',
                    'name' => $slug,
                    'posts_per_page' => 1,
                    'fields' => 'ids'
                ));
                
                if (!empty($existing_attach)) {
                    $attach_id = $existing_attach[0];
                } else {
                    // Register attachment
                    $filetype = wp_check_filetype(basename($local_file), null);
                    $attachment = array(
                        'guid'           => home_url('/wp-content/uploads/2022/11/' . basename($local_file)), 
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => $slug,
                        'post_name'      => $slug,
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $local_file);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $local_file);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }
                
                set_post_thumbnail($product_id, $attach_id);
                echo "  [OK] Created product '{$title}' with unique image (ID: {$product_id})\n";
            } else {
                echo "  [OK] Created product '{$title}' without image (ID: {$product_id}) - Local file not found: {$local_file}\n";
            }
            $success_count++;
        }
    }
}

// Re-enable term counting and trigger recount
wp_defer_term_counting(false);
wp_defer_comment_counting(false);

echo "Seeding completed: {$success_count}/50 products created\n";
