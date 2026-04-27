<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = $_POST['response'];
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare("UPDATE reviews SET response=? WHERE order_id=?");
    $stmt->bind_param("si", $response, $order_id);
    
    if ($stmt->execute()) {
        echo "Review updated successfully.";
    } else {
        echo "Error Updating Review: " . $conn->error;
    }

    header("Location: reviews.php");
    exit();
}
?>
