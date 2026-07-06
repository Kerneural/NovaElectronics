<?php
// Custom theme functions
// Active Development: Custom theme hooks, shortcodes, and route overrides.

// ==========================================
// 1. Pretty Links Router for Affiliate Products
// ==========================================

// Register rewrite rule for /go/product-slug
add_action('init', function() {
    add_rewrite_rule('^go/([^/]+)/?', 'index.php?affiliate_go=$matches[1]', 'top');
});

// Declare affiliate_go query variable
add_filter('query_vars', function($vars) {
    $vars[] = 'affiliate_go';
    return $vars;
});

// Redirect template hook
add_action('template_redirect', function() {
    $slug = get_query_var('affiliate_go');
    if ($slug) {
        $product_post = get_page_by_path($slug, OBJECT, 'product');
        if ($product_post) {
            $product_id = $product_post->ID;
            $product = wc_get_product($product_id);
            if ($product && $product->is_type('external')) {
                $target_url = $product->get_product_url(); // Retrieve _product_url meta
                
                // Increment click count postmeta
                $clicks = (int)get_post_meta($product_id, '_affiliate_clicks', true);
                update_post_meta($product_id, '_affiliate_clicks', $clicks + 1);
                
                // Force close connection to prevent client socket hanging
                session_write_close();
                header("Connection: close");
                
                // HTTP 307 Temporary Redirect
                wp_redirect($target_url, 307);
                exit;
            }
        }
        // Fallback for non-existent products: HTTP 302 Redirect to Homepage
        session_write_close();
        header("Connection: close");
        wp_redirect(home_url(), 302);
        exit;
    }
});

// Overwrite woocommerce cart/purchase URL to point to /go/slug
add_filter('woocommerce_product_add_to_cart_url', function($url, $product) {
    if ($product->is_type('external')) {
        return home_url('/go/' . $product->get_slug() . '/');
    }
    return $url;
}, 10, 2);


// ==========================================
// 2. Product Admin Columns for Click Counts
// ==========================================

// Register new column
add_filter('manage_edit-product_columns', function($columns) {
    $columns['affiliate_clicks'] = 'Aff Clicks';
    return $columns;
});

// Render column value
add_action('manage_product_posts_custom_column', function($column, $post_id) {
    if ($column === 'affiliate_clicks') {
        $clicks = get_post_meta($post_id, '_affiliate_clicks', true);
        echo $clicks ? esc_html($clicks) : '0';
    }
}, 10, 2);

// Make column sortable
add_filter('manage_edit-product_sortable_columns', function($columns) {
    $columns['affiliate_clicks'] = 'affiliate_clicks';
    return $columns;
});

// Implement sorting query modification
add_action('pre_get_posts', function($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('orderby') === 'affiliate_clicks') {
        $query->set('meta_key', '_affiliate_clicks');
        $query->set('orderby', 'meta_value_num');
    }
});
