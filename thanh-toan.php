<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if (!isLoggedIn()) {
    header("Location: manager/dang-nhap.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $ghi_chu = $_POST['ghi_chu'];
    $tong_tien = 0;

    foreach ($_SESSION['cart'] as $item) {
        $tong_tien += $item['gia'] * $item['so_luong'];
    }

    try {
        $pdo->beginTransaction(); // Bắt đầu Transaction để đảm bảo an toàn dữ liệu

        // 1. Lưu vào bảng don_hang
        $stmt = $pdo->prepare("INSERT INTO don_hang (khach_hang_id, tong_tien, ghi_chu, trang_thai) VALUES (?, ?, ?, 'cho_duyet')");
        $stmt->execute([$user_id, $tong_tien, $ghi_chu]);
        $order_id = $pdo->lastInsertId();

        // 2. Lưu chi tiết và cập nhật kho
        foreach ($_SESSION['cart'] as $item) {
            // Lưu chi tiết
            $stmt = $pdo->prepare("INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia_luc_mua) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['so_luong'], $item['gia']]);

            // Trừ tồn kho
            $stmt = $pdo->prepare("UPDATE san_pham SET so_luong_kho = so_luong_kho - ? WHERE id = ?");
            $stmt->execute([$item['so_luong'], $item['id']]);
        }

        $pdo->commit(); // Hoàn tất giao dịch
        unset($_SESSION['cart']); // Xóa giỏ hàng
        echo "<script>alert('Đặt hàng thành công!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack(); // Nếu lỗi thì hủy bỏ toàn bộ thay đổi
        die("Lỗi đặt hàng: " . $e->getMessage());
    }
}
?>

<div class="container" style="padding: 20px;">
    <h2>Thông tin thanh toán</h2>
    <form method="POST">
        <p>Người đặt: <strong><?php echo $_SESSION['ho_ten']; ?></strong></p>
        <p>Tổng tiền cần thanh toán: <strong style="color: red;">
                <?php
                $t = 0;
                foreach ($_SESSION['cart'] as $i) $t += $i['gia'] * $i['so_luong'];
                echo formatMoney($t);
                ?>
            </strong></p>
        <textarea name="ghi_chu" placeholder="Ghi chú đơn hàng (Địa chỉ, số điện thoại...)" style="width: 100%; height: 100px;" required></textarea><br><br>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="padding: 15px; background: #27ae60; color: white; border: none; cursor: pointer; font-weight: bold;">XÁC NHẬN ĐẶT HÀNG</button>

            <a href="gio-hang.php" style="padding: 15px; background: #95a5a6; color: white; text-decoration: none; border: none; cursor: pointer; font-weight: bold;">QUAY LẠI GIỎ HÀNG</a>
        </div>
    </form>
</div>