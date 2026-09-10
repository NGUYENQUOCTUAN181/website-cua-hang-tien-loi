<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT 
                    u.id,
                    u.role_id,
                    u.name,
                    u.email,
                    u.password,
                    u.phone,
                    u.status,
                    u.created_at,
                    u.updated_at,
                    r.name AS role_name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT id
                FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        return (bool) $stmt->fetch();
    }

    /**
     * Lấy ID của role CUSTOMER
     */
    public function getCustomerRoleId(): int
    {
        $sql = "SELECT id
                FROM roles
                WHERE name = 'CUSTOMER'
                LIMIT 1";

        $stmt = $this->pdo->query($sql);

        $role = $stmt->fetch();

        if (!$role) {
            throw new RuntimeException(
                'Không tìm thấy role CUSTOMER trong database.'
            );
        }

        return (int) $role['id'];
    }

    /**
     * Tạo tài khoản CUSTOMER
     */
    public function create(
        string $name,
        string $email,
        string $password,
        string $phone = ''
    ): bool {
        $roleId = $this->getCustomerRoleId();

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users (
                    role_id,
                    name,
                    email,
                    password,
                    phone,
                    status,
                    created_at,
                    updated_at
                ) VALUES (
                    :role_id,
                    :name,
                    :email,
                    :password,
                    :phone,
                    'active',
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'role_id' => $roleId,
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone
        ]);
    }

    /**
     * Kiểm tra mật khẩu
     */
    public function verifyPassword(
        string $password,
        string $hashedPassword
    ): bool {
        return password_verify(
            $password,
            $hashedPassword
        );
    }
}