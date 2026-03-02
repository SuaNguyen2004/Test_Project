<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn thông tin sản phẩm và tên danh mục
$stmt = $pdo->prepare("SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
                       JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
                       WHERE sp.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container' style='padding: 40px; text-align: center;'>
            <h2>Sản phẩm không tồn tại!</h2>
            <a href='index.php' style='color: #3498db;'>Quay lại trang chủ</a>
          </div>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="container" style="display: flex; padding: 40px; gap: 40px; flex-wrap: wrap;">
    <div class="product-image" style="flex: 1; min-width: 300px;">
        <img src="assets/images/<?php echo $product['anh']; ?>"
            style="width: 100%; border-radius: 10px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    </div>

    <div class="product-info" style="flex: 1; min-width: 300px;">
        <h1 style="margin-top: 0; color: #2c3e50;"><?php echo $product['ten_san_pham']; ?></h1>

        <p style="background: #f1f1f1; display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; margin-bottom: 15px;">
            <strong>Danh mục:</strong> <?php echo $product['ten_danh_muc']; ?>
        </p>

        <h2 style="color: #e74c3c; font-size: 2.2rem; margin: 10px 0;">
            <?php echo formatMoney($product['gia_ban']); ?>
        </h2>

        <div style="margin: 20px 0; padding: 20px; background: #fffcf5; border-left: 4px solid #f1c40f; border-radius: 4px;">
            <p style="margin-bottom: 10px;">
                <strong>Trạng thái:</strong>
                <?php echo $product['so_luong_kho'] > 0
                    ? '<span style="color:green; font-weight:bold;">Còn hàng</span>'
                    : '<span style="color:red; font-weight:bold;">Hết hàng</span>';
                ?>
            </p>
            <p style="color: #555; line-height: 1.6;">
                <strong>Mô tả:</strong><br>
                <?php echo nl2br(htmlspecialchars($product['mo_ta'])); ?>
            </p>
        </div>

        <?php if ($product['so_luong_kho'] > 0): ?>
            <form action="them-vao-gio.php" method="POST" style="display: flex; gap: 15px; align-items: center; margin-top: 30px;">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div style="display: flex; flex-direction: column;">
                    <label style="font-size: 0.8rem; color: #666; margin-bottom: 5px;">Số lượng:</label>
                    <input type="number" name="so_luong" value="1" min="1"
                        max="<?php echo $product['so_luong_kho']; ?>"
                        style="width: 70px; padding: 12px; border: 1px solid #ccc; border-radius: 6px; text-align: center; font-size: 1rem;">
                </div>

                <button type="submit"
                    style="background: #27ae60; color: white; border: none; padding: 15px 30px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1.1rem; flex: 1; transition: background 0.3s; align-self: flex-end;">
                    🛒 Thêm vào giỏ hàng
                </button>
            </form>
        <?php else: ?>
            <button disabled style="width: 100%; padding: 15px; background: #bdc3c7; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: not-allowed;">
                Sản phẩm tạm thời hết hàng
            </button>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <a href="index.php" style="text-decoration: none; color: #7f8c8d; font-size: 0.9rem;">← Tiếp tục mua sắm</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>