<?php
// Kiểm tra và khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    // Đảm bảo session có hiệu lực cho tất cả các thư mục trong dự án
    session_set_cookie_params(0, '/test_project/');
    session_start();
}

/**
 * Kiểm tra xem người dùng đã đăng nhập hay chưa
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra vai trò của người dùng hiện tại
 * @param string $role (admin, nhan_vien, khach_hang)
 * @return bool
 */
function hasRole($role)
{
    return (isset($_SESSION['vai_tro']) && trim($_SESSION['vai_tro']) === $role);
}

/**
 * Bảo vệ trang Admin: Nếu không phải Admin thì chuyển hướng về trang đăng nhập
 */
function checkAdmin()
{
    if (!isLoggedIn() || !hasRole('admin')) {
        header("Location: /test_project/manager/dang-nhap.php");
        exit();
    }
}

/**
 * Bảo vệ trang Nhân viên
 */
function checkNhanVien()
{
    if (!isLoggedIn() || (!hasRole('nhan_vien') && !hasRole('admin'))) {
        header("Location: /test_project/manager/dang-nhap.php");
        exit();
    }
}

/**
 * Hàm hỗ trợ định dạng tiền tệ Việt Nam (Dùng cho các file hiển thị)
 */
function formatMoney($number)
{
    return number_format($number, 0, ',', '.') . ' VNĐ';
}
