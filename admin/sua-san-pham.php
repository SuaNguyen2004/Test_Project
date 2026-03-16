<?php
require_once '../config/db.php';
require_once '../includes/function.php';

// 1. KIỂM TRA QUYỀN TRUY CẬP TRƯỚC
checkAdmin();

// 2. LẤY THÔNG TIN SẢN PHẨM HIỆN TẠI
if (!isset($_GET['id'])) {
    header("Location: quan-ly-san-pham.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    die("Sản phẩm không tồn tại!");
}

// 3. XỬ LÝ LOGIC CẬP NHẬT (PHẢI ĐẶT TRƯỚC KHI INCLUDE HEADER)
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

    // Cập nhật Database
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

    // CHUYỂN HƯỚNG NGAY SAU KHI CẬP NHẬT XONG
    header("Location: quan-ly-san-pham.php");
    exit();
}

// 4. LẤY DANH SÁCH DANH MỤC ĐỂ HIỂN THỊ TRONG FORM
$categories = $pdo->query("SELECT * FROM danh_muc")->fetchAll();

// 5. BẮT ĐẦU PHẦN GIAO DIỆN
include '../includes/header.php';
?>

<link rel="stylesheet" href="../assets/css/style3.css">

<div class="container" style="padding: 20px; max-width: 800px; margin: auto;">
    <div class="card" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50;">Sửa sản phẩm: <?php echo htmlspecialchars($p['ten_san_pham']); ?></h2>
        <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Tên sản phẩm:</label><br>
                <input type="text" name="ten_san_pham" value="<?php echo htmlspecialchars($p['ten_san_pham']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Danh mục:</label><br>
                <select name="danh_muc_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $p['danh_muc_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold;">Giá nhập (VNĐ):</label><br>
                    <input type="number" name="gia_nhap" value="<?php echo (int)$p['gia_nhap']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="flex: 1;">
                    <label style="font-weight: bold;">Giá bán (VNĐ):</label><br>
                    <input type="number" name="gia_ban" value="<?php echo (int)$p['gia_ban']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="flex: 1;">
                    <label style="font-weight: bold;">Số lượng kho:</label><br>
                    <input type="number" name="so_luong_kho" value="<?php echo $p['so_luong_kho']; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Mô tả sản phẩm:</label><br>
                <textarea name="mo_ta" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($p['mo_ta']); ?></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Ảnh sản phẩm:</label><br>
                <div style="margin: 10px 0;">
                    <p style="font-size: 0.8rem; color: #666;">Ảnh hiện tại:</p>
                    <img src="../assets/images/<?php echo $p['anh']; ?>" width="150" style="border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <input type="file" name="anh" accept="image/*">
                <p style="font-size: 0.8rem; color: #e67e22; margin-top: 5px;">* Chỉ chọn file nếu bạn muốn thay đổi ảnh.</p>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" style="background: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    💾 Lưu thay đổi
                </button>
                <a href="quan-ly-san-pham.php" style="padding: 12px 25px; background: #95a5a6; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>