<?php
require_once 'config/db.php';
require_once 'includes/function.php';
include 'includes/header.php';

if (!isLoggedIn()) {
  header("Location: manager/dang-nhap.php");
  exit();
}

$user_id = $_SESSION['user_id'];

try {
  // Đã sửa ngay_tao thành ngay_dat để fix lỗi Unknown column
  $stmt = $pdo->prepare("SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY ngay_dat DESC");
  $stmt->execute([$user_id]);
  $orders = $stmt->fetchAll();
} catch (PDOException $e) {
  // Nếu vẫn lỗi, có thể tên cột của bạn là created_at, hãy thử đổi lại nếu cần
  die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<style>
  .history-container {
    max-width: 1100px;
    margin: 100px auto 50px auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .page-title {
    color: #2c3e50;
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 3px solid #e74c3c;
    /* Màu đỏ Genius */
    display: inline-block;
  }

  .order-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 12px;
  }

  .order-table th {
    background: #f8f9fa;
    padding: 15px;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
    text-align: left;
  }

  .order-row {
    background: #fff;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    transition: 0.3s;
    cursor: pointer;
  }

  .order-row:hover {
    background: #fdfdfd;
    transform: scale(1.01);
  }

  .order-row td {
    padding: 20px 15px;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
  }

  .order-row td:first-child {
    border-left: 1px solid #eee;
    border-radius: 8px 0 0 8px;
  }

  .order-row td:last-child {
    border-right: 1px solid #eee;
    border-radius: 0 8px 8px 0;
  }

  .price-text {
    color: #e74c3c;
    font-weight: bold;
    font-size: 17px;
  }

  .badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
  }

  .status-cho_duyet {
    background: #fff3cd;
    color: #856404;
  }

  .status-da_thanh_toan {
    background: #d4edda;
    color: #155724;
  }

  .status-da_huy {
    background: #f8d7da;
    color: #721c24;
  }

  /* Chi tiết sổ xuống */
  .detail-row {
    display: none;
  }

  .detail-content {
    background: #ffffff;
    margin: -10px 15px 15px 15px;
    padding: 25px;
    border: 1px solid #eee;
    border-top: none;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  }

  .product-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px dotted #ddd;
  }

  .product-item:last-child {
    border-bottom: none;
  }

  .product-img {
    width: 65px;
    height: 65px;
    object-fit: contain;
    margin-right: 20px;
    border: 1px solid #f1f1f1;
    border-radius: 6px;
  }

  .btn-detail {
    background: #34495e;
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
  }
</style>

<div class="history-container">
  <h2 class="page-title">📦 Lịch sử đơn hàng</h2>

  <?php if (empty($orders)): ?>
    <div style="text-align: center; padding: 60px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
      <p style="color: #95a5a6; font-size: 18px; margin-bottom: 20px;">Bạn chưa thực hiện đơn hàng nào.</p>
      <a href="index.php" style="background: #e74c3c; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">ĐI MUA SẮM NGAY</a>
    </div>
  <?php else: ?>
    <table class="order-table">
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Ngày mua</th>
          <th>Tổng cộng</th>
          <th style="text-align: center;">Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr class="order-row" onclick="toggleOrder(<?php echo $o['id']; ?>)">
            <td><strong>#<?php echo $o['id']; ?></strong></td>
            <td style="color: #34495e;">
              <?php echo date('d/m/Y H:i', strtotime($o['ngay_dat'])); ?>
            </td>
            <td class="price-text"><?php echo formatMoney($o['tong_tien']); ?></td>
            <td style="text-align: center;">
              <span class="badge status-<?php echo $o['trang_thai']; ?>">
                <?php
                $labels = [
                  'cho_duyet' => '⏳ Chờ duyệt',
                  'da_thanh_toan' => '✅ Hoàn tất',
                  'da_huy' => '❌ Đã hủy'
                ];
                echo $labels[$o['trang_thai']] ?? $o['trang_thai'];
                ?>
              </span>
            </td>
            <td>
              <button class="btn-detail" id="btn-<?php echo $o['id']; ?>">Chi tiết</button>
            </td>
          </tr>

          <tr class="detail-row" id="detail-<?php echo $o['id']; ?>">
            <td colspan="5">
              <div class="detail-content">
                <div style="margin-bottom: 15px;">
                  <strong style="color: #2c3e50;">📍 Thông tin giao hàng:</strong>
                  <p style="margin: 5px 0; color: #7f8c8d; font-size: 14px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($o['ghi_chu'])); ?>
                  </p>
                </div>

                <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                  <?php
                  $stmt_items = $pdo->prepare("
                                        SELECT ct.*, sp.ten_san_pham, sp.anh 
                                        FROM chi_tiet_don_hang ct 
                                        JOIN san_pham sp ON ct.san_pham_id = sp.id 
                                        WHERE ct.don_hang_id = ?
                                    ");
                  $stmt_items->execute([$o['id']]);
                  $items = $stmt_items->fetchAll();

                  foreach ($items as $item):
                  ?>
                    <div class="product-item">
                      <img src="assets/images/<?php echo $item['anh']; ?>" class="product-img">
                      <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($item['ten_san_pham']); ?></div>
                        <div style="font-size: 13px; color: #7f8c8d;">
                          Đơn giá: <?php echo formatMoney($item['gia_luc_mua']); ?> | SL: <?php echo $item['so_luong']; ?>
                        </div>
                      </div>
                      <div style="font-weight: bold; color: #2c3e50;">
                        <?php echo formatMoney($item['gia_luc_mua'] * $item['so_luong']); ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script>
  function toggleOrder(id) {
    const detailRow = document.getElementById('detail-' + id);
    const btn = document.getElementById('btn-' + id);

    // Đóng các đơn hàng khác đang mở để tránh rối mắt
    document.querySelectorAll('.detail-row').forEach(row => {
      if (row.id !== 'detail-' + id) row.style.display = 'none';
    });
    document.querySelectorAll('.btn-detail').forEach(b => {
      if (b.id !== 'btn-' + id) {
        b.innerText = 'Chi tiết';
        b.style.background = '#34495e';
      }
    });

    if (detailRow.style.display === 'table-row') {
      detailRow.style.display = 'none';
      btn.innerText = 'Chi tiết';
      btn.style.background = '#34495e';
    } else {
      detailRow.style.display = 'table-row';
      btn.innerText = 'Đóng';
      btn.style.background = '#e74c3c';
    }
  }
</script>

<?php include 'includes/footer.php'; ?>