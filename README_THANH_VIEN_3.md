# TÀI LIỆU HƯỚNG DẪN BÁO CÁO & SỬ DỤNG: MODULE THÀNH VIÊN 3
## CLIENT: GIỎ HÀNG, ÁP DỤNG VOUCHER & THANH TOÁN (CHECKOUT)
**Đồ án:** Website Cửa Hàng Tiện Lợi (PHP thuần - Mô hình MVC - PDO MySQL - Bootstrap 5)

---

### 1. TỔNG QUAN CÁC CHỨC NĂNG ĐÃ TRIỂN KHAI
Module hoàn thành 100% các chức năng theo yêu cầu đồ án:
1. **Giỏ hàng (Cart)**:
   - Lưu trữ tạm thời qua `$_SESSION['cart']`.
   - Cấu trúc: `$_SESSION['cart'][$productId] = ['id', 'name', 'price', 'image', 'quantity']`.
   - Thêm sản phẩm (cộng dồn số lượng, kiểm tra không vượt quá tồn kho `stock`).
   - Cập nhật số lượng (+ / - hoặc nhập trực tiếp, tự động xóa món nếu số lượng $\le 0$, cảnh báo nếu vượt tồn kho).
   - Xóa từng món và Xóa sạch giỏ hàng.
   - Tự động tính tiền tạm tính chính xác.

2. **Áp dụng Voucher (Khuyến mãi)**:
   - Kiểm tra điều kiện thời gian thực: Tồn tại trong CSDL, còn lượt sử dụng (`quantity > 0`), còn hạn sử dụng (`end_date / expires_at >= NOW()`), đạt giá trị đơn hàng tối thiểu (`min_order_value`).
   - Lưu voucher hợp lệ vào `$_SESSION['voucher']`.
   - Hỗ trợ cả 2 hình thức: Giảm theo % (có khống chế mức trần `max_discount`) và Giảm số tiền cố định (`fixed`).
   - Nút gỡ bỏ voucher đang áp dụng.

3. **Quy trình Thanh toán & Tạo Đơn Hàng (Checkout)**:
   - Giao diện 2 cột chuẩn UI/UX thương mại điện tử:
     + **Cột 1:** Form thông tin giao hàng (Họ tên, SĐT, Email, Địa chỉ cụ thể, Ghi chú) và Chọn phương thức thanh toán (COD hoặc Chuyển khoản ngân hàng có kèm số tài khoản / mã QR VietQR).
     + **Cột 2:** Tóm tắt đơn hàng (ảnh món, tên, số lượng, tạm tính, tiền giảm voucher, tổng tiền thanh toán).
   - **Bảo mật & Toàn vẹn dữ liệu (Pessimistic Locking & PDO Transaction)**:
     + Tuyệt đối **không** lấy giá tiền gửi lên từ form HTML (chống sửa giá bằng F12 / DevTools).
     + Query lại DB bảng `products` với `SELECT ... FOR UPDATE` để lấy giá bán thực tế và khóa kiểm tra tồn kho tại thời điểm đặt hàng.
     + Quy trình Transaction 7 bước:
       1) Thẩm định tồn kho và tính tổng tiền thực tế.
       2) Thẩm định lại voucher hợp lệ.
       3) Tạo bản ghi trong `orders`, lấy `order_id` qua `lastInsertId()`.
       4) Chèn từng món vào `order_items` (lưu cứng giá tại thời điểm mua).
       5) Trừ tồn kho (`stock = stock - quantity`) trong bảng `products`.
       6) Giảm lượt dùng voucher (`quantity = quantity - 1`).
       7) Commit transaction, xóa giỏ hàng và chuyển hướng sang trang Hoàn tất đơn hàng (Thank You page). Nếu có lỗi: Rollback ngay lập tức.

4. **Lịch sử Đơn Hàng, Theo Dõi Trạng Thái & Hủy Đơn**:
   - Tra cứu đơn hàng theo Số điện thoại hoặc Email (dành cho cả khách vãng lai và khách có tài khoản).
   - Xem chi tiết đơn hàng kèm **Timeline tiến trình giao hàng trực quan** (Chờ xác nhận $\rightarrow$ Đã xác nhận $\rightarrow$ Đang giao $\rightarrow$ Hoàn thành).
   - Cho phép khách **Hủy đơn hàng** khi đơn còn ở trạng thái Chờ xác nhận (`pending`).
   - Khi hủy đơn: Hệ thống tự động kích hoạt Transaction để **hoàn lại số lượng tồn kho** vào bảng `products` và **hoàn lại lượt dùng voucher**.

---

