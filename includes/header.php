<?php
// Đảm bảo session đã được khởi tạo để kiểm tra quyền
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav style="background: #2c3e50; color: white; width: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">

        <div class="nav-left">
            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.4rem; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.8rem;">🛒</span> TẠP HÓA GENIUS
            </a>
        </div>

        <div class="nav-right" style="display: flex; align-items: center; gap: 20px;">
            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: 500;">Trang Chủ</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu" style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: #ecf0f1;">
                        Chào,
                        <a href="/test_project/manager/profile.php" style="color: #f1c40f; text-decoration: none; font-weight: bold; border-bottom: 1px dashed #f1c40f;">
                            <?php echo $_SESSION['ho_ten']; ?>
                        </a>
                    </span>

                    <?php if ($_SESSION['vai_tro'] == 'admin'): ?>
                        <a href="/test_project/admin/index.php" style="color: #e74c3c; text-decoration: none; font-weight: bold; background: rgba(231, 76, 60, 0.1); padding: 5px 10px; border-radius: 4px;">Quản trị</a>

                    <?php elseif ($_SESSION['vai_tro'] == 'nhan_vien'): ?>
                        <a href="/test_project/nhan-vien/index.php" style="color: #2ecc71; text-decoration: none; font-weight: bold; background: rgba(46, 204, 113, 0.1); padding: 5px 10px; border-radius: 4px;">Xử lý đơn</a>

                    <?php else: ?>
                        <a href="/test_project/gio-hang.php" style="color: #5dade2; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                            <span>🛒</span> Giỏ hàng
                        </a>
                        <a href="/test_project/lich-su-mua-hang.php" style="color: white; text-decoration: none;">Lịch sử mua</a>
                    <?php endif; ?>

                    <a href="/test_project/manager/dang-xuat.php"
                        onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')"
                        style="color: #bdc3c7; text-decoration: none; padding: 5px 12px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 0.9rem; transition: 0.3s;">
                        Đăng xuất
                    </a>
                </div>

            <?php else: ?>
                <a href="/test_project/manager/dang-nhap.php" style="color: white; text-decoration: none;">Đăng nhập</a>
                <a href="/test_project/manager/dang-ky.php" style="background: #e67e22; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: bold;">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>

    <div style="background: #f8f9fa; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 30px; padding: 10px 20px; overflow-x: auto;">
            <a href="/test_project/index.php" style="text-decoration: none; color: #e74c3c; font-weight: bold; font-size: 0.85rem; text-transform: uppercase;">TẤT CẢ</a>

            <?php
            // Truy vấn lấy danh sách danh mục để tạo menu nhảy nhanh
            // $pdo được kế thừa từ file index.php hoặc các file có include header này
            if (isset($pdo)) {
                $stmt_nav = $pdo->query("SELECT * FROM danh_muc ORDER BY id ASC");
                while ($nav_item = $stmt_nav->fetch()):
            ?>
                    <a href="/test_project/index.php#cate-<?php echo $nav_item['id']; ?>"
                        class="nav-cate-link"
                        style="text-decoration: none; color: #333; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; white-space: nowrap; transition: 0.3s;">
                        <?php echo $nav_item['ten_danh_muc']; ?>
                    </a>
            <?php
                endwhile;
            }
            ?>
            <a href="#" style="text-decoration: none; color: #333; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Liên hệ</a>
        </div>
    </div>
</nav>

<style>
    /* Hiệu ứng hover cho các link danh mục */
    .nav-cate-link:hover {
        color: #e74c3c !important;
    }

    /* Đảm bảo thanh danh mục không bị vỡ trên mobile */
    div::-webkit-scrollbar {
        height: 4px;
    }

    div::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    /* SỬA LẠI ĐOẠN NÀY */
    nav {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        z-index: 99999 !important;
    }
</style>