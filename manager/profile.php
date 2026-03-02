<?php
require_once '../config/db.php';
require_once '../includes/function.php';

if (!isLoggedIn()) {
    header("Location: dang-nhap.php");
    exit();
}

include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Xử lý khi người dùng nhấn nút Cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten_moi = trim($_POST['ho_ten']);
    $mat_khau_moi = $_POST['new_password'];

    try {
        // 1. Cập nhật Họ tên
        $stmt = $pdo->prepare("UPDATE nguoi_dung SET ho_ten = ? WHERE id = ?");
        $stmt->execute([$ho_ten_moi, $user_id]);
        $_SESSION['ho_ten'] = $ho_ten_moi; // Cập nhật hiển thị trên Header ngay lập tức

        // 2. Cập nhật Mật khẩu (nếu có nhập)
        if (!empty($mat_khau_moi)) {
            $pass_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
            $stmt_pass = $pdo->prepare("UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?");
            $stmt_pass->execute([$pass_hash, $user_id]);
        }

        $message = "<p style='color: green;'>Cập nhật thông tin thành công!</p>";
    } catch (PDOException $e) {
        $message = "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
    }
}

// Lấy thông tin mới nhất từ DB để hiển thị vào Form
$user = $pdo->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
$user->execute([$user_id]);
$u = $user->fetch();
?>

<div class="container" style="padding: 20px; max-width: 500px; margin-top: 30px;">
    <div class="card" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
        <h2 style="text-align: center; color: #2c3e50;">Thiết lập tài khoản</h2>
        <hr>
        <?php echo $message; ?>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label>Tên đăng nhập (Không thể sửa):</label><br>
                <input type="text" value="<?php echo $u['ten_dang_nhap']; ?>" disabled
                    style="width: 100%; background: #eee; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Họ và tên:</label><br>
                <input type="text" name="ho_ten" value="<?php echo $u['ho_ten']; ?>" required
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Mật khẩu mới (Để trống nếu không muốn đổi):</label><br>
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới"
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem;">
                Lưu thay đổi
            </button>
        </form>

        <div style="margin-top: 15px; text-align: center;">
            <a href="../index.php" style="color: #7f8c8d; text-decoration: none;">← Quay lại trang chủ</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>