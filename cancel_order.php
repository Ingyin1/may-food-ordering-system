<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    error_log("Received order ID: $orderId, reason: $reason");

    if ($orderId > 0 && !empty($reason)) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Cancelled', cancel_reason = ? WHERE order_id = ?");
        $stmt->bind_param("si", $reason, $orderId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Order has been cancelled.";
        } else {
            echo "Order not found or already cancelled.";
        }
    } else {
        echo "Invalid order ID or reason.";
    }
} else {
    echo "Invalid request.";
}
?>
