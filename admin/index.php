<?php
// 1. Nhúng các file cấu hình và hàm (phải có ../)
require_once '../config/db.php';
require_once '../includes/function.php';

// 2. Kiểm tra quyền Admin - Nếu không phải admin sẽ bị đẩy ra
checkAdmin();

// 3. Nhúng Header của Admin (Hoặc dùng chung header nhưng phải sửa link)
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container" style="padding: 20px;">
        <h1>Chào mừng Admin: <?php echo $_SESSION['ho_ten']; ?></h1>
        <hr>

        <div class="admin-dashboard" style="display: flex; gap: 20px; margin-top: 20px;">
            <div class="card" style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #e3f2fd;">
                <h3>Sản phẩm</h3>
                <p>Quản lý danh sách hàng hóa</p>
                <a href="quan-ly-san-pham.php" style="text-decoration: none;">Truy cập ngay</a>
            </div>

            <div class="card" style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #e9bafb;">
                <h3>Danh mục</h3>
                <p>Quản lý nhóm sản phẩm</p>
                <a href="quan-ly-danh-muc.php" style="text-decoration: none;">Quản lý ngay</a>
            </div>

            <div class="card" style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #f1f8e9;">
                <h3>Đơn hàng</h3>
                <p>Xem các đơn hàng mới</p>
                <a href="../nhan-vien/quan-ly-don-hang.php" style="text-decoration: none;">Xử lý ngay</a>
            </div>

            <div class="card" style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #fff3e0;">
                <h3>Thống kê</h3>
                <p>Xem báo cáo doanh thu</p>
                <a href="bao-cao.php" style="text-decoration: none;">Xem biểu đồ</a>
            </div>
        </div>

        <?php
        // Giả sử ta thống kê số lượng sản phẩm theo từng danh mục
        $query = $pdo->query("SELECT dm.ten_danh_muc, COUNT(sp.id) as total 
                      FROM danh_muc dm 
                      LEFT JOIN san_pham sp ON dm.id = sp.danh_muc_id 
                      GROUP BY dm.ten_danh_muc");
        $chartData = $query->fetchAll();

        $labels = [];
        $data = [];
        foreach ($chartData as $row) {
            $labels[] = $row['ten_danh_muc'];
            $data[] = $row['total'];
        }
        ?>

        <div style="width: 50%; margin: 20px auto;">
            <canvas id="myChart"></canvas>
        </div>

        <script>
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Có thể đổi thành 'pie' hoặc 'line'
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Số lượng sản phẩm theo danh mục',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                }
            });
        </script>

        <?php include '../includes/footer.php'; ?>

</body>

</html>