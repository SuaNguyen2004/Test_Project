<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkNhanVien();
include '../includes/header.php';

// Thống kê nhanh cho nhân viên
$don_cho = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai = 'cho_duyet'")->fetchColumn();
$het_hang = $pdo->query("SELECT COUNT(*) FROM san_pham WHERE so_luong_kho <= 5")->fetchColumn();
?>

<div class="container" style="padding: 20px;">
    <h1>Khu vực Nhân Viên</h1>
    <p>Chào mừng <strong><?php echo $_SESSION['ho_ten']; ?></strong>. Chúc bạn một ngày làm việc hiệu quả!</p>

    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <div style="flex: 1; padding: 20px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px;">
            <h3>Đơn hàng chờ duyệt</h3>
            <h2 style="color: #856404;"><?php echo $don_cho; ?></h2>
            <a href="quan-ly-don-hang.php">Xử lý ngay →</a>
        </div>

        <div style="flex: 1; padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px;">
            <h3>Sản phẩm sắp hết hàng</h3>
            <h2 style="color: #721c24;"><?php echo $het_hang; ?></h2>
            <a href="../admin/quan-ly-san-pham.php">Kiểm tra kho →</a>
        </div>
    </div>

    <div style="margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3>Đơn đặt hàng mới nhất</h3>
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT dh.*, nd.ho_ten FROM don_hang dh 
                                     JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id 
                                     ORDER BY dh.ngay_dat DESC LIMIT 5");
                while ($row = $stmt->fetch()):
                ?>
                    <tr style="text-align: center;">
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['ho_ten']; ?></td>
                        <td><?php echo formatMoney($row['tong_tien']); ?></td>
                        <td><?php echo $row['ngay_dat']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>