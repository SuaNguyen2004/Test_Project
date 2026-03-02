<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

// Lấy danh sách danh mục để chọn
$categories = $pdo->query("SELECT * FROM danh_muc")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['ten_san_pham'];
    $danh_muc_id = $_POST['danh_muc_id'];
    $gia_nhap = $_POST['gia_nhap'];
    $gia_ban = $_POST['gia_ban'];
    $ton_kho = $_POST['so_luong_kho'];
    $mo_ta = $_POST['mo_ta'];

    // Xử lý ảnh
    $anh = $p['anh'];
    if ($_FILES['anh']['name'] != "") {
        $anh = $_FILES['anh']['name'];
        move_uploaded_file($_FILES['anh']['tmp_name'], "../assets/images/" . $anh);
    }

    // Cập nhật đầy đủ các trường
    $sql = "UPDATE san_pham SET 
            ten_san_pham = ?, 
            danh_muc_id = ?, 
            gia_nhap = ?, 
            gia_ban = ?, 
            so_luong_kho = ?, 
            mo_ta = ?, 
            anh = ? 
            WHERE id = ?";

    $pdo->prepare($sql)->execute([
        $ten,
        $danh_muc_id,
        $gia_nhap,
        $gia_ban,
        $ton_kho,
        $mo_ta,
        $anh,
        $id
    ]);

    header("Location: quan-ly-san-pham.php");
    exit();
}
?>

<div class="container" style="padding: 20px; max-width: 800px; margin: auto;">
    <div class="card" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2>Sửa sản phẩm: <?php echo htmlspecialchars($p['ten_san_pham']); ?></h2>
        <hr>
        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label>Tên sản phẩm:</label><br>
                <input type="text" name="ten_san_pham" value="<?php echo $p['ten_san_pham']; ?>" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Danh mục:</label><br>
                <select name="danh_muc_id" required style="width: 100%; padding: 8px;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $p['danh_muc_id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['ten_danh_muc']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Giá nhập (VNĐ):</label><br>
                    <input type="number" name="gia_nhap" value="<?php echo (int)$p['gia_nhap']; ?>" required style="width: 100%; padding: 8px;">
                </div>

                <div style="flex: 1;">
                    <label>Giá bán (VNĐ):</label><br>
                    <input type="number" name="gia_ban" value="<?php echo (int)$p['gia_ban']; ?>" required style="width: 100%; padding: 8px;">
                </div>
                <div style="flex: 1;">
                    <label>Số lượng kho:</label><br>
                    <input type="number" name="so_luong_kho" value="<?php echo $p['so_luong_kho']; ?>" required style="width: 100%; padding: 8px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Mô tả sản phẩm:</label><br>
                <textarea name="mo_ta" rows="5" style="width: 100%; padding: 8px;"><?php echo $p['mo_ta']; ?></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Ảnh sản phẩm:</label><br>
                <img src="../assets/images/<?php echo $p['anh']; ?>" width="75%" id="preview-image" style="margin-bottom: 10px; border: 1px solid #ddd;"><br>
                <input type="file" name="anh" accept="image/*">
                <p style="font-size: 0.8rem; color: #666;">Chọn ảnh mới nếu muốn thay đổi ảnh hiện tại.</p>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" style="background: #27ae60; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    Lưu thay đổi
                </button>
                <a href="quan-ly-san-pham.php" style="margin-left: 10px; color: #666; text-decoration: none;">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>