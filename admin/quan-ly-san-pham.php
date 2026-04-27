<?php
require_once '../config/db.php';
require_once '../includes/function.php';

if (!isLoggedIn() || (!hasRole('admin') && !hasRole('nhan_vien'))) {
  header("Location: ../manager/dang-nhap.php");
  exit();
}

include '../includes/header.php';

// 1. Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_sql = "";
$params = [];

if (!empty($search)) {
  // Tìm theo tên sản phẩm hoặc mã ID
  $where_sql = " WHERE sp.ten_san_pham LIKE ? OR sp.id = ? ";
  $params = ["%$search%", $search];
}

// 2. Truy vấn danh sách sản phẩm
$sql = "SELECT sp.*, dm.ten_danh_muc 
        FROM san_pham sp 
        LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        $where_sql
        ORDER BY sp.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// 3. Thống kê nhanh (Dựa trên toàn bộ kho, không dựa trên kết quả tìm kiếm)
$total_all = $pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
$low_stock_all = $pdo->query("SELECT COUNT(*) FROM san_pham WHERE so_luong_kho <= 5")->fetchColumn();
?>

<style>
  :root {
    --primary-color: #2c3e50;
    --accent-color: #e74c3c;
    --success-color: #27ae60;
  }

  .admin-container {
    max-width: 1300px;
    margin: 100px auto 50px;
    padding: 0 20px;
    font-family: 'Segoe UI', sans-serif;
  }

  /* Stats Cards */
  .stats-grid {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 5px solid var(--primary-color);
  }

  .stat-card.warning {
    border-left-color: var(--accent-color);
  }

  .stat-number {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
  }

  /* Search & Filter Bar */
  .filter-bar {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .search-form {
    display: flex;
    gap: 10px;
    flex: 1;
    max-width: 500px;
  }

  .search-input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: 0.3s;
  }

  .search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 5px rgba(44, 62, 80, 0.2);
  }

  .btn-search {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
  }

  .btn-add-new {
    background: var(--success-color);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
  }

  /* Modern Table */
  .modern-table-wrapper {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  }

  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }

  .modern-table th {
    background: #f8f9fa;
    padding: 18px 15px;
    text-align: left;
    color: #34495e;
    border-bottom: 2px solid #eee;
  }

  .modern-table td {
    padding: 15px;
    border-bottom: 1px solid #f1f1f1;
    vertical-align: middle;
  }

  .prod-img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #eee;
  }

  .stock-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
  }

  .stock-low {
    background: #fee2e2;
    color: #dc2626;
  }

  .stock-normal {
    background: #dcfce7;
    color: #16a34a;
  }

  .action-btns {
    display: flex;
    gap: 8px;
  }

  .btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    transition: 0.2s;
  }

  .edit {
    background: #ebf5ff;
    color: #2563eb;
  }

  .delete {
    background: #fff1f2;
    color: #e11d48;
  }
</style>

<div class="admin-container">
  <div class="stats-grid">
    <div class="stat-card">
      <div style="font-size: 30px;">📦</div>
      <div>
        <div class="stat-number"><?php echo $total_all; ?></div>
        <div class="stat-label">Sản phẩm trong kho</div>
      </div>
    </div>
    <div class="stat-card warning">
      <div style="font-size: 30px;">⚠️</div>
      <div>
        <div class="stat-number"><?php echo $low_stock_all; ?></div>
        <div class="stat-label">Sắp hết hàng</div>
      </div>
    </div>
  </div>

  <div class="filter-bar">
    <form action="" method="GET" class="search-form">
      <input type="text" name="search" class="search-input"
        placeholder="Tìm theo tên sản phẩm hoặc mã ID..."
        value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit" class="btn-search">Tìm kiếm</button>
      <?php if (!empty($search)): ?>
        <a href="quan-ly-san-pham.php" style="align-self: center; color: #7f8c8d; font-size: 14px;">Xóa lọc</a>
      <?php endif; ?>
    </form>

    <?php if (hasRole('admin')): ?>
      <a href="them-san-pham.php" class="btn-add-new">+ Thêm sản phẩm mới</a>
    <?php endif; ?>
  </div>

  <?php if (!empty($search)): ?>
    <p style="margin-bottom: 15px; color: #7f8c8d;">Tìm thấy <strong><?php echo count($products); ?></strong> kết quả cho: "<?php echo htmlspecialchars($search); ?>"</p>
  <?php endif; ?>

  <div class="modern-table-wrapper">
    <table class="modern-table">
      <thead>
        <tr>
          <th width="70">ID</th>
          <th width="80">Ảnh</th>
          <th>Sản phẩm</th>
          <th>Danh mục</th>
          <th>Giá Bán</th>
          <th>Tồn kho</th>
          <?php if (hasRole('admin')): ?>
            <th width="140">Thao tác</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 40px; color: #95a5a6;">
              Không tìm thấy sản phẩm nào phù hợp.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><span style="color: #95a5a6;">#<?php echo $p['id']; ?></span></td>
              <td><img src="../assets/images/<?php echo $p['anh']; ?>" class="prod-img"></td>
              <td>
                <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($p['ten_san_pham']); ?></div>
              </td>
              <td><span style="font-size: 14px; color: #7f8c8d;"><?php echo htmlspecialchars($p['ten_danh_muc']); ?></span></td>
              <td><strong style="color: var(--accent-color);"><?php echo formatMoney($p['gia_ban']); ?></strong></td>
              <td>
                <?php $low = ($p['so_luong_kho'] <= 5); ?>
                <span class="stock-badge <?php echo $low ? 'stock-low' : 'stock-normal'; ?>">
                  <?php echo ($low ? '⚠️ ' : '') . $p['so_luong_kho']; ?>
                </span>
              </td>
              <?php if (hasRole('admin')): ?>
                <td>
                  <div class="action-btns">
                    <a href="sua-san-pham.php?id=<?php echo $p['id']; ?>" class="btn-action edit">Sửa</a>
                    <a href="quan-ly-san-pham.php?delete_id=<?php echo $p['id']; ?>"
                      class="btn-action delete"
                      onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                  </div>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>