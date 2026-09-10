<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    /**
     * Xử lý đăng ký
     */
    public function register(): array
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Kiểm tra họ tên
        if ($name === '') {
            $errors[] = 'Vui lòng nhập họ tên.';
        } elseif (mb_strlen($name) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự.';
        }

        // Kiểm tra email
        if ($email === '') {
            $errors[] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Email đã được sử dụng.';
        }

        // Kiểm tra số điện thoại
        if ($phone !== '' && !preg_match('/^[0-9]{9,11}$/', $phone)) {
            $errors[] = 'Số điện thoại phải gồm 9-11 chữ số.';
        }

        // Kiểm tra mật khẩu
        if (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

        // Nếu có lỗi
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        try {
            $this->userModel->create(
                $name,
                $email,
                $password,
                $phone
            );

            return [
                'success' => true,
                'errors' => []
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'errors' => [
                    'Không thể tạo tài khoản. Vui lòng thử lại.'
                ]
            ];
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(): array
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ email và mật khẩu.'
            ];
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ];
        }

        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa.'
            ];
        }

        if (!$this->userModel->verifyPassword(
            $password,
            $user['password']
        )) {
            return [
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ];
        }

        // Tạo session mới để chống session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'role_id' => (int) $user['role_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role_name' => $user['role_name']
        ];

        return [
            'success' => true,
            'user' => $_SESSION['user']
        ];
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}