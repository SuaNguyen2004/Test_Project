<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

// 1. Lấy dữ liệu từ URL
$search = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// 2. Logic truy vấn danh mục hiển thị
if ($category_id > 0) {
  // Nếu chọn 1 danh mục cụ thể
  $sql_cate = "SELECT * FROM danh_muc WHERE id = ?";
  $stmt_cate = $pdo->prepare($sql_cate);
  $stmt_cate->execute([$category_id]);
  $categories = $stmt_cate->fetchAll();
} else {
  // Nếu ở trang chủ mặc định hoặc đang tìm kiếm
  $sql_cate = "SELECT DISTINCT dm.* FROM danh_muc dm 
                 JOIN san_pham sp ON dm.id = sp.danh_muc_id 
                 WHERE sp.ten_san_pham LIKE ? ORDER BY dm.id ASC";
  $stmt_cate = $pdo->prepare($sql_cate);
  $stmt_cate->execute(["%$search%"]);
  $categories = $stmt_cate->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Tạp Hóa Genius</title>
  <link rel="stylesheet" href="assets/css/style1.css">
  <style>
    body {
      padding-top: 80px !important;
    }

    html {
      scroll-behavior: smooth;
      scroll-padding-top: 100px;
    }

    .main-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .category-title {
      background: #f8f9fa;
      padding: 10px 20px;
      border-left: 5px solid #e74c3c;
      margin: 30px 0 20px 0;
      text-transform: uppercase;
      color: #2c3e50;
    }

    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .product-card {
      width: 220px;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      background: #fff;
      transition: 0.3s;
      position: relative;
      overflow: hidden;
      box-sizing: border-box;
    }

    .product-card:hover {
      background-color: #f0f0f0 !important;
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: contain;
      transition: transform 0.4s ease;
    }

    .product-card:hover img {
      transform: scale(1.1);
    }

    .price {
      color: #e74c3c;
      font-weight: bold;
      font-size: 1.1rem;
      margin: 10px 0;
    }

    .btn-add-red {
      background: #ee4d2d;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 20px;
      width: 100%;
      cursor: pointer;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: 0.3s;
    }

    .btn-add-red:hover {
      background: #d73211;
    }

    .cart-icon {
      background: white;
      color: #ee4d2d;
      border-radius: 50%;
      width: 22px;
      height: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* banner */
    .banner-slider-container {
      max-width: 600px;
      /* GIẢM MỘT NỬA (Từ 1200px xuống 600px) */
      margin: 20px auto;
      /* NẰM Ở GIỮA */
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .banner-wrapper {
      display: flex;
      transition: transform 1.2s ease-in-out;
    }

    .banner-slide {
      min-width: 100%;
    }

    .banner-slide img {
      width: 100%;
      height: auto;
      display: block;
      object-fit: cover;
    }

    /* Điều chỉnh lại kích thước nút bấm cho phù hợp với banner nhỏ */
    .slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.2);
      color: white;
      border: none;
      padding: 10px 15px;
      /* Nhỏ hơn một chút */
      cursor: pointer;
      font-size: 18px;
      border-radius: 50%;
      transition: 0.3s;
      z-index: 10;
    }

    .slider-btn:hover {
      background: rgba(231, 76, 60, 0.8);
    }

    .prev-btn {
      left: 10px;
    }

    .next-btn {
      right: 10px;
    }

    .slider-dots {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
    }

    .dot {
      width: 8px;
      height: 8px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }

    .dot.active {
      background: #e74c3c;
      width: 20px;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <!-- banner -->
  <div class="banner-slider-container">
    <div class="banner-wrapper" id="bannerWrapper">
      <div class="banner-slide">
        <img src="./img_banner/banner1.png" alt="Banner 1">
      </div>
      <div class="banner-slide">
        <img src="./img_banner/banner2.png" alt="Banner 2">
      </div>
      <div class="banner-slide">
        <img src="./img_banner/banner3.png" alt="Banner 3">
      </div>
    </div>

    <button class="slider-btn prev-btn" onclick="moveSlide(-1)">&#10094;</button>
    <button class="slider-btn next-btn" onclick="moveSlide(1)">&#10095;</button>

    <div class="slider-dots">
      <span class="dot active" onclick="currentSlide(0)"></span>
      <span class="dot" onclick="currentSlide(1)"></span>
      <span class="dot" onclick="currentSlide(2)"></span>
    </div>
  </div>

  <script>
    let currentIndex = 0;
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;
    let autoSlideInterval;

    function showSlide(index) {
      if (index >= totalSlides) currentIndex = 0;
      else if (index < 0) currentIndex = totalSlides - 1;
      else currentIndex = index;

      // Trượt cụm wrapper
      const offset = -currentIndex * 100;
      document.getElementById('bannerWrapper').style.transform = `translateX(${offset}%)`;

      // Cập nhật trạng thái dấu chấm
      dots.forEach(dot => dot.classList.remove('active'));
      dots[currentIndex].classList.add('active');
    }

    function moveSlide(step) {
      showSlide(currentIndex + step);
      resetTimer(); // Reset lại thời gian 2s khi người dùng bấm nút
    }

    function currentSlide(index) {
      showSlide(index);
      resetTimer();
    }

    // Thiết lập tự động chạy 2 giây
    function startTimer() {
      autoSlideInterval = setInterval(() => {
        moveSlide(1);
      }, 2000); // 2000ms = 2s
    }

    function resetTimer() {
      clearInterval(autoSlideInterval);
      startTimer();
    }

    // Khởi chạy khi trang web tải xong
    startTimer();
  </script>

  <div class="main-wrapper">
    <h2 style="text-align: center; color: #2c3e50;">
      <?php
      if ($search) echo "KẾT QUẢ TÌM KIẾM: \"" . htmlspecialchars($search) . "\"";
      elseif ($category_id && isset($categories[0])) echo "DANH MỤC: " . $categories[0]['ten_danh_muc'];
      else echo "DANH SÁCH SẢN PHẨM";
      ?>
    </h2>

    <?php if (empty($categories)): ?>
      <p style="text-align: center; margin-top: 50px;">Không tìm thấy sản phẩm nào phù hợp.</p>
    <?php endif; ?>

    <?php foreach ($categories as $cat): ?>
      <h3 class="category-title" id="cate-<?php echo $cat['id']; ?>">
        <?php echo $cat['ten_danh_muc']; ?>
      </h3>

      <div class="product-grid">
        <?php
        // Truy vấn sản phẩm theo danh mục VÀ từ khóa tìm kiếm
        // Nếu không chọn danh mục cụ thể, LIMIT 4 để làm trang chủ đẹp
        // Nếu đã chọn danh mục, bỏ LIMIT để xem tất cả
        $limit = ($category_id > 0) ? "" : "LIMIT 4";
        $sql_sp = "SELECT * FROM san_pham WHERE danh_muc_id = ? AND ten_san_pham LIKE ? ORDER BY id DESC $limit";
        $stmt_sp = $pdo->prepare($sql_sp);
        $stmt_sp->execute([$cat['id'], "%$search%"]);

        while ($row = $stmt_sp->fetch()):
        ?>
          <div class="product-card">
            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>">
              <img src="assets/images/<?php echo $row['anh']; ?>">
            </a>
            <a href="chi-tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
              <h4 style="height: 40px; overflow: hidden; margin: 10px 0;"><?php echo $row['ten_san_pham']; ?></h4>
            </a>
            <p class="price"><?php echo formatMoney($row['gia_ban']); ?></p>

            <form action="them-vao-gio.php" method="POST">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="so_luong" value="1">
              <button type="submit" class="btn-add-red">
                <span>Thêm vào giỏ</span>
                <span class="cart-icon">🛒</span>
              </button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <?php include 'includes/footer.php'; ?>
</body>

</html>