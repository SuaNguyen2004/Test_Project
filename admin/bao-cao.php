<link rel="stylesheet" href="../assets/css/style3.css">
<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';

// 1. Truy vấn doanh thu theo tháng (Chỉ tính các đơn đã thanh toán)
$sql_revenue = "SELECT MONTH(ngay_dat) as thang, SUM(tong_tien) as doanh_thu 
                FROM don_hang 
                WHERE trang_thai = 'da_thanh_toan' AND YEAR(ngay_dat) = YEAR(CURDATE())
                GROUP BY MONTH(ngay_dat)";
$stmt_rev = $pdo->query($sql_revenue);
$revenue_data = $stmt_rev->fetchAll();

// Chuẩn bị mảng dữ liệu cho Chart.js (Mặc định 12 tháng bằng 0)
$months = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
$revenue_values = array_fill(0, 12, 0);
foreach ($revenue_data as $row) {
    $revenue_values[$row['thang'] - 1] = $row['doanh_thu'];
}

// 2. Truy vấn tỷ lệ sản phẩm theo danh mục
$sql_cate = "SELECT dm.ten_danh_muc, COUNT(sp.id) as so_luong 
             FROM danh_muc dm 
             LEFT JOIN san_pham sp ON dm.id = sp.danh_muc_id 
             GROUP BY dm.id";
$stmt_cate = $pdo->query($sql_cate);
$cate_data = $stmt_cate->fetchAll();
$cate_labels = [];
$cate_counts = [];
foreach ($cate_data as $row) {
    $cate_labels[] = $row['ten_danh_muc'];
    $cate_counts[] = $row['so_luong'];
}
?>

<div class="container" style="padding: 20px;">
    <h2>Báo cáo & Thống kê kinh doanh</h2>
    <a href="index.php">← Quay lại Dashboard</a>

    <div style="display: flex; gap: 40px; margin-top: 30px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 500px; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3>Doanh thu theo tháng (năm <?php echo date('Y'); ?>)</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <div style="flex: 1; min-width: 300px; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h3>Cơ cấu hàng hóa</h3>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Biểu đồ Doanh thu (Bar Chart)
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($revenue_values); ?>,
                backgroundColor: '#3498db',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Biểu đồ Danh mục (Pie Chart)
    const ctxCate = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCate, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($cate_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($cate_counts); ?>,
                backgroundColor: ['#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#34495e']
            }]
        }
    });
</script>

<?php include '../includes/footer.php'; ?>