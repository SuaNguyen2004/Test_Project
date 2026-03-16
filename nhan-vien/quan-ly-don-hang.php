<?php
require_once '../config/db.php';
require_once '../includes/function.php';

// 1. KIỂM TRA QUYỀN TRƯỚC (PHẢI TRÊN CÙNG)
if (!isLoggedIn() || (!hasRole('nhan_vien') && !hasRole('admin'))) {
    header("Location: ../manager/dang-nhap.php");
    exit();
}

// 2. XỬ LÝ LOGIC CẬP NHẬT TRẠNG THÁI (PHẢI TRƯỚC KHI INCLUDE HEADER)
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
    // Sau khi xử lý xong xuôi mới chuyển hướng
    header("Location: quan-ly-don-hang.php");
    exit();
}

// 3. LẤY DỮ LIỆU ĐỂ HIỂN THỊ
$orders = $pdo->query("SELECT dh.*, nd.ho_ten FROM don_hang dh JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id ORDER BY dh.ngay_dat DESC")->fetchAll();

// 4. BẮT ĐẦU PHẦN GIAO DIỆN
include '../includes/header.php';
?>

<link rel="stylesheet" href="../assets/css/style3.css">

<div class="container" style="padding: 20px;">
    <div style="margin-bottom: 20px;">
        <?php if (hasRole('admin')): ?>
            <a href="../admin/index.php" style="text-decoration: none; color: #555; font-weight: bold;">← Dashboard Admin</a>
        <?php else: ?>
            <a href="index.php" style="text-decoration: none; color: #555; font-weight: bold;">← Dashboard Nhân viên</a>
        <?php endif; ?>
    </div>

    <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">📋 Danh sách đơn đặt hàng</h2>

    <table border="1" style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 12px;">Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr style="text-align: center; border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">
                        <a href="../admin/chi-tiet-don-hang.php?id=<?php echo $o['id']; ?>" style="font-weight: bold; color: #3498db; text-decoration: none;">
                            #<?php echo $o['id']; ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($o['ho_ten']); ?></td>
                    <td style="color: #e74c3c; font-weight: bold;"><?php echo formatMoney($o['tong_tien']); ?></td>
                    <td style="color: #7f8c8d;"><?php echo date('d/m/Y H:i', strtotime($o['ngay_dat'])); ?></td>
                    <td>
                        <?php
                        $bg = '#f39c12'; // Mặc định chờ duyệt
                        if ($o['trang_thai'] == 'da_thanh_toan') $bg = '#27ae60';
                        if ($o['trang_thai'] == 'da_huy') $bg = '#e74c3c';
                        ?>
                        <span style="padding: 5px 10px; border-radius: 20px; color: white; font-size: 12px; font-weight: bold; background: <?php echo $bg; ?>;">
                            <?php echo strtoupper($o['trang_thai']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($o['trang_thai'] == 'cho_duyet'): ?>
                            <a href="?action=confirm&order_id=<?php echo $o['id']; ?>" onclick="return confirm('Xác nhận thanh toán đơn này?')" style="color: #27ae60; font-weight: bold; text-decoration: none;">Duyệt</a>
                            <span style="color: #ccc;"> | </span>
                            <a href="?action=cancel&order_id=<?php echo $o['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn này?')" style="color: #e74c3c; font-weight: bold; text-decoration: none;">Hủy</a>
                        <?php else: ?>
                            <span style="color: #bdc3c7; font-style: italic;">Đã xử lý</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>