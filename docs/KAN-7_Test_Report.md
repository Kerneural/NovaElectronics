# Báo Cáo Kết Quả Kiểm Thử (Test Report) - KAN-7

Tệp này ghi nhận chi tiết kết quả thực hiện kiểm thử cho task **KAN-7: [TESTING] Kiểm Thử Quy Trình Tích Hợp Pretty Links và Đồng Bộ Dữ Liệu Affiliate** đối với các cấu phần Backend đã triển khai.

---

## 📊 1. Kiểm thử Seeder dữ liệu (`seed_affiliate_products.py`)
*   **Thực thi**: Chạy thành công kịch bản `python seed_affiliate_products.py`.
*   **Kết quả**:
    *   Xóa sạch toàn bộ sản phẩm và danh mục sản phẩm cũ thành công.
    *   Tự động tạo 10 danh mục tiếng Anh mục tiêu.
    *   Khởi tạo thành công **50/50 sản phẩm** WooCommerce dạng `external` (Product Affiliate).
    *   Tất cả các sản phẩm được gán đúng trường postmeta khởi tạo `_affiliate_clicks = 0`.
    *   Ánh xạ chính xác các ID hình ảnh đại diện từ Media Library.

---

## 🔗 2. Kiểm thử bộ định tuyến Pretty Links & Ghi nhận lượt click
*   **Tình huống 1: Click link rút gọn hợp lệ**
    *   **Thực hiện**: Gửi request qua `curl.exe` đến đường dẫn `/go/asus-rog-zephyrus-g14/`.
    *   **Kết quả phản hồi**:
        ```http
        HTTP/1.1 307 Temporary Redirect
        Connection: close
        X-Redirect-By: WordPress
        Location: https://www.amazon.com/s?k=ASUS+ROG+Zephyrus+G14+Gaming+Laptop
        ```
        👉 **Đạt**: Chuyển hướng 307 an toàn đến đúng Amazon Affiliate Link gốc của sản phẩm.
    *   **Đếm click count**:
        *   Trước khi click: `_affiliate_clicks = 0`
        *   Sau 1 lượt click đầu tiên: `_affiliate_clicks = 1`
        *   Sau khi chạy lặp mô phỏng 4 lượt click liên tục: `_affiliate_clicks = 5`
        *   Được xác thực trực tiếp qua WP-CLI meta query:
            ```bash
            wp post meta get 77 _affiliate_clicks
            # Kết quả trả về: 5
            ```
        👉 **Đạt**: Click count cộng dồn chính xác theo thời gian thực.

*   **Tình huống 2: Truy cập slug không tồn tại**
    *   **Thực hiện**: Gửi request qua `curl.exe` đến đường dẫn `/go/san-pham-khong-ton-tai/`.
    *   **Kết quả phản hồi**:
        ```http
        HTTP/1.1 302 Found
        Connection: close
        X-Redirect-By: WordPress
        Location: http://localhost:8080
        ```
        👉 **Đạt**: Fallback an toàn chuyển hướng 302 người dùng về trang chủ, tránh hiển thị trang lỗi 404.

---

## 🖥️ 3. Kiểm thử trang quản lý Admin Dashboard
*   **Thực hiện**: Chạy code PHP kiểm tra đăng ký filter của Flatsome Child Theme.
*   **Kết quả**:
    *   `COLUMN_OK`: Cột `affiliate_clicks` (Aff Clicks) đã được đăng ký thành công vào hook `manage_edit-product_columns`.
    *   `SORT_OK`: Cột đã được thiết lập tính năng sắp xếp (Sortable) trong admin và sửa đổi truy vấn cơ sở dữ liệu `pre_get_posts` thành công.
    👉 **Đạt**: Quản trị viên có thể xem và click sắp xếp danh sách sản phẩm theo số lượt click một cách mượt mà.

---

## ✅ Kết luận
Cấu phần Backend phục vụ tính năng Pretty Links, Tracking Clicks và Seeding Dữ Liệu đã hoàn thành **100% tiêu chí chấp nhận (DoD)** và sẵn sàng bàn giao.
