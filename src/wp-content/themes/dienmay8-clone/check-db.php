<?php
// Load WordPress bootstrap
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');

header('Content-Type: text/plain');

echo "--- WOOCOMMERCE PRODUCT CATEGORIES DIAGNOSTIC ---\n\n";

if (!class_exists('WooCommerce')) {
    echo "ERROR: WooCommerce is not active!\n";
    exit;
}

// 1. List all product categories in database
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
));

echo "Available categories in WooCommerce:\n";
if (empty($categories)) {
    echo "No categories found!\n";
} else {
    foreach ($categories as $cat) {
        echo "- ID: {$cat->term_id}, Name: '{$cat->name}', Slug: '{$cat->slug}', Count: {$cat->count}\n";
    }
}

echo "\n--- PRODUCT TO CATEGORY RELATIONSHIPS ---\n\n";

// 2. List first 10 products and their assigned categories
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 20,
);
$products = get_posts($args);

if (empty($products)) {
    echo "No products found in database!\n";
} else {
    echo "Products list:\n";
    foreach ($products as $p) {
        $terms = wp_get_post_terms($p->ID, 'product_cat');
        $cat_names = array();
        foreach ($terms as $t) {
            $cat_names[] = "{$t->name} (slug: {$t->slug})";
        }
        $cats_str = empty($cat_names) ? 'Uncategorized' : implode(', ', $cat_names);
        echo "- Product ID: {$p->ID}, Title: '{$p->post_title}', Slug: '{$p->post_name}', Categories: [{$cats_str}]\n";
    }
}
