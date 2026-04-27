<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

$response = array('status' => '', 'message' => '');

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
  
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    if (!$userData) {
        echo json_encode(['status' => 'error', 'message' => 'User not found!']);
        exit;
    }
    
    $user_id = $userData['user_id'];

    if (isset($_POST['pid']) && !empty($_POST['pid'])) {
        $pid = intval($_POST['pid']);
        $pimage = $_POST['pimage'];

        $prod_stmt = $conn->prepare('SELECT product_name, product_price FROM products WHERE id = ?');
        $prod_stmt->bind_param("i", $pid);
        $prod_stmt->execute();
        $prod_result = $prod_stmt->get_result();
        $product = $prod_result->fetch_assoc();

        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Product no longer exists!']);
            exit;
        }

        $pname = $product['product_name'];
        $pprice = floatval($product['product_price']);

        $stmt = $conn->prepare('SELECT cartId, quantity FROM cart WHERE itemId = ? AND user_id = ?');
        $stmt->bind_param("ii", $pid, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingItem = $result->fetch_assoc();

        if (!$existingItem) {
            $pqty = 1;
            $total_price = $pprice * $pqty;
            $query = $conn->prepare('INSERT INTO cart (user_id, itemId, itemName, image, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $query->bind_param("iissidd", $user_id, $pid, $pname, $pimage, $pqty, $pprice, $total_price);
            
            if ($query->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Item added to your cart!';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to add item.';
            }
        } else {
            $newQty = $existingItem['quantity'] + 1;
            $newTotal = $pprice * $newQty;
            $update = $conn->prepare('UPDATE cart SET quantity = ?, total_price = ? WHERE itemId = ? AND user_id = ?');
            $update->bind_param("idii", $newQty, $newTotal, $pid, $user_id);
            
            if ($update->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Cart updated (Quantity increased)!';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid item data!';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Please login first!';
}

echo json_encode($response);
?>