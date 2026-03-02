<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

// Lấy danh sách danh mục để người dùng chọn
$categories = $pdo->query("SELECT * FROM danh_muc")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['ten_san_pham'];
    $danh_muc_id = $_POST['danh_muc_id'];
    $gia_nhap = $_POST['gia_nhap'];
    $gia_ban = $_POST['gia_ban'];
    $so_luong = $_POST['so_luong_kho'];
    $mo_ta = $_POST['mo_ta'];

    // Xử lý upload ảnh
    $anh = "";
    if ($_FILES['anh']['name'] != "") {
        $anh = $_FILES['anh']['name'];
        move_uploaded_file($_FILES['anh']['tmp_name'], "../assets/images/" . $anh);
    }

    $sql = "INSERT INTO san_pham (ten_san_pham, danh_muc_id, gia_nhap, gia_ban, so_luong_kho, mo_ta, anh) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$ten, $danh_muc_id, $gia_nhap, $gia_ban, $so_luong, $mo_ta, $anh]);

    header("Location: quan-ly-san-pham.php");
    exit();
}
?>

<div class="container" style="padding: 40px 20px;">
    <div style="max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #2c3e50; margin-bottom: 30px; font-size: 1.8rem;">📦 Thêm Sản Phẩm Mới</h2>
        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Tên sản phẩm:</label>
                <input type="text" name="ten_san_pham" placeholder="Nhập tên sản phẩm..." required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Danh mục:</label>
                <select name="danh_muc_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; background: white;">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['ten_danh_muc']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Giá nhập (VNĐ):</label>
                    <input type="number" name="gia_nhap" placeholder="Ví dụ: 100000" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Giá bán (VNĐ):</label>
                    <input type="number" name="gia_ban" placeholder="Ví dụ: 150000" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Số lượng kho:</label>
                    <input type="number" name="so_luong_kho" placeholder="Số lượng" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #34495e;">Mô tả chi tiết:</label>
                <textarea name="mo_ta" rows="4" placeholder="Nhập đặc điểm nổi bật của sản phẩm..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; resize: vertical;"></textarea>
            </div>

            <div style="margin-bottom: 30px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px dashed #ccc;">
                <label style="display: block; font-weight: bold; margin-bottom: 10px; color: #34495e;">Hình ảnh sản phẩm:</label>
                <input type="file" name="anh" accept="image/*" id="imageInput" style="margin-bottom: 15px;">
                <div id="preview-container" style="display: none;">
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Xem trước ảnh:</p>
                    <img id="preview-image" src="#" style="max-width: 150px; border-radius: 6px; border: 2px solid #fff;">
                </div>
            </div>

            <div style="display: flex; gap: 15px; align-items: center;">
                <button type="submit"
                    style="flex: 2; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s;">
                    🚀 Thêm Sản Phẩm Ngay
                </button>
                <a href="quan-ly-san-pham.php"
                    style="flex: 1; text-align: center; padding: 15px; background: #95a5a6; color: white; text-decoration: none; border-radius: 8px; font-size: 1rem; transition: background 0.3s;">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Script xem trước ảnh nhanh (Preview Image)
    const imageInput = document.getElementById('imageInput');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include '../includes/footer.php'; ?>