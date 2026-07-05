# KAN-7: [TESTING] Kiểm Thử Quy Trình Tích Hợp Pretty Links và Đồng Bộ Dữ Liệu Affiliate

Thực hiện kiểm thử toàn diện giao diện trang chủ tiếng Anh, khả năng định tuyến và chuyển tiếp liên kết rút gọn Pretty Links (`/go/<slug>`) sang link affiliate gốc của nhà cung cấp, cơ chế đếm lượt click tăng dần trong cơ sở dữ liệu và khả năng quản lý hiển thị/sắp xếp của Admin Dashboard.

---

### 🛠️ Yêu cầu triển khai chi tiết (Implementation Requirements)

#### 🔧 1. Kiểm thử giao diện hiển thị và dịch thuật (Frontend Verification)
*   **Địa chỉ truy cập**: Mở trình duyệt và truy cập `http://localhost:8080/`.
*   **Nhiệm vụ kiểm thử**:
    1. Xác nhận thanh menu dọc ở sidebar hiển thị đúng 10 danh mục tiếng Anh mục tiêu từ `Electronics & Computer` đến `Smart Lighting & Ambient`.
    2. Đảm bảo toàn bộ tiêu đề các phần sản phẩm trên trang chủ đã được đổi tên sang tiếng Anh (không còn chữ `Điều hòa`, `Máy giặt`, `Tủ lạnh`... tiếng Việt).
    3. Kiểm tra địa chỉ email hiển thị ở cả chân trang (Footer) và đầu trang (Header) xem đã chuyển thành `info@dailysmartlife.com` chưa.
    4. Kiểm tra placeholder của thanh tìm kiếm ở cả hai phiên bản mobile và desktop hiển thị đúng chuỗi `Search for products...`.
    5. Đảm bảo các nút mua hàng trên các thẻ sản phẩm có nhãn nút tiếng Anh hiển thị chính xác (ví dụ: `Check Latest Price`, `Buy on Amazon`) và không bị lỗi vỡ giao diện.

#### 🔧 2. Kiểm thử bộ định tuyến và đếm lượt click (Backend Routing & Click Verification)
*   **Phương pháp thực hiện**:
    1. Rê chuột (hover) vào nút mua hàng của một sản phẩm bất kỳ trên trang chủ và trang chi tiết sản phẩm. Nhìn vào thanh trạng thái (status bar) dưới góc trái trình duyệt, xác minh đường dẫn hiển thị là `/go/<product-slug>` (ví dụ: `http://localhost:8080/go/asus-rog-zephyrus-g14`).
    2. Nhấp chuột vào nút đó. Trình duyệt phải thực hiện chuyển tiếp thành công đến link đích affiliate gốc của Amazon (ví dụ: `https://www.amazon.com/dp/...`) bằng mã HTTP 307.
    3. Mở cơ sở dữ liệu `wp_postmeta` hoặc truy cập trực tiếp trang quản trị WordPress `/wp-admin/edit.php?post_type=product`, xác nhận số lượt click của sản phẩm đó đã được tăng thêm `+1`.
    4. Thực hiện click thử nghiệm 5 lần liên tục trên cùng một sản phẩm và kiểm tra xem lượt click có cộng dồn chính xác hay không.
    5. Nhập thủ công một đường dẫn sai (ví dụ: `http://localhost:8080/go/san-pham-khong-ton-tai`). Đảm bảo hệ thống bắt được lỗi, tự động chuyển hướng người dùng về trang chủ một cách an toàn và trả về mã HTTP 302.

#### 🔧 3. Kiểm thử trang quản lý Admin Dashboard
*   **Đường dẫn truy cập**: `http://localhost:8080/wp-admin/` (Đăng nhập: `admin / adminpassword`).
*   **Nhiệm vụ kiểm thử**:
    1. Truy cập vào mục **Products** (Sản phẩm). Xác minh có sự xuất hiện của cột mới mang tên `Aff Clicks`.
    2. Kiểm tra các dòng sản phẩm hiển thị số lượt click tương ứng, đối chiếu chéo số liệu này với cơ sở dữ liệu.
    3. Nhấn vào tiêu đề cột `Aff Clicks` để kiểm tra khả năng sắp xếp (Sorting). Đảm bảo danh sách sản phẩm được sắp xếp tăng dần hoặc giảm dần theo số lượt click một cách chính xác mà không xảy ra lỗi trắng trang hay lỗi truy vấn.

---

### ✅ Tiêu chí hoàn thành (DoD - Definition of Done)

*   [ ] Xác nhận 100% trang chủ và các trang danh mục hiển thị ngôn ngữ tiếng Anh, không chứa bất kỳ từ tiếng Việt tĩnh nào.
*   [ ] Tất cả các nút mua hàng trên trang chủ đều dẫn đến link rút gọn `/go/<slug>` tương ứng.
*   [ ] Thực hiện click thử nghiệm Pretty Link chuyển hướng đúng đích gốc với mã HTTP 307 thành công.
*   [ ] Biến đếm lượt click `_affiliate_clicks` tăng chính xác theo số lượt click thử nghiệm thực tế.
*   [ ] Cột "Aff Clicks" trong Admin hiển thị đúng số liệu click và hoạt động sắp xếp tăng/giảm dần trơn tru.
