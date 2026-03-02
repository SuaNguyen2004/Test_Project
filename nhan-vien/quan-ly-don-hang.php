<?php
require_once '../config/db.php';
require_once '../includes/function.php';

// Kiểm tra quyền: Chỉ Admin hoặc Nhân viên mới được vào
if (!isLoggedIn() || (!hasRole('nhan_vien') && !hasRole('admin'))) {
    header("Location: ../manager/dang-nhap.php");
    exit();
}

include '../includes/header.php';

// XỬ LÝ CẬP NHẬT TRẠNG THÁI
if (isset($_GET['action']) && isset($_GET['order_id'])) {
    $id = $_GET['order_id'];
    $current_user_id = $_SESSION['user_id'];

    if ($_GET['action'] == 'confirm') {
        $stmt = $pdo->prepare("UPDATE don_hang SET trang_thai = 'da_thanh_toan', nhan_vien_id = ? WHERE id = ?");
        $stmt->execute([$current_user_id, $id]);
    } elseif ($_GET['action'] == 'cancel') {
        try {
            $pdo->beginTransaction();
            // Lấy SP để cộng lại kho
            $stmt = $pdo->prepare("SELECT san_pham_id, so_luong FROM chi_tiet_don_hang WHERE don_hang_id = ?");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $up = $pdo->prepare("UPDATE san_pham SET so_luong_kho = so_luong_kho + ? WHERE id = ?");
                $up->execute([$item['so_luong'], $item['san_pham_id']]);
            }

            $stmt = $pdo->prepare("UPDATE don_hang SET trang_thai = 'da_huy', nhan_vien_id = ? WHERE id = ?");
            $stmt->execute([$current_user_id, $id]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Lỗi: " . $e->getMessage());
        }
    }
    header("Location: quan-ly-don-hang.php");
    exit();
}

$orders = $pdo->query("SELECT dh.*, nd.ho_ten FROM don_hang dh JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id ORDER BY dh.ngay_dat DESC")->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <div style="margin-bottom: 20px;">
        <?php if (hasRole('admin')): ?>
            <a href="../admin/index.php" style="text-decoration: none; color: #555; font-weight: bold;">← Dashboard Admin</a>
        <?php else: ?>
            <a href="index.php" style="text-decoration: none; color: #555; font-weight: bold;">← Dashboard Nhân viên</a>
        <?php endif; ?>
    </div>

    <h2>Danh sách đơn đặt hàng</h2>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 10px;">Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr style="text-align: center;">
                    <td style="padding: 10px;">
                        <a href="../admin/chi-tiet-don-hang.php?id=<?php echo $o['id']; ?>" style="font-weight: bold; color: #3498db; text-decoration: none;">
                            #<?php echo $o['id']; ?> (Xem)
                        </a>
                    </td>
                    <td><?php echo $o['ho_ten']; ?></td>
                    <td><strong><?php echo formatMoney($o['tong_tien']); ?></strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($o['ngay_dat'])); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; color: white; background: <?php echo ($o['trang_thai'] == 'da_thanh_toan' ? '#27ae60' : ($o['trang_thai'] == 'da_huy' ? '#e74c3c' : '#f39c12')); ?>;">
                            <?php echo strtoupper($o['trang_thai']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($o['trang_thai'] == 'cho_duyet'): ?>
                            <a href="?action=confirm&order_id=<?php echo $o['id']; ?>" onclick="return confirm('Xác nhận thanh toán?')" style="color: green;">Duyệt</a> |
                            <a href="?action=cancel&order_id=<?php echo $o['id']; ?>" onclick="return confirm('Hủy đơn?')" style="color: red;">Hủy</a>
                        <?php else: ?>
                            <span style="color: #999;">Xong</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>