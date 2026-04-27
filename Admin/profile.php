<?php
session_start();
if (!isset($_SESSION['adminloggedin']) || !$_SESSION['adminloggedin']) {
    header('Location: login.php');
    exit;
}
$admin_email = $_SESSION['email'] ?? '';
include 'db_connection.php';

function getAdminInfo($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT firstName, lastName, email, contact, profile_image FROM users WHERE email = ? AND role = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return [
        'firstName' => $row['firstName'] ?? '',
        'lastName' => $row['lastName'] ?? '',
        'email' => $row['email'] ?? '',
        'contact' => $row['contact'] ?? '',
        'profile_image' => $row['profile_image'] ?? 'default.jpg'
    ];
}

function updateAdminInfo($email, $firstName, $lastName, $contact, $profile_image) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, contact = ?, profile_image = ? WHERE email = ? AND role = 'Admin'");
    $stmt->bind_param("sssss", $firstName, $lastName, $contact, $profile_image, $email);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contact = $_POST['contact'];
    $admin_info = getAdminInfo($admin_email);
    $profile_image = $admin_info['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        $profile_image = basename($_FILES["profile_image"]["name"]);
    }
    updateAdminInfo($admin_email, $firstName, $lastName, $contact, $profile_image);
    header('Location: profile.php');
    exit;
}
$admin_info = getAdminInfo($admin_email);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="sidebar.css">
  <style>
    .wrapper { display: flex; justify-content: center; padding: 40px 20px; }
    .container { 
        background: #fff; padding: 40px; border-radius: 15px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
        width: 100%; max-width: 650px; text-align: center;
    }
    .profile-image { 
        width: 130px; height: 130px; border-radius: 50%; 
        object-fit: cover; border: 4px solid #007bff; margin-bottom: 30px;
    }
    .form-row { display: flex; gap: 20px; margin-bottom: 25px; }
    .form-group { flex: 1; text-align: left; }
    .form-group label { 
        display: block; font-size: 14px; font-weight: 600; 
        margin-bottom: 8px; color: #444; 
    }
    .form-group input { 
        width: 100%; padding: 12px 15px; 
        border: 1px solid #e1e1e1; border-radius: 8px; 
        font-family: 'Poppins', sans-serif; font-size: 14px; color: #333;
        box-sizing: border-box; transition: 0.3s;
    }
    .form-group input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 5px rgba(0,123,255,0.2); }
    .form-group input[readonly] { background-color: #f8f9fa; color: #777; }

    .file-group { text-align: left; margin-top: 10px; }
    .file-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
    
    .save-btn { 
        background-color: #007bff; color: white; border: none; 
        padding: 14px; border-radius: 8px; cursor: pointer; 
        font-weight: 600; width: 100%; font-size: 16px; margin-top: 15px;
        transition: 0.3s;
    }
    .save-btn:hover { background-color: #0056b3; }
  </style>
</head>

<body>
  <div class="sidebar">
    <button class="close-sidebar" id="closeSidebar">&times;</button>
    <div class="profile-section">
      <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
      <div class="info">
        <h3>Welcome!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>
    <ul>
      <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
      <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profile Setting</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="header">
      <button id="toggleSidebar" class="toggle-button"><i class="fas fa-bars"></i></button>
      <h2><i class="fas fa-user"></i> Profile Setting</h2>
    </div>

    <div class="wrapper">
      <div class="container">
        <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile" class="profile-image">
        
        <form action="profile.php" method="post" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group">
              <label>First Name</label>
              <input type="text" name="firstName" value="<?php echo htmlspecialchars($admin_info['firstName']); ?>" required>
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" name="lastName" value="<?php echo htmlspecialchars($admin_info['lastName']); ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" value="<?php echo htmlspecialchars($admin_info['email']); ?>" readonly>
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" name="contact" value="<?php echo htmlspecialchars($admin_info['contact']); ?>" required>
            </div>
          </div>

          <div class="file-group">
            <label>Change Profile Image</label>
            <input type="file" name="profile_image" style="border:none; padding-left:0;">
          </div>

          <button type="submit" class="save-btn">Save Settings</button>
        </form>
      </div>
    </div>
  </div>

  <script src="sidebar.js"></script>
</body>
</html>