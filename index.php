<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

$search = isset($_GET['keyword']) ? $_GET['keyword'] : '';

$sql_cate = "SELECT DISTINCT dm.* FROM danh_muc dm 
             JOIN san_pham sp ON dm.id = sp.danh_muc_id 
             WHERE sp.ten_san_pham LIKE ? ORDER BY dm.id ASC";
$stmt_cate = $pdo->prepare($sql_cate);
$stmt_cate->execute(["%$search%"]);
$categories = $stmt_cate->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tạp Hóa Genius</title>
    <link rel="stylesheet" href="assets/css/style1.css">
    <style>
        body {
            padding-top: 80px !important;
        }

        /* Giảm padding vì header đã gọn hơn */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        .main-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .category-title {
            background: #f8f9fa;
            padding: 10px 20px;
            border-left: 5px solid #e74c3c;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
            color: #2c3e50;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        /* GIỮ NGUYÊN KÍCH THƯỚC 220PX */
        .product-card {
            width: 220px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #fff;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        .product-card:hover {
            background-color: #f0f0f0 !important;
            /* Nền xám khi hover */
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            transition: transform 0.4s ease;
        }

        /* Phóng to ảnh khi hover */
        .product-card:hover img {
            transform: scale(1.1);
        }

        .price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 10px 0;
        }

        /* Nút thêm vào giỏ màu đỏ */
        .btn-add-red {
            background: #ee4d2d;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 20px;
            width: 100%;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .btn-add-red:hover {
            background: #d73211;
        }

        .cart-icon {
            background: white;
            color: #ee4d2d;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <h2 style="text-align: center; color: #2c3e50;">DANH SÁCH SẢN PHẨM</h2>

        <?php foreach ($categories as $cat): ?>
            <h3 class="category-title" id="cate-<?php echo $cat['id']; ?>">
                <?php echo $cat['ten_danh_muc']; ?>
            </h3>

            <div class="product-grid">
                <?php
                $stmt_sp = $pdo->prepare("SELECT * FROM san_pham WHERE danh_muc_id = ? AND ten_san_pham LIKE ? ORDER BY id DESC LIMIT 4");
                $stmt_sp->execute([$cat['id'], "%$search%"]);
                while ($row = $stmt_sp->fetch()):
                ?>
                    <div class="product-card">
                        <a href="chi-tiet.php?id=<?php echo $row['id']; ?>">
                            <img src="assets/images/<?php echo $row['anh']; ?>">
                        </a>
                        <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                            <h4 style="height: 40px; overflow: hidden; margin: 10px 0;"><?php echo $row['ten_san_pham']; ?></h4>
                        </a>
                        <p class="price"><?php echo formatMoney($row['gia_ban']); ?></p>

                        <form action="them-vao-gio.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn-add-red">
                                <span>Thêm vào giỏ</span>
                                <span class="cart-icon">🛒</span>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>