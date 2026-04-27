<?php
session_start();
require 'db_connection.php';
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$status = $_GET['status'] ?? 'All';
$email = $_SESSION['email'];

$user_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result()->fetch_assoc();
$user_id = $user_result['user_id'];

$sql = "SELECT o.*, u.firstName, u.lastName, u.contact 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.user_id = ?";

if ($status !== 'All') {
    $sql .= " AND o.order_status = ?";
}

$stmt = $conn->prepare($sql);
if ($status !== 'All') {
    $stmt->bind_param("is", $user_id, $status);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($orders as &$order) {
    $item_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $item_stmt->bind_param("i", $order['order_id']);
    $item_stmt->execute();
    $order['items'] = $item_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['status'])) {
    header('Content-Type: application/json');
    echo json_encode($orders);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css' />
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; padding-top: 100px; background: #FFFFFF; }
        .tabs { display: flex; cursor: pointer; justify-content: space-evenly; border-bottom: 1px solid #dee2e6; }
        .tab { padding: 10px 20px; font-size: 1.2rem; transition: 0.3s; }
        .tab.active { border-bottom: 2px solid #4DA8FF; color: #4DA8FF; }
        .tab-content { display: none; padding: 40px 60px; }
        .tab-content.active { display: block; }
        .order { background-color: #F2F4F7; padding: 20px; margin-bottom: 25px; border-radius: 5px; border: 1px solid #dee2e6; }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; font-weight: bold; }
        .customer-details { display: flex; justify-content: space-between; font-size: 1.1rem; }
        .status-text { font-weight: bold; }
        .status-pending .status-text { color: #ffc107; }
        .status-processing .status-text { color: #17a2b8; }
        .status-on-the-way .status-text { color: #4DA8FF; }
        .status-completed .status-text { color: #28a745; }
        .status-cancelled .status-text { color: #dc3545; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: #fff; margin: 15% auto; padding: 20px; width: 80%; max-width: 500px; border-radius: 5px; position: relative; }
        .modal-close { position: absolute; right: 15px; top: 5px; font-size: 25px; cursor: pointer; color: red; }
        .cancel-btn { background: #1E3A5F; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .review-btn { background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .star-rating { direction: rtl; display: inline-block; font-size: 2rem; }
        .star-rating input { display: none; }
        .star-rating label { color: #ddd; cursor: pointer; }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
    </style>
</head>
<body>
    <?php include_once("nav-logged.php"); ?>

    <div class="container mt-4">
        <div class="tabs">
            <div class="tab active" data-status="All">All</div>
            <div class="tab" data-status="Pending">Pending</div>
            <div class="tab" data-status="Processing">Processing</div>
            <div class="tab" data-status="On the way">On the way</div>
            <div class="tab" data-status="Completed">Completed</div>
            <div class="tab" data-status="Cancelled">Cancelled</div>
        </div>

        <div id="orders-container">
            <div class="tab-content active" id="all-orders"></div>
            <div class="tab-content" id="pending-orders"></div>
            <div class="tab-content" id="processing-orders"></div>
            <div class="tab-content" id="on-the-way-orders"></div>
            <div class="tab-content" id="completed-orders"></div>
            <div class="tab-content" id="cancelled-orders"></div>
        </div>
    </div>

    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('cancelModal')">&times;</span>
            <h4>Cancel Order</h4>
            <textarea id="cancelReason" class="form-control mb-3" placeholder="Reason for cancellation..."></textarea>
            <button id="cancelOrderBtn" class="btn btn-danger w-100">Confirm Cancellation</button>
        </div>
    </div>

    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('reviewModal')">&times;</span>
            <h4>Submit Your Review</h4>
            <form id="reviewForm" action="submit_reviews.php" method="POST">
                <input type="hidden" id="reviewOrderId" name="orderId">
                <div class="star-rating mb-3">
                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1">&#9733;</label>
                </div>
                <textarea name="reviewText" class="form-control mb-3" placeholder="Your feedback..."></textarea>
                <button type="submit" class="btn btn-success w-100">Submit Review</button>
            </form>
        </div>
    </div>

    <?php include_once('footer.html'); ?>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            fetchOrders('All');

            $('.tab').click(function() {
                $('.tab').removeClass('active');
                $(this).addClass('active');
                const status = $(this).data('status');
                
                $('.tab-content').removeClass('active');
                $(`#${status.toLowerCase().replace(/ /g, '-')}-orders`).addClass('active');
                
                fetchOrders(status);
            });
        });

        function fetchOrders(status) {
            const containerId = `${status.toLowerCase().replace(/ /g, '-')}-orders`;
            const container = document.getElementById(containerId);
            
            fetch(`orders.php?status=${status}`)
                .then(res => res.json())
                .then(data => {
                    if(data.length === 0) {
                        container.innerHTML = "<p class='text-center mt-4'>No orders found.</p>";
                        return;
                    }
                    
                    data.sort((a, b) => new Date(b.order_date) - new Date(a.order_date));
                    
                    container.innerHTML = data.map(order => `
                        <div class="order">
                            <div class="order-header">
                                <div>Order ID: #${order.order_id}</div>
                                <div class="${getStatusClass(order.order_status)}">Status: <span class="status-text">${order.order_status}</span></div>
                            </div>
                            <div class="mt-3">
                                <div class="customer-details"><strong>Name:</strong> <span>${order.firstName} ${order.lastName}</span></div>
                                <div class="customer-details"><strong>Address:</strong> <span>${order.address}</span></div>
                                <div class="customer-details"><strong>Contact:</strong> <span>${order.contact}</span></div>
                                <div class="customer-details"><strong>Date:</strong> <span>${new Date(order.order_date).toLocaleString()}</span></div>
                            </div>
                            <hr>
                            <div class="order-items">
                                ${order.items.map(item => `
                                    <div class="d-flex justify-content-between">
                                        <span>${item.itemName} (x${item.quantity})</span>
                                        <span>${item.total_price} MMK</span>
                                    </div>
                                `).join('')}
                                <div class="text-right mt-2"><strong>Grand Total: ${order.grand_total} MMK</strong></div>
                            </div>
                            <div class="mt-3">
                                ${order.order_status === 'Pending' ? `<button class="cancel-btn" onclick="openCancelModal(${order.order_id})">Cancel Order</button>` : ''}
                                ${(order.order_status === 'Completed' || order.order_status === 'Cancelled') && !order.review_text ? 
                                    `<button class="review-btn" onclick="openReviewModal(${order.order_id})">Write a Review</button>` : ''}
                            </div>
                        </div>
                    `).join('');
                });
        }

        function getStatusClass(status) {
            return 'status-' + status.toLowerCase().replace(/ /g, '-');
        }

        function openCancelModal(id) {
            $('#cancelModal').data('order-id', id).show();
        }

        function openReviewModal(id) {
            $('#reviewOrderId').val(id);
            $('#reviewModal').show();
        }

        function closeModal(id) {
            $(`#${id}`).hide();
        }

        $('#cancelOrderBtn').click(function() {
            const id = $('#cancelModal').data('order-id');
            const reason = $('#cancelReason').val();
            if(!reason) return alert("Please enter reason");

            $.post('cancel_order.php', { orderId: id, reason: reason }, function(res) {
                alert("Order Cancelled");
                location.reload();
            });
        });

        window.onclick = function(event) {
            if ($(event.target).hasClass('modal')) $('.modal').hide();
        }
    </script>
</body>
</html>