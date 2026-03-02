<?php
require_once '../config/db.php';
require_once '../includes/function.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mat_khau'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['ten_dang_nhap'];
        $_SESSION['ho_ten'] = $user['ho_ten'];
        $_SESSION['vai_tro'] = $user['vai_tro'];

        if ($user['vai_tro'] == 'admin') {
            header("Location: ../admin/index.php");
        } elseif ($user['vai_tro'] == 'nhan_vien') {
            header("Location: ../nhan-vien/index.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h2>ĐĂNG NHẬP</h2>
            <p>Chào mừng bạn quay trở lại!</p>
        </div>

        <?php if ($error) echo "<div class='alert alert-error'>$error</div>"; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Nhập username..." required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu..." required>
            </div>
            <button type="submit" class="btn-auth">Vào hệ thống</button>
        </form>

        <div class="auth-footer">
            <p>Chưa có tài khoản? <a href="dang-ky.php">Đăng ký ngay</a></p>
            <p style="margin-top: 10px;"><a href="../index.php" style="color: #7f8c8d;">← Quay lại trang chủ</a></p>
        </div>
    </div>
</body>

</html>