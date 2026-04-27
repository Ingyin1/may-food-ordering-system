<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    header('location:login.php');
    exit;
}

$email = $_SESSION['email'];
$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); 

if (!$user) {
    die("User not found in database.");
}

$current_user_id = $user['user_id']; 

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$res = $stmt->get_result();

$itemDetails = [];
$subtotal = 0;

while ($row = $res->fetch_assoc()) {
    $itemDetails[] = $row;
    $subtotal += ($row['price'] * $row['quantity']);
}
$selectedItems = isset($_POST['selected_items']) ? json_decode($_POST['selected_items'], true) : [];
$payment_mode = $_POST['payment_mode'] ?? 'Takeaway';
$deliveryFee = ($payment_mode === 'Takeaway') ? 0 : 130;
$total = $subtotal + $deliveryFee;

$orderId = time(); 
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css' />
    <link rel="stylesheet" href="order_review.css">
    <title>Complete Order</title>
</head>
<body>
    <?php include('nav-logged.php'); ?>
    
    <div class="container mt-4">
        <div class="title text-center mb-4">
            <h3>Hi <?= htmlspecialchars($user['firstname'] ?? $user['firstName']) ?>, Complete your order!</h3>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card p-4 shadow-sm">
                    <h4>Customer Details</h4>
                    <hr>
                    <form action="process_order.php" method="post">
                        <input type="hidden" name="total" value="<?= $total ?>">
                        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                        <input type="hidden" name="selected_items" value='<?= json_encode($selectedItems) ?>'>
                        <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($payment_mode) ?>">

                        <div class="row mb-3">
                            <div class="col">
                                <label>First Name:</label>
                                <input type="text" class="form-control" name="firstName" value="<?= htmlspecialchars($user['firstname'] ?? $user['firstName']) ?>" required>
                            </div>
                            <div class="col">
                                <label>Last Name:</label>
                                <input type="text" class="form-control" name="lastName" value="<?= htmlspecialchars($user['lastname'] ?? $user['lastName']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Contact Number:</label>
                            <input type="text" class="form-control" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address:</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Delivery Address:</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Order Note (Optional):</label>
                            <textarea class="form-control" name="order_note" rows="2"></textarea>
                        </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card p-4 shadow-sm bg-light">
                    <h4>Order Summary</h4>
                    <hr>
                    <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($itemDetails as $item) : ?>
                            <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" width="60" height="60" class="rounded me-3" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= htmlspecialchars($item['itemName']) ?></div>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?> x MMK <?= number_format($item['price']) ?></small>
                                </div>
                                <div class="fw-bold text-end">Rs <?= number_format($item['total_price']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="fee-section">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>MMK <?= number_format($subtotal) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Payment:</span>
                            <span class="badge bg-primary"><?= htmlspecialchars($payment_mode) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Delivery Fee:</span>
                            <span>MMK <?= number_format($deliveryFee) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Grand Total:</span>
                            <span class="text-danger">MMK <?= number_format($total) ?></span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?php if ($payment_mode == 'Card'): ?>
                            <button type="submit" class="btn btn-dark w-100 py-2">
                                <i class="fa-regular fa-credit-card me-2"></i> Pay Now
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                PLACE ORDER
                            </button>
                        <?php endif; ?>
                    </div>
                    </form> </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>