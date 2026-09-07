<?php
/**
 * Controller: Giỏ hàng, Áp dụng Voucher & Thanh toán (Checkout)
 * Module: Thành Viên 3 - Client
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/VoucherModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class CartController {
    private $productModel;
    private $voucherModel;
    private $orderModel;

    public function __construct() {
        // Khởi tạo các Model nghiệp vụ
        $this->productModel = new ProductModel();
        $this->voucherModel = new VoucherModel();
        $this->orderModel   = new OrderModel();

        // Đảm bảo session giỏ hàng luôn tồn tại dưới dạng mảng
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * 1. Hiển thị Trang Giỏ hàng
     * URL: index.php?act=cart
     */
    public function index() {
        $cart = $_SESSION['cart'];
        $subtotal = $this->calculateSubtotal($cart);
        $voucher = $_SESSION['voucher'] ?? null;
        $discount = 0;

        // Nếu đang có voucher, kiểm tra lại điều kiện so với tổng tiền hiện tại của giỏ
        if ($voucher) {
            $validation = $this->voucherModel->validateVoucher($voucher['code'], $subtotal);
            if ($validation['success']) {
                $discount = $validation['discount_amount'];
                $_SESSION['voucher']['discount_amount'] = $discount;
            } else {
                // Nếu xóa bớt hàng dẫn tới không còn đủ điều kiện min_order_value thì gỡ voucher
                unset($_SESSION['voucher']);
                $voucher = null;
                setFlash('warning', 'Mã giảm giá đã bị gỡ do giỏ hàng không còn đủ điều kiện: ' . $validation['message']);
            }
        }

        $total = max(0, $subtotal - $discount);

        // Nạp view giỏ hàng
        require __DIR__ . '/../views/cart/cart.php';
    }

    /**
     * 2. Thêm sản phẩm vào giỏ hàng
     * URL: index.php?act=add-to-cart (POST hoặc GET)
     */
    public function addToCart() {
        $productId = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : 0;
        $quantity  = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

        if ($quantity <= 0) {
            $quantity = 1;
        }

        // Lấy thông tin sản phẩm chuẩn từ CSDL
        $product = $this->productModel->getById($productId);
        if (!$product) {
            setFlash('danger', 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh!');
            redirect(BASE_URL . '?act=cart');
        }

        $stock = (int)$product['stock'];
        $currentQtyInCart = isset($_SESSION['cart'][$productId]) ? (int)$_SESSION['cart'][$productId]['quantity'] : 0;
        $newTotalQty = $currentQtyInCart + $quantity;

        // Kiểm tra tồn kho
        if ($newTotalQty > $stock) {
            setFlash('danger', "Kho chỉ còn {$stock} món '{$product['name']}'. Bạn đã có {$currentQtyInCart} trong giỏ, không thể thêm {$quantity} món nữa!");
            redirect($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '?act=cart'));
        }

        // Giá bán áp dụng (lấy từ DB, ưu tiên giá khuyến mãi sale_price)
        $effectivePrice = $this->productModel->calculateEffectivePrice($product);

        // Lưu hoặc cập nhật vào $_SESSION['cart'] theo đúng cấu trúc yêu cầu
        $_SESSION['cart'][$productId] = [
            'id'       => (int)$product['id'],
            'name'     => $product['name'],
            'price'    => $effectivePrice,
            'image'    => $product['image'],
            'quantity' => $newTotalQty
        ];

        setFlash('success', "Đã thêm '{$product['name']}' vào giỏ hàng thành công!");
        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 3. Cập nhật số lượng sản phẩm trong giỏ hàng
     * URL: index.php?act=update-cart (POST)
     */
    public function updateCart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?act=cart');
        }

        $quantities = $_POST['quantity'] ?? []; // Mảng [productId => qty]

        if (!empty($quantities) && is_array($quantities)) {
            $hasError = false;

            foreach ($quantities as $productId => $qty) {
                $productId = (int)$productId;
                $qty = (int)$qty;

                // Nếu số lượng <= 0: Tự động xóa khỏi giỏ
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$productId]);
                    continue;
                }

                // Kiểm tra lại tồn kho DB
                $product = $this->productModel->getById($productId);
                if (!$product) {
                    unset($_SESSION['cart'][$productId]);
                    continue;
                }

                if ($qty > (int)$product['stock']) {
                    setFlash('danger', "Sản phẩm '{$product['name']}' chỉ còn {$product['stock']} trong kho!");
                    $_SESSION['cart'][$productId]['quantity'] = (int)$product['stock'];
                    $hasError = true;
                } else {
                    $_SESSION['cart'][$productId]['quantity'] = $qty;
                }
            }

            if (!$hasError) {
                setFlash('success', 'Đã cập nhật giỏ hàng thành công!');
            }
        }

        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 4. Xóa một sản phẩm khỏi giỏ hàng
     * URL: index.php?act=delete-cart&id=1
     */
    public function deleteItem() {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (isset($_SESSION['cart'][$productId])) {
            $productName = $_SESSION['cart'][$productId]['name'];
            unset($_SESSION['cart'][$productId]);
            setFlash('info', "Đã xóa '{$productName}' khỏi giỏ hàng.");
        }

        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 5. Xóa sạch toàn bộ giỏ hàng
     * URL: index.php?act=clear-cart
     */
    public function clearCart() {
        $_SESSION['cart'] = [];
        unset($_SESSION['voucher']);
        setFlash('info', 'Đã xóa sạch giỏ hàng.');
        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 6. Áp dụng Mã giảm giá (Voucher)
     * URL: index.php?act=apply-voucher (POST)
     */
    public function applyVoucher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?act=cart');
        }

        $code = trim($_POST['voucher_code'] ?? '');
        $subtotal = $this->calculateSubtotal($_SESSION['cart']);

        if ($subtotal <= 0) {
            setFlash('warning', 'Giỏ hàng đang trống, không thể áp dụng mã giảm giá!');
            redirect(BASE_URL . '?act=cart');
        }

        $result = $this->voucherModel->validateVoucher($code, $subtotal);

        if ($result['success']) {
            $voucher = $result['voucher'];
            $_SESSION['voucher'] = [
                'id'              => $voucher['id'],
                'code'            => $voucher['code'],
                'discount_type'   => $voucher['discount_type'],
                'discount_value'  => $voucher['discount_value'],
                'discount_amount' => $result['discount_amount'],
                'description'     => $voucher['description'] ?? ''
            ];
            setFlash('success', $result['message']);
        } else {
            setFlash('danger', $result['message']);
        }

        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 7. Hủy áp dụng Voucher
     * URL: index.php?act=remove-voucher
     */
    public function removeVoucher() {
        if (isset($_SESSION['voucher'])) {
            unset($_SESSION['voucher']);
            setFlash('info', 'Đã gỡ bỏ mã giảm giá.');
        }
        redirect(BASE_URL . '?act=cart');
    }

    /**
     * 8. Hiển thị trang Thanh toán (Checkout)
     * URL: index.php?act=checkout
     */
    public function checkout() {
        $cart = $_SESSION['cart'];

        // Nếu giỏ hàng trống thì quay về trang giỏ hàng
        if (empty($cart)) {
            setFlash('warning', 'Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
            redirect(BASE_URL . '?act=cart');
        }

        $subtotal = $this->calculateSubtotal($cart);
        $voucher = $_SESSION['voucher'] ?? null;
        $discount = 0;

        if ($voucher) {
            $validation = $this->voucherModel->validateVoucher($voucher['code'], $subtotal);
            if ($validation['success']) {
                $discount = $validation['discount_amount'];
            } else {
                unset($_SESSION['voucher']);
                $voucher = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        // Thông tin khách hàng điền sẵn nếu đã đăng nhập tài khoản
        $user = $_SESSION['user'] ?? null;

        require __DIR__ . '/../views/cart/checkout.php';
    }

    /**
     * 9. Xử lý đặt hàng (Submit Form Checkout)
     * URL: index.php?act=process-checkout (POST)
     */
    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?act=checkout');
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            setFlash('danger', 'Giỏ hàng đang trống!');
            redirect(BASE_URL . '?act=cart');
        }

        // Lấy và làm sạch dữ liệu từ Form
        $customerName    = trim($_POST['customer_name'] ?? '');
        $customerPhone   = trim($_POST['customer_phone'] ?? '');
        $customerEmail   = trim($_POST['customer_email'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $paymentMethod   = trim($_POST['payment_method'] ?? 'COD');
        $note            = trim($_POST['note'] ?? '');

        // Validation dữ liệu bắt buộc
        $errors = [];
        if (empty($customerName)) {
            $errors[] = 'Vui lòng nhập họ và tên người nhận.';
        }
        if (empty($customerPhone) || !preg_match('/^[0-9]{9,11}$/', $customerPhone)) {
            $errors[] = 'Số điện thoại không hợp lệ (từ 9 đến 11 chữ số).';
        }
        if (empty($shippingAddress)) {
            $errors[] = 'Vui lòng nhập địa chỉ giao hàng cụ thể.';
        }
        if (!in_array(strtoupper($paymentMethod), ['COD', 'BANKING', 'BANK_TRANSFER'])) {
            $errors[] = 'Phương thức thanh toán không hợp lệ.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect(BASE_URL . '?act=checkout');
        }

        $customerData = [
            'name'           => $customerName,
            'phone'          => $customerPhone,
            'email'          => $customerEmail,
            'address'        => $shippingAddress,
            'payment_method' => $paymentMethod,
            'note'           => $note,
            'user_id'        => $_SESSION['user']['id'] ?? null
        ];

        try {
            // GỌI MODEL THỰC HIỆN TOÀN BỘ TRANSACTION BẢO VỆ TỒN KHO VÀ GIÁ
            $orderResult = $this->orderModel->createOrderWithTransaction(
                $customerData, 
                $cart, 
                $_SESSION['voucher'] ?? null
            );

            // Xóa session giỏ hàng và voucher sau khi đặt thành công
            $_SESSION['cart'] = [];
            unset($_SESSION['voucher']);

            // Lưu mã đơn hàng vừa đặt vào session để trang Thank you tiện hiển thị
            $_SESSION['last_order_id'] = $orderResult['order_id'];
            $_SESSION['last_order_code'] = $orderResult['order_code'];

            // Nếu khách vãng lai, lưu số điện thoại vào session để tra cứu lại lịch sử
            $_SESSION['customer_phone_lookup'] = $customerPhone;

            setFlash('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm.');
            redirect(BASE_URL . '?act=order-success&id=' . $orderResult['order_id']);

        } catch (Exception $e) {
            // Có lỗi (như hết hàng hoặc DB exception): rollback đã được thực thi trong Model
            setFlash('danger', $e->getMessage());
            redirect(BASE_URL . '?act=checkout');
        }
    }

    /**
     * 10. Hiển thị Trang Đặt Hàng Thành Công (Thank You / Order Success)
     * URL: index.php?act=order-success&id=1
     */
    public function orderSuccess() {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : ($_SESSION['last_order_id'] ?? 0);
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            setFlash('warning', 'Không tìm thấy thông tin đơn hàng.');
            redirect(BASE_URL);
        }

        $orderItems = $this->orderModel->getOrderItems($orderId);
        require __DIR__ . '/../views/cart/success.php';
    }

    /**
     * 11. Lịch sử đơn hàng của khách
     * URL: index.php?act=orders
     */
    public function orderHistory() {
        $userId = $_SESSION['user']['id'] ?? null;
        $searchPhone = trim($_GET['phone'] ?? ($_SESSION['customer_phone_lookup'] ?? ''));

        $orders = [];
        if ($userId || !empty($searchPhone)) {
            $orders = $this->orderModel->getOrdersHistory($userId, $searchPhone);
        }

        require __DIR__ . '/../views/cart/orders.php';
    }

    /**
     * 12. Chi tiết và theo dõi trạng thái đơn hàng
     * URL: index.php?act=order-detail&id=1
     */
    public function orderDetail() {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $orderCode = isset($_GET['code']) ? trim($_GET['code']) : '';

        if ($orderId > 0) {
            $order = $this->orderModel->getOrderById($orderId);
        } elseif (!empty($orderCode)) {
            $order = $this->orderModel->getOrderByCode($orderCode);
        } else {
            $order = false;
        }

        if (!$order) {
            setFlash('danger', 'Không tìm thấy đơn hàng theo yêu cầu.');
            redirect(BASE_URL . '?act=orders');
        }

        $orderItems = $this->orderModel->getOrderItems($order['id']);
        require __DIR__ . '/../views/cart/order_detail.php';
    }

    /**
     * 13. Hủy đơn hàng (khi đơn ở trạng thái 'pending')
     * URL: index.php?act=cancel-order (POST)
     */
    public function cancelOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '?act=orders');
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $reason = trim($_POST['reason'] ?? 'Khách hàng yêu cầu hủy qua website');

        try {
            $this->orderModel->cancelOrder($orderId, $reason);
            setFlash('success', 'Đã hủy đơn hàng thành công! Số lượng sản phẩm đã được hoàn lại vào kho.');
        } catch (Exception $e) {
            setFlash('danger', $e->getMessage());
        }

        redirect(BASE_URL . '?act=order-detail&id=' . $orderId);
    }

    /**
     * Hàm tiện ích: Tính Tổng tiền tạm tính của giỏ hàng
     *
     * @param array $cart
     * @return float
     */
    public function calculateSubtotal($cart) {
        $subtotal = 0;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                $subtotal += ((float)$item['price']) * ((int)$item['quantity']);
            }
        }
        return $subtotal;
    }

    /**
     * Hàm tiện ích: Đếm tổng số lượng món trong giỏ hàng (để hiện badge trên header)
     *
     * @return int
     */
    public static function getCartTotalQuantity() {
        $count = 0;
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += (int)$item['quantity'];
            }
        }
        return $count;
    }
}
