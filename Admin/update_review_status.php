<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php';
    $review_id = isset($_POST['review_id']) ? $_POST['review_id'] : (isset($_POST['order_id']) ? $_POST['order_id'] : null);
    $status = $_POST['status'];

    if ($review_id) {
        error_log("Updating review_id: $review_id to status: $status");
        $stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE review_id = ?");
        $stmt->bind_param("si", $status, $review_id);

        if ($stmt->execute()) {
            echo "Status updated successfully";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    } else {
        echo "Error: No ID provided";
    }
}
?>