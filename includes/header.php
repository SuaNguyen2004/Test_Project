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
                            <a href="/test_project/index.php#cate-<?php echo $nav_item['id']; ?>">
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

                    <?php if ($_SESSION['vai_tro'] == 'khach_hang'): ?>
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
        /* KHÓA CỨNG CHIỀU CAO HEADER */
        display: flex;
        align-items: center;
    }

    .nav-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        padding: 0 20px;
        height: 100%;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-shrink: 0;
    }

    .logo-text {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.3rem;
        letter-spacing: 1px;
    }

    /* Ô TÌM KIẾM CHIẾM KHÔNG GIAN NHƯNG GỌN CHIỀU CAO */
    .nav-search-fill {
        flex: 1;
        display: flex;
        justify-content: center;
        margin-top: 20px;
        padding: 0 30px;
    }

    .search-flex {
        display: flex;
        background: white;
        border-radius: 20px;
        padding: 2px 2px 2px 15px;
        width: 100%;
        max-width: 500px;
        /* Thu hẹp chiều rộng lại một chút cho đỡ thô */
        height: 32px;
        /* GIẢM CHIỀU CAO Ô SEARCH */
        align-items: center;
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
        width: 28px;
        height: 28px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-shrink: 0;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-name {
        color: #f1c40f;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-logout {
        color: #bdc3c7;
        text-decoration: none;
        padding: 2px 8px;
        border: 1px solid #bdc3c7;
        border-radius: 4px;
        font-size: 0.75rem;
    }

    .dropdown-btn {
        background: #34495e;
        color: white;
        border: none;
        padding: 6px 12px;
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
        top: 40px;
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