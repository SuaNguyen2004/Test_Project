<?php
// Cấu hình thông số kết nối trên Laragon
$host = 'localhost';
$db   = 'test_btl'; // Thay bằng tên DB bạn tạo trên Laragon
$user = 'root'; // Mặc định của Laragon là root
$pass = '';     // Mặc định của Laragon là để trống
$charset = 'utf8mb4';

// Cấu hình DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Các tùy chọn cấu hình PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Đẩy lỗi ra để dễ debug
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về dữ liệu dạng mảng kết hợp
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Tắt giả lập để tăng tính bảo mật
];

try {
    // Khởi tạo đối tượng PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Kết nối thành công!"; // Bỏ comment để kiểm tra khi mới chạy
} catch (\PDOException $e) {
    // Nếu lỗi, dừng hệ thống và thông báo
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
