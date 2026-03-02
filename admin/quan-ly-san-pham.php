<?php
require_once '../config/db.php';
require_once '../includes/function.php';

// 1. CHẶN TRUY CẬP: Chỉ cho phép Admin và Nhân viên vào xem
if (!isLoggedIn() || (!hasRole('admin') && !hasRole('nhan_vien'))) {
    header("Location: ../manager/dang-nhap.php");
    exit();
}

include '../includes/header.php';

// 2. XỬ LÝ XÓA: Chỉ Admin mới có quyền thực thi lệnh xóa
if (isset($_GET['delete_id']) && hasRole('admin')) {
    $id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM san_pham WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: quan-ly-san-pham.php");
    exit();
}

// 3. TRUY VẤN DANH SÁCH SẢN PHẨM
$sql = "SELECT sp.*, dm.ten_danh_muc 
        FROM san_pham sp 
        LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        ORDER BY sp.id DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>

<div style="margin-top: 20px;">
    <?php if (hasRole('admin')): ?>
        <a href="index.php">← Quay lại Dashboard Admin</a>
    <?php else: ?>
        <a href="../nhan-vien/index.php">← Quay lại Dashboard Nhân viên</a>
    <?php endif; ?>
</div>
<div class="container" style="padding: 20px;">
    <h2>Quản lý sản phẩm</h2>

    <?php if (hasRole('admin')): ?>
        <a href="them-san-pham.php" style="background: #28a745; color: white; padding: 10px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px;">
            + Thêm sản phẩm mới
        </a>
    <?php endif; ?>

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th style="padding: 10px;">ID</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá nhập</th>
                <th>Giá bán</th>
                <th>Tồn kho</th>
                <?php if (hasRole('admin')): ?>
                    <th>Hành động</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td style="padding: 10px;"><?php echo $p['id']; ?></td>
                    <td><img src="../assets/images/<?php echo $p['anh']; ?>" width="50" style="border-radius: 4px;"></td>
                    <td><?php echo $p['ten_san_pham']; ?></td>
                    <td><?php echo $p['ten_danh_muc']; ?></td>
                    <td><?php echo formatMoney($p['gia_nhap']); ?></td>
                    <td><?php echo formatMoney($p['gia_ban']); ?></td>
                    <td>
                        <strong style="color: <?php echo ($p['so_luong_kho'] <= 5) ? 'red' : 'black'; ?>;">
                            <?php echo $p['so_luong_kho']; ?>
                        </strong>
                    </td>

                    <?php if (hasRole('admin')): ?>
                        <td>
                            <a href="sua-san-pham.php?id=<?php echo $p['id']; ?>" style="color: blue; text-decoration: none;">Sửa</a> |
                            <a href="quan-ly-san-pham.php?delete_id=<?php echo $p['id']; ?>"
                                class="delete-link" style="color: red; text-decoration: none;">Xóa</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php include '../includes/footer.php'; ?>