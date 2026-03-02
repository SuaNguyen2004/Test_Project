<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

if (!isLoggedIn()) {
    header("Location: manager/dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY ngay_dat DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Lịch sử mua hàng của bạn</h2>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th>Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr style="text-align: center;">
                    <td>#<?php echo $o['id']; ?></td>
                    <td><?php echo $o['ngay_dat']; ?></td>
                    <td><?php echo formatMoney($o['tong_tien']); ?></td>
                    <td>
                        <span style="padding: 5px; border-radius: 3px; background: 
                        <?php echo ($o['trang_thai'] == 'da_thanh_toan') ? '#d4edda' : '#fff3cd'; ?>">
                            <?php echo $o['trang_thai']; ?>
                        </span>
                    </td>
                    <td><?php echo $o['ghi_chu']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>