<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $san_pham_id = $_POST['san_pham_id'];
    $noi_dung = htmlspecialchars($_POST['noi_dung']); // Bảo mật chống XSS
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO danh_gia (nguoi_dung_id, san_pham_id, noi_dung) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $san_pham_id, $noi_dung]);

    header("Location: chi-tiet.php?id=" . $san_pham_id);
    exit();
}
