<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

$success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $name = $_POST['fullname'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, ho_ten, vai_tro) VALUES (?, ?, ?, 'nhan_vien')");
    $stmt->execute([$user, $pass, $name]);
    $success = "Đã tạo tài khoản nhân viên thành công!";
}

$staffs = $pdo->query("SELECT * FROM nguoi_dung WHERE vai_tro = 'nhan_vien'")->fetchAll();
?>

<div class="container" style="padding: 20px;">
    <h2>Quản lý nhân sự</h2>
    <?php if ($success) echo "<p style='color:green'>$success</p>"; ?>

    <form method="POST" style="margin-bottom: 30px; background: #f4f4f4; padding: 20px;">
        <h4>Cấp tài khoản mới cho nhân viên</h4>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="text" name="fullname" placeholder="Họ tên nhân viên" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Tạo tài khoản</button>
    </form>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #eee;">
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Ngày gia nhập</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $s): ?>
                <tr style="text-align: center;">
                    <td><?php echo $s['id']; ?></td>
                    <td><?php echo $s['ten_dang_nhap']; ?></td>
                    <td><?php echo $s['ho_ten']; ?></td>
                    <td><?php echo $s['ngay_tao']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>