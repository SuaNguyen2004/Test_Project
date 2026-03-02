<?php
require_once 'config/db.php';
require_once 'includes/function.php';

// Quan trọng: Header phải nằm ngoài cùng để tràn viền 100% màn hình
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
        /* CSS bổ trợ để căn giữa nội dung bên dưới Nav */
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
            /* Căn giữa sản phẩm khi số lượng ít */
        }

        .product-card {
            width: 220px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #fff;
            transition: 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }

        .price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
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
                <h3 class="category-title"><?php echo $cat['ten_danh_muc']; ?></h3>

                <div class="product-grid">
                    <?php
                    // CÁCH 1: Lấy 4 sản phẩm mới nhất của danh mục này (Khuyên dùng)
                    $sql_sp = "SELECT * FROM san_pham 
                   WHERE danh_muc_id = ? AND ten_san_pham LIKE ? 
                   ORDER BY id DESC 
                   LIMIT 4";

                    /* CÁCH 2: Nếu bạn muốn lấy 4 sản phẩm NGẪU NHIÊN, hãy dùng dòng này thay thế:
                    $sql_sp = "SELECT * FROM san_pham 
                   WHERE danh_muc_id = ? AND ten_san_pham LIKE ? 
                   ORDER BY RAND() 
                   LIMIT 4"; 
        */

                    $stmt_sp = $pdo->prepare($sql_sp);
                    $stmt_sp->execute([$cat['id'], "%$search%"]);

                    while ($row = $stmt_sp->fetch()):
                    ?>
                        <div class="product-card">
                            <img src="assets/images/<?php echo $row['anh']; ?>" alt="<?php echo $row['ten_san_pham']; ?>">
                            <h4><?php echo $row['ten_san_pham']; ?></h4>
                            <p class="price"><?php echo formatMoney($row['gia_ban']); ?></p>
                            <p class="stock">Kho: <?php echo $row['so_luong_kho']; ?></p>
                            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" class="view-detail"
                                style="display: inline-block; text-decoration: none; background: #3498db; color: white; padding: 8px 15px; border-radius: 5px;">Xem chi tiết</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>