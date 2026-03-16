<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

// 1. Lấy thông tin sản phẩm hiện tại từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT sp.*, dm.ten_danh_muc FROM san_pham sp JOIN danh_muc dm ON sp.danh_muc_id = dm.id WHERE sp.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Nếu không có sản phẩm thì dừng lại
if (!$product) die("<div class='container' style='margin-top:100px;'>Sản phẩm không tồn tại!</div>");

// 2. Lấy danh sách sản phẩm cùng danh mục (loại trừ sản phẩm hiện tại)
// Sử dụng ORDER BY RAND() để lấy ngẫu nhiên và LIMIT 4 để lấy tối đa 4 cái
$stmt_related = $pdo->prepare("SELECT * FROM san_pham WHERE danh_muc_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
$stmt_related->execute([$product['danh_muc_id'], $id]);
$related_products = $stmt_related->fetchAll();
?>

<link rel="stylesheet" href="assets/css/style3.css">
<style>
    /* Phần chi tiết sản phẩm */
    .product-container {
        max-width: 1100px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 50px;
        flex-wrap: wrap;
    }

    .product-image {
        flex: 1;
        min-width: 350px;
        text-align: center;
        background: #fdfdfd;
        border-radius: 12px;
        padding: 20px;
    }

    .product-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        transition: 0.3s;
    }

    .product-image img:hover {
        transform: scale(1.05);
    }

    .product-info {
        flex: 1.2;
        min-width: 350px;
    }

    .product-title {
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .category-tag {
        display: inline-block;
        padding: 5px 15px;
        background: #ebf5ff;
        color: #007bff;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .product-price {
        font-size: 2.2rem;
        color: #e74c3c;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .description-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid #27ae60;
        margin-bottom: 30px;
    }

    .stock-status {
        font-weight: 600;
        color: <?php echo ($product['so_luong_kho'] > 0) ? '#27ae60' : '#e74c3c'; ?>;
        margin-top: 10px;
    }

    /* Bộ tăng giảm số lượng */
    .quantity-control {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .btn-qty {
        width: 45px;
        height: 45px;
        border: 1px solid #ddd;
        background: #f8f9fa;
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-qty:hover {
        background: #e9ecef;
    }

    .btn-minus {
        border-radius: 8px 0 0 8px;
    }

    .btn-plus {
        border-radius: 0 8px 8px 0;
    }

    #input-qty {
        width: 70px;
        height: 45px;
        text-align: center;
        border: 1px solid #ddd;
        border-left: none;
        border-right: none;
        font-weight: bold;
        font-size: 1.1rem;
        outline: none;
        -moz-appearance: textfield;
    }

    #input-qty::-webkit-outer-spin-button,
    #input-qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .btn-add-cart {
        background: #27ae60;
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }

    .btn-add-cart:hover {
        background: #219150;
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
    }

    /* --- PHẦN SẢN PHẨM LIÊN QUAN --- */
    .related-section {
        max-width: 1100px;
        margin: 60px auto;
        padding: 0 20px;
    }

    .related-title {
        font-size: 1.5rem;
        color: #2c3e50;
        border-left: 5px solid #e74c3c;
        padding-left: 15px;
        margin-bottom: 30px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .related-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .related-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .related-card img {
        width: 100%;
        height: 160px;
        object-fit: contain;
        margin-bottom: 15px;
    }

    .related-card h4 {
        font-size: 1rem;
        margin: 10px 0;
        color: #2c3e50;
        height: 40px;
        overflow: hidden;
    }

    .related-card .price {
        color: #e74c3c;
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>

<div class="product-container">
    <div class="product-image">
        <img src="assets/images/<?php echo $product['anh']; ?>" alt="<?php echo htmlspecialchars($product['ten_san_pham']); ?>">
    </div>

    <div class="product-info">
        <div class="category-tag">Danh mục: <?php echo htmlspecialchars($product['ten_danh_muc']); ?></div>
        <h1 class="product-title"><?php echo htmlspecialchars($product['ten_san_pham']); ?></h1>
        <div class="product-price"><?php echo formatMoney($product['gia_ban']); ?></div>

        <div class="description-box">
            <p><strong>Mô tả:</strong></p>
            <p style="color: #555;"><?php echo nl2br(htmlspecialchars($product['mo_ta'])); ?></p>
            <p class="stock-status">
                <?php if ($product['so_luong_kho'] > 0): ?>
                    ● Còn hàng (Trong kho: <?php echo $product['so_luong_kho']; ?>)
                <?php else: ?>
                    ● Tạm hết hàng
                <?php endif; ?>
            </p>
        </div>

        <?php if ($product['so_luong_kho'] > 0): ?>
            <form action="them-vao-gio.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <p style="margin-bottom: 10px; font-weight: bold;">Số lượng mua:</p>
                <div class="quantity-control">
                    <button type="button" class="btn-qty btn-minus" onclick="changeQty(-1)">-</button>
                    <input type="number" name="so_luong" id="input-qty" value="1" min="1" max="<?php echo $product['so_luong_kho']; ?>" oninput="validateInput(this)">
                    <button type="button" class="btn-qty btn-plus" onclick="changeQty(1)">+</button>
                </div>
                <button type="submit" class="btn-add-cart">🛒 Thêm vào giỏ hàng</button>
            </form>
        <?php else: ?>
            <button class="btn-add-cart" style="background: #bdc3c7;" disabled>Hết hàng</button>
        <?php endif; ?>
    </div>
</div>

<?php if (count($related_products) > 0): ?>
    <div class="related-section">
        <h3 class="related-title">Sản phẩm cùng danh mục</h3>
        <div class="related-grid">
            <?php foreach ($related_products as $item): ?>
                <a href="chi-tiet.php?id=<?php echo $item['id']; ?>" class="related-card">
                    <img src="assets/images/<?php echo $item['anh']; ?>" alt="<?php echo htmlspecialchars($item['ten_san_pham']); ?>">
                    <h4><?php echo htmlspecialchars($item['ten_san_pham']); ?></h4>
                    <p class="price"><?php echo formatMoney($item['gia_ban']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<script>
    function changeQty(amount) {
        const input = document.getElementById('input-qty');
        const max = parseInt(input.getAttribute('max'));
        let current = parseInt(input.value) || 1;
        current += amount;
        if (current < 1) current = 1;
        if (current > max) {
            alert("Tối đa " + max + " sản phẩm!");
            current = max;
        }
        input.value = current;
    }

    function validateInput(input) {
        const max = parseInt(input.getAttribute('max'));
        let val = parseInt(input.value);
        if (val > max) {
            alert("Tối đa " + max + " sản phẩm!");
            input.value = max;
        }
        if (val < 1 || isNaN(val)) input.value = 1;
    }
</script>

<?php include 'includes/footer.php'; ?>