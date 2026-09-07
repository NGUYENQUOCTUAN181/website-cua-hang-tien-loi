<?php
/**
 * Kết nối Cơ sở dữ liệu MySQL bằng PDO
 * Hỗ trợ cả biến $pdo toàn cục và hàm getDBConnection() cho Model
 */

$host     = "localhost";
$dbname   = "convenience_store";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Không die trực tiếp nếu đang chạy ở môi trường test/cli không có MySQL, nhưng log hoặc thông báo
    // die("Database connection failed: " . $e->getMessage());
    $pdo = null;
    $db_connection_error = $e->getMessage();
}

/**
 * Lấy đối tượng kết nối PDO
 *
 * @return PDO
 * @throws Exception nếu kết nối thất bại
 */
function getDBConnection() {
    global $pdo, $db_connection_error;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    throw new Exception("Không thể kết nối CSDL: " . ($db_connection_error ?? 'Lỗi không xác định'));
}