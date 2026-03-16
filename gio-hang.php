<?php
require_once 'config/db.php';
require_once 'includes/function.php';

/**
 * 1. XỬ LÝ LOGIC (PHẢI ĐẶT TRƯỚC MỌI MÃ HTML)
 */

// Xử lý Xóa sản phẩm khỏi giỏ
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    // Sau khi xóa, chuyển hướng lại trang giỏ hàng để cập nhật dữ liệu
    header("Location: gio-hang.php");
    exit();
}

// Xử lý Thêm sản phẩm vào giỏ (nếu file này nhận dữ liệu từ các form POST khác)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $qty = (int)$_POST['qty'];

    $stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $cart_item = [
            'id' => $id,
            'ten' => $product['ten_san_pham'],
            'gia' => $product['gia_ban'],
            'so_luong' => $qty,
            'anh' => $product['anh']
        ];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['so_luong'] += $qty;
        } else {
            $_SESSION['cart'][$id] = $cart_item;
        }
    }
}

/**
 * 2. GIAO DIỆN NGƯỜI DÙNG
 */
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/style3.css">

<div class="container" style="padding: 20px;">
    <h2 style="margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        🛒 Giỏ hàng của bạn
    </h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="background: #f9f9f9; padding: 40px; text-align: center; border-radius: 8px; border: 1px dashed #ccc;">
            <p style="font-size: 1.1rem; color: #666;">Giỏ hàng của bạn đang trống.</p>
            <a href="index.php" style="display: inline-block; margin-top: 15px; padding: 10px 25px; background: #3498db; color: white; text-decoration: none; border-radius: 4px;">
                Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <table border="1" style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['gia'] * $item['so_luong'];
                    $total += $subtotal;
                ?>
                    <tr style="text-align: center; border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">
                            <img src="assets/images/<?php echo $item['anh']; ?>" width="60" style="border-radius: 4px;">
                        </td>
                        <td style="font-weight: 500;"><?php echo $item['ten']; ?></td>
                        <td style="color: #555;"><?php echo formatMoney($item['gia']); ?></td>
                        <td><?php echo $item['so_luong']; ?></td>
                        <td style="font-weight: bold; color: #2c3e50;"><?php echo formatMoney($subtotal); ?></td>
                        <td>
                            <a href="gio-hang.php?action=delete&id=<?php echo $item['id']; ?>"
                                onclick="return confirm('Bạn có chắc muốn bỏ sản phẩm này?')"
                                style="color: #e74c3c; text-decoration: none; font-weight: bold;">
                                [Xóa]
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
            <a href="index.php" style="color: #3498db; text-decoration: none; font-weight: bold;">← Tiếp tục mua hàng</a>

            <div style="text-align: right;">
                <p style="font-size: 1.2rem; margin-bottom: 10px;">Tổng cộng: <span style="color: #e74c3c; font-weight: bold; font-size: 1.5rem;"><?php echo formatMoney($total); ?></span></p>
                <a href="thanh-toan.php" style="display: inline-block; padding: 12px 30px; background: #e67e22; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 1.1rem; transition: background 0.3s;">
                    TIẾN HÀNH THANH TOÁN
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>