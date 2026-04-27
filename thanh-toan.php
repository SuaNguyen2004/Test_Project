<?php
require_once 'config/db.php';
require_once 'includes/function.php';

if (!isLoggedIn()) {
  header("Location: manager/dang-nhap.php");
  exit();
}

// 1. TÍNH TOÁN TỔNG TIỀN TRƯỚC KHI HIỂN THỊ
$tong_tien = 0;
if (!empty($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) {
    $tong_tien += $item['gia'] * $item['so_luong'];
  }
} else {
  // Nếu giỏ hàng trống mà truy cập trang này thì quay lại giỏ hàng
  header("Location: gio-hang.php");
  exit();
}

// 2. XỬ LÝ KHI BẤM NÚT XÁC NHẬN ĐẶT HÀNG (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user_id = $_SESSION['user_id'];
  $ghi_chu = trim($_POST['ghi_chu']);

  try {
    $pdo->beginTransaction();

    // Lưu đơn hàng vào bảng don_hang
    $stmt = $pdo->prepare("INSERT INTO don_hang (khach_hang_id, tong_tien, ghi_chu, trang_thai) VALUES (?, ?, ?, 'cho_duyet')");
    $stmt->execute([$user_id, $tong_tien, $ghi_chu]);
    $order_id = $pdo->lastInsertId();

    // Lưu chi tiết và trừ tồn kho
    foreach ($_SESSION['cart'] as $item) {
      // Kiểm tra tồn kho thực tế
      $st = $pdo->prepare("SELECT so_luong_kho, ten_san_pham FROM san_pham WHERE id = ? FOR UPDATE");
      $st->execute([$item['id']]);
      $p_check = $st->fetch();

      if ($p_check['so_luong_kho'] < $item['so_luong']) {
        $pdo->rollBack();
        echo "<script>alert('Sản phẩm " . $p_check['ten_san_pham'] . " không đủ hàng. Vui lòng kiểm tra lại!'); window.location.href = 'gio-hang.php';</script>";
        exit();
      }

      // Thêm vào bảng chi tiết
      $stmt_detail = $pdo->prepare("INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia_luc_mua) VALUES (?, ?, ?, ?)");
      $stmt_detail->execute([$order_id, $item['id'], $item['so_luong'], $item['gia']]);

      // Cập nhật trừ kho
      $stmt_update = $pdo->prepare("UPDATE san_pham SET so_luong_kho = so_luong_kho - ? WHERE id = ?");
      $stmt_update->execute([$item['so_luong'], $item['id']]);
    }

    $pdo->commit();
    unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt thành công
    echo "<script>alert('Đặt hàng thành công!'); window.location='index.php';</script>";
  } catch (Exception $e) {
    $pdo->rollBack();
    die("Lỗi hệ thống: " . $e->getMessage());
  }
}

include 'includes/header.php';
?>

<style>
  .checkout-body {
    background-color: #f4f7f6;
    min-height: 100vh;
    padding-top: 100px;
    /* Cách ra để không bị Header che */
    padding-bottom: 50px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .checkout-container {
    max-width: 650px;
    margin: 0 auto;
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  }

  .checkout-container h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    font-size: 1.8rem;
  }

  .info-row {
    margin-bottom: 15px;
    font-size: 1.1rem;
    color: #34495e;
  }

  .info-row strong.price-total {
    color: #e74c3c;
    font-size: 1.4rem;
  }

  .form-group {
    margin-top: 25px;
  }

  .form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    color: #2c3e50;
  }

  /* FIX LỖI TEXTAREA TRÀN VIỀN */
  .checkout-textarea {
    width: 100%;
    height: 120px;
    padding: 15px;
    border: 1px solid #dcdde1;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
    box-sizing: border-box;
    /* Quan trọng nhất để không bị tràn */
    resize: vertical;
    outline: none;
    transition: border-color 0.3s;
  }

  .checkout-textarea:focus {
    border-color: #27ae60;
  }

  .btn-group {
    display: flex;
    gap: 15px;
    margin-top: 30px;
  }

  .btn-confirm {
    flex: 2;
    padding: 16px;
    background-color: #27ae60;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
  }

  .btn-confirm:hover {
    background-color: #219150;
  }

  .btn-back {
    flex: 1;
    text-align: center;
    padding: 16px;
    background-color: #95a5a6;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: bold;
    transition: background 0.3s;
  }

  .btn-back:hover {
    background-color: #7f8c8d;
  }
</style>

<div class="checkout-body">
  <div class="checkout-container">
    <h2>Xác nhận đơn hàng</h2>

    <div class="info-row">
      Khách hàng: <strong><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></strong>
    </div>

    <div class="info-row">
      Tổng tiền thanh toán: <strong class="price-total"><?php echo formatMoney($tong_tien); ?></strong>
    </div>

    <form method="POST">
      <div class="form-group">
        <label for="ghi_chu">Thông tin nhận hàng (Địa chỉ & SĐT):</label>
        <textarea
          name="ghi_chu"
          id="ghi_chu"
          class="checkout-textarea"
          required
          placeholder="Nhập địa chỉ chi tiết và số điện thoại liên lạc của bạn..."></textarea>
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
        <a href="gio-hang.php" class="btn-back">QUAY LẠI</a>
      </div>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>