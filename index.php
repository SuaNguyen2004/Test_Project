<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php'; // Header ở ngoài cùng để tràn viền

$search = isset($_GET['keyword']) ? $_GET['keyword'] : '';

$sql_cate = "SELECT DISTINCT dm.* FROM danh_muc dm 
             JOIN san_pham sp ON dm.id = sp.danh_muc_id 
             WHERE sp.ten_san_pham LIKE ? ORDER BY dm.id ASC";
$stmt_cate = $pdo->prepare($sql_cate);
$stmt_cate->execute(["%$search%"]);
$categories = $stmt_cate->fetchAll();
?>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <h2 style="text-align: center; color: #2c3e50;">DANH SÁCH SẢN PHẨM</h2>

    <div style="text-align: center; margin: 20px 0;">
        <form method="GET">
            <input type="text" name="keyword" placeholder="Tìm tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" style="padding: 10px 20px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer;">Tìm kiếm</button>
        </form>
    </div>

    <?php foreach ($categories as $cat): ?>
        <h3 style="background: #f8f9fa; padding: 10px 20px; border-left: 5px solid #e74c3c; margin: 30px 0 20px 0;"><?php echo $cat['ten_danh_muc']; ?></h3>
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <?php
            $stmt_sp = $pdo->prepare("SELECT * FROM san_pham WHERE danh_muc_id = ? AND ten_san_pham LIKE ?");
            $stmt_sp->execute([$cat['id'], "%$search%"]);
            while ($row = $stmt_sp->fetch()):
            ?>
                <div style="width: 220px; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: #fff;">
                    <img src="assets/images/<?php echo $row['anh']; ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 5px;">
                    <h4 style="margin: 10px 0; height: 40px; overflow: hidden;"><?php echo $row['ten_san_pham']; ?></h4>
                    <p style="color: #e74c3c; font-weight: bold;"><?php echo formatMoney($row['gia_ban']); ?></p>
                    <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; display: inline-block;">Xem chi tiết</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>