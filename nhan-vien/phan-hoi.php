<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkNhanVien();
include '../includes/header.php';

$comments = $pdo->query("SELECT dg.*, nd.ho_ten, sp.ten_san_pham 
                         FROM danh_gia dg 
                         JOIN nguoi_dung nd ON dg.khach_hang_id = nd.id 
                         JOIN san_pham sp ON dg.san_pham_id = sp.id 
                         ORDER BY ngay_danh_gia DESC")->fetchAll();
?>
<div class="container" style="padding: 20px;">
    <h2>Quản lý bình luận & Đánh giá</h2>
    <?php foreach ($comments as $c): ?>
        <div style="border-bottom: 1px solid #eee; padding: 10px;">
            <p><strong><?php echo $c['ho_ten']; ?></strong> đánh giá về <em><?php echo $c['ten_san_pham']; ?></em>:</p>
            <p>"<?php echo $c['noi_dung']; ?>"</p>
            <button onclick="alert('Tính năng trả lời đang được cập nhật!')">Phản hồi</button>
        </div>
    <?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>