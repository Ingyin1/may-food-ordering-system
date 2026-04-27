<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';

$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : '';
$paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

if ($orderId && $paymentStatus) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $paymentStatus, $orderId);
    
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating payment status.";
    }
} else {
    echo "Invalid order ID or payment status.";
}
?>
