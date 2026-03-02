<?php
require_once '../config/db.php';
require_once '../includes/function.php';
checkAdmin();
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Admin</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset lề body để Nav tràn viền */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }

        /* Container bọc nội dung dưới Nav */
        .admin-wrapper {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Hệ thống Grid dùng Flexbox để dàn hàng ngang */
        .dashboard-grid {
            display: flex;
            justify-content: space-between;
            /* Căn đều khoảng cách giữa các thẻ */
            gap: 20px;
            /* Khoảng cách giữa các thẻ */
            margin-bottom: 40px;
            flex-wrap: nowrap;
            /* Ép tất cả nằm trên 1 dòng */
        }

        /* Định dạng chung cho mỗi thẻ Card */
        .admin-card {
            flex: 1;
            /* Ép các thẻ có độ rộng bằng nhau 100% */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px;
            border-radius: 12px;
            color: white;
            text-align: center;
            min-height: 160px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Màu sắc hiện đại cho từng thẻ */
        .card-blue {
            background: #379cdf;
        }

        .card-purple {
            background: #9f41c4;
        }

        .card-green {
            background: #32b96a;
        }

        .card-orange {
            background: #ea9245;
        }

        .admin-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.3rem;
        }

        .admin-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        /* Nút bấm trong thẻ */
        .card-btn {
            margin-top: 20px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .card-btn:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        /* Container cho biểu đồ */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">
        <h1 style="color: #2c3e50; margin-bottom: 10px;">Chào mừng Admin: <?php echo $_SESSION['ho_ten']; ?></h1>
        <p style="color: #7f8c8d; margin-bottom: 30px;">Hệ thống quản lý cửa hàng tạp hóa Genius</p>

        <div class="dashboard-grid">
            <div class="admin-card card-blue">
                <div>
                    <h3>Sản phẩm</h3>
                    <p>Quản lý kho hàng & giá</p>
                </div>
                <a href="quan-ly-san-pham.php" class="card-btn">Truy cập ngay</a>
            </div>

            <div class="admin-card card-purple">
                <div>
                    <h3>Danh mục</h3>
                    <p>Quản lý nhóm sản phẩm</p>
                </div>
                <a href="quan-ly-danh-muc.php" class="card-btn">Quản lý ngay</a>
            </div>

            <div class="admin-card card-green">
                <div>
                    <h3>Đơn hàng</h3>
                    <p>Duyệt & Xử lý đơn hàng</p>
                </div>
                <a href="../nhan-vien/quan-ly-don-hang.php" class="card-btn">Xử lý ngay</a>
            </div>

            <div class="admin-card card-orange">
                <div>
                    <h3>Thống kê</h3>
                    <p>Báo cáo doanh thu</p>
                </div>
                <a href="bao-cao.php" class="card-btn">Xem biểu đồ</a>
            </div>
        </div>

        <div class="chart-container">
            <h3 style="text-align: center; color: #34495e; margin-bottom: 20px;">Số lượng sản phẩm theo danh mục</h3>
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <?php
    // Lấy dữ liệu thống kê cho biểu đồ
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

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Số lượng sản phẩm',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>