<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if (!isLoggedIn()) {
    header("Location: manager/dang-nhap.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $ghi_chu = trim($_POST['ghi_chu']);
    $tong_tien = 0;

    foreach ($_SESSION['cart'] as $item) {
        $tong_tien += $item['gia'] * $item['so_luong'];
    }

    try {
        $pdo->beginTransaction();

        // 1. Lưu đơn hàng
        $stmt = $pdo->prepare("INSERT INTO don_hang (khach_hang_id, tong_tien, ghi_chu, trang_thai) VALUES (?, ?, ?, 'cho_duyet')");
        $stmt->execute([$user_id, $tong_tien, $ghi_chu]);
        $order_id = $pdo->lastInsertId();

        // 2. Kiểm tra tồn kho từng sản phẩm và trừ kho
        foreach ($_SESSION['cart'] as $item) {
            // Lấy lại tồn kho chính xác nhất tại thời điểm bấm nút Thanh toán
            $st = $pdo->prepare("SELECT so_luong_kho, ten_san_pham FROM san_pham WHERE id = ? FOR UPDATE");
            $st->execute([$item['id']]);
            $p_check = $st->fetch();

            if ($p_check['so_luong_kho'] < $item['so_luong']) {
                // Nếu không đủ, hủy Transaction (Rollback)
                $pdo->rollBack();
                echo "<script>alert('Sản phẩm " . $p_check['ten_san_pham'] . " vừa hết hàng hoặc không đủ số lượng. Vui lòng cập nhật lại giỏ hàng!'); window.location.href = 'gio-hang.php';</script>";
                exit();
            }

            // Lưu chi tiết
            $stmt_detail = $pdo->prepare("INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia_luc_mua) VALUES (?, ?, ?, ?)");
            $stmt_detail->execute([$order_id, $item['id'], $item['so_luong'], $item['gia']]);

            // Trừ tồn kho
            $stmt_update = $pdo->prepare("UPDATE san_pham SET so_luong_kho = so_luong_kho - ? WHERE id = ?");
            $stmt_update->execute([$item['so_luong'], $item['id']]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        echo "<script>alert('Đặt hàng thành công!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi đặt hàng: " . $e->getMessage());
    }
}
?>

<link rel="stylesheet" href="assets/css/style3.css">
<div class="container" style="padding: 20px;">
    <h2>Xác nhận thanh toán</h2>
    <form method="POST" style="max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <p>Khách hàng: <strong><?php echo $_SESSION['ho_ten']; ?></strong></p>
        <p>Tổng tiền thanh toán: <strong style="color: red; font-size: 1.2rem;"><?php echo formatMoney($tong_tien); ?></strong></p>

        <label style="display: block; margin: 15px 0 5px;">Ghi chú giao hàng (Địa chỉ, SĐT):</label>
        <textarea name="ghi_chu" style="width: 100%; height: 100px; padding: 10px;" required placeholder="Ví dụ: Số 123, đường ABC, SĐT: 090xxx..."></textarea>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" style="flex: 1; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">XÁC NHẬN ĐẶT HÀNG</button>
            <a href="gio-hang.php" style="flex: 1; text-align: center; padding: 15px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">HỦY BỎ</a>
        </div>
    </form>
</div>
<?php include 'includes/footer.php'; ?>