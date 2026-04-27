<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../login.php");
  exit();
}

include 'db_connection.php';
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';

if ($orderId) {
  $stmt = $conn->prepare("
      SELECT o.*, u.firstName, u.lastName, u.email, u.contact 
      FROM orders o 
      JOIN users u ON o.user_id = u.user_id 
      WHERE o.order_id = ?
  ");
  $stmt->bind_param("i", $orderId);
  $stmt->execute();
  $result = $stmt->get_result();
  $order = $result->fetch_assoc();

  if (!$order) {
      echo "Order not found.";
      exit();
  }

  $itemsStmt = $conn->prepare("
      SELECT oi.*, mi.itemName, mi.image 
      FROM order_items oi 
      JOIN menuitem mi ON oi.itemId = mi.itemId 
      WHERE oi.order_id = ?
  ");
  $itemsStmt->bind_param("i", $orderId);
  $itemsStmt->execute();
  $itemsResult = $itemsStmt->get_result();
  $orderItems = [];
  while ($row = $itemsResult->fetch_assoc()) {
    $orderItems[] = $row;
  }
} else {
  echo "Invalid order ID.";
  exit();
}

$paymentMode = $order['pmode'] ?? 'Takeaway';
$deliveryFee = $order['delivery_fee'] ?? (($paymentMode === 'Takeaway') ? 0 : 130);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - View Order #<?php echo $orderId; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="admin_orders.css">
  <link rel="stylesheet" href="view_order.css">
  <style>
    #successOverlay {
      display: none; 
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      margin-bottom: 20px;
    }
    .back-btn-success {
      display: inline-block;
      margin-top: 10px;
      background-color: #198754;
      color: white;
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
    }
    .back-btn-success:hover {
      background-color: #146c43;
      color: white;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <button class="close-sidebar" id="closeSidebar">&times;</button>
    <div class="profile-section text-center p-3">
       <h3 class="text-white h5">Admin Panel</h3>
    </div>
    <ul>
      <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
      <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="header d-flex justify-content-between align-items-center p-3 shadow-sm rounded bg-white">
        <div>
          <button id="toggleSidebar" class="toggle-button btn btn-light border-0"><i class="fas fa-bars"></i></button>
          <h2 class="d-inline-block ms-2 mb-0 h4"><i class="fas fa-shopping-cart text-primary"></i> Order Detail #<?php echo htmlspecialchars($order['order_id']); ?></h2>
        </div>
        <a href="admin_orders.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>

    <div class="row mt-4">
      <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0 mb-4">
          <h5 class="mb-4">Items Purchased</h5>
          <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr><th>Dish</th><th>Price</th><th>Qty</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                        <td>Rs <?php echo number_format($item['price'], 2); ?></td>
                        <td>x<?php echo $item['quantity']; ?></td>
                        <td class="text-end fw-bold text-danger">Rs <?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 mb-4">
          <div id="successOverlay">
            <i class="fas fa-check-circle fa-3x mb-2"></i>
            <h5 class="mb-1">Success!</h5>
            <p class="small mb-2">Order status updated successfully.</p>
            <a href="admin_orders.php" class="back-btn-success">
               <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
          </div>

          <div id="formSection">
            <h5 class="mb-3">Update Status</h5>
            <form id="statusUpdateForm">
              <label class="small fw-bold mb-1">Status</label>
              <select class="form-select mb-3 border-primary" id="orderStatus" name="order_status">
                  <option value="Pending" <?= ($order['order_status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                  <option value="Processing" <?= ($order['order_status'] == 'Processing') ? 'selected' : '' ?>>Processing</option>
                  <option value="On the way" <?= ($order['order_status'] == 'On the way') ? 'selected' : '' ?>>On the way</option>
                  <option value="Completed" <?= ($order['order_status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                  <option value="Cancelled" <?= ($order['order_status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
              </select>

              <div id="cancelReasonContainer" class="mb-3" style="display: <?= ($order['order_status'] == 'Cancelled') ? 'block' : 'none' ?>;">
                <label class="form-label small">Cancellation Reason</label>
                <textarea class="form-control" name="cancel_reason" id="cancelReason" rows="2"><?= htmlspecialchars($order['cancel_reason'] ?? '') ?></textarea>
              </div>

              <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
              <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" id="updateBtn">Update Status</button>
            </form>
          </div>
          
          <hr>
          <div class="mb-2 d-flex justify-content-between small"><span>Subtotal</span> <strong>Rs <?php echo number_format($order['sub_total'], 2); ?></strong></div>
          <div class="mb-3 d-flex justify-content-between text-danger h5 mt-2 pt-2 border-top"><strong>Total</strong> <strong>Rs <?php echo number_format($order['grand_total'], 2); ?></strong></div>
        </div>

        <div class="card p-4 shadow-sm border-0 bg-light">
            <h5 class="mb-3 small fw-bold text-primary"><i class="fas fa-user-circle"></i> Customer Information</h5>
            
            <div class="mb-2">
                <label class="text-muted small d-block">Full Name</label>
                <span class="fw-bold"><?php echo htmlspecialchars($order['firstName'] . ' ' . $order['lastName']); ?></span>
            </div>

            <div class="mb-2">
                <label class="text-muted small d-block">Email Address</label>
                <span><?php echo htmlspecialchars($order['email']); ?></span>
            </div>

            <div class="mb-2">
                <label class="text-muted small d-block">Phone / Contact</label>
                <span><?php echo htmlspecialchars($order['contact']); ?></span>
            </div>

            <div class="mb-2">
                <label class="text-muted small d-block">Delivery Address</label>
                <p class="small mb-0 text-dark"><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
            </div>

            <div class="mt-3 pt-2 border-top">
                <label class="text-muted small d-block">Payment Method</label>
                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($order['pmode']); ?></span>
            </div>

            <?php if(!empty($order['note'])): ?>
            <div class="mt-3 p-2 bg-white rounded border-start border-warning border-4 shadow-sm">
                <label class="text-muted d-block" style="font-size: 0.75rem;">Order Note:</label>
                <small class="fst-italic text-secondary">"<?php echo htmlspecialchars($order['note']); ?>"</small>
            </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="sidebar.js"></script>
  <script>
    document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const btn = document.getElementById('updateBtn');
      const status = document.getElementById('orderStatus').value;
      const reason = document.getElementById('cancelReason').value.trim();

      if (status === 'Cancelled' && reason === '') {
        alert('Please provide a reason for cancellation.');
        return;
      }

      btn.innerHTML = "Updating...";
      btn.disabled = true;
      const formData = new FormData(this);
      fetch('update_order_status.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        document.getElementById('formSection').style.display = 'none';
        document.getElementById('successOverlay').style.display = 'block';
      })
      .catch(error => {
        alert("Error updating order.");
        btn.innerHTML = "Update Status";
        btn.disabled = false;
      });
    });
    document.getElementById('orderStatus').addEventListener('change', function() {
      document.getElementById('cancelReasonContainer').style.display = (this.value === 'Cancelled') ? 'block' : 'none';
    });
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');
    if(toggleBtn) { toggleBtn.addEventListener('click', () => sidebar.classList.toggle('active')); }
  </script>
</body>
</html>