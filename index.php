<?php
require_once 'config/db.php';
require_once 'includes/function.php';

// Header nằm ngoài cùng để tràn viền 100% màn hình
include 'includes/header.php';

// Lấy từ khóa tìm kiếm
$search = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Lấy danh sách các danh mục có sản phẩm khớp với từ khóa
$sql_cate = "SELECT DISTINCT dm.* FROM danh_muc dm 
             JOIN san_pham sp ON dm.id = sp.danh_muc_id 
             WHERE sp.ten_san_pham LIKE ? 
             ORDER BY dm.id ASC";
$stmt_cate = $pdo->prepare($sql_cate);
$stmt_cate->execute(["%$search%"]);
$categories = $stmt_cate->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cửa Hàng Tạp Hóa Online</title>
    <link rel="stylesheet" href="assets/css/style1.css">
    <style>
        /* GIỮ NGUYÊN PHẦN STYLE CŨ CỦA BẠN */
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

        .product-card {
            width: 220px;
            /* GIỮ ĐÚNG KÍCH THƯỚC BẠN MUỐN */
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #fff;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            /* Để ảnh phóng to không tràn khung */
            box-sizing: border-box;
        }

        /* HIỆU ỨNG MỚI: Đổi nền xám và bóng đổ khi hover */
        .product-card:hover {
            background-color: #f0f0f0 !important;
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
            z-index: 10;
            /* Thấp hơn nav (99999) */
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            /* Sửa lỗi ảnh bị méo/to - giữ nguyên tỉ lệ lon bia/hộp sữa */
            border-radius: 5px;
            transition: transform 0.4s ease;
        }

        /* HIỆU ỨNG MỚI: Phóng to ảnh khi hover */
        .product-card:hover img {
            transform: scale(1.1);
        }

        .price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 10px 0;
        }
    </style>
</head>

<body style="margin: 0; padding: 0;">

    <div class="main-wrapper">
        <h2 style="text-align: center; color: #2c3e50;">DANH SÁCH SẢN PHẨM</h2>

        <div style="text-align: center; margin: 20px 0;">
            <form method="GET">
                <input type="text" name="keyword" placeholder="Tìm tên sản phẩm..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" style="padding: 10px 20px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer;">Tìm kiếm</button>
            </form>
        </div>

        <?php if (empty($categories)): ?>
            <p style="text-align: center; color: #666;">Không tìm thấy sản phẩm nào.</p>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <h3 class="category-title" id="cate-<?php echo $cat['id']; ?>">
                    <?php echo $cat['ten_danh_muc']; ?>
                </h3>

                <div class="product-grid">
                    <?php
                    $sql_sp = "SELECT * FROM san_pham 
                               WHERE danh_muc_id = ? AND ten_san_pham LIKE ? 
                               ORDER BY id DESC LIMIT 4";
                    $stmt_sp = $pdo->prepare($sql_sp);
                    $stmt_sp->execute([$cat['id'], "%$search%"]);

                    while ($row = $stmt_sp->fetch()):
                    ?>
                        <div class="product-card">
                            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; display: block;">
                                <img src="assets/images/<?php echo $row['anh']; ?>" alt="<?php echo $row['ten_san_pham']; ?>">
                            </a>

                            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <h4 style="margin: 10px 0; height: 40px; overflow: hidden;"><?php echo $row['ten_san_pham']; ?></h4>
                            </a>

                            <p class="price"><?php echo formatMoney($row['gia_ban']); ?></p>
                            <p style="font-size: 0.85rem; color: #666;">Kho: <?php echo $row['so_luong_kho']; ?></p>

                            <?php if ($row['so_luong_kho'] > 0): ?>
                                <form action="them-vao-gio.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" class="btn-add-cart-custom">
                                        <span>Thêm vào giỏ</span>
                                        <span class="cart-icon-circle">🛒</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="btn-add-cart-custom" style="background: #bdc3c7; cursor: not-allowed;">
                                    <span>Hết hàng</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>