### 2. CẤU TRÚC MÃ NGUỒN (MVC CHUẨN)
```
website-cua-hang-tien-loi/
├── config/
│   ├── config.php                      # Cấu hình BASE_URL, Session, hàm formatPrice(), flashMessage()
│   └── database.php                    # Kết nối PDO MySQL (hàm getDBConnection())
├── controllers/
│   └── CartController.php              # Điều khiển toàn bộ logic Cart, Voucher, Checkout, Orders
├── models/
│   ├── ProductModel.php                # Lấy thông tin sản phẩm, giá bán, kiểm tra tồn kho DB
│   ├── VoucherModel.php                # Thẩm định điều kiện voucher, tính tiền giảm
│   └── OrderModel.php                  # PDO Transaction tạo đơn, trừ kho, trừ voucher, hủy đơn
├── views/
│   ├── layouts/
│   │   ├── header.php                  # Navbar Bootstrap 5, Badge giỏ hàng, Flash Alert
│   │   └── footer.php                  # Footer trang web
│   ├── cart/
│   │   ├── cart.php                    # Giao diện Giỏ hàng
│   │   ├── checkout.php                # Giao diện Thanh toán 2 cột
│   │   ├── success.php                 # Giao diện Đặt hàng thành công (kèm VietQR)
│   │   ├── orders.php                  # Giao diện Lịch sử đơn hàng
│   │   └── order_detail.php            # Chi tiết đơn, Timeline trạng thái, Modal hủy đơn
│   └── products/
│       └── index.php                   # Trang danh sách sản phẩm mẫu để test thêm vào giỏ
├── database/
│   ├── convenience_store.sql           # Database gốc
│   └── member3_patch_and_seed.sql      # Script SQL dữ liệu mẫu (sản phẩm, voucher, orders)
├── index.php                           # Front Controller (Router điều hướng)
└── README_THANH_VIEN_3.md              # Tài liệu này
```

---

### 3. HƯỚNG DẪN TEST & CHẠY THỬ

#### Bước 1: Khởi động MySQL & Nạp Database
1. Mở ứng dụng **XAMPP Control Panel**, bấm **Start** dịch vụ **Apache** và **MySQL**.
2. Mở trình duyệt vào `http://localhost/phpmyadmin/`.
3. Tạo Database tên: `convenience_store` (nếu chưa có).
4. Import file `database/convenience_store.sql`.
5. Import tiếp file `database/member3_patch_and_seed.sql` để cập nhật cột và nạp sẵn 6 sản phẩm mẫu cùng 5 mã voucher.

#### Bước 2: Trải nghiệm các chức năng
Mở trình duyệt truy cập: `http://localhost/website-cua-hang-tien-loi/`

- **Thêm vào giỏ**: Bấm "Thêm vào giỏ" ở từng món trên trang chủ.
- **Xem & Chỉnh giỏ hàng**: Bấm vào nút "Giỏ hàng" trên góc phải navbar (`?act=cart`). Thử bấm nút `+` / `-`, hoặc gõ số lượng về `0` để xem tính năng tự xóa món.
- **Thử các mã Voucher mẫu**:
  + `CHAOBANMOI`: Giảm 10% (tối đa 30.000đ) cho đơn từ 50.000đ.
  + `GIAM20K`: Giảm trực tiếp 20.000đ cho đơn từ 100.000đ.
  + `FREESHIP`: Giảm 15.000đ cho đơn từ 80.000đ.
  + `HETHAN`: Mã đã hết hạn (để test thông báo lỗi).
  + `HETLUOT`: Mã đã hết số lượt dùng (để test thông báo lỗi).
- **Thanh toán (Checkout)**: Bấm "Tiến hành thanh toán", điền thông tin người nhận, chọn COD hoặc Chuyển khoản, bấm "Xác nhận đặt hàng".
- **Kiểm tra sau khi đặt**:
  + Xem trang Hoàn tất đơn hàng với mã đơn và mã QR VietQR tự động tạo.
  + Kiểm tra trong phpMyAdmin: bảng `products` số lượng `stock` đã được trừ chính xác, bảng `orders` và `order_items` đã được chèn dữ liệu.
- **Xem Lịch sử & Hủy đơn**:
  + Vào menu "Tra cứu đơn hàng" (`?act=orders`), nhập Số điện thoại đã đặt.
  + Bấm "Chi tiết" để xem Timeline trạng thái.
  + Bấm "Hủy đơn hàng này", chọn lý do và xác nhận để thấy đơn chuyển sang trạng thái "Đã hủy", đồng thời số lượng `stock` trong kho được hoàn trả tự động!

---

### 4. CÁC URL ROUTING TRONG HỆ THỐNG
| Tuyến đường (URL) | Chức năng | Phương thức |
|---|---|---|
| `index.php` hoặc `?act=products` | Danh sách sản phẩm mua sắm | GET |
| `index.php?act=cart` | Xem trang giỏ hàng | GET |
| `index.php?act=add-to-cart` | Thêm món vào giỏ hàng | POST / GET |
| `index.php?act=update-cart` | Cập nhật số lượng món trong giỏ | POST |
| `index.php?act=delete-cart&id={id}` | Xóa 1 món khỏi giỏ hàng | GET |
| `index.php?act=clear-cart` | Xóa sạch toàn bộ giỏ hàng | GET |
| `index.php?act=apply-voucher` | Áp dụng mã giảm giá | POST |
| `index.php?act=remove-voucher` | Gỡ bỏ mã giảm giá | GET |
| `index.php?act=checkout` | Xem giao diện thanh toán 2 cột | GET |
| `index.php?act=process-checkout` | Submit đặt hàng (Transaction) | POST |
| `index.php?act=order-success&id={id}`| Trang đặt hàng thành công | GET |
| `index.php?act=orders` | Lịch sử danh sách đơn hàng | GET |
| `index.php?act=order-detail&id={id}` | Chi tiết & Timeline đơn hàng | GET |
| `index.php?act=cancel-order` | Hủy đơn hàng đang chờ xác nhận | POST |
