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


// ==========================================
// 3. Static Templates Request Router
// ==========================================
add_filter('template_include', function($template) {
    // Extract normalized request path
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
    
    // Check if path matches crawled routes
    $routes_file = get_stylesheet_directory() . '/routes.json';
    if (file_exists($routes_file)) {
        $routes = json_decode(file_get_contents($routes_file), true);
        if (is_array($routes) && isset($routes[$path])) {
            $template_file = get_stylesheet_directory() . '/' . $routes[$path];
            if (file_exists($template_file)) {
                return $template_file;
            }
        }
    }
    return $template;
});

if ( ! function_exists( 'dienmay8_get_product_grid' ) ) {
    function dienmay8_get_product_grid( $category_slug, $limit = 5 ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div class="col"><div class="col-inner text-center"><p>WooCommerce is required for product display.</p></div></div>';
        }

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => absint( $limit ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => array( sanitize_title( $category_slug ) ),
                ),
            ),
        );

        $loop = new WP_Query( $args );
        ob_start();

        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) {
                $loop->the_post();
                global $product;
                if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
                    $product = wc_get_product( get_the_ID() );
                }

                $button_text = '';
                if ( $product && is_callable( array( $product, 'get_button_text' ) ) ) {
                    $button_text = $product->get_button_text();
                }
                if ( empty( $button_text ) ) {
                    $button_text = 'Check Latest Price';
                }

                $image_id  = $product ? $product->get_image_id() : 0;
                $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '/wp-content/uploads/2022/11/lighting.svg';
                $slug      = $product ? $product->get_slug() : get_post_field( 'post_name', get_the_ID() );
                $pretty_link = home_url( '/go/' . sanitize_title( $slug ) );
                ?>
                <div class="col">
                    <div class="col-inner">
                        <div class="product-small box product_home has-hover box-normal box-text-bottom">
                            <div class="box-image">
                                <div class="image-cover" style="padding-top:100%;">
                                    <a href="<?php the_permalink(); ?>">
                                        <img width="600" height="450" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                                    </a>
                                </div>
                            </div>
                            <div class="box-text text-center">
                                <div class="title-wrapper">
                                    <p class="name product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                                </div>
                                <div class="price-wrapper">
                                    <span class="price"><?php echo $product ? $product->get_price_html() : ''; ?></span>
                                </div>
                                <div class="add-to-cart-button" style="margin-top: 10px;">
                                    <?php 
                                    // Use the registered shortcode to render the button
                                    echo do_shortcode( sprintf( 
                                        '[affiliate_button url="%s" label="%s" style="primary" size="small" newtab="yes" nofollow="yes"]', 
                                        esc_url( $pretty_link ), 
                                        esc_attr( $button_text ) 
                                    ) ); 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="col">
                <div class="col-inner text-center">
                    <p>No products found for this category.</p>
                </div>
            </div>
            <?php
        }

        wp_reset_postdata();
        return ob_get_clean();
    }
}

if ( ! function_exists( 'dienmay8_affiliate_button_shortcode' ) ) {
    function dienmay8_affiliate_button_shortcode( $atts ) {
        $a = shortcode_atts(
            array(
                'id'      => '',
                'url'     => '',
                'label'   => 'Buy Now',
                'style'   => 'primary',
                'icon'    => '',
                'color'   => '',
                'size'    => 'medium',
                'nofollow'=> 'yes',
                'newtab'  => 'yes',
                'price'   => 'yes',
            ),
            $atts,
            'affiliate_button'
        );

        $url = '';
        if ( ! empty( $a['url'] ) ) {
            $url = esc_url_raw( $a['url'] );
        } elseif ( ! empty( $a['id'] ) ) {
            $url = get_permalink( absint( $a['id'] ) );
        }

        if ( empty( $url ) ) {
            return '';
        }

        $classes = array( 'button', 'affiliate-button', sanitize_html_class( $a['style'] ) );
        $size = strtolower( $a['size'] );
        if ( in_array( $size, array( 'small', 'medium', 'large', 'xlarge' ), true ) ) {
            $classes[] = 'is-' . sanitize_html_class( $size );
        }

        $inline_style = '';
        if ( ! empty( $a['color'] ) ) {
            $color = sanitize_hex_color_no_hash( $a['color'] );
            if ( $color ) {
                $inline_style = sprintf( 'background-color:#%1$s;border-color:#%1$s;color:#ffffff;', $color );
            } else {
                $inline_style = 'color:' . esc_attr( $a['color'] ) . ';';
            }
        }

        $nofollow = 'noindex';
        if ( strtolower( $a['nofollow'] ) === 'yes' ) {
            $nofollow = 'nofollow sponsored';
        }

        $target = strtolower( $a['newtab'] ) === 'yes' ? '_blank' : '_self';
        $icon_html = '';
        if ( ! empty( $a['icon'] ) ) {
            $icon_html = '<i class="' . esc_attr( $a['icon'] ) . '" aria-hidden="true"></i> ';
        }

        $price_html = '';
        if ( strtolower( $a['price'] ) === 'yes' && ! empty( $a['id'] ) ) {
            $product = wc_get_product( absint( $a['id'] ) );
            if ( $product ) {
                $price_html = '<span class="affiliate-button-price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
            }
        }

        return sprintf(
            '<a href="%1$s" class="%2$s" style="%3$s" rel="%4$s" target="%5$s">%6$s<span>%7$s</span></a>%8$s',
            esc_url( $url ),
            esc_attr( implode( ' ', array_filter( $classes ) ) ),
            esc_attr( $inline_style ),
            esc_attr( $nofollow ),
            esc_attr( $target ),
            $icon_html,
            esc_html( $a['label'] ),
            $price_html
        );
    }
    add_shortcode( 'affiliate_button', 'dienmay8_affiliate_button_shortcode' );
}

