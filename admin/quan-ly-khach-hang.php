<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

// Lấy danh sách khách hàng (loại bỏ Admin và Nhân viên khỏi danh sách này)
$customers = $pdo->query("SELECT * FROM nguoi_dung WHERE vai_tro = 'khach_hang' ORDER BY ngay_tao DESC")->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Quản lý danh sách Khách hàng</h2>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th style="padding: 10px;">ID</th>
                <th>Họ tên</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Ngày đăng ký</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
                <tr style="text-align: center;">
                    <td style="padding: 10px;"><?php echo $c['id']; ?></td>
                    <td><?php echo $c['ho_ten']; ?></td>
                    <td><?php echo $c['ten_dang_nhap']; ?></td>
                    <td><?php echo $c['email'] ?? 'Chưa cập nhật'; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($c['ngay_tao'])); ?></td>
                    <td>
                        <a href="lich-su-mua-hang-khach.php?id=<?php echo $c['id']; ?>" style="color: blue;">Xem đơn hàng</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>