<?php
require_once '../config/db.php';
require_once '../includes/function.php';
// Cho phép cả Admin và Nhân viên nhập kho
if (!isLoggedIn() || (!hasRole('admin') && !hasRole('nhan_vien'))) {
    header("Location: ../manager/dang-nhap.php");
    exit();
}
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sp_id = $_POST['san_pham_id'];
    $sl_nhap = $_POST['so_luong'];
    $gia_nhap = $_POST['gia_nhap_moi'];

    try {
        $pdo->beginTransaction();
        // 1. Ghi vào bảng nhap_kho
        $stmt = $pdo->prepare("INSERT INTO nhap_kho (san_pham_id, so_luong, gia_nhap) VALUES (?, ?, ?)");
        $stmt->execute([$sp_id, $sl_nhap, $gia_nhap]);

        // 2. Cập nhật số lượng và giá nhập mới trong bảng san_pham
        $update = $pdo->prepare("UPDATE san_pham SET so_luong_kho = so_luong_kho + ?, gia_nhap = ? WHERE id = ?");
        $update->execute([$sl_nhap, $gia_nhap, $sp_id]);

        $pdo->commit();
        echo "<script>alert('Nhập kho thành công!');</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi: " . $e->getMessage());
    }
}

$products = $pdo->query("SELECT id, ten_san_pham, so_luong_kho FROM san_pham")->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Nhập hàng vào kho</h2>
    <form method="POST" style="max-width: 500px; background: #eef; padding: 20px; border-radius: 8px;">
        <label>Chọn sản phẩm:</label>
        <select name="san_pham_id" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <?php foreach ($products as $p): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo $p['ten_san_pham']; ?> (Hiện có: <?php echo $p['so_luong_kho']; ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>Số lượng nhập thêm:</label>
        <input type="number" name="so_luong" min="1" required style="width: 100%; padding: 8px; margin-bottom: 15px;">

        <label>Giá nhập mỗi đơn vị (VNĐ):</label>
        <input type="number" name="gia_nhap_moi" required style="width: 100%; padding: 8px; margin-bottom: 15px;">

        <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer;">Xác nhận nhập kho</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>