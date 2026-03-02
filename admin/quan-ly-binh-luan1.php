<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

// Xử lý xóa bình luận
if (isset($_GET['delete_id'])) {
    $pdo->prepare("DELETE FROM danh_gia WHERE id = ?")->execute([$_GET['delete_id']]);
    header("Location: quan-ly-binh-luan.php");
    exit();
}

$comments = $pdo->query("SELECT dg.*, nd.ho_ten, sp.ten_san_pham 
                         FROM danh_gia dg 
                         JOIN nguoi_dung nd ON dg.nguoi_dung_id = nd.id 
                         JOIN san_pham sp ON dg.san_pham_id = sp.id 
                         ORDER BY dg.ngay_danh_gia DESC")->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Quản lý bình luận</h2>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #eee;">
            <tr>
                <th>Người gửi</th>
                <th>Sản phẩm</th>
                <th>Nội dung</th>
                <th>Ngày gửi</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?php echo $c['ho_ten']; ?></td>
                    <td><?php echo $c['ten_san_pham']; ?></td>
                    <td><?php echo $c['noi_dung']; ?></td>
                    <td><?php echo $c['ngay_danh_gia']; ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $c['id']; ?>" onclick="return confirm('Xóa bình luận này?')" style="color:red;">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>