<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if (!isLoggedIn()) {
    echo "<script>alert('Vui lòng đăng nhập để mua hàng!'); window.location.href = 'manager/dang-nhap.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $qty = (int)$_POST['so_luong'];

    // Lấy tồn kho thực tế
    $stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        // KIỂM TRA TỒN KHO:
        if ($qty > $product['so_luong_kho']) {
            echo "<script>alert('Sản phẩm " . $product['ten_san_pham'] . " chỉ còn " . $product['so_luong_kho'] . " sản phẩm trong kho!'); window.history.back();</script>";
            exit();
        }

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$id])) {
            $new_qty = $_SESSION['cart'][$id]['so_luong'] + $qty;
            // Kiểm tra tổng số lượng trong giỏ sau khi cộng thêm
            if ($new_qty > $product['so_luong_kho']) {
                echo "<script>alert('Số lượng trong giỏ đã đạt tối đa tồn kho!'); window.location.href = 'gio-hang.php';</script>";
                exit();
            }
            $_SESSION['cart'][$id]['so_luong'] = $new_qty;
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
header("Location: gio-hang.php");
exit();
