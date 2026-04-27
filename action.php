<?php
session_start();
require 'db_connection.php';
if (!isset($_SESSION['email'])) {
    echo "Please login first";
    exit;
}

$email = $_SESSION['email'];
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();

if (!$userData) {
    echo "User not found";
    exit;
}
$user_id = $userData['user_id'];

if (isset($_POST['pid']) && isset($_POST['pname'])) {
    $pid = $_POST['pid'];
    $pname = $_POST['pname'];
    $pprice = $_POST['pprice'];
    $pimage = $_POST['pimage'];

    $stmt = $conn->prepare('SELECT quantity FROM cart WHERE itemId = ? AND user_id = ?');
    $stmt->bind_param("ii", $pid, $user_id);
    $stmt->execute();
    $existingItem = $stmt->get_result()->fetch_assoc();

    if (!$existingItem) {
        $pqty = 1;
        $total_price = $pprice * $pqty;
        
        $query = $conn->prepare('INSERT INTO cart (user_id, itemId, itemName, image, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $query->bind_param("iissidd", $user_id, $pid, $pname, $pimage, $pqty, $pprice, $total_price);
        
        if ($query->execute()) {
            echo "Item added to cart!";
        } else {
            echo "Error adding item!";
        }
    } 
    else {
        $new_qty = $existingItem['quantity'] + 1;
        $new_total_price = $pprice * $new_qty;
        
        $update = $conn->prepare('UPDATE cart SET quantity = ?, total_price = ? WHERE itemId = ? AND user_id = ?');
        $update->bind_param("idii", $new_qty, $new_total_price, $pid, $user_id);
        
        if ($update->execute()) {
            echo "Quantity updated!";
        }
    }
    exit;
}

if (isset($_GET['cartItem']) && $_GET['cartItem'] == 'cart_item') {
    $stmt = $conn->prepare('SELECT SUM(quantity) AS qty FROM cart WHERE user_id = ?');
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $quantity = ($row['qty'] !== null) ? $row['qty'] : 0;
    echo $quantity;
    exit;
}
?>