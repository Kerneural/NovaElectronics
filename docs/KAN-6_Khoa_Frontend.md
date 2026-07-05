# KAN-6: [FRONTEND] Dịch Giao Diện, Menu và Tích Hợp Nút Mua Hàng Tùy Chỉnh

Triển khai cấu trúc giao diện tiếng Anh toàn diện cho trang chủ và các trang con của cửa hàng, đồng thời thay thế nút "Add to Cart" truyền thống bằng nút tiếp thị liên kết (Affiliate Button) có thể tùy biến kiểu dáng, màu sắc và icon theo hệ thống thiết kế Flatsome.

---

### 🛠️ Yêu cầu triển khai chi tiết (Implementation Requirements)

#### 🔧 1. Cấu hình lại thanh Danh mục sản phẩm dọc và ngang (`src/wp-content/themes/dienmay8-clone/index.php`)

Khoa thay đổi menu dọc (sidebar) và các thanh điều hướng tĩnh tiếng Việt sang 10 danh mục tiếng Anh mục tiêu.

**Mã nguồn HTML/PHP của Menu dọc sidebar cần thay đổi (Dòng 544 - 555)**:
```html
<div class="header-vertical-menu__fly-out header-vertical-menu__fly-out--open has-shadow">
    <div class="menu-danh-muc-san-pham-container">
        <ul id="menu-danh-muc-san-pham" class="ux-nav-vertical-menu nav-vertical-fly-out">
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/electronics-computer/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/tv-monitor.png" alt="" />Electronics & Computer
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/smart-home-security/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/fridge.png" alt="" />Smart Home & Security
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/home-appliances/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/washer.png" alt="" />Home Appliances
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/kitchen-dining/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/Screenshot-2022-11-14-at-09-46-15-BEP-AN-TOAN-CHUYEN-THIET-BI-NHA-BEP-PHONG-TAM.png" alt="" />Kitchen & Dining
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/power-charging/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/air-conditioner.png" alt="" />Power & Charging
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/gaming-entertainment/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/tv-monitor.png" alt="" />Gaming & Entertainment
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/automotive-outdoor/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/air-conditioner2.png" alt="" />Automotive & Outdoor
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/health-personal-care/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/washer.png" alt="" />Health & Personal Care
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/office-electronics/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/tv-monitor.png" alt="" />Office Electronics
                </a>
            </li>
            <li class="menu-item menu-item-design-default has-icon-left">
                <a href="/product-category/smart-lighting/" class="nav-top-link">
                    <img class="ux-menu-icon" width="20" height="20" src="/wp-content/uploads/2022/11/lighting.svg" alt="" />Smart Lighting & Ambient
                </a>
            </li>
        </ul>
    </div>
</div>
```

*   **Vị trí thay thế thông tin Email liên hệ**: Tìm kiếm chuỗi `webdemo@gmail.com` ở Header (quanh dòng 200-300) và Footer (quanh dòng 2800) thay thế thành `info@dailysmartlife.com`.
*   **Vị trí sửa ô Tìm kiếm**: Sửa placeholder từ `Tìm sản phẩm bạn mong muốn ...` thành `Search for products...`.

#### 🔧 2. Triển khai cấu trúc hiển thị sản phẩm động trên Trang chủ (`src/wp-content/themes/dienmay8-clone/index.php`)

Thay vì hiển thị sản phẩm tĩnh HTML, Khoa thay thế mã nguồn thành vòng lặp PHP (`WP_Query`) để tải sản phẩm tự động từ cơ sở dữ liệu.

**Mã nguồn hiển thị danh sách sản phẩm động theo từng Danh mục**:
```php
<div class="row product_home equalize-box large-columns-5 medium-columns-3 small-columns-2 row-small">
    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 5,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'electronics-computer', // Thay đổi slug tương ứng cho mỗi mục (electronics-computer, smart-home-security, v.v.)
            ),
        ),
    );
    $loop = new WP_Query( $args );
    if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
            $pretty_link = home_url('/go/' . $product->get_slug());
            $button_text = $product->get_button_text() ? $product->get_button_text() : 'Check Latest Price';
            $image_id = $product->get_image_id();
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '/wp-content/uploads/2022/11/lighting.svg';
            ?>
            <div class="col">
                <div class="col-inner">
                    <div class="product-small box product_home has-hover box-normal box-text-bottom">
                        <div class="box-image">
                            <div class="image-cover" style="padding-top:100%;">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>">
                                    <img width="600" height="450" src="<?php echo esc_url($image_url); ?>" class="attachment-original size-original" alt="" decoding="async" loading="lazy" />
                                </a>
                            </div>
                        </div>
                        <div class="box-text text-center">
                            <div class="title-wrapper">
                                <p class="name product-title woocommerce-loop-product__title">
                                    <a href="<?php the_permalink(); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><?php the_title(); ?></a>
                                </p>
                            </div>
                            <div class="price-wrapper">
                                <span class="price"><?php echo $product->get_price_html(); ?></span>
                            </div>
                            <div class="add-to-cart-button" style="margin-top: 10px;">
                                <a href="<?php echo esc_url($pretty_link); ?>" class="button primary is-small" style="border-radius: 4px;" rel="nofollow sponsored" target="_blank">
                                    <span><?php echo esc_html($button_text); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    } else {
        echo '<p style="padding: 20px;">No products found in this category.</p>';
    }
    ?>
</div>
```

