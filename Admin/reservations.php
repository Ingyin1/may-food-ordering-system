<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../../login.php");
  exit();
}
include 'db_connection.php';

date_default_timezone_set('Asia/Colombo');

$result = $conn->query("SELECT COUNT(*) AS total FROM reservations");
$totalReservations = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS today FROM reservations WHERE DATE(reservedDate) = CURDATE()");
$todaysReservations = $result->fetch_assoc()['today'];

$result = $conn->query("SELECT COUNT(*) AS upcoming FROM reservations WHERE reservedDate > CURDATE() AND status != 'Cancelled' AND status != 'Completed' AND status != 'On Process'");
$upcomingReservations = $result->fetch_assoc()['upcoming'];

$result = $conn->query("SELECT COUNT(*) AS cancelled FROM reservations WHERE status = 'Cancelled'");
$cancelledReservations = $result->fetch_assoc()['cancelled'];

$dateFilter = isset($_GET['dateFilter']) ? $_GET['dateFilter'] : '';
$statusFilter = isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '';

$params = [];
$types = "";
$conditions = [];

if (!empty($dateFilter)) {
  $conditions[] = "reservedDate = ?";
  $params[] = $dateFilter;
  $types .= "s";
}

if (!empty($statusFilter)) {
  $conditions[] = "status = ?";
  $params[] = $statusFilter;
  $types .= "s";
}

$query = "SELECT * FROM reservations";
if (!empty($conditions)) {
  $query .= " WHERE " . implode(' AND ', $conditions);
}
$query .= " ORDER BY reservedDate DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$reservations = [];
while ($row = $result->fetch_assoc()) {
  $reservations[] = $row;
}
?>

<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="admin_reservation.css">
  <style>
    .content{ margin-bottom: 40px; }
    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal.open { display: block; }
  </style>
