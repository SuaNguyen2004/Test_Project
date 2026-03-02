<nav style="background: #2c3e50; padding: 15px 0; color: white; width: 100%; font-family: 'Segoe UI', sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
        <div class="nav-left">
            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.3rem;">🛒 TẠP HÓA GENIUS</a>
        </div>

        <div class="nav-right" style="display: flex; align-items: center; gap: 20px;">
            <a href="/test_project/index.php" style="color: white; text-decoration: none;">Trang Chủ</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span>Chào, <strong style="color: #f1c40f;"><?php echo $_SESSION['ho_ten']; ?></strong></span>
                    <?php if ($_SESSION['vai_tro'] == 'admin'): ?>
                        <a href="/test_project/admin/index.php" style="color: #e74c3c; font-weight: bold; text-decoration: none;">Quản trị</a>
                    <?php else: ?>
                        <a href="/test_project/gio-hang.php" style="color: #5dade2; text-decoration: none;">Giỏ hàng</a>
                    <?php endif; ?>
                    <a href="/test_project/manager/dang-xuat.php" style="color: #bdc3c7; text-decoration: none; border: 1px solid #bdc3c7; padding: 5px 10px; border-radius: 4px;">Đăng xuất</a>
                </div>
            <?php else: ?>
                <a href="/test_project/manager/dang-nhap.php" style="color: white; text-decoration: none;">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>