<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $qty = (int)$_POST['so_luong'];

    // Lấy thông tin SP
    $stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['so_luong'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'ten' => $product['ten_san_pham'],
                'gia' => $product['gia_ban'],
                'so_luong' => $qty,
                'anh' => $product['anh']
            ];
        }
    }
}
// Chuyển hướng ngay lập tức để tránh lỗi Resubmission khi Back
header("Location: gio-hang.php");
exit();
