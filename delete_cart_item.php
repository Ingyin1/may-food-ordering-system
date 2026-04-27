<?php
session_start();
header('Content-Type: application/json');
require 'db_connection.php';

if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
  echo json_encode(['success' => false, 'message' => 'User not logged in']);
  exit();
}

$email = $_SESSION['email'];
$cart_id = intval($_POST['id']);

$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData) {
  echo json_encode(['success' => false, 'message' => 'User not found']);
  exit();
}

$user_id = $userData['user_id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Item not found or already deleted']);
}
?>
