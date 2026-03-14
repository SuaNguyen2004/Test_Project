<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav style="background: #2c3e50; color: white; width: 100%; position: fixed; top: 0; left: 0; z-index: 99999; font-family: 'Segoe UI', sans-serif; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 10px 20px;">

        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="menu-dropdown" style="position: relative;">
                <button onclick="toggleMenu()" style="background: #34495e; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-weight: bold; font-size: 0.9rem;">
                    ☰ <span class="menu-text">Danh mục</span>
                </button>
                <div id="myDropdown" class="dropdown-content">
                    <a href="/test_project/index.php" style="font-weight: bold; color: #e74c3c;">Tất cả sản phẩm</a>
                    <?php
                    // Kết nối DB đã có từ file cha (index.php)
                    if (isset($pdo)) {
                        $stmt_nav = $pdo->query("SELECT * FROM danh_muc ORDER BY id ASC");
                        while ($nav_item = $stmt_nav->fetch()):
                    ?>
                            <a href="/test_project/index.php#cate-<?php echo $nav_item['id']; ?>">
                                <?php echo $nav_item['ten_danh_muc']; ?>
                            </a>
                    <?php endwhile;
                    } ?>
                </div>
            </div>

            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: bold; font-size: 1.4rem; letter-spacing: 1px;">
                GENIUS
            </a>
        </div>

        <div class="nav-right" style="display: flex; align-items: center; gap: 20px;">
            <a href="/test_project/index.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 0.95rem;">Trang Chủ</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 0.9rem;">Chào, <a href="/test_project/manager/profile.php" style="color: #f1c40f; text-decoration: none; font-weight: bold;"><?php echo $_SESSION['ho_ten']; ?></a></span>

                    <?php if ($_SESSION['vai_tro'] == 'admin'): ?>
                        <a href="/test_project/admin/index.php" style="color: #ff4d4d; font-weight: bold; text-decoration: none; font-size: 0.9rem;">[Quản trị]</a>
                    <?php elseif ($_SESSION['vai_tro'] == 'nhan_vien'): ?>
                        <a href="/test_project/nhan-vien/index.php" style="color: #2ecc71; font-weight: bold; text-decoration: none; font-size: 0.9rem;">[Nhân viên]</a>
                    <?php else: ?>
                        <a href="/test_project/gio-hang.php" style="color: #5dade2; text-decoration: none; font-size: 0.9rem;">🛒 Giỏ hàng</a>
                        <a href="/test_project/lich-su-mua-hang.php" style="color: white; text-decoration: none; font-size: 0.9rem;">Lịch sử</a>
                    <?php endif; ?>

                    <a href="/test_project/manager/dang-xuat.php" onclick="return confirm('Bạn muốn đăng xuất?')" style="color: #bdc3c7; text-decoration: none; padding: 4px 8px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 0.8rem;">Thoát</a>
                </div>
            <?php else: ?>
                <a href="/test_project/manager/dang-nhap.php" style="color: white; text-decoration: none; font-size: 0.9rem;">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 220px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 100000;
        border-radius: 4px;
        margin-top: 10px;
        border: 1px solid #ddd;
    }

    .dropdown-content a {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #f1f1f1;
        font-size: 0.9rem;
    }

    .dropdown-content a:last-child {
        border-bottom: none;
    }

    .dropdown-content a:hover {
        background-color: #f8f9fa;
        color: #e74c3c;
        padding-left: 20px;
        transition: 0.2s;
    }

    .show {
        display: block;
    }

    /* Ẩn chữ "Danh mục" trên màn hình quá nhỏ để tiết kiệm diện tích */
    @media (max-width: 600px) {
        .menu-text {
            display: none;
        }
    }
</style>

<script>
    function toggleMenu() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    // Đóng menu nếu click ra ngoài
    window.onclick = function(event) {
        if (!event.target.closest('.menu-dropdown')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>