#### 🔧 3. Triển khai shortcode hiển thị nút mua hàng tùy chọn (`[affiliate_button]`) (`src/wp-content/themes/dienmay8-clone/functions.php`)

Khoa thêm đoạn mã PHP đăng ký shortcode tùy chọn giúp hiển thị nút mua hàng theo các cấu hình linh hoạt (màu sắc, icon, cỡ chữ) tương ứng với yêu cầu.

```php
add_shortcode('affiliate_button', function($atts) {
    $a = shortcode_atts(array(
        'id' => '', // ID hoặc slug sản phẩm
        'label' => '', // Ghi đè chữ trên nút
        'style' => 'primary', // primary, success, alert, warning, outline
        'icon' => '', // cart, external, none
        'color' => '', // mã màu hex tùy chọn
        'size' => 'medium', // small, medium, large
        'nofollow' => 'yes',
        'newtab' => 'yes',
        'price' => 'yes'
    ), $atts);

    if (empty($a['id'])) {
        return '<!-- Affiliate Button Error: ID/slug is required -->';
    }

    if (is_numeric($a['id'])) {
        $product = wc_get_product($a['id']);
    } else {
        $product_post = get_page_by_path($a['id'], OBJECT, 'product');
        $product = $product_post ? wc_get_product($product_post->ID) : null;
    }

    if (!$product) {
        return '<!-- Affiliate Button Error: Product not found -->';
    }

    $pretty_link = home_url('/go/' . $product->get_slug());
    $button_label = !empty($a['label']) ? $a['label'] : ($product->get_button_text() ? $product->get_button_text() : 'Check Latest Price');
    
    // Xây dựng class CSS Flatsome
    $btn_classes = array('button', 'button-affiliate');
    if ($a['style'] === 'outline') {
        $btn_classes[] = 'primary';
        $btn_classes[] = 'is-outline';
    } else {
        $btn_classes[] = $a['style'];
    }
    $btn_classes[] = $a['size'];
    
    $style_attr = !empty($a['color']) ? ' style="background-color: ' . esc_attr($a['color']) . '; border-color: ' . esc_attr($a['color']) . '; color: #fff;"' : '';
    
    $rel = array();
    if ($a['nofollow'] === 'yes') {
        $rel[] = 'nofollow';
        $rel[] = 'sponsored';
    }
    $rel_attr = !empty($rel) ? ' rel="' . implode(' ', $rel) . '"' : '';
    $target = ($a['newtab'] === 'yes') ? ' target="_blank"' : '';
    
    $icon_html = '';
    if (!empty($a['icon']) && $a['icon'] !== 'none') {
        if ($a['icon'] === 'cart') {
            $icon_html = '<i class="icon-shopping-cart" style="margin-right: 5px;"></i>';
        } elseif ($a['icon'] === 'external') {
            $icon_html = '<i class="icon-angle-right" style="margin-right: 5px;"></i>';
        }
    }
    
    $price_html = ($a['price'] === 'yes') ? '<span class="affiliate-button-price" style="margin-right: 10px; font-weight: bold;">' . $product->get_price_html() . '</span>' : '';
    
    return '<div class="affiliate-button-container" style="display: inline-flex; align-items: center;">' . 
           $price_html . 
           '<a href="' . esc_url($pretty_link) . '" class="' . esc_attr(implode(' ', $btn_classes)) . '"' . $style_attr . $rel_attr . $target . '>' .
           $icon_html . '<span>' . esc_html($button_label) . '</span>' .
           '</a></div>';
});
```

---

### ✅ Tiêu chí hoàn thành (DoD - Definition of Done)

*   [ ] Toàn bộ trang chủ hiển thị ngôn ngữ tiếng Anh, dịch đúng nghĩa và email đổi sang `info@dailysmartlife.com`.
*   [ ] Vòng lặp `WP_Query` hoạt động tốt trên trang chủ, hiển thị đủ 5 sản phẩm của mỗi danh mục.
*   [ ] Sử dụng shortcode `[affiliate_button]` trong bài viết hoặc trang sản phẩm render ra đúng cấu trúc nút kèm theo giá và chuyển hướng trơn tru.
*   [ ] Giao diện responsive của Flatsome được giữ nguyên, các nút bấm mua hàng hiển thị đúng tỷ lệ, không bị vỡ giao diện trên Mobile/Tablet.
