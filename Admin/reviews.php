<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}
include 'db_connection.php';

include 'sidebar.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Review Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="admin_reservation.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_review.css">
</head>
<body>
    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>
        <div class="profile-section">
            <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image'] ?? 'default.png'); ?>" alt="Profile Picture">
            <div class="info">
                <h3>Welcome Back!</h3>
                <p><?php echo htmlspecialchars(($admin_info['firstName'] ?? 'Admin') . ' ' . ($admin_info['lastName'] ?? '')); ?></p>
            </div>
        </div>
        <ul>
            <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-star"></i> Reviews</h2>
        </div>

        <div class="actions">
            <select id="statusFilter" onchange="filterByStatus()">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <div class="table">
            <table id="reviewTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Email</th>
                        <th>Review Text</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT r.*, u.email 
                              FROM reviews r 
                              LEFT JOIN users u ON r.user_id = u.user_id";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $order_id = $row['order_id'] ?? 'N/A';
                            $email = $row['email'] ?? 'No Email';
                            $review_text = $row['review_text'] ?? '';
                            $rating = (int)($row['rating'] ?? 0);
                            $status = $row['status'] ?? 'pending';
                            $review_id = $row['review_id']; 

                            $ratingStars = str_repeat('&#9733;', $rating) . str_repeat('&#9734;', 5 - $rating);

                            echo "<tr id='row_{$review_id}'>
                                    <td>" . htmlspecialchars($order_id) . "</td>
                                    <td>" . htmlspecialchars($email) . "</td>
                                    <td>" . htmlspecialchars($review_text) . "</td>
                                    <td class='rating-stars'>{$ratingStars}</td>
                                    <td>
                                        <select onchange='updateStatus({$review_id}, this.value)' class='status-select'>
                                            <option value='pending' " . ($status == 'pending' ? 'selected' : '') . ">Pending</option>
                                            <option value='approved' " . ($status == 'approved' ? 'selected' : '') . ">Approved</option>
                                            <option value='rejected' " . ($status == 'rejected' ? 'selected' : '') . ">Rejected</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class='deletebtn' onclick=\"deleteReview('{$review_id}', '{$email}')\">
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center;'>No Reviews Found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
            function deleteReview(review_id, email) {
                if (confirm('Are you sure you want to delete this review from ' + email + '?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_review.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        alert(response.message);
                                        location.reload(); 
                                    } else {
                                        alert("Error: " + response.message);
                                    }
                                } catch (e) {
                                    console.log("Raw Response:", xhr.responseText);
                                    alert("Server error. Check console for details.");
                                }
                            } else {
                                alert("Connection failed with status: " + xhr.status);
                            }
                        }
                    };
                    xhr.send("reviewId=" + encodeURIComponent(review_id));
                }
            }

            function updateStatus(review_id, status) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_review_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Status updated successfully");
                    }
                };
                xhr.send("review_id=" + review_id + "&status=" + status);
            }

            function filterByStatus() {
                const status = document.getElementById('statusFilter').value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetch_review_status.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.querySelector('#reviewTable tbody').innerHTML = xhr.responseText;
                    }
                };
                xhr.send('status=' + encodeURIComponent(status));
            }
            </script>
        </body>
</html>