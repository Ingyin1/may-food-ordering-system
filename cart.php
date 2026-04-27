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
  header('location:login.php');
  exit;
}

$user_id = $user['user_id'];

$stmt = $conn->prepare('SELECT * FROM cart WHERE user_id=?');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);
$itemCount = count($cartItems);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart | May Food</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; padding-top: 80px; }
    .cart-wrapper { margin-top: 30px; margin-bottom: 50px; }
    .cart-card { background: #fff; border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
    .cart-header { background: #fff; padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .cart-item { padding: 20px; border-bottom: 1px solid #f8f9fa; transition: background 0.3s; position: relative; }
    .item-img { width: 90px; height: 90px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .selection { width: 22px !important; height: 22px !important; cursor: pointer; accent-color: #ff714d; }
    .qty-group { display: flex; align-items: center; background: #f1f3f5; border-radius: 50px; padding: 2px; width: fit-content; }
    .qty-btn { width: 30px; height: 30px; border-radius: 50%; border: none; background: #fff; color: #333; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all 0.2s; }
    .qty-btn:hover { background: #ff714d; color: #fff; }
    .itemQty { width: 35px; border: none; background: transparent; text-align: center; font-weight: 700; font-size: 14px; }
    .summary-card { background: #fff; border-radius: 15px; border: none; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 100px; }
    .total-row { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #eee; font-size: 1.25rem; font-weight: 800; color: #222; }
    .checkout-btn { background: #ff714d; border: none; padding: 14px; border-radius: 10px; font-weight: 700; color: #fff; transition: all 0.3s; }
    .checkout-btn:hover { background: #e65a39; transform: translateY(-2px); color: #fff; }
    .delete-btn { color: #ccc; transition: color 0.2s; background: none; border: none; font-size: 1.2rem; }
    .delete-btn:hover { color: #dc3545; }
    .history-controls { display: flex; gap: 10px; }
    .btn-food { 
        background-color: #fff; border: 2px solid #ff714d; color: #ff714d; 
        border-radius: 30px; padding: 6px 16px; font-weight: 600; font-size: 0.85rem; 
        transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(255,113,77,0.1);
        display: flex; align-items: center; gap: 6px;
    }
    .btn-food:hover:not(:disabled) { background-color: #ff714d; color: #fff; transform: translateY(-1px); }
    .btn-food:disabled { border-color: #e2e8f0; color: #cbd5e0; cursor: not-allowed; box-shadow: none; }
    #redoBtn:not(:disabled) { border-color: #48bb78; color: #48bb78; }
    #redoBtn:hover:not(:disabled) { background-color: #48bb78; color: #fff; }
  </style>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <?php include_once($_SESSION['userloggedin'] ? 'nav-logged.php' : 'navbar.php'); ?>

  <div class="container cart-wrapper">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="cart-card">
          <div class="cart-header">
            <h4 class="m-0 fw-bold">Shopping Cart (<span id="cart-count"><?= $itemCount ?></span> Items)</h4>
            <div class="history-controls">
              <button id="undoBtn" class="btn-food" disabled>
                <i class="fas fa-rotate-left"></i> Undo
              </button>
              <button id="redoBtn" class="btn-food" disabled>
                <i class="fas fa-rotate-right"></i> Redo
              </button>
            </div>
          </div>
          
          <div class="cart-body">
            <ul class="list-unstyled mb-0" id="cart-list">
              <?php if ($itemCount > 0): ?>
                <?php foreach ($cartItems as $item) : ?>
                  <li class="cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <input type="checkbox" class="form-check-input selection" data-price="<?= $item['price'] ?>" checked>
                      </div>
                      <div class="col-auto">
                        <img src="uploads/<?= htmlspecialchars($item['image'] ?: 'default.jpg') ?>" alt="Food" class="item-img">
                      </div>
                      <div class="col">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['itemname'] ?? $item['itemName'] ?? ''); ?></h6>
                        <div class="qty-group mt-2">
                          <button class="qty-btn minus-btn" type="button" data-id="<?= $item['cart_id'] ?>" data-price="<?= $item['price'] ?>">
                            <i class="fas fa-minus"></i>
                          </button>
                          <input type="text" class="itemQty" value="<?= $item['quantity'] ?>" readonly>
                          <button class="qty-btn plus-btn" type="button" data-id="<?= $item['cart_id'] ?>" data-price="<?= $item['price'] ?>">
                            <i class="fas fa-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-3 text-end price-info">
                        <div class="text-muted small">MMK <?= number_format($item['price']) ?> x <span class="item-quantity"><?= $item['quantity'] ?></span></div>
                        <div class="fw-bold text-dark mt-1">MMK <span class="item-total-price-text"><?= number_format($item['total_price']) ?></span></div>
                        <button class="delete-btn mt-2 delete-icon" data-id="<?= $item['cart_id'] ?>">
                          <i class="far fa-trash-alt"></i>
                        </button>
                      </div>
                    </div>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="p-5 text-center empty-msg">
                  <i class="fas fa-shopping-basket fa-3x text-light mb-3"></i>
                  <p class="text-muted">Your cart is currently empty.</p>
                  <a href="menu.php" class="btn btn-outline-primary rounded-pill px-4">Browse Menu</a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="summary-card">
          <h5 class="summary-title">Order Summary</h5>
          <div class="price-row d-flex justify-content-between mb-2 text-muted">
            <span>Subtotal</span>
            <span>MMK <span id="subtotal">0</span></span>
          </div>
          <div class="payment-option my-4">
            <p class="fw-bold small mb-2 text-uppercase text-muted">Payment Method</p>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="payment_mode" id="takeaway" value="Takeaway" checked>
              <label class="form-check-label small" for="takeaway">Takeaway (No Fee)</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_mode" id="cash" value="Cash">
              <label class="form-check-label small" for="cash">Cash on Delivery</label>
            </div>
          </div>
          <div class="price-row d-flex justify-content-between mb-2 text-muted">
            <span>Delivery Fee</span>
            <span>MMK <span id="delivery-fee">0</span></span>
          </div>
          <div class="total-row">
            <span>Total</span>
            <span class="text-primary">MMK <span id="total">0</span></span>
          </div>
          <form id="checkout-form" action="order_review.php" method="post" class="mt-4">
            <input type="hidden" id="selected-items" name="selected_items">
            <input type="hidden" id="payment-mode-hidden" name="payment_mode">
            <button type="button" id="checkout-button" class="btn checkout-btn w-100 shadow-sm">
              Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('footer.html'); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let undoStack = [];
      let redoStack = [];

      function saveState() {
        undoStack.push(document.getElementById('cart-list').innerHTML);
        redoStack = []; 
        updateHistoryButtons();
      }

      function updateHistoryButtons() {
        document.getElementById('undoBtn').disabled = (undoStack.length === 0);
        document.getElementById('redoBtn').disabled = (redoStack.length === 0);
      }

      function rebindEvents() {
        document.querySelectorAll('.selection').forEach(cb => cb.addEventListener('change', calculateSubtotal));
        document.querySelectorAll('.plus-btn').forEach(btn => btn.onclick = function() { updateQuantity(this, 1); });
        document.querySelectorAll('.minus-btn').forEach(btn => btn.onclick = function() { updateQuantity(this, -1); });
        document.querySelectorAll('.delete-icon').forEach(btn => btn.onclick = deleteHandler);
      }

      function calculateSubtotal() {
        let subtotal = 0;
        const selected = document.querySelectorAll('.selection:checked');
        selected.forEach(checkbox => {
          const itemContainer = checkbox.closest('.cart-item');
          if (itemContainer) {
            const price = parseFloat(checkbox.dataset.price);
            const qty = parseInt(itemContainer.querySelector('.itemQty').value);
            subtotal += (price * qty);
          }
        });

        const selectedPaymentMode = document.querySelector('input[name="payment_mode"]:checked').value;
        const deliveryFee = selectedPaymentMode === 'Cash' ? 130 : 0;
        
        document.getElementById('subtotal').textContent = subtotal.toLocaleString();
        document.getElementById('delivery-fee').textContent = deliveryFee.toLocaleString();
        document.getElementById('total').textContent = (subtotal + deliveryFee).toLocaleString();
        document.getElementById('cart-count').textContent = document.querySelectorAll('.cart-item').length;
      }

      function updateQuantity(element, change) {
        saveState();
        let itemContainer = element.closest('.cart-item');
        let itemId = element.dataset.id;
        let itemPrice = parseFloat(element.dataset.price);
        let qtyInput = itemContainer.querySelector('.itemQty');
        let newQty = parseInt(qtyInput.value) + change;

        if (newQty < 1) return;

        qtyInput.value = newQty;
        itemContainer.querySelector('.item-quantity').textContent = newQty;
        let newTotalPrice = (itemPrice * newQty);
        itemContainer.querySelector('.item-total-price-text').textContent = newTotalPrice.toLocaleString();

        $.ajax({
          url: 'update_cart_quantity.php',
          method: 'post',
          data: { id: itemId, quantity: newQty, total_price: newTotalPrice },
          success: calculateSubtotal
        });
      }

      const deleteHandler = function() {
        saveState();
        let itemId = this.dataset.id;
        let $item = $(this).closest('.cart-item');
        
        $item.fadeOut(300, function() {
            $(this).remove();
            if (document.querySelectorAll('.cart-item').length === 0) {
                document.getElementById('cart-list').innerHTML = `
                    <li class="p-5 text-center empty-msg">
                      <i class="fas fa-shopping-basket fa-3x text-light mb-3"></i>
                      <p class="text-muted">Your cart is currently empty.</p>
                      <a href="menu.php" class="btn btn-outline-primary rounded-pill px-4">Browse Menu</a>
                    </li>`;
            }
            calculateSubtotal();
            updateHistoryButtons();
        });

        $.ajax({ url: 'delete_cart_item.php', method: 'post', data: { id: itemId } });
      };

      document.getElementById('undoBtn').onclick = function() {
        if (undoStack.length > 0) {
          redoStack.push(document.getElementById('cart-list').innerHTML);
          document.getElementById('cart-list').innerHTML = undoStack.pop();
          rebindEvents();
          calculateSubtotal();
          updateHistoryButtons();
        }
      };

      document.getElementById('redoBtn').onclick = function() {
        if (redoStack.length > 0) {
          undoStack.push(document.getElementById('cart-list').innerHTML);
          document.getElementById('cart-list').innerHTML = redoStack.pop();
          rebindEvents();
          calculateSubtotal();
          updateHistoryButtons();
        }
      };

      document.querySelectorAll('input[name="payment_mode"]').forEach(input => {
        input.addEventListener('change', calculateSubtotal);
      });

      document.getElementById('checkout-button').onclick = function() {
        const selectedItems = [];
        document.querySelectorAll('.selection:checked').forEach(checkbox => {
          const itemContainer = checkbox.closest('.cart-item');
          selectedItems.push({
            id: itemContainer.querySelector('.delete-icon').dataset.id,
            quantity: itemContainer.querySelector('.itemQty').value
          });
        });

        if (selectedItems.length === 0) {
          alert('Please select at least one dish to order!');
          return;
        }

        document.getElementById('selected-items').value = JSON.stringify(selectedItems);
        document.getElementById('payment-mode-hidden').value = document.querySelector('input[name="payment_mode"]:checked').value;
        document.getElementById('checkout-form').submit();
      };

      rebindEvents();
      calculateSubtotal();
    });
  </script>
</body>
</html>