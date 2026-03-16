<?php
require_once '../config/db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'khach_hang';

    try {
        $sql = "INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, vai_tro) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password, $fullname, $role]);
        $success = "Đăng ký thành công! <a href='dang-nhap.php' style='color: #3498db; font-weight: bold;'>Đăng nhập ngay</a>";
    } catch (PDOException $e) {
        $error = "Lỗi: Tên đăng nhập có thể đã tồn tại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký thành viên</title>
    <link rel="stylesheet" href="../assets/css/style2.css">
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h2>TẠO TÀI KHOẢN</h2>
            <p>Tham gia cùng Tạp Hóa Genius</p>
        </div>

        <?php if ($error) echo "<div class='alert alert-error'>$error</div>"; ?>
        <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Nhập username..." required>
            </div>
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" placeholder="Nhập họ tên đầy đủ..." required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu..." required>
            </div>
            <button type="submit" class="btn-auth">Đăng ký tài khoản</button>
        </form>

        <div class="auth-footer">
            <p>Đã có tài khoản? <a href="dang-nhap.php">Đăng nhập ngay</a></p>
        </div>
    </div>
</body>

</html>