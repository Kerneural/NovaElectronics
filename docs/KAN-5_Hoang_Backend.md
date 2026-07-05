# KAN-5: [BACKEND] Triển Khai Seeding Dữ Liệu, Bộ Định Tuyến Pretty Links và Đếm Click

Nâng cấp mã nguồn child theme `functions.php` và xây dựng mã nguồn tự động hóa database nhằm thiết lập hệ thống định tuyến Pretty Links cục bộ (`/go/product-slug`), chuyển hướng người dùng an toàn sang các liên kết affiliate (HTTP 307 Redirect) và ghi nhận số lượt click vào bảng cơ sở dữ liệu `wp_postmeta`.

---

### 🛠️ Yêu cầu triển khai chi tiết (Implementation Requirements)

#### 🔧 1. Lập trình tệp tự động hóa dữ liệu `seed_affiliate_products.py`

Kịch bản Python này nằm tại thư mục gốc của dự án, tự động tương tác với container `wp-cli` trong Docker để thiết lập môi trường dữ liệu đồng bộ.

**Mã nguồn tham khảo cho kịch bản Seeder (`seed_affiliate_products.py`)**:
```python
import subprocess
import json
import os

def run_wp_cli(args):
    cmd = ["docker", "compose", "run", "--rm", "wp-cli"] + args
    result = subprocess.run(cmd, capture_output=True, text=True, shell=True)
    if result.returncode == 0:
        return result.stdout.strip()
    else:
        print(f"Lỗi lệnh WP-CLI: {result.stderr}")
        return None

# 1. Xóa sạch sản phẩm cũ để làm trống DB
print("Dọn dẹp database...")
run_wp_cli(["wp", "post", "delete", "$(wp post list --post_type=product --format=ids)", "--force"])
run_wp_cli(["wp", "term", "list", "product_cat", "--field=term_id", "|", "xargs", "wp", "term", "delete", "product_cat"])

# 2. Khởi tạo 10 danh mục tiếng Anh mục tiêu
categories = [
    {"name": "Electronics & Computer", "slug": "electronics-computer"},
    {"name": "Smart Home & Security", "slug": "smart-home-security"},
    {"name": "Home Appliances", "slug": "home-appliances"},
    {"name": "Kitchen & Dining", "slug": "kitchen-dining"},
    {"name": "Power & Charging", "slug": "power-charging"},
    {"name": "Gaming & Entertainment", "slug": "gaming-entertainment"},
    {"name": "Automotive & Outdoor", "slug": "automotive-outdoor"},
    {"name": "Health & Personal Care", "slug": "health-personal-care"},
    {"name": "Office Electronics", "slug": "office-electronics"},
    {"name": "Smart Lighting & Ambient", "slug": "smart-lighting"}
]

cat_ids = {}
for cat in categories:
    res = run_wp_cli(["wp", "term", "create", "product_cat", cat["name"], f"--slug={cat['slug']}", "--porcelain"])
    if res and res.isdigit():
        cat_ids[cat["slug"]] = int(res)
        print(f"Đã tạo danh mục {cat['name']} - ID: {res}")

# 3. Danh sách 5 sản phẩm/mỗi danh mục kèm mock affiliate URL và button text tùy biến
# Ví dụ cấu trúc dữ liệu seed mẫu:
products_to_seed = [
    {
        "title": "ASUS ROG Zephyrus G14 Gaming Laptop",
        "slug": "asus-rog-zephyrus-g14",
        "price": 1499,
        "cat_slug": "electronics-computer",
        "aff_url": "https://www.amazon.com/dp/B0D15ROG14",
        "btn_text": "Check Latest Price",
        "img": "tivi-lg-60uq8150psb.jpg" # Hoàng ánh xạ hình ảnh phù hợp trong uploads/2022/11/
    },
    # Lặp lại cho 50 sản phẩm (5 sản phẩm cho 10 danh mục)
]

for p in products_to_seed:
    # Tạo sản phẩm dạng external (affiliate)
    res_id = run_wp_cli([
        "wp", "wc", "product", "create",
        f"--name={p['title']}",
        f"--slug={p['slug']}",
        f"--regular_price={p['price']}",
        "--status=publish",
        "--type=external",
        "--porcelain"
    ])
    
    if res_id and res_id.isdigit():
        post_id = int(res_id)
        # Gán danh mục
        run_wp_cli(["wp", "post", "term", "set", str(post_id), "product_cat", str(cat_ids[p['cat_slug']])])
        # Thiết lập meta dữ liệu affiliate
        run_wp_cli(["wp", "post", "meta", "update", str(post_id), "_product_url", p['aff_url']])
        run_wp_cli(["wp", "post", "meta", "update", str(post_id), "_button_text", p['btn_text']])
        run_wp_cli(["wp", "post", "meta", "update", str(post_id), "_affiliate_clicks", "0"])
        
        # Đính kèm ảnh từ WordPress Media
        # Hoàng viết logic tìm ID hình ảnh dựa trên tên tệp của p['img'] rồi gắn _thumbnail_id
        print(f"Đã tạo sản phẩm: {p['title']} ID: {post_id}")
```

