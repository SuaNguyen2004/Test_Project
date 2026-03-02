<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Xóa toàn bộ biến session
$_SESSION = array();

// Nếu muốn xóa cả Cookie session (an toàn tuyệt đối)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ
header("Location: ../index.php");
exit();
