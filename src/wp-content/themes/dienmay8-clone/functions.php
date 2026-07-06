<?php
// Custom theme functions

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
                                    // Sử dụng shortcode đã khai báo để render nút
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

