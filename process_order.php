<?php
session_start();
require 'db_connection.php';
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];

$address = $_POST['address'] ?? '';
$orderNote = $_POST['order_note'] ?? '';
$paymentMode = $_POST['payment_mode'] ?? ''; 
$selectedItems = json_decode($_POST['selected_items'], true) ?? [];

$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if (!$userData) {
    die('User error. Please login again.');
}

$user_id = $userData['user_id'];

if (empty($selectedItems)) {
    die('Your cart is empty.');
}

$conn->begin_transaction();

try {
    $subtotal = 0;
    $orderItemsData = [];

    foreach ($selectedItems as $item) {
        $cartId = intval($item['id']);
        $qty = intval($item['quantity']);

        $stmt = $conn->prepare('SELECT * FROM cart WHERE cart_id = ? AND user_id = ?');
        $stmt->bind_param("ii", $cartId, $user_id);
        $stmt->execute();
        $itemDetails = $stmt->get_result()->fetch_assoc();

        if ($itemDetails) {
            $price = floatval($itemDetails['price']);
            $itemTotal = $price * $qty;
            $subtotal += $itemTotal;

            $orderItemsData[] = [
                'itemId' => $itemDetails['itemId'],
                'itemName' => $itemDetails['itemName'],
                'quantity' => $qty,
                'price' => $price,
                'total_price' => $itemTotal,
                'cart_id' => $cartId
            ];
        }
    }
    $deliveryFee = ($paymentMode === 'Cash') ? 130 : 0;
    $grandTotal = $subtotal + $deliveryFee;

    
    $orderQuery = 'INSERT INTO orders (user_id, sub_total, delivery_fee, grand_total, pmode, note, address) VALUES (?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("idddsss", $user_id, $subtotal, $deliveryFee, $grandTotal, $paymentMode, $orderNote, $address);
    $stmt->execute();
    $orderId = $conn->insert_id;

    
    $itemInsertQuery = 'INSERT INTO order_items (order_id, itemId, itemName, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?)';
    $itemStmt = $conn->prepare($itemInsertQuery);
    
    $cartDeleteQuery = 'DELETE FROM cart WHERE cart_id = ? AND user_id = ?';
    $deleteStmt = $conn->prepare($cartDeleteQuery);

    foreach ($orderItemsData as $oi) {
        $itemStmt->bind_param("iisidd", $orderId, $oi['itemId'], $oi['itemName'], $oi['quantity'], $oi['price'], $oi['total_price']);
        $itemStmt->execute();

        $deleteStmt->bind_param("ii", $oi['cart_id'], $user_id);
        $deleteStmt->execute();
    }
    $conn->commit();
    header('Location: order_confirm.php?order_id=' . $orderId);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "There is somthing wrong with your order. " . $e->getMessage();
}
?>