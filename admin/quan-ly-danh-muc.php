<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

// 1. Xử lý Thêm danh mục mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_them'])) {
    $ten_dm = trim($_POST['ten_danh_muc']);
    if (!empty($ten_dm)) {
        $stmt = $pdo->prepare("INSERT INTO danh_muc (ten_danh_muc) VALUES (?)");
        $stmt->execute([$ten_dm]);
        echo "<script>alert('Thêm danh mục thành công!'); window.location='quan-ly-danh-muc.php';</script>";
    }
}

// 2. Xử lý Xóa danh mục
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM danh_muc WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: quan-ly-danh-muc.php");
    } catch (PDOException $e) {
        // Lỗi này xảy ra nếu danh mục đang có sản phẩm (ràng buộc khóa ngoại)
        echo "<script>alert('Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục!'); window.location='quan-ly-danh-muc.php';</script>";
    }
}

// 3. Lấy danh sách danh mục
$categories = $pdo->query("SELECT * FROM danh_muc ORDER BY id DESC")->fetchAll();
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: auto;">
    <h2 style="color: #2c3e50;">📁 Quản lý danh mục</h2>

    <div style="background: #f4f7f6; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #ddd;">
        <form method="POST" style="display: flex; gap: 10px;">
            <input type="text" name="ten_danh_muc" placeholder="Nhập tên danh mục mới (VD: Sữa Bột)..." required
                style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" name="btn_them"
                style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                + Thêm mới
            </button>
        </form>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse; background: white;">
        <thead style="background: #2c3e50; color: white;">
            <tr>
                <th style="padding: 12px;">ID</th>
                <th>Tên danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr style="text-align: center;">
                    <td style="padding: 10px;"><?php echo $cat['id']; ?></td>
                    <td style="text-align: left; padding-left: 20px;"><?php echo $cat['ten_danh_muc']; ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $cat['id']; ?>"
                            onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')"
                            style="color: #e74c3c; text-decoration: none; font-weight: bold;">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <a href="index.php" style="color: #7f8c8d; text-decoration: none;">← Quay lại Dashboard</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>