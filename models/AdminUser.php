<?php

class AdminUser
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(
        string $keyword = '',
        int $roleId = 0,
        string $status = ''
    ): array {
        $sql = "SELECT
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.status,
                    u.created_at,
                    u.updated_at,
                    r.id AS role_id,
                    r.name AS role_name,
                    r.description AS role_description
                FROM users u
                INNER JOIN roles r
                    ON r.id = u.role_id
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                        u.name LIKE :keyword
                        OR u.email LIKE :keyword
                        OR u.phone LIKE :keyword
                      )";

            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($roleId > 0) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = $roleId;
        }

        if (
            $status !== '' &&
            in_array($status, ['active', 'locked'], true)
        ) {
            $sql .= " AND u.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getRoles(): array
    {
        $sql = "SELECT
                    id,
                    name,
                    description
                FROM roles
                ORDER BY id ASC";

        return $this->pdo
            ->query($sql)
            ->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT
                    u.id,
                    u.role_id,
                    u.name,
                    u.email,
                    u.phone,
                    u.status,
                    u.created_at,
                    u.updated_at,
                    r.name AS role_name
                FROM users u
                INNER JOIN roles r
                    ON r.id = u.role_id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updateRole(
        int $userId,
        int $roleId
    ): bool {
        $sql = "UPDATE users
                SET
                    role_id = :role_id,
                    updated_at = NOW()
                WHERE id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }

    public function updateStatus(
        int $userId,
        string $status
    ): bool {
        if (!in_array(
            $status,
            ['active', 'locked'],
            true
        )) {
            throw new InvalidArgumentException(
                'Trạng thái tài khoản không hợp lệ.'
            );
        }

        $sql = "UPDATE users
                SET
                    status = :status,
                    updated_at = NOW()
                WHERE id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'status' => $status
        ]);
    }
}