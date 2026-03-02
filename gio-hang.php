<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

// 1. Thêm sản phẩm vào giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $qty = $_POST['qty'];

    // Lấy thông tin SP từ DB
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

        // Nếu giỏ hàng đã tồn tại, kiểm tra xem SP đã có chưa
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['so_luong'] += $qty;
        } else {
            $_SESSION['cart'][$id] = $cart_item;
        }
    }
}

// 2. Xóa sản phẩm khỏi giỏ
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: gio-hang.php");
    exit();
}
?>

<div class="container" style="padding: 20px;">
    <h2>Giỏ hàng của bạn</h2>
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Giỏ hàng trống. <a href="index.php">Tiếp tục mua sắm</a></p>
    <?php else: ?>
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #eee;">
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['gia'] * $item['so_luong'];
                    $total += $subtotal;
                ?>
                    <tr style="text-align: center;">
                        <td><img src="assets/images/<?php echo $item['anh']; ?>" width="50"></td>
                        <td><?php echo $item['ten']; ?></td>
                        <td><?php echo formatMoney($item['gia']); ?></td>
                        <td><?php echo $item['so_luong']; ?></td>
                        <td><?php echo formatMoney($subtotal); ?></td>
                        <td><a href="gio-hang.php?action=delete&id=<?php echo $item['id']; ?>" style="color: red;">Xóa</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3 style="text-align: right;">Tổng cộng: <span style="color: red;"><?php echo formatMoney($total); ?></span></h3>
        <div style="text-align: right; margin-top: 20px;">
            <a href="index.php" style="padding: 10px; background: #95a5a6; color: white; text-decoration: none;">Tiếp tục mua hàng</a>
            <a href="thanh-toan.php" style="padding: 10px; background: #e67e22; color: white; text-decoration: none;">Tiến hành thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>