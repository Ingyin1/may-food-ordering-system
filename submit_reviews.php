<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    $reviewText = isset($_POST['reviewText']) ? $_POST['reviewText'] : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $email = $_SESSION['email']; 
    $status = 'approved'; 

    $userQuery = $conn->prepare("SELECT user_id, firstname FROM users WHERE email = ?");
    $userQuery->bind_param("s", $email);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userRow = $userResult->fetch_assoc();

    if (!$userRow) {
        die('Error: User not found.');
    }

    $userId = $userRow['user_id'];
    $firstName = $userRow['firstname'];

    $checkStmt = $conn->prepare("SELECT order_id FROM reviews WHERE order_id = ?");
    $checkStmt->bind_param("i", $orderId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $existingReview = $checkResult->fetch_assoc();

    if ($existingReview) {
       
        $stmt = $conn->prepare("UPDATE reviews SET review_text = ?, rating = ? WHERE order_id = ?");
        $stmt->bind_param("sii", $reviewText, $rating, $orderId);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (order_id, user_id, first_name, rating, review_text, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisiss", $orderId, $userId, $firstName, $rating, $reviewText, $status);
        $stmt->execute();
    }

    echo '<script>alert("Review submitted successfully!");</script>';
    echo '<script>window.location.href = "orders.php";</script>';
} else {
    header('Location: orders.php');
    exit;
}
?>