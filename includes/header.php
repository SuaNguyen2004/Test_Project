<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="nav-genius">
    <div class="nav-container">

        <div class="nav-left">
            <div class="menu-dropdown" style="position: relative;">
                <button class="dropdown-btn" onclick="toggleMenu()">
                    ☰ <span class="menu-text">Danh mục</span>
                </button>
                <div id="myDropdown" class="dropdown-content">
                    <a href="/test_project/index.php" style="font-weight: bold; color: #e74c3c;">Tất cả sản phẩm</a>
                    <?php
                    if (isset($pdo)) {
                        $stmt_nav = $pdo->query("SELECT * FROM danh_muc ORDER BY id ASC");
                        while ($nav_item = $stmt_nav->fetch()):
                    ?>
                            <a href="/test_project/index.php?category=<?php echo $nav_item['id']; ?>">
                                <?php echo htmlspecialchars($nav_item['ten_danh_muc']); ?>
                            </a>
                    <?php endwhile;
                    } ?>
                </div>
            </div>
            <a href="/test_project/index.php" class="logo-text">GENIUS</a>
        </div>

        <div class="nav-search-fill">
            <form action="/test_project/index.php" method="GET" class="search-flex">
                <input type="text" name="keyword" placeholder="Bạn muốn tìm gì?" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                <button type="submit">🔍</button>
            </form>
        </div>

        <div class="nav-right">
            <a href="/test_project/index.php" class="nav-link">Trang Chủ</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-box">
                    <span class="welcome-text">Chào, <a href="/test_project/manager/profile.php" class="user-name"><?php echo $_SESSION['ho_ten']; ?></a></span>

                    <?php if ($_SESSION['vai_tro'] == 'admin'): ?>
                        <a href="/test_project/admin/index.php" class="role-link admin-link">[Quản trị Admin]</a>
                    <?php elseif ($_SESSION['vai_tro'] == 'nhan_vien'): ?>
                        <a href="/test_project/nhan-vien/index.php" class="role-link staff-link">[Nhân viên]</a>
                    <?php else: ?>
                        <a href="/test_project/gio-hang.php" class="nav-link">🛒 Giỏ hàng</a>
                        <a href="/test_project/lich-su-mua-hang.php" class="nav-link">Lịch sử</a>
                    <?php endif; ?>

                    <a href="/test_project/manager/dang-xuat.php" class="btn-logout" onclick="return confirm('Bạn muốn thoát?')">Thoát</a>
                </div>
            <?php else: ?>
                <a href="/test_project/manager/dang-nhap.php" class="nav-link">Đăng nhập</a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<style>
    .nav-genius {
        background: #2c3e50;
        color: white;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 99999;
        font-family: 'Segoe UI', sans-serif;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        height: 60px;
        display: flex;
        align-items: center;
    }

    .nav-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        height: 100%;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .logo-text {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.35rem;
        letter-spacing: 1px;
        white-space: nowrap;
    }

    .nav-search-fill {
        flex: 1.5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .search-flex {
        display: flex;
        background: white;
        border-radius: 20px;
        padding: 0 2px 0 15px;
        width: 100%;
        max-width: 450px;
        height: 34px;
        align-items: center;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .search-flex input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 0.85rem;
        color: #333;
        background: transparent;
    }

    .search-flex button {
        background: #f1c40f;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .search-flex button:hover {
        background: #f39c12;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        justify-content: flex-end;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-name {
        color: #f1c40f;
        text-decoration: none;
        font-weight: bold;
    }

    /* Style riêng cho các link quản trị để làm nổi bật */
    .role-link {
        text-decoration: none;
        font-weight: bold;
        font-size: 0.85rem;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .admin-link {
        color: #ff4d4d;
        border: 1px solid #ff4d4d;
    }

    .admin-link:hover {
        background: #ff4d4d;
        color: white;
    }

    .staff-link {
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }

    .staff-link:hover {
        background: #2ecc71;
        color: white;
    }

    .btn-logout {
        color: #bdc3c7;
        text-decoration: none;
        padding: 3px 8px;
        border: 1px solid #bdc3c7;
        border-radius: 4px;
        font-size: 0.75rem;
    }

    .btn-logout:hover {
        background: #bdc3c7;
        color: #2c3e50;
    }

    .dropdown-btn {
        background: #34495e;
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 200px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 100000;
        border-radius: 4px;
        top: 45px;
    }

    .dropdown-content a {
        color: #333;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #eee;
        font-size: 0.85rem;
    }

    .show {
        display: block;
    }
</style>

<script>
    function toggleMenu() {
        document.getElementById("myDropdown").classList.toggle("show");
    }
    window.onclick = function(event) {
        if (!event.target.closest('.menu-dropdown')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
            }
        }
    }
</script>