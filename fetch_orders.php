<?php
session_start();
include 'db_connection.php';

$email = $_SESSION['email'];
$orderStatus = isset($_GET['status']) ? $_GET['status'] : 'All';

$query = "SELECT orders.*, reviews.review_text, reviews.response 
          FROM orders 
          LEFT JOIN reviews ON orders.order_id = reviews.order_id 
          WHERE orders.email = ?";
if ($orderStatus !== 'All') {
    $query .= " AND orders.order_status = ?";
}

if ($orderStatus === 'All') {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $orderStatus);
}
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($order = $result->fetch_assoc()) {
    $orderId = $order['order_id'];
    
    $itemsQuery = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemsQuery->bind_param("i", $orderId);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();
    $order['items'] = $itemsResult->fetch_all(MYSQLI_ASSOC);

    if ($order['order_status'] === 'Cancelled') {
        $cancelQuery = $conn->prepare("SELECT cancel_reason FROM orders WHERE order_id = ?");
        $cancelQuery->bind_param("i", $orderId);
        $cancelQuery->execute();
        $cancelResult = $cancelQuery->get_result();
        $cancelData = $cancelResult->fetch_assoc();
        $order['cancel_reason'] = $cancelData['cancel_reason'];
    }

    $orders[] = $order;
}

echo json_encode($orders);
?>
