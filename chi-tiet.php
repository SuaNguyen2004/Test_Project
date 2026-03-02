<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php'; // Header tràn viền

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT sp.*, dm.ten_danh_muc FROM san_pham sp JOIN danh_muc dm ON sp.danh_muc_id = dm.id WHERE sp.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("<div class='container'>Sản phẩm không tồn tại!</div>");
?>

<div style="max-width: 1200px; margin: 0 auto; display: flex; padding: 40px; gap: 40px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 300px;">
        <img src="assets/images/<?php echo $product['anh']; ?>" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    </div>

    <div style="flex: 1; min-width: 300px;">
        <h1 style="color: #2c3e50;"><?php echo $product['ten_san_pham']; ?></h1>
        <p style="background: #f1f1f1; display: inline-block; padding: 5px 12px; border-radius: 20px;">Danh mục: <?php echo $product['ten_danh_muc']; ?></p>
        <h2 style="color: #e74c3c; font-size: 2rem;"><?php echo formatMoney($product['gia_ban']); ?></h2>

        <div style="margin: 20px 0; padding: 15px; background: #fff8f8; border-left: 4px solid #e74c3c;">
            <p><strong>Mô tả:</strong> <?php echo nl2br($product['mo_ta']); ?></p>
        </div>

        <?php if ($product['so_luong_kho'] > 0): ?>
            <form action="them-vao-gio.php" method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <input type="number" name="so_luong" value="1" min="1" style="width: 60px; padding: 10px;">
                <button type="submit" style="background: #27ae60; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; flex: 1;">🛒 Thêm vào giỏ hàng</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>