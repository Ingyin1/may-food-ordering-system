<?php
session_start();
require 'db_connection.php';

if (isset($_POST['id']) && isset($_POST['quantity']) && isset($_POST['total_price'])) {
    $cart_id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];

    $stmt = $conn->prepare('UPDATE cart SET quantity=?, total_price=? WHERE cart_id=?');
    $stmt->bind_param("idi", $quantity, $total_price, $cart_id);
    $stmt->execute();
}
?>