</head>
<body>
  <div class="sidebar">
    <button class="close-sidebar" id="closeSidebar">&times;</button>
    <div class="profile-section">
      <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
      <div class="info">
        <h3>Welcome Back!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>
    <ul>
      <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="reservations.php" class="active"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="header">
      <button id="toggleSidebar" class="toggle-button"><i class="fas fa-bars"></i></button>
      <h2><i class="fas fa-calendar-alt"></i> Reservations</h2>
    </div>

    <div class="stats">
      <div class="stat-item" id="total">
        <div class="stat-icon" id="total-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-text"><p>Total</p><p><?php echo $totalReservations; ?></p></div>
      </div>
      <div class="stat-item" id="today">
        <div class="stat-icon" id="today-icon"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-text"><p>Today</p><p><?php echo $todaysReservations; ?></p></div>
      </div>
      <div class="stat-item" id="upcoming">
        <div class="stat-icon" id="upcoming-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-text"><p>Upcoming</p><p><?php echo $upcomingReservations; ?></p></div>
      </div>
      <div class="stat-item" id="cancelled">
        <div class="stat-icon" id="cancelled-icon"><i class="fas fa-calendar-times"></i></div>
        <div class="stat-text"><p>Cancelled</p><p><?php echo $cancelledReservations; ?></p></div>
      </div>
    </div>

    <div class="buttons-container">
      <button onclick="openaddReservationModal()"><i class="fas fa-calendar-plus"></i> &nbsp; Add Reservation</button>
      <div class="actions">
        <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
          <option value="">All</option>
          <option value="Pending">Pending</option>
          <option value="On Process">On Process</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
        <input type="date" id="dateFilter" name="dateFilter" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="filterByDate()">
        <button type="button" onclick="clearFilter()">Clear</button>
      </div>
    </div>

    <table id="userTable">
      <thead>
        <tr>
          <th>NO</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Guests</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($reservations) > 0): ?>
          <?php foreach ($reservations as $row): ?>
            <tr>
              <td><?php echo $row['reservation_id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['contact']); ?></td>
              <td><?php echo $row['noOfGuests']; ?></td>
              <td><?php echo $row['reservedDate']; ?></td>
              <td><?php echo $row['reservedTime']; ?></td>
              <td>
                <select id='status-<?php echo $row['reservation_id']; ?>' onchange="updateStatus('<?php echo $row['reservation_id']; ?>', this.value)" class='status-select'>
                  <option value='Pending' <?php echo ($row['status'] == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                  <option value='On Process' <?php echo ($row['status'] == 'On Process' ? 'selected' : ''); ?>>On Process</option>
                  <option value='Completed' <?php echo ($row['status'] == 'Completed' ? 'selected' : ''); ?>>Completed</option>
                  <option value='Cancelled' <?php echo ($row['status'] == 'Cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
              </td>
              <td>
                <button id='editbtn' onclick='openEditReservationModal(this)' 
                        data-id='<?php echo $row['reservation_id']; ?>' 
                        data-name='<?php echo htmlspecialchars($row['name']); ?>' 
                        data-contact='<?php echo htmlspecialchars($row['contact']); ?>' 
                        data-reservedDate='<?php echo $row['reservedDate']; ?>' 
                        data-reservedTime='<?php echo $row['reservedTime']; ?>' 
                        data-noOfGuests='<?php echo $row['noOfGuests']; ?>'><i class='fas fa-edit'></i></button>
                <button id='deletebtn' onclick="deleteItem('<?php echo $row['reservation_id']; ?>')"><i class='fas fa-trash'></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan='8' style='text-align: center;'>No Reservations Found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div id="addReservationModal" class="modal">
    <div class="modal-container">
      <form method="POST" action="add_reservation.php">
        <div class="modal-header">
          <h2>Add Reservation</h2>
          <span class="close-icon" onclick="closeaddReservationModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group"><input type="text" name="name" class="input" required><label class="label">Name</label></div>
          <div class="input-group"><input type="text" name="contact" class="input" required><label class="label">Contact</label></div>
          <div class="input-group"><input type="number" name="noOfGuests" class="input" required><label class="label">No Of Guests</label></div>
          <div class="input-group"><input type="date" name="reservedDate" class="input" required></div>
          <div class="input-group"><input type="time" name="reservedTime" class="input" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button" onclick="closeaddReservationModal()">Cancel</button>
          <button type="submit" class="button">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div id="editReservationModal" class="modal">
    <div class="modal-container">
      <form id="editReservationForm" method="POST" action="edit_reservation.php">
        <div class="modal-header">
          <h2>Edit Reservation</h2>
          <span class="close-icon" onclick="closeEditReservationModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group"><input type="text" name="name" id="editName" class="input" required><label class="label">Name</label></div>
          <div class="input-group"><input type="text" name="contact" id="editContact" class="input" required><label class="label">Contact</label></div>
          <div class="input-group"><input type="number" name="noOfGuests" id="editNoOfGuests" class="input" required><label class="label">No Of Guests</label></div>
          <div class="input-group"><input type="date" name="reservedDate" id="editReservedDate" class="input" required></div>
          <div class="input-group"><input type="time" name="reservedTime" id="editReservedTime" class="input" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button" onclick="closeEditReservationModal()">Cancel</button>
          <button type="submit" class="button">Update</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openaddReservationModal() { document.getElementById('addReservationModal').classList.add('open'); }
    function closeaddReservationModal() { document.getElementById('addReservationModal').classList.remove('open'); }

    function openEditReservationModal(button) {
      document.getElementById('editName').value = button.getAttribute('data-name');
      document.getElementById('editContact').value = button.getAttribute('data-contact');
      document.getElementById('editNoOfGuests').value = button.getAttribute('data-noOfGuests');
      document.getElementById('editReservedDate').value = button.getAttribute('data-reservedDate');
      document.getElementById('editReservedTime').value = button.getAttribute('data-reservedTime');
      
      let form = document.getElementById('editReservationForm');
      let hiddenInput = form.querySelector('input[name="reservation_id"]');
      if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'reservation_id';
        form.appendChild(hiddenInput);
      }
      hiddenInput.value = button.getAttribute('data-id');
      document.getElementById('editReservationModal').classList.add('open');
    }
    function closeEditReservationModal() { document.getElementById('editReservationModal').classList.remove('open'); }

    function updateStatus(id, status) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "update_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() { if (xhr.readyState == 4 && xhr.status == 200) location.reload(); };
      xhr.send("reservation_id=" + id + "&status=" + status);
    }

    function deleteItem(id) {
      if (confirm('Are you sure?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_reservation.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() { if (xhr.readyState == 4 && xhr.status == 200) location.reload(); };
        xhr.send("reservation_id=" + id);
      }
    }

    function filterByDate() {
      window.location.href = 'reservations.php?dateFilter=' + encodeURIComponent(document.getElementById('dateFilter').value) + '&statusFilter=' + encodeURIComponent(document.getElementById('statusFilter').value);
    }
    function filterByStatus() { filterByDate(); }
    function clearFilter() { window.location.href = 'reservations.php'; }
  </script>
  <script src="sidebar.js"></script>
</body>
</html>