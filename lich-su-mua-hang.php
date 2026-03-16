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

<link rel="stylesheet" href="assets/css/style3.css">

<style>
    .history-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-title {
        color: #2c3e50;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 3px solid #3498db;
        display: inline-block;
    }

    .order-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        /* Tạo khoảng cách giữa các hàng */
    }

    .order-table thead tr {
        background-color: #f8f9fa;
    }

    .order-table th {
        padding: 15px;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        border: none;
    }

    .order-table tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
    }

    .order-table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .order-table td {
        padding: 20px 15px;
        border: none;
        color: #2c3e50;
        vertical-align: middle;
    }

    /* Bo góc cho hàng */
    .order-table td:first-child {
        border-radius: 8px 0 0 8px;
        font-weight: bold;
        color: #3498db;
    }

    .order-table td:last-child {
        border-radius: 0 8px 8px 0;
    }

    /* Trang trí trạng thái */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-cho_duyet {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-da_thanh_toan {
        background-color: #d4edda;
        color: #155724;
    }

    .status-da_huy {
        background-color: #f8d7da;
        color: #721c24;
    }

    .price-text {
        color: #e74c3c;
        font-weight: bold;
    }

    .empty-state {
        text-align: center;
        padding: 50px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="history-container">
    <h2 class="page-title">📦 Lịch sử mua hàng của bạn</h2>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <p style="color: #95a5a6; font-size: 18px;">Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" style="color: #3498db; text-decoration: none; font-weight: bold;">Mua sắm ngay thôi!</a>
        </div>
    <?php else: ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt hàng</th>
                    <th>Tổng tiền</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th>Ghi chú / Địa chỉ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td style="color: #7f8c8d;">
                            <?php echo date('d/m/Y H:i', strtotime($o['ngay_dat'])); ?>
                        </td>
                        <td class="price-text"><?php echo formatMoney($o['tong_tien']); ?></td>
                        <td style="text-align: center;">
                            <span class="badge status-<?php echo $o['trang_thai']; ?>">
                                <?php
                                $status_map = [
                                    'cho_duyet' => 'Chờ duyệt',
                                    'da_thanh_toan' => 'Đã giao hàng',
                                    'da_huy' => 'Đã hủy'
                                ];
                                echo $status_map[$o['trang_thai']] ?? $o['trang_thai'];
                                ?>
                            </span>
                        </td>
                        <td style="font-size: 14px; color: #34495e;">
                            <?php echo htmlspecialchars($o['ghi_chu']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>