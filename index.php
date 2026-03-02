<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

// 1. Lấy từ khóa tìm kiếm
$search = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// 2. Lấy danh sách các danh mục (Chỉ lấy danh mục có sản phẩm khớp với từ khóa)
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
    <style>
        /* Container chính */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Tiêu đề danh mục */
        .category-title {
            background: #f8f9fa;
            padding: 10px 20px;
            border-left: 5px solid #e74c3c;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
            color: #2c3e50;
        }

        /* Grid sản phẩm sử dụng Flexbox để dễ căn giữa */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* CĂN GIỮA: Nếu chỉ có 1 sản phẩm nó sẽ nằm giữa màn hình */
            justify-content: center;
        }

        /* Thẻ sản phẩm */
        .product-card {
            width: 220px;
            /* Độ rộng phù hợp để chia cột */
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: 0.3s;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-card h4 {
            margin: 10px 0;
            font-size: 1rem;
            color: #333;
            height: 40px;
            /* Giữ tiêu đề đều nhau */
            overflow: hidden;
        }

        .price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 5px 0;
        }

        .stock {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .view-detail {
            display: inline-block;
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .view-detail:hover {
            background: #2980b9;
        }

        /* Thanh tìm kiếm */
        .search-box {
            text-align: center;
            margin: 20px 0;
        }

        .search-box input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-box button {
            padding: 10px 20px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h2 style="text-align: center; color: #2c3e50;">DANH SÁCH SẢN PHẨM</h2>

        <div class="search-box">
            <form method="GET">
                <input type="text" name="keyword" placeholder="Tìm tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>

        <?php if (empty($categories)): ?>
            <p style="text-align: center; color: #666;">Không tìm thấy sản phẩm nào.</p>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <h3 class="category-title"><?php echo $cat['ten_danh_muc']; ?></h3>

                <div class="product-grid">
                    <?php
                    // Lấy sản phẩm thuộc danh mục hiện tại và theo từ khóa tìm kiếm
                    $sql_sp = "SELECT * FROM san_pham WHERE danh_muc_id = ? AND ten_san_pham LIKE ? ORDER BY id DESC";
                    $stmt_sp = $pdo->prepare($sql_sp);
                    $stmt_sp->execute([$cat['id'], "%$search%"]);

                    while ($row = $stmt_sp->fetch()):
                    ?>
                        <div class="product-card">
                            <img src="assets/images/<?php echo $row['anh']; ?>" alt="<?php echo $row['ten_san_pham']; ?>">
                            <h4><?php echo $row['ten_san_pham']; ?></h4>
                            <p class="price"><?php echo formatMoney($row['gia_ban']); ?></p>
                            <p class="stock">Kho: <?php echo $row['so_luong_kho']; ?></p>
                            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" class="view-detail">Xem chi tiết</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>
<?php include 'includes/footer.php'; ?>