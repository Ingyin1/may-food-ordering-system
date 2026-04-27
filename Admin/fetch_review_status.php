<?php
include 'db_connection.php';
$statusFilter = isset($_POST['status']) ? $_POST['status'] : '';

if ($statusFilter !== "" && $statusFilter !== "all") {
    $sql = "SELECT r.*, u.email 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.user_id 
            WHERE r.status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $statusFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT r.*, u.email 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.user_id 
            ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
}

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'] ?? 'N/A';
            $email = $row['email'] ?? 'No Email';
            $review_id = $row['review_id'];
            $rating = (int)($row['rating'] ?? 0);
            $current_status = $row['status'] ?? 'pending';
            $ratingStars = str_repeat('&#9733;', $rating) . str_repeat('&#9734;', 5 - $rating);

            echo "<tr>
                    <td>" . htmlspecialchars($order_id) . "</td>
                    <td>" . htmlspecialchars($email) . "</td>
                    <td>" . htmlspecialchars($row['review_text'] ?? '') . "</td>
                    <td class='rating-stars'>{$ratingStars}</td>
                    <td>
                        <select onchange='updateStatus({$review_id}, this.value)' class='status-select'>
                            <option value='pending' " . ($current_status == 'pending' ? 'selected' : '') . ">Pending</option>
                            <option value='approved' " . ($current_status == 'approved' ? 'selected' : '') . ">Approved</option>
                            <option value='rejected' " . ($current_status == 'rejected' ? 'selected' : '') . ">Rejected</option>
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
        echo "<tr><td colspan='6' style='text-align: center;'>No Reviews Found for this status.</td></tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align: center;'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
}
?>