#### 🔧 2. Tùy biến định tuyến và ghi nhận lượt click (`src/wp-content/themes/dienmay8-clone/functions.php`)

Hoàng tích hợp các đoạn mã PHP sau để tạo router cloaking link và tăng click count khi user bấm liên kết.

```php
// 2.1. Đăng ký rewrite rule cho đường dẫn Pretty Link dạng /go/product-slug
add_action('init', function() {
    add_rewrite_rule('^go/([^/]+)/?', 'index.php?affiliate_go=$matches[1]', 'top');
});

// 2.2. Khai báo biến truy vấn với WordPress
add_filter('query_vars', function($vars) {
    $vars[] = 'affiliate_go';
    return $vars;
});

// 2.3. Intercept request để thực hiện chuyển hướng và tăng lượt click
add_action('template_redirect', function() {
    $slug = get_query_var('affiliate_go');
    if ($slug) {
        $product_post = get_page_by_path($slug, OBJECT, 'product');
        if ($product_post) {
            $product_id = $product_post->ID;
            $product = wc_get_product($product_id);
            if ($product && $product->is_type('external')) {
                $target_url = $product->get_product_url(); // Lấy link affiliate thực tế (_product_url)
                
                // Tăng biến đếm lượt click và lưu lại
                $clicks = (int)get_post_meta($product_id, '_affiliate_clicks', true);
                update_post_meta($product_id, '_affiliate_clicks', $clicks + 1);
                
                // Chuyển hướng 307
                wp_redirect($target_url, 307);
                exit;
            }
        }
        // Dự phòng: Nếu không khớp sản phẩm, chuyển về trang chủ
        wp_redirect(home_url(), 302);
        exit;
    }
});

// 2.4. Lọc URL mua hàng ở giao diện client để sử dụng Pretty Link
add_filter('woocommerce_product_add_to_cart_url', function($url, $product) {
    if ($product->is_type('external')) {
        return home_url('/go/' . $product->get_slug());
    }
    return $url;
}, 10, 2);
```

#### 🔧 3. Hiển thị click count trong admin (`src/wp-content/themes/dienmay8-clone/functions.php`)

Đảm bảo hiển thị và sắp xếp được số lượt click trực tiếp trên màn hình danh sách sản phẩm.

```php
// 3.1. Thêm cột Aff Clicks vào bảng quản trị sản phẩm
add_filter('manage_edit-product_columns', function($columns) {
    $columns['affiliate_clicks'] = 'Aff Clicks';
    return $columns;
});

// 3.2. Đọc giá trị click count từ database postmeta hiển thị ra bảng
add_action('manage_product_posts_custom_column', function($column, $post_id) {
    if ($column === 'affiliate_clicks') {
        $clicks = get_post_meta($post_id, '_affiliate_clicks', true);
        echo $clicks ? esc_html($clicks) : '0';
    }
}, 10, 2);

// 3.3. Kích hoạt tính năng sắp xếp khi nhấn tiêu đề cột
add_filter('manage_edit-product_sortable_columns', function($columns) {
    $columns['affiliate_clicks'] = 'affiliate_clicks';
    return $columns;
});

// 3.4. Định nghĩa logic sắp xếp truy vấn trong database
add_action('pre_get_posts', function($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('orderby') === 'affiliate_clicks') {
        $query->set('meta_key', '_affiliate_clicks');
        $query->set('orderby', 'meta_value_num');
    }
});
```

---

### ✅ Tiêu chí hoàn thành (DoD - Definition of Done)

*   [ ] Chạy thành công tệp lệnh `seed_affiliate_products.py` và hiển thị đầy đủ 50 sản phẩm trên WordPress Admin.
*   [ ] Khi kiểm tra bảng `wp_postmeta`, các trường `_product_url`, `_button_text` và `_affiliate_clicks` được tạo đúng giá trị.
*   [ ] Truy cập đường dẫn `http://localhost:8080/go/{product-slug}` chuyển hướng thành công đến mock link affiliate bằng mã HTTP 307.
*   [ ] Giá trị `_affiliate_clicks` tăng chính xác theo số lượt click thử nghiệm thực tế.
*   [ ] Cột "Aff Clicks" hiển thị đúng số lượt click và hoạt động sắp xếp tăng/giảm dần trơn tru mà không lỗi cú pháp PHP.
