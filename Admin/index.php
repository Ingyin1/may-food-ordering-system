<?php

session_start();
if (!isset($_SESSION['adminloggedin']) || !$_SESSION['adminloggedin']) {
    header('Location: ../login.php');
    exit;
}

include 'db_connection.php';

date_default_timezone_set('Asia/Yangon');

function calculateEarnings($conn, $dateColumn, $startDate, $endDate)
{
    $query = "SELECT SUM(grand_total) AS total FROM orders WHERE $dateColumn BETWEEN ? AND ? AND payment_status = 'Successful'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

function calculateTotalOrders($conn, $startDate, $endDate)
{
    $query = "SELECT COUNT(*) AS total_orders FROM orders WHERE order_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_orders'] ?? 0;
}

function calculateTotalUsers($conn)
{
    $result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $row = $result->fetch_assoc();
    return $row['total_users'] ?? 0;
}

function calculateTotalReservations($conn)
{
    $result = $conn->query("SELECT COUNT(*) AS total_reservations FROM reservations");
    $row = $result->fetch_assoc();
    return $row['total_reservations'] ?? 0;
}

function calculateChange($current, $previous)
{
    $change = $previous ? (($current - $previous) / $previous) * 100 : 0;
    if ($change < -100) {
        return -100;
    } elseif ($change > 100) {
        return 100;
    }
    return number_format($change, 2);
}

$totalEarning = calculateEarnings($conn, 'order_date', '1970-01-01 00:00:00', date('Y-m-d') . ' 23:59:59');
$todaysEarning = calculateEarnings($conn, 'order_date', date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59');
$todaysOrders = calculateTotalOrders($conn, date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59');
$yesterdaysOrders = calculateTotalOrders($conn, date('Y-m-d', strtotime('-1 day')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59');
$totalOrders = calculateTotalOrders($conn, '1970-01-01 00:00:00', date('Y-m-d') . ' 23:59:59');
$previousTotalOrders = calculateTotalOrders($conn, date('Y-m-d', strtotime('-1 month')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59');
$totalUsers = calculateTotalUsers($conn);
$previousTotalUsers = $totalUsers - 100; 
$totalReservations = calculateTotalReservations($conn);
$previousTotalReservations = $totalReservations - 100;

$totalEarningChange = calculateChange($totalEarning, calculateEarnings($conn, 'order_date', '1970-01-01 00:00:00', date('Y-m-d', strtotime('-1 month')) . ' 23:59:59'));
$todaysEarningChange = calculateChange($todaysEarning, calculateEarnings($conn, 'order_date', date('Y-m-d', strtotime('-1 day')) . ' 00:00:00', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59'));
$totalOrdersChange = calculateChange($totalOrders, $previousTotalOrders);
$totalUsersChange = calculateChange($totalUsers, $previousTotalUsers);
$totalReservationsChange = calculateChange($totalReservations, $previousTotalReservations);
$todaysOrdersChange = calculateChange($todaysOrders, $yesterdaysOrders);

function getOrderStatusCounts($conn)
{
    $statuses = ['Pending', 'Processing', 'On the Way', 'Completed', 'Cancelled'];
    $statusCounts = [];
    foreach ($statuses as $status) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM orders WHERE order_status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $statusCounts[$status] = $row['count'] ?? 0;
    }
    return $statusCounts;
}

function getDailyData($conn, $table, $column, $dateType, $days = 7) {
    $dailyData = [];
    $labels = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $start = $date . ' 00:00:00';
        $end = $date . ' 23:59:59';
        
        if ($table == 'orders' && $column == 'grand_total') {
            $query = "SELECT COALESCE(SUM(grand_total), 0) AS total FROM orders WHERE order_date BETWEEN ? AND ? AND payment_status = 'Successful'";
        } else {
            $query = "SELECT COUNT(*) AS total FROM $table WHERE $column BETWEEN ? AND ?";
        }
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $dailyData[] = $row['total'] ?? 0;
        $labels[] = date('M d', strtotime($date));
    }
    return ['labels' => $labels, 'data' => $dailyData];
}

$statusCounts = getOrderStatusCounts($conn);
$dailyEarnings = getDailyData($conn, 'orders', 'grand_total', 'datetime');
$dailyOrders = getDailyData($conn, 'orders', 'order_date', 'datetime');
$dailyUsers = getDailyData($conn, 'users', 'dateCreated', 'datetime');
$dailyReservations = getDailyData($conn, 'reservations', 'reservedDate', 'date');

include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="index.css">
    <style>.content { margin-bottom: 40px; }</style>
</head>
<body>

    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>
        <div class="profile-section">
            <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
            <div class="info">
                <h3>Welcome Back!</h3>
                <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
            </div>
        </div>
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-chart-line"></i> Overview</h2>
        </div>

        <div class="container">
            <div class="card" data-color="purple">
                <div class="card-content">
                    <h4>Total Earning</h4>
                    <h3>MMK <?php echo number_format($totalEarning); ?></h3>
                    <p class="<?php echo $totalEarningChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $totalEarningChange > 0 ? '▲' : '▼'; ?> <?php echo abs($totalEarningChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-dollar-sign"></i>
                <canvas id="chart1"></canvas>
            </div>

            <div class="card" data-color="orange">
                <div class="card-content">
                    <h4>Today's Earning</h4>
                    <h3>MMK <?php echo number_format($todaysEarning); ?></h3>
                    <p class="<?php echo $todaysEarningChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $todaysEarningChange > 0 ? '▲' : '▼'; ?> <?php echo abs($todaysEarningChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-calendar-day"></i>
                <canvas id="chart2"></canvas>
            </div>

            <div class="card" data-color="l-blue">
                <div class="card-content">
                    <h4>Total Orders</h4>
                    <h3><?php echo number_format($totalOrders); ?></h3>
                    <p class="<?php echo $totalOrdersChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $totalOrdersChange > 0 ? '▲' : '▼'; ?> <?php echo abs($totalOrdersChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-shopping-cart"></i>
                <canvas id="chart5"></canvas>
            </div>

            <div class="card" data-color="pink">
                <div class="card-content">
                    <h4>Today's Orders</h4>
                    <h3><?php echo number_format($todaysOrders); ?></h3>
                    <p class="<?php echo $todaysOrdersChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $todaysOrdersChange > 0 ? '▲' : '▼'; ?> <?php echo abs($todaysOrdersChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-calendar-day"></i>
                <canvas id="chart6"></canvas>
            </div>

            <div class="card" data-color="blue">
                <div class="card-content">
                    <h4>Total Users</h4>
                    <h3><?php echo number_format($totalUsers); ?></h3>
                    <p class="<?php echo $totalUsersChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $totalUsersChange > 0 ? '▲' : '▼'; ?> <?php echo abs($totalUsersChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-users"></i>
                <canvas id="chart3"></canvas>
            </div>

            <div class="card" data-color="green">
                <div class="card-content">
                    <h4>Total Reservations</h4>
                    <h3><?php echo number_format($totalReservations); ?></h3>
                    <p class="<?php echo $totalReservationsChange > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $totalReservationsChange > 0 ? '▲' : '▼'; ?> <?php echo abs($totalReservationsChange); ?>%
                    </p>
                </div>
                <i class="icon-top-right icon fas fa-calendar-check"></i>
                <canvas id="chart4"></canvas>
            </div>
        </div>

        <div class="table-chart">
            <div class="table">
                <?php
                $result = $conn->query("SELECT order_id, user_id, order_status, grand_total FROM orders ORDER BY order_date DESC LIMIT 6");
                if ($result->num_rows > 0) {
                    echo '<div class="latest-orders"><h2>Latest Orders</h2><table><thead><tr><th>Order ID</th><th>Customer ID</th><th>Status</th><th>Total Amount</th><th>Actions</th></tr></thead><tbody>';
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$row['order_id']}</td><td>{$row['user_id']}</td>
                        <td>{$row['order_status']}</td><td>MMK {$row['grand_total']}</td>
                        <td><button onclick='viewDetails({$row['order_id']})'>View Details</button></td>
                        </tr>";
                    }
                    echo '</tbody></table></div>';
                } else {
                    echo "No orders found.";
                }
                ?>
            </div>
            <div class="bar-chart">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <?php include_once('footer.html'); ?>
    <script src="sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script>
        function viewDetails(orderId) { window.location.href = 'admin_orders.php?orderId=' + orderId; }

        const chartOptions = {
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: { enabled: true },
            elements: { point: { radius: 0 } },
            scales: {
                xAxes: [{ gridLines: false, ticks: { display: false } }],
                yAxes: [{ gridLines: false, ticks: { display: false, suggestedMin: 0 } }]
            }
        };
        const createChart = (id, labels, data, bgColor, borderColor) => {
            new Chart(document.getElementById(id).getContext('2d'), {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{ backgroundColor: bgColor, borderColor: borderColor, borderWidth: 2, data: data }]
                },
                options: chartOptions
            });
        };

        createChart('chart1', <?php echo json_encode($dailyEarnings['labels']); ?>, <?php echo json_encode($dailyEarnings['data']); ?>, "rgba(101, 116, 205, 0.1)", "rgba(101, 116, 205, 0.8)");
        createChart('chart2', <?php echo json_encode($dailyEarnings['labels']); ?>, <?php echo json_encode($dailyEarnings['data']); ?>, "rgba(253, 108, 77, 0.1)", "rgba(253, 108, 77, 0.8)");
        createChart('chart3', <?php echo json_encode($dailyOrders['labels']); ?>, <?php echo json_encode($dailyOrders['data']); ?>, "rgba(60, 142, 245, 0.1)", "rgba(60, 142, 245, 0.8)");
        createChart('chart4', <?php echo json_encode($dailyUsers['labels']); ?>, <?php echo json_encode($dailyUsers['data']); ?>, "rgba(80, 198, 168, 0.1)", "rgba(80, 198, 168, 0.8)");
        createChart('chart5', <?php echo json_encode($dailyReservations['labels']); ?>, <?php echo json_encode($dailyReservations['data']); ?>, "rgba(54, 162, 235, 0.1)", "rgba(54, 162, 235, 0.8)");
        createChart('chart6', <?php echo json_encode($dailyOrders['labels']); ?>, <?php echo json_encode($dailyOrders['data']); ?>, "rgba(255, 99, 132, 0.1)", "rgba(255, 99, 132, 0.8)");
        new Chart(document.getElementById('orderStatusChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($statusCounts)); ?>,
                datasets: [{
                    label: 'Order Statuses',
                    data: <?php echo json_encode(array_values($statusCounts)); ?>,
                    backgroundColor: ['rgba(60, 142, 245, 0.4)', 'rgba(101, 116, 205, 0.4)', 'rgba(253, 108, 77, 0.4)', 'rgba(80, 198, 168, 0.4)', 'rgba(255, 0, 0, 0.4)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>
</body>
</html>