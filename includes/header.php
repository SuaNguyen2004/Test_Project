<nav style="background: #2c3e50; padding: 15px 0; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; width: 100%;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
        <div class="nav-left">
            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.3rem;">🛒 TẠP HÓA GENIUS</a>
        </div>

        <div class="nav-right" style="display: flex; align-items: center; gap: 20px;">
            <a href="/test_project/index.php" style="color: white; text-decoration: none;">Trang Chủ</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu" style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: #ecf0f1;">
                        Chào,
                        <a href="/test_project/manager/profile.php" style="color: #f1c40f; text-decoration: none; font-weight: bold;">
                            <?php echo $_SESSION['ho_ten']; ?>
                        </a>
                    </span>

                    <?php if ($_SESSION['vai_tro'] == 'khach_hang'): ?>
                        <a href="/test_project/gio-hang.php" style="color: #5dade2; text-decoration: none;">Giỏ hàng</a>
                        <a href="/test_project/lich-su-mua-hang.php" style="color: white; text-decoration: none;">Lịch sử mua</a>
                    <?php endif; ?>

                    <?php if ($_SESSION['vai_tro'] == 'admin'): ?>
                        <a href="/test_project/admin/index.php" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Quản trị</a>
                    <?php endif; ?>

                    <a href="/test_project/manager/dang-xuat.php" onclick="return confirm('Đăng xuất?')" style="color: #bdc3c7; text-decoration: none; padding: 5px 10px; border: 1px solid #bdc3c7; border-radius: 4px;">Đăng xuất</a>
                </div>
            <?php else: ?>
                <a href="/test_project/manager/dang-nhap.php" style="color: white; text-decoration: none;">Đăng nhập</a>
                <a href="/test_project/manager/dang-ky.php" style="background: #3498db; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px;">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>