<link rel="stylesheet" href="../assets/css/style3.css">
<?php
require_once '../config/db.php';
require_once '../includes/function.php';

if (!isLoggedIn()) {
    header("Location: dang-nhap.php");
    exit();
}

include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// Lấy thông tin người dùng hiện tại từ Database
$stmt_user = $pdo->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
$stmt_user->execute([$user_id]);
$u = $stmt_user->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten_moi = trim($_POST['ho_ten']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // 1. Cập nhật Họ tên (Luôn cập nhật nếu có thay đổi)
        $stmt_name = $pdo->prepare("UPDATE nguoi_dung SET ho_ten = ? WHERE id = ?");
        $stmt_name->execute([$ho_ten_moi, $user_id]);
        $_SESSION['ho_ten'] = $ho_ten_moi;

        // 2. Kiểm tra nếu người dùng muốn đổi mật khẩu
        if (!empty($old_password) || !empty($new_password) || !empty($confirm_password)) {

            // Bước A: Kiểm tra mật khẩu cũ có đúng không
            if (!password_verify($old_password, $u['mat_khau'])) {
                $error_msg = "Sai mật khẩu cũ!";
            }
            // Bước B: Kiểm tra mật khẩu mới và nhập lại có khớp không
            else if ($new_password !== $confirm_password) {
                $error_msg = "Hai mật khẩu mới chưa khớp nhau!";
            }
            // Bước C: Nếu mật khẩu mới để trống
            else if (empty($new_password)) {
                $error_msg = "Vui lòng nhập mật khẩu mới!";
            }
            // Nếu mọi thứ ok thì mới đổi
            else {
                $pass_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_pass = $pdo->prepare("UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?");
                $stmt_pass->execute([$pass_hash, $user_id]);
                $success_msg = "Cập nhật thông tin và mật khẩu thành công!";
            }
        } else {
            $success_msg = "Đã cập nhật họ tên thành công!";
        }
    } catch (PDOException $e) {
        $error_msg = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<div style="max-width: 500px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: 'Segoe UI', sans-serif;">
    <h2 style="text-align: center; color: #2c3e50; margin-bottom: 25px;">Chỉnh sửa hồ sơ</h2>

    <?php if ($error_msg): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <?php if ($success_msg): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Tên đăng nhập:</label>
            <input type="text" value="<?php echo htmlspecialchars($u['ten_dang_nhap']); ?>" disabled
                style="width: 100%; padding: 10px; background: #f1f1f1; border: 1px solid #ccc; border-radius: 4px; cursor: not-allowed;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Họ và tên:</label>
            <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($u['ho_ten']); ?>" required
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">
        <p style="font-size: 0.9rem; color: #e67e22; font-style: italic; margin-bottom: 15px;">* Chỉ nhập các ô dưới đây nếu bạn muốn đổi mật khẩu</p>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Mật khẩu cũ:</label>
            <input type="password" name="old_password" placeholder="Nhập mật khẩu hiện tại"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Mật khẩu mới:</label>
            <input type="password" name="new_password" placeholder="Mật khẩu mới"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Nhập lại mật khẩu mới:</label>
            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: bold; transition: background 0.3s;">
            Lưu thay đổi
        </button>

        <a href="../index.php" style="display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; font-size: 0.9rem;">Hủy bỏ</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>