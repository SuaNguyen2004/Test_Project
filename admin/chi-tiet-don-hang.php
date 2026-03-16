<link rel="stylesheet" href="../assets/css/style3.css">
<?php
require_once '../config/db.php';
require_once '../includes/function.php';

// Cả Admin và Nhân viên đều có quyền xem chi tiết đơn
checkNhanVien();

include '../includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: ../nhan-vien/quan-ly-don-hang.php");
    exit();
}

$order_id = $_GET['id'];

// 1. Lấy thông tin tổng quan đơn hàng
$stmt = $pdo->prepare("SELECT dh.*, nd.ho_ten, nd.ten_dang_nhap 
                       FROM don_hang dh 
                       JOIN nguoi_dung nd ON dh.khach_hang_id = nd.id 
                       WHERE dh.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Không tìm thấy đơn hàng!");
}

// 2. Lấy danh sách sản phẩm trong đơn hàng
$stmt_items = $pdo->prepare("SELECT ct.*, sp.ten_san_pham, sp.anh 
                             FROM chi_tiet_don_hang ct 
                             JOIN san_pham sp ON ct.san_pham_id = sp.id 
                             WHERE ct.don_hang_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Chi tiết đơn hàng #<?php echo $order_id; ?></h2>

    <div style="background: #f4f7f6; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #3498db;">
        <p><strong>Khách hàng:</strong> <?php echo $order['ho_ten']; ?> (<?php echo $order['ten_dang_nhap']; ?>)</p>
        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i:s', strtotime($order['ngay_dat'])); ?></p>
        <p><strong>Ghi chú khách hàng:</strong> <?php echo $order['ghi_chu'] ? $order['ghi_chu'] : "Không có"; ?></p>
        <p><strong>Trạng thái hiện tại:</strong> <b style="color: red;"><?php echo strtoupper($order['trang_thai']); ?></b></p>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #eee;">
            <tr>
                <th style="padding: 10px;">Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá lúc mua</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item):
                $subtotal = $item['gia_luc_mua'] * $item['so_luong'];
            ?>
                <tr style="text-align: center;">
                    <td style="padding: 10px;"><img src="../assets/images/<?php echo $item['anh']; ?>" width="60"></td>
                    <td><?php echo $item['ten_san_pham']; ?></td>
                    <td><?php echo formatMoney($item['gia_luc_mua']); ?></td>
                    <td><?php echo $item['so_luong']; ?></td>
                    <td><?php echo formatMoney($subtotal); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f9f9f9; font-weight: bold; font-size: 1.2rem;">
                <td colspan="4" style="text-align: right; padding: 10px;">TỔNG CỘNG:</td>
                <td style="text-align: center; color: red;"><?php echo formatMoney($order['tong_tien']); ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; display: flex; gap: 10px;">
        <a href="../nhan-vien/quan-ly-don-hang.php" style="padding: 10px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 4px;">← Quay lại danh sách</a>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">🖨️ In hóa đơn</button>
    </div>
</div>

<?php include '../includes/footer.php'; ?>